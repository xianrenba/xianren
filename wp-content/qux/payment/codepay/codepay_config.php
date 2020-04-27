<?php
require_once(dirname(__FILE__)."/../../../../../wp-load.php");
define('CODEPAY_URI', THEME_URI.'/payment/codepay');
date_default_timezone_set('Asia/Shanghai');
$codepay_id = _hui('codepay_id');//这里改成码支付ID
$codepay_key = _hui('codepay_key'); //这是您的通讯密钥


?>