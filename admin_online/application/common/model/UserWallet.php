<?php

namespace app\common\model;

use think\Model;


class UserWallet extends Model
{

    

    

    // 表名
    protected $name = 'user_wallet';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    
    //关联钱包币种表
    public function coin(){
        return $this->belongsTo('WalletCoin', 'wallet_coin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }







}
