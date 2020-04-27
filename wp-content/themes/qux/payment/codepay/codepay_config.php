<?php
require_once(dirname(__FILE__)."/../../../../../wp-load.php");
define('CODEPAY_URI', THEME_URI.'/payment/codepay');
date_default_timezone_set('Asia/Shanghai');
$codepay_id = _hui('codepay_id');//这里改成码支付ID
$codepay_key = _hui('codepay_key'); //这是您的通讯密钥


function create_link($params, $codepay_key, $host = ""){
	
    ksort($params); //重新排序$data数组
    reset($params); //内部指针指向数组中的第一个元素
    $sign = '';
    $urls = '';
    foreach ($params AS $key => $val) {
        if ($val == '') continue;
        if ($key != 'sign') {
            if ($sign != '') {
                $sign .= "&";
                $urls .= "&";
            }
            $sign .= "$key=$val"; //拼接为url参数形式
            $urls .= "$key=" . urlencode($val); //拼接为url参数形式
        }
    }

    $key = md5($sign . $codepay_key);//开始加密
    $query = $urls . '&sign=' . $key; //创建订单所需的参数
    $apiHost = ($host ? $host : "https://api.xiuxiu888.com/creat_order/?"); //网关
    $url = $apiHost . $query; //生成的地址
    return array("url" => $url, "query" => $query, "sign" => $sign, "param" => $urls);
}

?>