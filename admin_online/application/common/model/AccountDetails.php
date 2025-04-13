<?php

namespace app\common\model;

use think\Model;


class AccountDetails extends Model
{

    

    // 表名
    protected $name = 'account_details';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    

    
    public function getTypeList()
    {
        return [
            '1' => __('Type 1'),
            '2' => __('Type 2'),
            '3' => __('Type 3'),
            '4' => __('Type 4'),
            '5' => __('Type 5'),
            '6' => __('Type 6'),
            '7' => __('Type 7'),
            '8' => __('Type 8'),
            '9' => __('Type 9'),
            '10' => __('Type 10'),
            '11' => __('Type 11'),
            '12' => __('Type 12'),
            '13' => __('Type 13'),
            '14' => __('Type 14'),
            '15' => __('Type 15'),
            '16' => __('Type 16'),
            '17' => __('Type 17'),
        ];
    }







}
