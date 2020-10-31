<?php 
require_once(dirname(__FILE__)."/../../../../../wp-load.php"); //wp
require_once 'api.php';  //api

define('XHPAY_URI', get_stylesheet_directory_uri() .'/payment/xhpay');

date_default_timezone_set('Asia/Shanghai');

$appid = _hui('xhpay_appid');//'201906120277'; //测试账户，
$appsecret = _hui('xhpay_secret');//'8f2f063ceeaf4d848e16a7b0bd080deb'; //测试账户，
$aliappid = _hui('ali_xhpay_appid');//支付宝
$aliappsecret = _hui('ali_xhpay_secret');//支付宝
$my_plugin_id = 'xhpay_qux_id';

?>