<?php
//生成订单号
function getSn($head='')
{
    $order_id_main = date('YmdHis') . mt_rand(1000, 9999);
    //唯一订单号码（YYMMDDHHIISSNNN）
    $osn = $head.substr($order_id_main,2); //生成订单号
    return $osn;
}