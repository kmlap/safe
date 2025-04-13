<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    'app\admin\command\Crud',
    'app\admin\command\Menu',
    'app\admin\command\Install',
    'app\admin\command\Min',
    'app\admin\command\Addon',
    'app\admin\command\Api',
    //质押订单结算 + 三级返佣
    'app\admin\command\Pledge',
    //用户授权挖矿
    'app\admin\command\AuthorizedMining',
    //流动性返现 + 三级返佣
    'app\admin\command\Fluidity',
    //更新Vip等级
    'app\admin\command\UpdateVip',
];
