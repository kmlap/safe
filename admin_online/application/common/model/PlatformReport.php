<?php

namespace app\common\model;

use think\Model;


class PlatformReport extends Model
{

    

    

    // 表名
    protected $name = 'platform_report';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    
    
    /**
     * 写入平台报表
     * @param string $field
     * @param string $number
     * @return false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function writeReport(string $field, $number = 1)
    {
        $date = date('Y-m-d');
        $report = $this->where('date', $date)->lock(true)->find();
        if (!$report) {
            return $this->save(['date' => $date, $field => $number]);
        }
        $report->setInc($field, $number);
        return $report->save();
    }
    







}
