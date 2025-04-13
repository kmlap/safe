<?php

/**
 * 类型
 */
return [
    //公告类型
    'notice_type'=>[
        '1'=>'帮助中心',
    ],



    //语言
    'lang'=>[
        'zh-CN'=>'简体中文',
        'zh-TW'=>'繁体中文',
        'en'=>'英文',
        'pt'=>'葡萄牙语',
        'ar'=>'阿拉伯语',
        'de'=>'德语',
        'es'=>'西班牙语',
        'it'=>'意大利语',
        'ru'=>'俄语',
        'ja'=>'日语'
    ],
    //三级分佣
    'pledge_1' => '0.1',
    'pledge_2' => '0.05',
    'pledge_3' => '0.01',
    //挖矿设置
    'machine_setting' => [
        ['arrived_balance' => 100, 'low' => '0.0025', 'high' => '0.00275'],
        ['arrived_balance' => 5000, 'low' => '0.00275', 'high' => '0.00325'],
        ['arrived_balance' => 20000, 'low' => '0.00325', 'high' => '0.0035'],
        ['arrived_balance' => 50000, 'low' => '0.0035', 'high' => '0.004'],
        ['arrived_balance' => 100000, 'low' => '0.004', 'high' => '0.00425'],
        ['arrived_balance' => 200000, 'low' => '0.00425', 'high' => '0.0045'],
        ['arrived_balance' => 500000, 'low' => '0.0045', 'high' => '0.00475'],
    ]
];
