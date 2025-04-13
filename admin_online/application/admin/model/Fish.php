<?php

namespace app\admin\model;

use think\Model;

class Fish extends Model
{
    // 表名
    protected $name = 'fish';



    /**
     * [userSid 获取所有上级]
     * @param  [type] $id     [description]
     * @param  string $select [description]
     * @param  array  $array  [description]
     * @return [type]         [description]
     */
    public static function userSid($id,$select='id,pid',$array=array()){
        $user_info =  self::where('id',$id)->field($select)->find();	//查询上级

        $array[] = $user_info['id'];

        if($user_info['pid']){
            $array = self::userSid($user_info['pid'],$select,$array);
        }

        return $array;
    }
}
