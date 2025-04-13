<?php

namespace app\common\model;

use think\Model;


class ProxyReport extends Model
{

    

    

    // 表名
    protected $name = 'proxy_report';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [];
    
    //关联管理表
    public function admin()
    {
        return $this->belongsTo('Admin', 'aid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    
    
    /**
     * 写入代理报表
     * @param integer $aid
     * @param string $field
     * @param string $number
     * @return false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function writeReport(int $aid, string $field, $number = 1)
    {
        $date = date('Y-m-d');
        $report = $this->where(['date' => $date, 'aid' => $aid])->lock(true)->find();
        if (!$report) {
            return $this->save(['date' => $date, 'aid' => $aid, $field => $number]);
        }
        $report->setInc($field, $number);
        return $report->save();
    }






}
