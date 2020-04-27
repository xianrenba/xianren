<?php
date_default_timezone_set('Asia/Shanghai');
require_once "wxpay/WxPay.NativePay.php";
if (empty($_POST)) {
    echo '非法请求';exit();
}
$success = 1;
$msg = '';
$price = '';
$product_id = $_POST['product_id']; //商品ID
$order_id = $_POST['order_id'];  //订单号
if(!$product_id || !$order_id) $msg = '获取订单出错,请重试或联系卖家!';

if($product_id && $order_id && is_user_logged_in()){
	
	global $wpdb;
	$prefix = $wpdb->prefix;
	$table = $prefix.'um_orders';
	$order = $wpdb->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
	if(!$order) $msg = '获取订单信息出错！';

    $notify = new NativePay();
	
	//模式二
	$input = new WxPayUnifiedOrder();
	$input->SetBody($order->product_name);
	$input->SetAttach("ERPHP");
	$input->SetOut_trade_no($order->order_id);
	$input->SetTotal_fee(intval($order->order_total_price*100));
	$input->SetTime_start(date("YmdHis"));
	//$input->SetTime_expire(date("YmdHis", time() + 600));
	$input->SetGoods_tag("MBT");
	$input->SetNotify_url(UM_URI .'/payment/wxpay/notify.php');
	$input->SetTrade_type("NATIVE");
	$input->SetProduct_id($product_id);
	$result = $notify->GetPayUrl($input);
	
	if($result["return_code"] == 'FAIL'){
		$msg = $result["return_msg"];
	}else{
		$url2 = $result["code_url"];
		$price = $order->order_total_price;
		$code_url = add_query_arg('data', urlencode($url2), _url_for('qr'));
		$success = 0;
	}
}else{
	$msg = '缺少必要参数，获取订单失败！';
}

$return = array('success'=>$success,'msg'=>$msg,'order_price'=>$price,'code_url'=>$code_url,'order_id'=>$order->order_id);
echo json_encode($return);
exit;