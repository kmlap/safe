<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\ContractConfig;
use app\common\model\ContractFollow;
use app\common\model\ContractOrder;
use app\common\model\UserWallet;
use think\Exception;
use think\Validate;
use think\Db;
use think\Queue;

class Contract extends Api
{
    
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $needAuthorize = [];
    
    
    /**
     * 获取秒合约列表
     * @return void
     */
    public function getContractList() {
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
            $where = [];
            if ($type == 1) {                                                                                           //推荐
                $where['c.recommend'] = 1;
            }
            if ($type == 2) {
                $followIds = ContractFollow::where(['uid' => $this->auth->id])->column('contract_config_id');
                $where['c.id'] = ['IN', join(',', $followIds)];
            }
            $data = ContractConfig::alias('c')
                ->field('c.id AS contract_id, c.coin, w.image AS icon')
                ->where($where)
                ->join('eth_wallet_coin w', 'w.coin=c.coin', 'LEFT')
                ->order('c.weigh ASC')
                ->select();
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取秒合约详细
     * @return void
     */
    public function getContractDetails() {
        try {
            $params = $this->request->param();
            $rule = [
                'contract_id' => 'require',                                                                             //秒合约id
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $contractId = $params['contract_id'];
            $contractConfig = ContractConfig::alias('c')
                ->field('c.id AS contract_id, c.coin, c.configjson, w.image AS icon, CASE WHEN f.id is null THEN 0 ELSE 1 END AS is_follow')
                ->where('c.id', $contractId)
                ->join('eth_wallet_coin w', 'w.coin=c.coin', 'LEFT')
                ->join('eth_contract_follow f', 'f.contract_config_id=c.id', 'LEFT')
                ->order('c.weigh ASC')
                ->find();
            if (!$contractConfig) {
                $this->error(__('Contract does not exist'));
            }
            $arr = json_decode($contractConfig['configjson']);
            unset($contractConfig['configjson']);
            $riskList = [];
            $fallList = [];
            foreach ($arr as $k => $v) {
                if ($v->increase > 0) {
                    $riskList[] = $v;
                } else {
                    $fallList[] = $v;
                }
            }
            $contractConfig['risk_list'] = $riskList;
            $contractConfig['fall_list'] = $fallList;
            $data = $contractConfig->toArray();
            $userWallet = UserWallet::alias('u')
                ->field('u.balance, w.image AS icon, w.coin')
                ->where('u.uid', $this->auth->id)
                ->join('eth_wallet_coin w', 'w.id=u.wallet_coin_id', 'LEFT')
                ->select();
            $data['user_wallet_list'] = $userWallet;
            $this->success(__('Request Success'), $data);
            
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 关注秒合约
     * @return void
     */
    public function followContract() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                    //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'contract_id' => 'require',                                                                             //秒合约id
                'status'      => 'require|in:1,0'                                                                       //状态:1=关注,0=取关
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $contractId = $params['contract_id'];
            $status = $params['status'];
            $contractFollow = ContractFollow::where(['uid' => $this->auth->id, 'contract_config_id' => $contractId])->find();
            if ($status == 1) {
                $result = $contractFollow ? true : (new ContractFollow())->save(['uid' => $this->auth->id, 'contract_config_id' => $contractId]);
            } else {
                $result = $contractFollow ? $contractFollow->delete() : true;
            }
            if (!$result) {
                $this->error(__('Request Fail'));
            }
            $this->success(__('Request Success'));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取K线数据
     * @return void
     */
    public function getKlineData() {
        try {
            $params = $this->request->param();
            $rule = [
                'contract_id' => 'require',                                                                             //秒合约id
                'interval'    => 'require',                                                                             //间隔
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $contractId = $params['contract_id'];
            $interval = $params['interval'];
            $contractConfig = ContractConfig::where('id', $contractId)->field('id, coin')->find();
            if (!$contractConfig) {
                $this->error(__('Contract does not exist'));
            }
            switch ($interval) {
                case '1':
                    $period = "1min";
                    break;
                case '5':
                    $period = "5min";
                    break;
                case '15':
                    $period = "15min";
                    break;
                case '30':
                    $period = "30min";
                    break;
                case '60':
                    $period = "60min";
                    break;
                case 'd':
                    $period = "1day";
                    break;
                default:
                    exit;
            }
            $url = "https://api-aws.huobi.pro/market/history/kline?period=" . $period . "&size=500&symbol=" . strtolower($contractConfig->coin . 'usdt');
            $response = curl_request($url);
            $response = json_decode($response, 1);
            $list = $response['data'];
            $data = [];
            //开盘(open)，收盘(close)，最低(lowest)，最高(highest)
            foreach ($list as $k => $v) {
                $data[$k] = [
                    'now_time'  => $v['id'],
                    'close'     => $v['close'],
                    'open'      => (string)$v['open'],
                    'high'      => (string)$v['high'],
                    'low'       => $v['low'],
                    'vol'       => $v['vol']
                ];
            }
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 秒合约下单
     * @return void
     * @throws \Exception
     */
    public function contractPlaceOrder() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                    //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'contract_id' => 'require',                                                                             //秒合约id
                'increase'    => 'require|float',                                                                       //涨幅百分比
                'odds'        => 'require|float',                                                                       //倍率
                'second'      => 'require|number',                                                                      //时间/秒
                'coin'        => 'require',                                                                             //下单币种
                'amount'      => 'require|gt:0'                                                                         //下单金额
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $contractId = $params['contract_id'];
            $increase = $params['increase'];
            $odds = $params['odds'];
            $second = $params['second'];
            $coin = strtoupper($params['coin']);
            $amount = $params['amount'];
            $nowTime = time();
            Db::startTrans();
            try {
                $contractConfig = ContractConfig::where('id', $contractId)->lock(true)->field('id, coin, low_amount, configjson')->find();
                if (!$contractConfig) {
                    $this->error(__('Contract does not exist'));
                }
                $orderAmount = $coin == 'USDT' ? $amount : get_usdt_rate($coin);
                // if ($contractConfig->low_amount > $orderAmount) {
                //     $this->error(__('The amount has exceeded the limit'));
                // }
                $configArr = json_decode($contractConfig->configjson);
                $exist = false;
                foreach ($configArr as $v) {
                    if ($v->second == $second && $v->increase == $increase && $v->odds == $odds) {
                        
                        if ($v->min_amount > $orderAmount) {
                            $this->error(__('The minimum bet amount is: '.$v->min_amount.' USDT'));
                        }
                        
                        
                        $exist = true;
                        break;
                    }

                }
                

                if (!$exist) {
                    $this->error(__('Inconsistent parameters'));
                }
                $user = $this->auth->getUser();
                //减少用户钱包余额
                $userWallet = (new \app\common\services\UserWalletService($user->id, $coin, $amount))->reduceUBalance();
                //新增秒合约订单
                $contractOrderData = [
                    'contract_config_id' => $contractId,
                    'start_time'         => $nowTime,
                    'end_time'           => $nowTime + $second,
                    'increase'           => $increase,
                    'odds'               => $odds,
                    'amount'             => $amount,
                    'amount_unit'        => $coin,
                    'start_price'        => get_usdt_rate($contractConfig->coin),
                    'risk_mangement'     => $user->risk
                ];
                $contractOrder = $user->contractOrder()->save($contractOrderData);
                //新增质押锁仓账户明细
                $accountDetailsData = [
                    'coin'              => $coin,
                    'type'              => 13,
                    'change_quantity'   => -$amount,
                    'current_quantity'  => $userWallet->balance,
                    'extend_info'       => json_encode(['contract_order_id' => $contractOrder->id])
                ];
                $user->accountDetails()->save($accountDetailsData);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $jobHandlerClassName = 'app\api\job\ContractOrderJob';
            $jobQueueName = "contract-order-close";
            $jobData = ['coin' => $contractConfig->coin, 'order_id' => $contractOrder->id];
            Queue::later($second - 1, $jobHandlerClassName, $jobData, $jobQueueName);
            //Queue::push($jobHandlerClassName, $jobData, $jobQueueName);
            $this->success(__('Request Success'));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取秒合约订单列表
     * @return void
     */
    public function getOrderList() {
        try {
            $params = $this->request->param();
            $rule = [
                'contract_id' => 'require',                                                                             //秒合约id
                'status' => 'require|in:0,1',                                                                           //状态
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $contractId = $params['contract_id'];
            $status = $params['status'];
            $page = $params['page'] ?? 1;                                                                               //页数
            $pageSize = $params['page_size'] ?? 10;                                                                     //页数大小
            $data = ContractOrder::alias('o')
                ->field('o.*, w.image, w.coin')
                ->where(['o.contract_config_id' => $contractId, 'o.uid' => $this->auth->id, 'o.status' => $status])
                ->join('eth_contract_config c', 'c.id=o.contract_config_id', 'LEFT')
                ->join('eth_wallet_coin w', 'w.coin=c.coin', 'LEFT')
                ->order('o.id DESC')
                ->page($page, $pageSize)
                ->select();
            $list = [];
            foreach ($data as $k => $v) {
                $temp = [
                    'order_id'      => $v->id,
                    'icon'          => $v->image,
                    'coin'          => $v->coin,
                    'amount'        => floatval($v->amount) . $v->amount_unit,
                    'buy_price'     => $v->start_price,
                    'second'        => $v->end_time - $v->start_time,
                    'increase'      => (string)floatval($v->increase),
                    'odds'          => $v->odds,
                    'buy_time'      => date('Y-m-d H:i:s', $v->start_time),
                    'sell_time'     => date('Y-m-d H:i:s', $v->end_time),
                    'status'        => $v->status
                ];
                if (!$status) {
                    $temp['sell_price'] = $v->end_price;
                    $temp['incr'] = (string)round(($v->end_price - $v->start_price) / $v->start_price, 3);
                    $temp['profit_loss'] = $v->lose_win == 1 ? floatval($v->profit_loss - $v->amount) . $v->amount_unit : floatval($v->profit_loss) . $v->amount_unit;
                }
                $list[] = $temp;
            }
            $count = ContractOrder::where(['contract_config_id' => $contractId, 'uid' => $this->auth->id, 'status' => $status])->count('id');
            $response = ['count' => $count, 'list' => $list];
            $this->success(__('Request Success'), $response);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取订单状态
     * @return void
     */
    public function findOrderStatus() {
        try {
            $params = $this->request->param();
            $rule = [
                'order_id' => 'require',                                                                                //秒合约id                 //状态
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $orderId = $params['order_id'];
            $data = ContractOrder::where('id', $orderId)->field('status')->find();
            if (!$data) {
                $this->error(__('Order does not exist'));
            }
            $this->success(__('Request Success'), $data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}