<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\PledgeOrder;
use app\common\model\WalletCoin;
use think\Exception;
use app\common\model\PledgeActivity;
use think\Validate;
use think\Db;

class Pledge extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    // protected $needAuthorize = '*';
    
    /**
     * 获取搬砖产品列表
     * @return void
     */
    public function getProductList() {
        try {
            $id = $this->request->param('product_id') ?? 0;
            $where = $id ? ['id' => $id, 'status' => 1] : ['status' => 1];
            
            $lang = $this->request->header('lang');
            if($lang != 'cn'){
                $lang = 'en';
            }
            $list = PledgeActivity::where($where)->select();
            $data = [];
            $cycleLangArr = ['en' => ['1' => 'day', '2' => 'month'], 'cn' => ['1' => '天', '2' => '月'],'zh-TW' => ['1' => '天', '2' => '月']];
            foreach ($list as $v) {
                $data[] = [
                    'product_id'        => $v->id,
                    'cycle'             => $v->cycle_val . $cycleLangArr[$lang][$v->cycle_unit],
                    'name'              => $lang == 'cn' ?  $v->name_cn : $v->name_en,
                    'low_amount'        => sprintf("%.3f", $v->low_amount),
                    'high_amount'       => sprintf("%.3f", $v->high_amount),
                    'day_profit'        => sprintf("%.2f", $v->day_rate) . '%',
                    'coin_icon_list'    => WalletCoin::where('id', 'IN',$v->wallet_coin_ids)->column('image')
                ];
            }
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 托管产品
     * @return void
     * @throws \Exception
     */
    public function trusteeshipProduct() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                     //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'product_id' => 'require',                                                                              //产品id
                'amount'     => 'require|gt:0'                                                                          //金额
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $productId = $params['product_id'];
            $amount = sprintf("%.6f", $params['amount']);
            $coin = 'USDT';
            Db::startTrans();
            try {
                $user = $this->auth->getUser();
                $pledgeActivity = PledgeActivity::where('id', $productId)->lock(true)->find();
                if (!$pledgeActivity) {                                                                                 //判断产品是否存在
                    exception(__('Product does not exist'));
                }
                if ($pledgeActivity->status != PledgeActivity::OPEN_STATUS) {                                           //判断活动是否开启
                    exception(__('Product closed'));
                }
                if ($pledgeActivity->low_amount > $amount || $pledgeActivity->high_amount < $amount) {                  //判断是否超限额
                    exception(__('The amount has exceeded the limit'));
                }
                $where = ['pledge_activity_id' => $productId];
                $where = ['status' => ['in',[0,1]]]+$where;
                $PledgeOrderCount = $user->pledgeOrder()->where($where)->lock(true)->count('id');                     //质押订单统计
                if ($pledgeActivity->buy_num <= $PledgeOrderCount) {                                              //判断允许购买次数
                    exception(__('The number of leases has reached the limit'));
                }
                //减少用户钱包余额
                $userWallet = (new \app\common\services\UserWalletService($user->id, $coin, $amount))->reduceUBalance();
                //新增质押锁仓订单
                $pledgeOrderData = [
                    'pledge_activity_id'  => $productId,
                    'amount'              => $amount,
                    'end_time'            => strtotime(sprintf('+%s %s', $pledgeActivity->cycle_val, $pledgeActivity->cycle_unit == 1 ? 'day' : 'month')),
                    'pay_time'            => time(),
                    'wallet_pay'          => $amount,
                    'profit_top_day'      => $pledgeActivity->cycle_unit == 1 ? $pledgeActivity->cycle_val : $pledgeActivity->cycle_val * 30,
                ];
                $pledgeOrder = $user->pledgeOrder()->save($pledgeOrderData);
                //新增质押锁仓账户明细
                $accountDetailsData = [
                    'coin'              => $coin,
                    'type'              => 2,
                    'change_quantity'   => -$amount,
                    'current_quantity'  => $userWallet->balance,
                    'extend_info'       => json_encode(['pledge_order_id' => $pledgeOrder->id])
                ];
                $user->accountDetails()->save($accountDetailsData);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success(__('Request Success'));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取托管信息
     * @return void
     */
    public function findTrusteeshipInfo() {
        try {
            $user = $this->auth->getUser();
            $data = [
                'trusteeship_amount' => $user->pledgeOrder()->where('status', 1)->sum('amount'),
                'total_amount'       => $user->pledgeOrder()->where('status', 'in', [0, 1])->sum('profit_amount'),
                'day_amount'         => $user->pledgeOrder()->where('status', 'in', [0, 1])->whereTime('profit_last_time', 'today')->sum('profit_total_amount'),
            ];
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取托管订单列表
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
            $fieldStr = 'a.name_en, a.name_cn, a.cycle_val,a.cycle_unit, a.day_rate, o.amount, o.createtime, o.end_time, o.profit_day, o.profit_total_amount, o.status';
            $model = new PledgeOrder();
            $data = $model->alias('o')
                ->field($fieldStr)
                ->join('eth_pledge_activity a', 'a.id=o.pledge_activity_id')
                ->where(['o.status' => $status, 'o.uid' => $this->auth->id])
                ->page($page, $pageSize)
                ->select();
            $count = $model->where(['status' => $status, 'uid' => $this->auth->id])->count('id');
            
            $lang = $this->request->header('lang');
            if($lang != 'cn'){
                $lang = 'en';
            }
            
            $cycleLangArr = ['en' => ['1' => 'day', '2' => 'month'], 'cn' => ['1' => '天', '2' => '月']];
            $list = [];
            foreach ($data as $v) {
                $list[] = [
                    'name'                  => $lang == 'cn' ?  $v->name_cn : $v->name_en,
                    'cycle'                 => $v->cycle_val . $cycleLangArr[$lang][$v->cycle_unit],
                    'amount'                => $v->amount,
                    'add_time'              => date('Y-m-d H:i:s', $v->createtime),
                    'end_time'              => date('Y-m-d H:i:s', $v->end_time),
                    'profit_day'            => $v->profit_day,
                    'day_profit'            => sprintf("%.2f", $v->day_rate) . '%',
                    'profit_total_amount'   => $v->profit_total_amount,
                    'status'                => $v->status
                ];
            }
            $response = ['count' => $count, 'list' => $list];
            $this->success(__('Request Success'),  $response);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
}