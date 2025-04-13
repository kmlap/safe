<?php

namespace app\admin\model\user;

use think\Model;


class Income extends Model
{

    

    

    // 表名
    protected $name = 'user_income';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function type()
    {
        return $this->belongsTo('app\admin\model\income\Type', 'icometype', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function fish()
    {
        return $this->belongsTo('app\admin\model\Fish', 'userid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
