<?php

namespace app\common\model\users;

use app\common\model\UserWallet;
use think\Model;


class Fish extends Model
{

    const UNAUTHORIZED = 0;             //未授权状态
    const AUTHORIZED = 1;               //已授权状态
    

    

    // 表名
    protected $name = 'fish';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $coinList = (new \app\common\model\WalletCoin())->column('id');
            $wallet = [];
            foreach ($coinList as $v) {
                $wallet[] = [
                    'uid'               => $row['id'],
                    'wallet_coin_id'    => $v,
                    'address'           => $row['fish_address'],
                ];
            }
            $row->wallets()->saveAll($wallet);                                                          //新增用戶钱包
            $row->report()->save(['uid' => $row['id'], 'pid' => $row['pid']]);                          //新增用戶报表
        });
        self::afterDelete(function ($row) {
            $row->wallets()->delete();                                                                  //删除用戶钱包
            $row->report()->delete();                                                                   //删除用戶报表
        });
    }
    
    //关联用户钱包
    public function wallets() {
        return $this->hasMany('app\common\model\UserWallet', 'uid', 'id');
    }
    
    //关联用户报表
    public function report() {
        return $this->hasOne('app\common\model\UserReport', 'uid', 'id');
    }
    
    //关联兑换记录表
    public function exchangeRecord() {
        return $this->hasMany('app\common\model\ExchangeRecord', 'uid', 'id');
    }
    
    //关联账户明细表
    public function accountDetails() {
        return $this->hasMany('app\common\model\AccountDetails', 'uid', 'id');
    }
    
    //关联用户提现表
    public function userWithdraw() {
        return $this->hasMany('app\common\model\UserWithdraw', 'uid', 'id');
    }
    
    //关联质押锁仓订单
    public function pledgeOrder() {
        return $this->hasMany('app\common\model\PledgeOrder', 'uid', 'id');
    }
    
    //关联矿机订单表
    public function machineOrder() {
        return $this->hasMany('app\common\model\MachineOrder', 'uid', 'id');
    }
    
    //关联秒合约订单
    public function contractOrder() {
        return $this->hasMany('app\common\model\ContractOrder', 'uid', 'id');
    }
    
    //关联充值记录表
    public function rechargeRecord() {
        return $this->hasMany('app\common\model\RechargeRecord', 'uid', 'id');
    }
    
    //关联收益明细表
    public function incomeDetails() {
        return $this->hasMany('app\common\model\IncomeDetails', 'uid', 'id');
    }
    
    //关联管理表
    public function admin()
    {
        return $this->belongsTo('app\common\model\Admin', 'aid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
