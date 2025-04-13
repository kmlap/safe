<?php

namespace app\common\model;

use think\Model;


class WithdrawSettings extends Model
{

    

    

    // 表名
    protected $name = 'withdraw_settings';
    
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
    
//    public function getDayStartTimeAttr($value, $data)
//    {
//
//       $value = $value ? $value : (isset($data['day_start_time']) ? $data['day_start_time'] : '');
//        return is_numeric($value) && $value != 0 ? date("Y-m-d H:i:s", $value) : "";
//    }
//
//    public function getDayEndTimeAttr($value, $data)
//    {
//        $value = $value ? $value : (isset($data['day_end_time']) ? $data['day_end_time'] : '');
//        return is_numeric($value) && $value != 0 ? date("Y-m-d H:i:s", $value) : "";
//    }
//
//    protected function setDayStartTimeAttr($value)
//    {
//        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
//    }
//
//    protected function setDayEndTimeAttr($value)
//    {
//        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
//    }


}
