<?php

namespace app\admin\model\user;

use think\Model;


class C2c extends Model
{

    

    

    // 表名
    protected $name = 'user_c2c';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'c2c_time_text',
        'verify_time_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3'), '4' => __('Status 4')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getC2cTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['c2c_time']) ? $data['c2c_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getVerifyTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['verify_time']) ? $data['verify_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setC2cTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setVerifyTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
