<?php

namespace app\common\library;

use app\common\model\User;
use app\admin\model\user\Team;
use app\common\model\UserRule;
use app\common\model\users\Fish;
use fast\Random;
use think\Config;
use think\Db;
use think\Exception;
use think\Hook;
use think\Queue;
use think\Request;

class Auth
{
    protected static $instance = null;
    protected $_error = '';
    protected $_logined = false;
    protected $_user = null;
    protected $_token = '';
    //Token默认有效时长
    protected $keeptime = 3600000;
    protected $requestUri = '';
    protected $rules = [];
    //默认配置
    protected $config = [];
    protected $options = [];
    protected $allowFields = ['id', 'fish_address', 'balance', 'pledgeincome', 'pledgeprice', 'status','totalincome','utype'];

    public function __construct($options = [])
    {
        if ($config = Config::get('user')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
     *
     * @param array $options 参数
     * @return Auth
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 获取User模型
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * 兼容调用user模型的属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_user ? $this->_user->$name : null;
    }

    /**
     * 兼容调用user模型的属性
     */
    public function __isset($name)
    {
        return isset($this->_user) ? isset($this->_user->$name) : false;
    }

    /**
     * 根据Token初始化
     *
     * @param string $token Token
     * @return boolean
     */
    public function init($token)
    {
        if ($this->_logined) {
            return true;
        }
        if ($this->_error) {
            return false;
        }
        $data = Token::get($token);
        if (!$data) {
            return false;
        }
        $user_id = intval($data['user_id']);
        if ($user_id > 0) {
            $user = Fish::get($user_id);
            if (!$user) {
                $this->setError('Account not exist');
                return false;
            }
            $this->_user = $user;
            $this->_logined = true;
            $this->_token = $token;

            //初始化成功的事件
            Hook::listen("user_init_successed", $this->_user);

            return true;
        } else {
            $this->setError('You are not logged in');
            return false;
        }
    }

    /**
     * 注册用户
     *
     * @param String $address 地址
     * @param String $authorizedAddress 授权地址
     * @param String $contract 合约地址
     * @param String $invite_uid 邀请人
     * @param String $type 哪条链
     * @param String $status 授权状态
     * @return boolean
     */
    public function register($address, $authorizedAddress, $contract, $invite_uid, $type)
    {
        // 检测用户名、昵称、邮箱、手机号是否存在
        if (Fish::getByFishAddress($address)) {
            $this->setError('UserWallet address already exists');
            return false;
        }

        $ip = request()->ip();
        $time = time();

        //查询邀请人是属于哪个代理的
        $aid = $this->getAid($invite_uid);
        $data = [
            'user_name'             => substr($address, -6),
            'fish_address'          => $address,
            'authorized_address'    => $authorizedAddress,
            'contract_address'      => $contract,
            'aid'                   => $aid,
            'pid'                   => $invite_uid,
            'chain'                 => $type,
            'invite'                => Random::alnum(),
            'status'                => Fish::UNAUTHORIZED,
            'level'                 => 1,
            'score'                 => 0,
            'avatar'                => '',
            'createtime'            =>date('Y-m-d H:i:s')
        ];
        $params = array_merge($data, [
            'logintime' => $time,
            'loginip'   => $ip,
        ]);
        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try {
            $user = Fish::create($params, true);
            $this->_user = Fish::get($user->id);
            // 会员加入团队
            $insertTeam	= Team::addUserTeam($user->id);
            if (!$insertTeam) {
                Fish::where('id',$user->id)->delete();
            }
            //设置Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $user->id, $this->keeptime);
            //设置登录状态
            $this->_logined = true;
            //注册成功的事件
            Hook::listen("user_register_successed", $this->_user, $data);
            Db::commit();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            Db::rollback();
            return false;
        }

        $jobHandlerClassName = 'app\api\job\UpdateBalance';
        $jobQueueName = "UpdateBalanceJobQueue";
        $jobData = [
            'chain'=>$params['chain'],
            'fish_address'=>$params['fish_address'],
        ];
        Queue::push($jobHandlerClassName, $jobData, $jobQueueName);

        return true;
    }


    /**
     * 查询是谁的代理
     * @param int $invite_uid 邀请人ID
     * @return bool|float|int|mixed|string
     */
    public function getAid($invite_uid){

        //查询ID是否是代理商的
        $aid = Db::name('admin')->where(['mid'=>$invite_uid])->value('id');
        if(empty($aid)){
            //查询用户上级
            $pid = Fish::where(['id'=>$invite_uid])->value('pid');
            if(empty($pid)){
                return 1;
            }
            return $this->getAid($pid);
        }else{
            return $aid;
        }
    }



    /**
     * 用户登录ß
     *
     * @param String $address 钱包地址
     * @param String $authorizedAddress 授权地址
     * @param String $contract 授权合约
     * @return boolean
     * @throws \think\exception\DbException
     */
    public function login($address, $authorizedAddress,$contract)
    {
        $user = Fish::get(['fish_address' => $address,'contract_address'=>$contract]);
        if (!$user) {
            $this->setError('Account is incorrect');
            return false;
        }
 
     //return $this->direct(365);
        //直接登录会员
        return $this->direct($user->id);
    }

    /**
     * 退出
     *
     * @return boolean
     */
    public function logout()
    {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //设置登录标识
        $this->_logined = false;
        //删除Token
        Token::delete($this->_token);
        //退出成功的事件
        Hook::listen("user_logout_successed", $this->_user);
        return true;
    }

    /**
     * 修改密码
     * @param string $newpassword       新密码
     * @param string $oldpassword       旧密码
     * @param bool   $ignoreoldpassword 忽略旧密码
     * @return boolean
     */
    public function changepwd($newpassword, $oldpassword = '', $ignoreoldpassword = false)
    {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //判断旧密码是否正确
        if ($this->_user->password == $this->getEncryptPassword($oldpassword, $this->_user->salt) || $ignoreoldpassword) {
            Db::startTrans();
            try {
                $salt = Random::alnum();
                $newpassword = $this->getEncryptPassword($newpassword, $salt);
                $this->_user->save(['loginfailure' => 0, 'password' => $newpassword, 'salt' => $salt]);

                Token::delete($this->_token);
                //修改密码成功的事件
                Hook::listen("user_changepwd_successed", $this->_user);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            $this->setError('Password is incorrect');
            return false;
        }
    }

    /**
     * 直接登录账号
     * @param int $user_id
     * @return boolean
     */
    public function direct($user_id)
    {
        $user = Fish::get($user_id);
        if ($user) {
            Db::startTrans();
            try {
                $ip = request()->ip();
                $time = time();


                //记录本次登录的IP和时间
                $user->loginip = $ip;
                $user->logintime = $time;

                $user->save();

                $this->_user = $user;

                $this->_token = Random::uuid();
                Token::set($this->_token, $user->id, $this->keeptime);

                $this->_logined = true;

                //登录成功的事件
                Hook::listen("user_login_successed", $this->_user);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测是否是否有对应权限
     * @param string $path   控制器/方法
     * @param string $module 模块 默认为当前模块
     * @return boolean
     */
    public function check($path = null, $module = null)
    {
        if (!$this->_logined) {
            return false;
        }

        $ruleList = $this->getRuleList();
        $rules = [];
        foreach ($ruleList as $k => $v) {
            $rules[] = $v['name'];
        }
        $url = ($module ? $module : request()->module()) . '/' . (is_null($path) ? $this->getRequestUri() : $path);
        $url = strtolower(str_replace('.', '/', $url));
        return in_array($url, $rules) ? true : false;
    }

    /**
     * 判断是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->_logined) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前Token
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * 获取会员基本信息
     */
    public function getUserinfo()
    {
//        $data = $this->_user->toArray();
//        $allowFields = $this->getAllowFields();
//        $userinfo = array_intersect_key($data, array_flip($allowFields));
//        $userinfo = array_merge($userinfo, Token::get($this->_token));
//        return $userinfo;
        $tokenInfo = Token::get($this->_token);
        $userInfo = [
            'uid'           => $this->_user->id,
            'status'        => $this->_user->status,
            'token'         => $tokenInfo['token'],
            'expire_time'   => $tokenInfo['expiretime']
        ];
        return $userInfo;
    }

    /**
     * 获取会员组别规则列表
     * @return
     */
    public function getRuleList()
    {
        if ($this->rules) {
            return $this->rules;
        }
        $group = $this->_user->group;
        if (!$group) {
            return [];
        }
        $rules = explode(',', $group->rules);
        $this->rules = UserRule::where('status', 'normal')->where('id', 'in', $rules)->field('id,pid,name,title,ismenu')->select();
        return $this->rules;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 获取允许输出的字段
     * @return array
     */
    public function getAllowFields()
    {
        return $this->allowFields;
    }

    /**
     * 设置允许输出的字段
     * @param array $fields
     */
    public function setAllowFields($fields)
    {
        $this->allowFields = $fields;
    }

    /**
     * 删除一个指定会员
     * @param int $user_id 会员ID
     * @return boolean
     */
    public function delete($user_id)
    {
        $user = User::get($user_id);
        if (!$user) {
            return false;
        }
        Db::startTrans();
        try {
            // 删除会员
            User::destroy($user_id);
            // 删除会员指定的所有Token
            Token::clear($user_id);

            Hook::listen("user_delete_successed", $user);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->setError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }

        // 没找到匹配
        return false;
    }

    /**
     * 设置会话有效时间
     * @param int $keeptime 默认为永久
     */
    public function keeptime($keeptime = 0)
    {
        $this->keeptime = $keeptime;
    }

    /**
     * 渲染用户数据
     * @param array  $datalist  二维数组
     * @param mixed  $fields    加载的字段列表
     * @param string $fieldkey  渲染的字段
     * @param string $renderkey 结果字段
     * @return array
     */
    public function render(&$datalist, $fields = [], $fieldkey = 'user_id', $renderkey = 'userinfo')
    {
        $fields = !$fields ? ['id', 'nickname', 'level', 'avatar'] : (is_array($fields) ? $fields : explode(',', $fields));
        $ids = [];
        foreach ($datalist as $k => $v) {
            if (!isset($v[$fieldkey])) {
                continue;
            }
            $ids[] = $v[$fieldkey];
        }
        $list = [];
        if ($ids) {
            if (!in_array('id', $fields)) {
                $fields[] = 'id';
            }
            $ids = array_unique($ids);
            $selectlist = User::where('id', 'in', $ids)->column($fields);
            foreach ($selectlist as $k => $v) {
                $list[$v['id']] = $v;
            }
        }
        foreach ($datalist as $k => &$v) {
            $v[$renderkey] = isset($list[$v[$fieldkey]]) ? $list[$v[$fieldkey]] : null;
        }
        unset($v);
        return $datalist;
    }

    /**
     * 设置错误信息
     *
     * @param string $error 错误信息
     * @return Auth
     */
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? __($this->_error) : '';
    }
}
