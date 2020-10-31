<?php
require_once(dirname(__FILE__)."/../../../../../wp-load.php"); //wp
require_once 'alipayservice.php';  //api
define('P2PPAY_URI', get_stylesheet_directory_uri() .'/payment/f2fpay');

date_default_timezone_set('Asia/Shanghai');

$appid = _hui('f2f_appid');
$rsaPrivateKey = _hui('rsa_private_key');
$notifyUrl = P2PPAY_URI . '/notify.php'; 

//支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
$alipayPublicKey = _hui('alipay_public_key');
