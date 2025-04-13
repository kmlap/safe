<?php

namespace app\api\job;


use app\common\model\AccountDetails;
use app\common\model\CommissionDetails;
use app\common\model\MachineProduce;
use app\common\model\UserTeam;
use think\Queue;
use think\queue\Job;
use think\Exception;
use think\Db;
use think\Config;
use app\common\services\UserWalletService;

class MachineProduceJob {
    
    const USDT = 'USDT';
    const ETH = 'ETH';
    protected $errorMsg = '';
    
    // 默认执行的方法
    public function fire(Job $job, $id)
    {
        $isJobDone = $this->send($id);
        if ($isJobDone) {
            echo sprintf("%s 矿机产出id: %s 执行成功 ", date('Y-m-d H:i:s'), $id);
            //成功删除任务
            $job->delete();
        } else {
            //任务轮询2次后删除
            if ($job->attempts() < 2) {
                // 第1种处理方式：重新发布任务,该任务延迟5秒后再执行
                $job->release(5);
            }else{
                echo sprintf("%s 矿机产出id: %s error: %s ", date('Y-m-d H:i:s'), $id, $this->errorMsg);
                $job->delete();
            }
        }
    }
    
    private function send($id)
    {
        $newMachineProduce = false;
        Db::startTrans();
        try {
            $machineProduce = MachineProduce::where('id', $id)->find();
            if (!$machineProduce->status) {                                                                      //矿机已经产出
                Db::rollback();
                return true;
            }
            $machineOrder = $machineProduce->machineOrder;
            if (!$machineOrder) {                                                                                //如果订单已经不存在,则删除矿机产出
                $machineProduce->delete();
                Db::commit();
                return true;
            }
            $user = $machineProduce->machineOrder->user;
            $userWallet = (new UserWalletService($user->id, self::USDT))->getUserWallet();
            $machineSetting = Config::get('custom.machine_setting');
            array_multisort(array_column($machineSetting,'arrived_balance'),SORT_ASC, $machineSetting);
            $lowRandom = 0;
            $highRandom = 0;
            foreach ($machineSetting as $k => $v) {
                if ($k == 0) {
                    $lowRandom = $v['low'];
                    $highRandom = $v['high'];
                }
                if ($userWallet->balance >= $v['arrived_balance']) {
                    $lowRandom = $v['low'];
                    $highRandom = $v['high'];
                }
            }
            //随机产生ETH数
            $randomEth = round($lowRandom + mt_rand() / mt_getrandmax() * ($highRandom - $lowRandom), 6);
            $ethUserWallet = (new UserWalletService($user->id, self::ETH, $randomEth))->addBalance();
            //新增账户明细
            $accountDetailsData = [
                'coin' => self::ETH,
                'type' => 1,
                'change_quantity' => $randomEth,
                'current_quantity' => $ethUserWallet->balance,
                'extend_info' => json_encode(['machine_order_id' => $machineOrder->id])
            ];
            $user->accountDetails()->save($accountDetailsData);
            //新增收益明细
            $incomeDetailsData = [
                'type'        => 2,
                'income_usdt' => bcmul($randomEth, get_usdt_rate(self::ETH), 6),
                'income_coin' => $randomEth,
            ];
            $user->incomeDetails()->save($incomeDetailsData);
            //写入报表
            (new \app\common\model\PlatformReport())->writeReport('mining', $randomEth);               //写入平台报表
            (new \app\common\model\ProxyReport())->writeReport($user->aid,'mining', $randomEth);       //写入代理报表
            (new \app\common\model\UserReport())->writeReport($user->id,'mining', $randomEth);         //写入用户报表
            $machineProduce->produce_eth = $randomEth;
            $machineProduce->receive_status = 1;                                                            //领取状态:1=已领取,0未领取
            $machineProduce->status = 0;                                                                    //状态:1=进行中,0=结束
            $machineProduce->save();
            $this->rebate($machineOrder->id, $user->id, $randomEth);                                        //分佣
            $newStartTime = $machineProduce->end_time;
            $newEndTime = strtotime('+1 day', $machineProduce->end_time);
            //如果第二天的产出结束时间大于订单有效时间，则矿机产出和订单结束
            if ($newEndTime >= $machineOrder->expire_time) {
                $machineOrder->status = 0;
                $machineOrder->save();
            } else {                                                                                        //不大于则继续新增
                $newMachineProduceData = ['start_time' => $newStartTime, 'end_time' => $newEndTime];
                $newMachineProduce = $machineOrder->machineProduce()->save($newMachineProduceData);
            }
            Db::commit();
        } catch (Exception $e){
            Db::rollback();                                                                                 // 回滚事务
            $this->errorMsg = $e->getMessage() .$e->getLine();
            return false;
        }
        if ($newMachineProduce) {
            $jobHandlerClassName = 'app\api\job\MachineProduceJob';
            $jobQueueName = "machine-produce-close";
            $jobData = $newMachineProduce->id;
            Queue::later( $newMachineProduce->end_time - $newMachineProduce->start_time - 1, $jobHandlerClassName, $jobData, $jobQueueName);
        }
        return true;
    }
    
    /**
     * 分佣
     * @param int $orderId 订单id
     * @param int $uid  用户id
     * @param string $amount 用来分佣的金额
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function rebate(int $orderId, int $uid, string $amount) {
        $where = ['team' => $uid, 'level' => ['between', '1,3']];
        $userList =  UserTeam::where($where)->field('uid, level')->order('level ASC')->select();
        $accountDetailsData = [];
        $commissionDetailsData = [];
        $childUid = $uid;
        foreach ($userList as $v) {
            $commission = bcmul($amount, Config::get('custom.pledge_' . $v->level), 6);                //计算佣金
            $userWallet = (new UserWalletService($v->uid, self::ETH, $commission))->addBalance();      //增加eth余额
            $accountDetailsData[] = [                                                                       //新增质押分佣账户明细
                'uid'               => $v->uid,
                'coin'              => self::ETH,
                'type'              => 11,
                'change_quantity'   => $commission,
                'current_quantity'  => $userWallet->balance,
                'extend_info' => json_encode(['machine_order_id' => $orderId])
            ];
            $commissionDetailsData[] = [                                                                    //新增佣金明细账户明细
                'uid'           => $v->uid,
                'type'          => 1,
                'commission'    => $commission,
                'child_uid'     => $childUid,
            ];
            $childUid = $v->uid;
        }
        (new AccountDetails())->saveAll($accountDetailsData);
        (new CommissionDetails())->saveAll($commissionDetailsData);
    }
}