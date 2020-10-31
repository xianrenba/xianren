<?php
require_once("payjs/config.php");
use Musnow\Payjs\Pay;
if (empty($_POST)) {
    echo '非法请求';exit();
}
$success = 1;
$msg = '';
$price = '';
if(!is_user_logged_in()) $msg = '请先登录！';
$product_id = $_POST['product_id']; //商品ID
$order_id = $_POST['order_id'];  //订单号
if(!$product_id || !$order_id) $msg = '获取订单出错,请重试或联系卖家!';
global $wpdb;
$prefix = $wpdb->prefix;
$table = $prefix.'um_orders';
$order = $wpdb->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
if(!$order) $msg = '获取订单信息出错！';

if($order->user_alipay  && strtotime("now") - strtotime($order->order_time) < 7020){
 		$code_url = $order->user_alipay;
 		$price = $order->order_total_price; 
		$success = 0; 
        //$msg = strtotime("now") - strtotime($order->order_time);
}else{
  
//payjs初始化设置
$payjs = new Pay([
	"MerchantID" => $config['mchid'],
	"MerchantKey" => $config['key'],
	"NotifyURL" => Payjs_URI . "/notify.php",
]);

$res = $payjs->qrPay(array(
		"total_fee" => $order->order_total_price * 100, //单位为分*100
		"out_trade_no" => $order->order_id,
		//"attach" => '',
		"body" => mb_substr($order->product_name, 0, 32),
	));
	
	if ($res->return_code == 1) {
		$code_url = add_query_arg('data', $res->code_url, _url_for('qr'));//$res->qrcode;
		$price = $order->order_total_price; 
        $wpdb->query( "UPDATE $table SET user_alipay='$code_url'  WHERE order_id='$order->order_id'" );
		$success = 0;
	}else{
        $msg = $res->return_msg;
    }
}
	
$return = array('success'=>$success,'msg'=>$msg,'order_price'=>$price,'code_url'=>$code_url,'order_id'=>$order->order_id);
echo json_encode($return);
exit;