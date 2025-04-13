<?php

namespace app\common\model;

use think\Model;


class ExchangeRecord extends Model
{

    

    

    // 表名
    protected $name = 'exchange_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
