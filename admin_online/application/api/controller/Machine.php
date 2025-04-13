<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\MachineConfig;
use app\common\model\MachineOrder;
use app\common\model\MachineProduce;
use think\Exception;
use think\Queue;
use think\Validate;
use think\Db;


class Machine extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    // protected $needAuthorize = '*';
    
    /**
     * 获取矿机产品列表
     * @return void
     */
    public function getProductList() {
        try {
            $id = $this->request->param('product_id') ?? 0;
            $where = $id ? ['id' => $id, 'status' => 1] : ['status' => 1];
            $list = MachineConfig::where($where)->select();
            $data = [];
            $expireLangArr = ['en' => ['1' => 'day', '2' => 'month'], 'cn' => ['1' => '天', '2' => '月'],'zh-TW' => ['1' => '天', '2' => '月']];
            $lang = $this->request->header('lang');
            foreach ($list as $v) {
                $data[] = [
                    'product_id'        => $v->id,
                    'cycle'             => $v->expire_val . $expireLangArr[$this->request->header('lang')][$v->expire_unit],
                    'name'              => $lang == 'cn' ?  $v->name_cn : $lang == 'zh-TW' ?$v->name_cn : $v->name_en,
                    'stars'             => $v->stars,
                    'price'             => abs($v->price),
                    'image'             => $v->image,
                    'low_produce'       => $v->low_produce,
                    'high_produce'      => $v->high_produce,
                    'calc'              => $v->calc,
                    'power'             => $v->power,
                ];
            }
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 租赁矿机产品
     * @return void
     * @throws \Exception
     */
    public function leaseProduct() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                     //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'product_id' => 'require',                                                                              //产品id
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $productId = $params['product_id'];
            $coin = 'USDT';
            Db::startTrans();
            try {
                $user = $this->auth->getUser();
                $machineConfig = MachineConfig::where('id', $productId)->lock(true)->find();
                if (!$machineConfig) {                                                                                  //判断产品是否存在
                    exception(__('Product does not exist'));
                }
                if ($machineConfig->status != MachineConfig::OPEN_STATUS) {                                             //判断产品是否开启
                    exception(__('Product closed'));
                }
                $amount = $machineConfig->type == 1 ? $machineConfig->price : 0;
                $where = ['machine_config_id' => $productId];
                if ($machineConfig->buy_num_mode == 1) {                                                                 //1=不包括过期
                    $where = ['machine_config_id' => $productId, 'status' => 1];
                }
                $machineOrderCount = $user->machineOrder()->where($where)->lock(true)->count('id');                     //矿机订单统计
                if ($machineConfig->allow_buy_num <= $machineOrderCount) {                                              //判断允许购买次数
                    exception(__('The number of leases has reached the limit'));
                }
                //减少用户钱包余额
                $userWallet = (new \app\common\services\UserWalletService($user->id, $coin, $amount))->reduceUBalance();
                $nowTime = time();
                //新增矿机订单
                $machineOrderData = [
                    'machine_config_id'   => $productId,
                    'wallet_pay'          => $amount,
                    'pay_time'            => $nowTime,
                    'buy_time'            => $nowTime,
                    'expire_time'         => strtotime(sprintf('+%s %s', $machineConfig->expire_val, $machineConfig->expire_unit == 1 ? 'day' : 'month')),
                ];
                $machineOrder = $user->machineOrder()->save($machineOrderData);
                //新增矿机产出
                $machineProduceData = ['start_time'  => $nowTime, 'end_time'  => strtotime('+1 day', $nowTime)];
                $machineProduce = $machineOrder->machineProduce()->save($machineProduceData);
                //新增购买矿机账户明细
                $accountDetailsData = [
                    'coin'              => $coin,
                    'type'              => 9,
                    'change_quantity'   => -$amount,
                    'current_quantity'  => $userWallet->balance,
                    'extend_info'       => json_encode(['machine_order_id' => $machineOrder->id])
                ];
                $user->accountDetails()->save($accountDetailsData);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $jobHandlerClassName = 'app\api\job\MachineProduceJob';
            $jobQueueName = "machine-produce-close";
            $jobData = $machineProduce->id;
            Queue::later($machineProduce->end_time - $machineProduce->start_time - 1, $jobHandlerClassName, $jobData, $jobQueueName);
            $this->success(__('Request Success'));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取矿机订单列表
     * @return void
     */
    public function getOrderList() {
        try {
            $params = $this->request->param();
            $rule = [
                'status' => 'require|in:0,1',                                                                           //状态
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $status = $params['status'];
            $page = $params['page'] ?? 1;                                                                               //页数
            $pageSize = $params['page_size'] ?? 10;                                                                     //页数大小
            $produceSql = '( SELECT sum(produce_eth) AS produce_eth_total, machine_order_id FROM `eth_machine_produce` GROUP BY `machine_order_id` )';
            $configSql = '( SELECT * FROM `eth_machine_config` )';
            $model = new MachineOrder();
            $data = $model->alias('o')
                ->join([$configSql=> 'c'], 'c.id = o.machine_config_id', 'LEFT')
                ->join([$produceSql=> 'p'], 'p.machine_order_id = o.id', 'LEFT')
                ->where(['o.uid' => $this->auth->id, 'o.status' => $status])
                ->field('o.buy_time, o.expire_time, o.status, c.expire_val, c.expire_unit, c.price, c.name_en,c.name_cn, p.produce_eth_total')
                ->page($page, $pageSize)
                ->order('o.buy_time')
                ->select();
            $count = $model->where(['uid' => $this->auth->id, 'status' => $status])->count('id');
            $expireLangArr = ['en' => ['1' => 'day', '2' => 'month'], 'cn' => ['1' => '天', '2' => '月'],'zh-TW' => ['1' => '天', '2' => '月']];
            $list = [];
            foreach ($data as $v) {
                $list[] = [
                    'name'                  => $this->request->header('lang') == 'cn' ?  $v->name_cn : $v->name_en,
                    'cycle'                 => $v->expire_val . $expireLangArr[$this->request->header('lang')][$v->expire_unit],
                    'price'                 => $v->price,
                    'buy_time'              => date('Y-m-d H:i:s', $v->buy_time),
                    'end_time'              => date('Y-m-d H:i:s', $v->expire_time),
                    'produce_eth_total'     => $v->produce_eth_total ? $v->produce_eth_total : 0,
                    'status'                => $v->status
                ];
            }
            $response = ['count' => $count, 'list' => $list];
            $this->success(__('Request Success'), $response);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 我的矿机
     * @return void
     */
    public function myMachine() {
        try {
            $trusteeshipCount = MachineOrder::where(['uid' => $this->auth->id, 'status' => 1])->count('id');
    
            $totalMining = MachineProduce::where('machine_order_id', 'IN', function($query){
                $query->table('eth_machine_order')->where('uid', $this->auth->id)->field('id');;
            })->sum('produce_eth');
            $dayMining = MachineProduce::where('machine_order_id', 'IN', function($query){
                $query->table('eth_machine_order')->where('uid', $this->auth->id)->field('id');;
            })->whereTime('start_time', 'today')->sum('produce_eth');
            $data = [
                'trusteeship_count' => $trusteeshipCount,
                'total_mining'      => sprintf("%.6f", $totalMining),
                'day_mining'        => sprintf("%.6f", $dayMining),
            ];
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}