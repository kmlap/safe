<?php

namespace app\admin\model\user;

use think\Model;


class Authentication extends Model
{

    

    

    // 表名
    protected $name = 'user_authentication';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status1_text',
        'status2_text'
    ];
    

    
    public function getStatus1List()
    {
        return ['0' => __('Status1 0'), '1' => __('Status1 1'), '2' => __('Status1 2')];
    }

    public function getStatus2List()
    {
        return ['0' => __('Status2 0'), '1' => __('Status2 1'), '2' => __('Status2 2')];
    }


    public function getStatus1TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status1']) ? $data['status1'] : '');
        $list = $this->getStatus1List();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatus2TextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status2']) ? $data['status2'] : '');
        $list = $this->getStatus2List();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
