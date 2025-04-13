<?php

namespace app\common\model;

use think\Model;


class PledgeOrder extends Model
{

    

    

    // 表名
    protected $name = 'pledge_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    
    
    //关联质押活动表
    public function activity()
    {
        return $this->belongsTo('PledgeActivity', 'pledge_activity_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    //关联用户表
    public function user()
    {
        return $this->belongsTo('app\common\model\users\Fish', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }
    
    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setProfitLastTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    


}
