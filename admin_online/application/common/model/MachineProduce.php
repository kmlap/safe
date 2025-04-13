<?php

namespace app\common\model;

use think\Model;


class MachineProduce extends Model
{

    

    

    // 表名
    protected $name = 'machine_produce';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    
    public function getReceiveStatusList()
    {
        return ['1' => __('Receive_status 1'), '0' => __('Receive_status 0')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }
    
    //关联矿机订单表
    public function machineOrder() {
        return $this->belongsTo('MachineOrder', 'machine_order_id', 'id');
    }

    protected function setStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
