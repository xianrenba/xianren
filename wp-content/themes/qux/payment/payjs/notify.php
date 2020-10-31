<?php 
require_once("config.php");

$data = $_POST;
$payjsInstance = new Musnow\Payjs\Pay([
	"MerchantID" => $config['mchid'],
	"MerchantKey" => $config['key'],
]);
$res = $payjsInstance->Checking($data);
if ($res) { //首先验证签名Sign
	$out_trade_no = $data['out_trade_no'];  //订单号
	$order_price = $data['total_fee'];  //支付金额（分）
	$trade_no = $data['transaction_id']; //微信商户订单号
	$openid = $data['openid'];
	
	global $wpdb;
	$prefix = $wpdb->prefix;
	$table = $prefix.'um_orders';
	$row = $wpdb->get_row("select * from ".$table." where order_id=".$out_trade_no);
	if($row){
		if($data['return_code'] == 1){
			if($row->order_status<=3){
				$success_time = $data['time_end'];
				$wpdb->query( "UPDATE $table SET order_status=4, trade_no='$trade_no', order_success_time='$success_time', user_alipay='$openid' WHERE order_id='$out_trade_no'" );
				update_success_order_product($row->product_id,$row->order_quantity);
				if(!empty($row->user_email)){$email = $row->user_email;}
				//发送订单状态变更email
				store_email_template($out_trade_no,'',$email);
				//发送购买可见内容或下载链接或会员状态变更
				send_goods_by_order($out_trade_no,'',$email);
			}
		}else{
			echo 'Fail';
			exit;
		}		
	}
	echo 'Success';
	exit;
}else{
	echo 'Fail';
	exit;
}
?>