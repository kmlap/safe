<?php

namespace app\api\job;

use app\common\model\AccountDetails;
use app\common\services\UserWalletService;
use think\queue\Job;
use app\common\model\ContractOrder;
use think\Exception;
use think\Db;

class ContractOrderJob {
    
    protected $errorMsg = '';
    
    // 默认执行的方法
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->send($data);
        if ($isJobDone) {
            echo sprintf("%s 秒合约订单id: %s 执行成功 ", date('Y-m-d H:i:s'), $data['order_id']);
            //成功删除任务
            $job->delete();
        } else {
            //任务轮询2次后删除
            if ($job->attempts() < 2) {
                // 第1种处理方式：重新发布任务,该任务延迟5秒后再执行
                $job->release(5);
            }else{
                echo sprintf("%s 秒合约订单id: %s error: %s", date('Y-m-d H:i:s'), $this->errorMsg);
                $job->delete();
            }
        }
    }
    
    private function send($data)
    {
        bcscale(4);
        Db::startTrans();
        try {
            $order = ContractOrder::where('id', $data['order_id'])->lock(true)->find();
            if (!$order->status) {                                                                      //订单已经平仓
                Db::rollback();
                return true;
            }
            $coin = $data['coin'];                                                                      //秒合约币种
            $endPrice = 0;                                                                              //默认结束价格
            $startPrice = $order->start_price;                                                          //开始价格
            $increase = $order->increase;                                                               //张福蝶百分比%
            switch ($order->risk_mangement) {                                                           //判断风控
                case 0:                                                                                 //不控制
                    $endPrice = get_usdt_rate($coin);
                    break;
                case 1:                                                                                 //控制赢
                    $randNum = mt_rand(991, 999) / 10;                                                  //随机百分比
                    $endPrice = $increase > 0 ? bcadd($startPrice, bcmul($startPrice,$increase / $randNum)) : bcsub($startPrice, bcmul($startPrice,abs($increase) / $randNum));
                    break;
                case 2:                                                                                 //控制输
                    $randNum = mt_rand(1001, 1009) / 10;                                                //随机百分比
                    $endPrice = $increase > 0 ? bcadd($startPrice,  bcmul($startPrice, $increase / $randNum)) : bcsub($startPrice, bcmul($startPrice, abs($increase) / $randNum));
                    break;
            }
            if ($increase > 0) {                                                                        //买涨
                $targetPrice = bcadd($startPrice,  bcmul($startPrice, $increase / 100));
                $loseWin = bccomp($endPrice, $targetPrice) != -1 ? 1 : 2;                               //如果结束价格大于等于目标价格，则为赢，1=赢,2=输
            } else {                                                                                    //买跌
                $targetPrice = bcsub($startPrice,  bcmul($startPrice, abs($increase) / 100));
                $loseWin = bccomp($endPrice, $targetPrice) == -1 ? 1 : 2;;                              //如果结束价格小于等于目标价格，则为赢, 1=赢,2=输
            }
            $profitLossAmount = -$order->amount;                                                        //默认盈亏金额
            if ($loseWin === 1) {                                                                       //赢
                $profitLossAmount = bcmul($order->amount, $order->odds);                                //下单金额乘以倍率
                //用户钱包
                $userWallet = (new UserWalletService($order->uid, $order->amount_unit, $profitLossAmount))->addBalance();
                //新增秒合约盈亏账户明细
                $accountDetailsData = [
                    'coin'              => $order->amount_unit,
                    'type'              => 14,
                    'uid'               => $order->uid,
                    'change_quantity'   => $profitLossAmount,
                    'current_quantity'  => $userWallet->balance,
                    'extend_info'       => json_encode(['contract_order_id' => $order->id])
                ];
                AccountDetails::insert($accountDetailsData);
            }
            $order->end_price = $endPrice;
            $order->status = 0;
            $order->lose_win = $loseWin;
            $order->profit_loss = $profitLossAmount;
            $order->save();
            Db::commit();
        } catch (Exception $e){
            Db::rollback();                                                                                 // 回滚事务
            $this->errorMsg = $e->getMessage();
            return false;
        }
        return true;
    }
    
}