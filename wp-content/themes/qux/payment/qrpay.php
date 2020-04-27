<?php 
require_once 'f2fpay/config.php';  //设置

if (empty($_POST)) {
    echo '非法请求';exit();
}

$product_id = $_POST['product_id']; //商品ID
$order_id = $_POST['order_id']; //订单号
$success = 1;
$price = '';
$msg = '';

if (!is_user_logged_in()) {
    $msg = '请先登录！';
} else if(!$appid || !$rsaPrivateKey ){
	$msg = '后台设置错误,请联系管理员！';
} else if(!$product_id || !$order_id ){
	$msg = '获取订单信息出错';
} else {
	global $wpdb;
    $current_user = wp_get_current_user(); 
    $prefix = $wpdb ->prefix;
    $table = $prefix.'um_orders';
    $order = $wpdb ->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
    if (!$order) {
        $msg = '获取订单信息出错！';
    } else {
    	$aliPay = new AlipayService($appid,$notifyUrl,$rsaPrivateKey);
    	
    	$aliPay->setTotalFee($order->order_total_price);
    	$aliPay->setOutTradeNo($order->order_id);
    	$aliPay->setOrderName($order->product_name);
    	$price = $order->order_total_price;
    	
    	$result = $aliPay->doPay();
    	$result = $result['alipay_trade_precreate_response'];
    	if($result['code'] && $result['code']=='10000'){
    		$success = 0;
    		$code_url = add_query_arg('data', $result['qr_code'], _url_for('qr'));
    	}else{
    		$msg = $result['msg'].' : '.$result['sub_msg'];
    	}
    }
}

$return = array('success'=>$success,'msg'=>$msg,'order_price'=>$price,'code_url'=>$code_url,'order_id'=>$order->order_id);
echo json_encode($return);
exit;