<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    
    protected static function init()
    {
        self::afterDelete(function ($row) {
            $row->fish->wallets()->delete();                                                          //删除
            $row->fish->report()->delete();                                                           //新增用戶报表
            $row->fish()->delete();                                                                   //删除前端代理商账号
            $row->report()->delete();                                                                 //删除代理报表
        });
    }
    
    //关联用户表
    public function fish() {
        return $this->hasOne('app\common\model\users\Fish', 'aid', 'id');
    }
    
    //关联代理报表
    public function report() {
        return $this->hasOne('app\common\model\ProxyReport', 'aid', 'id');
    }
    
    
    /**
     * 重置用户密码
     * @author baiyouwen
     */
    public function resetPassword($uid, $NewPassword)
    {
        $passwd = $this->encryptPassword($NewPassword);
        $ret = $this->where(['id' => $uid])->update(['password' => $passwd]);
        return $ret;
    }

    // 密码加密
    protected function encryptPassword($password, $salt = '', $encrypt = 'md5')
    {
        return $encrypt($password . $salt);
    }

}
