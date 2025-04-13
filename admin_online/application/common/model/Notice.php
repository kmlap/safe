<?php

namespace app\common\model;

use think\Model;


class Notice extends Model
{

    

    

    // 表名
    protected $name = 'notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    
    
    public function getLangList()
    {
        return [
        'cn' => __('Lang cn'), 'en' => __('lang en'),'zh-TW'=> __('lang zhTW')
        ,'fr'=> __('lang fr'),'ja'=> __('lang ja'),'it'=> __('lang it')
        ,'de'=> __('lang de'),'sv'=> __('lang sv')];
    }
    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3'), '4' => __('Type 4'),'5'=>__('Type 5'),'6'=>__('Type 6')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }
    



}
