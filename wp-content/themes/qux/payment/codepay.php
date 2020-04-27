<?php
require_once("codepay/codepay_config.php"); //导入配置文件
if (empty($_POST)) {
    echo '非法请求';exit();
}
$msg = '';
$success = 1;
if(!is_user_logged_in()) $msg = '请先登录！';
$product_id = $_POST['product_id']; //商品ID
$order_id = $_POST['order_id'];  //订单号
if(!$product_id || !$order_id) $msg = '获取订单出错,请重试或联系卖家!';


global $wpdb;
$prefix = $wpdb->prefix;
$table = $prefix.'um_orders';
$order = $wpdb->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
if(!$order){
	$msg = '获取订单信息出错！';
}else{
	$data = array(
		"id" => $codepay_id,//你的码支付ID
		"pay_id" => $order->order_id, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
		"type" => 1,//1支付宝支付 3微信支付 2QQ钱包
		"price" => $order->order_total_price,//金额100元
		"param" => "quxpay",//自定义参数
		"notify_url"=> CODEPAY_URI . "/notify.php",//通知地址
		"page"=> 4,//返回joson
	);
	
	$back = create_link($data, $codepay_key);

	if (function_exists('file_get_contents')) { //如果开启了获取远程HTML函数 file_get_contents
        $codepay_json = file_get_contents($back['url']); //获取远程HTML
    } else if (function_exists('curl_init')) {
        $ch = curl_init(); //使用curl请求
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $back['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $codepay_json = curl_exec($ch);
        curl_close($ch);
    }
    
    $codepay_data = json_decode($codepay_json,true);
	if ($codepay_data && $codepay_data['status'] == 0) {
		$code_url = $codepay_data['qrcode'];
		$price = $codepay_data['money'];
		$success = 0;
	}else{
		$msg = '获取信息失败';
	}

} 

$return = array('success'=>$success,'msg'=>$msg,'order_price'=>$price,'code_url' =>$code_url,'order_id'=>$order->order_id);
echo json_encode($return);
exit;