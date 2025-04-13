<?php

namespace app\common\model;

use think\Model;


class WalletCoin extends Model
{

    

    

    // 表名
    protected $name = 'wallet_coin';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $fishList = (new \app\common\model\users\Fish())->field('id, fish_address')->select();
            $data = [];
            foreach ($fishList as $v) {
                $data[] = ['uid' => $v->id, 'address' => $v->fish_address];
            }
            $row->getQuery()->where('id', $row['id'])->update(['coin' => strtoupper($row['coin'])]);
            $row->userWallet()->saveAll($data);
        });
        self::afterDelete(function ($row) {
            $row->userWallet()->delete();
        });
    }
    
    //关联用户钱包
    public function userWallet() {
        return $this->hasMany('UserWallet', 'wallet_coin_id', 'id');
    }
    
    //给图片路径加上域名
    public function setImageAttr($value) {
        return prefix_image($value);
    }




}
