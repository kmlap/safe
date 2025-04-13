<?php

namespace app\admin\model\user;

use app\admin\model\Fish;
use think\Model;


class Team extends Model
{

    

    

    // 表名
    protected $name = 'user_team';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];




    //添加至团队表
    public static function addUserTeam($id){
        $array = Fish::userSid($id);

        $insertArray = array();
        foreach ($array as $key => $value) {
            $insertArray[] = ['uid'=>$value, 'team'=>$id,'level'=>$key];
        }

        $res = self::insertAll($insertArray);
        if (!$res) return false;

        return true;
    }





}
