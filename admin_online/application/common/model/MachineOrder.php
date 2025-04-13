<?php

namespace app\common\model;

use think\Model;


class MachineOrder extends Model
{

    

    

    // 表名
    protected $name = 'machine_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    

    
    public function getStatusList()
    {
        return ['1' => __('status 1'), '0' => __('status 0')];
    }
    
    //关联矿机配置表
    public function config()
    {
        return $this->belongsTo('MachineConfig', 'machine_config_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    //关联用户表
    public function user()
    {
        return $this->belongsTo('app\common\model\users\Fish', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    
    protected function setBuyTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setExpireTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    
    //关联矿机产出表
    public function machineProduce() {
        return $this->hasMany('app\common\model\MachineProduce', 'machine_order_id', 'id');
    }

}
