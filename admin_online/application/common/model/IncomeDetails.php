<?php

namespace app\common\model;

use think\Model;


class IncomeDetails extends Model
{

    

    

    // 表名
    protected $name = 'income_details';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'is_calc_eth_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3')];
    }

    public function getIsCalcEthList()
    {
        return ['1' => __('Is_calc_eth 1'), '0' => __('Is_calc_eth 0')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsCalcEthTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_calc_eth']) ? $data['is_calc_eth'] : '');
        $list = $this->getIsCalcEthList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
