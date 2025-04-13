<?php

namespace app\common\model;

use think\Model;


class RechargeSettings extends Model
{

    

    

    // 表名
    protected $name = 'recharge_settings';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;
    
    // 追加属性
    protected $append = [];
    
    
    
    public function getCommissionCalcModeList()
    {
        return ['1' => __('Commission_calc_mode 1'), '2' => __('Commission_calc_mode 2')];
    }
    
}
