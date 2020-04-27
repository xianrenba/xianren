<?php 
require_once(dirname(__FILE__)."/../../../../../wp-load.php");
define('Payjs_DIR', get_stylesheet_directory() .'/payment/payjs');
define('Payjs_URI', get_stylesheet_directory_uri() .'/payment/payjs');

require_once(Payjs_DIR .'/lib/payjs.php');
date_default_timezone_set('Asia/Shanghai');
$payjs_id = _hui('payjs_id');
$payjs_key = _hui('payjs_key');
//if(empty($payjs_id)||empty($payjs_key))wp_die('商家认证信息为空!');

// 配置通信参数
$config = [
    'mchid' => $payjs_id,   // 配置商户号
    'key'   => $payjs_key,   // 配置通信密钥
];
?>