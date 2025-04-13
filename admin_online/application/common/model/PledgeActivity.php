<?php

namespace app\common\model;

use think\Model;


class PledgeActivity extends Model
{

    const OPEN_STATUS = 1;              //开启状态
    const CLOSE_STATUS = 0;             //关闭状态

    

    // 表名
    protected $name = 'pledge_activity';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    

    
    public function getCycleUnitList()
    {
        return ['1' => __('Cycle_unit 1'), '2' => __('Cycle_unit 2')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }
    
    //给图片路径加上域名
    public function setImageAttr($value) {
        return prefix_image($value);
    }



}
