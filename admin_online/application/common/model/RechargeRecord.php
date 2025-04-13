<?php

namespace app\common\model;

use think\Model;


class RechargeRecord extends Model
{

    

    

    // 表名
    protected $name = 'recharge_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
    ];
    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }

    protected function setRechargeTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    
    protected function setVerifyTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    
    //给图片路径加上域名
    public function setImageAttr($value) {
        return prefix_image($value);
    }
    
    //关联用户表
    public function user()
    {
        return $this->belongsTo('app\common\model\users\Fish', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
