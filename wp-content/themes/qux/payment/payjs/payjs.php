<?php
require_once("config.php");
use Musnow\Payjs\Pay;
if(!is_user_logged_in()){wp_die('请先登录！');}
$product_id = $_POST['product_id']; //商品ID
$order_id = $_POST['order_id'];  //订单号
if(!$product_id || !$order_id) wp_die('获取订单出错,请重试或联系卖家!');
$success = 0;
$msg = '';
global $wpdb;
$prefix = $wpdb->prefix;
$table = $prefix.'um_orders';
$order = $wpdb->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
if(!$order) $msg = '获取订单信息出错！';

//if($order->user_alipay  && strtotime("now") - strtotime($order->order_time) < 7020){
//		$code_url = $order->user_alipay;
//		$success = 1; 
        //$msg = strtotime("now") - strtotime($order->order_time);
//}else{
  
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
		$code_url = $res->qrcode;
        $wpdb->query( "UPDATE $table SET user_alipay='$code_url'  WHERE order_id='$order->order_id'" );
		$success = 1;
	}else{
        $msg = $res->return_msg;
    }
//}
	
$return = array('success'=>$success,'msg'=>$msg,'code_url'=>$code_url,'order_id'=>$order->order_id);
echo json_encode($return);
exit;