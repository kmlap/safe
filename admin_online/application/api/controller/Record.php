<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\AccountDetails;
use app\common\services\UserWalletService;
use think\Exception;
use think\Validate;

class Record extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $needAuthorize = [];
    
    
    /**
     * 获取账户与汇率
     * @return void
     */
    public function findAccountAndRate() {
        try {
            $ethUserWallet = (new UserWalletService($this->auth->id, 'ETH'))->getUserWallet();
            $usdtUserWallet = (new UserWalletService($this->auth->id, 'USDT'))->getUserWallet();
            $totalProduce = AccountDetails::where(['uid' => $this->auth->id, 'coin' => 'ETH', 'type' => 10])->sum('change_quantity');
            $dayReward = AccountDetails::where(['uid' => $this->auth->id, 'coin' => 'ETH', 'type' => 10])->whereTime('createtime', 'today')->sum('change_quantity');
            $data = [
                'eth_balance'   =>  $ethUserWallet->balance,
                'usdt_balance'  =>  $usdtUserWallet->balance,
                'rate'          =>  get_usdt_rate('ETH'),
                'total_produce' =>  sprintf("%.6f", $totalProduce),
                'day_produce'   =>  sprintf("%.6f", $dayReward)
            ];
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取记录列表
     * @return void
     */
    public function getRecordList() {
        try {
            $params = $this->request->param();
            $rule = [
                'type' => 'require|in:1,2,3',                                                                           //类型
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $type = $params['type'];
            $page = $params['page'] ?? 1;                                                                               //页数
            $pageSize = $params['page_size'] ?? 10;                                                                     //页数大小
            $startTime = $params['start_time'] ?? 0;                                                                    //开始时间
            $endTime = $params['end_time'] ?? 0;                                                                        //结束时间
            $coin = 'ETH';
            $model = new AccountDetails();
            $fieldStr = 'w.image AS icon, a.change_quantity AS amount, a.coin, from_unixtime(a.createtime) AS create_time, a.type';
            $data = ['count' => 0, 'list' => []];
            switch ($type) {
                case 1:                                                                                                 //兑换
                    $extendInfo = $model->where(['uid' => $this->auth->id, 'coin' => $coin, 'type' => 7])->column('extend_info');
                    if (!$extendInfo) {
                        $this->success(__('Request Success'), $data);
                    }
                    $where = ['a.extend_info' => ['IN', join(',', $extendInfo) ]];
                    $countWhere = ['extend_info' => ['IN', join(',', $extendInfo)]];
                    break;
                case 2:                                                                                                 //挖矿
                    $where = ['a.uid' => $this->auth->id, 'a.type' => 10, 'a.coin' => $coin];
                    $countWhere = ['uid' => $this->auth->id, 'type' => 10, 'coin' => $coin];
                    break;
                case 3:                                                                                                 //佣金
                    $where = ['a.uid' => $this->auth->id, 'a.type' => ['IN',[11,12]], 'a.coin' => $coin];
                    $countWhere = ['uid' => $this->auth->id, 'type' => ['IN',[11,12]], 'coin' => $coin];
                    break;
            }
            if ($startTime && $endTime) {
                $where['a.createtime'] = ['between', [$startTime, $endTime]];
                $countWhere['createtime'] = ['between', [$startTime, $endTime]];
            }
            $list = $model->alias('a')
                ->join('eth_wallet_coin w', 'w.coin=a.coin', 'LEFT')
                ->where($where)
                ->order('a.createtime DESC')
                ->page($page, $pageSize)
                ->field($fieldStr)
                ->select();
            $count = $model->where($countWhere)->count('id');
            $data['count'] = $count;
            $data['list'] = $list;
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    
    
}