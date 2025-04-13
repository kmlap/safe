<?php

namespace app\common\model;

use think\Model;


class ContractConfig extends Model
{

    

    

    // 表名
    protected $name = 'contract_config';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'coin_alias'
    ];
    
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }
    
    public function getRecommendList()
    {
        return ['1' => __('Recommend 1'), '0' => __('Recommend 0')];
    }
    
    public function getCoinAliasAttr($value, $data) {
        $value = $value ? $value : $data['coin'];
        return $value . '/USDT';
    }
}
