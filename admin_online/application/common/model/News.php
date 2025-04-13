<?php

namespace app\common\model;

use think\Model;


class News extends Model
{

    

    

    // 表名
    protected $name = 'news';
    
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
        return ['cn' => __('Lang cn'), 'en' => __('lang en'),'zh-TW'=> __('lang zhTW')
        ,'fr'=> __('lang fr'),'ja'=> __('lang ja'),'it'=> __('lang it')
        ,'de'=> __('lang de'),'sv'=> __('lang sv')];
    }
    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }
    
    protected function setNewsTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    
    
    //给图片路径加上域名
    public function setImageAttr($value) {
        return prefix_image($value);
    }


}
