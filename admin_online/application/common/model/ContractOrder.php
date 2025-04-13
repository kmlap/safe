<?php

namespace app\common\model;

use think\Model;


class ContractOrder extends Model
{

    

    

    // 表名
    protected $name = 'contract_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '0' => __('Status 0')];
    }

    public function getLoseWinList()
    {
        return ['0' => __('Lose_win 0'), '1' => __('Lose_win 1'), '2' => __('Lose_win 2')];
    }

    public function getRiskMangementList()
    {
        return ['0' => __('Risk_mangement 0'),'1' => __('Risk_mangement 1'), '2' => __('Risk_mangement 2')];
    }
    
    protected function setStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
    
    //关联秒合约配置表
    public function config()
    {
        return $this->belongsTo('ContractConfig', 'contract_config_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
