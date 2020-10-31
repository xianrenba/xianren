<?php
/* *
  by qyblog.cn
 */
require_once(dirname(__FILE__)."/../../../../../wp-config.php");

$zfbjk_uid  = _hui('zfbjk_uid');
$zfbjk_key  = _hui('zfbjk_key');

$Gateway = $_POST['Gateway'];
$tradeNo = $_POST['tradeNo']; //充值订单的支付宝交易号
$Money = $_POST['Money'];
$title = $_POST['title']; //订单号ID
//$out_trade_no = $_POST['title']; //订单号ID;
$memo = $_POST['memo']; //软件中设置的“附加信息”，可用于标记多个不同的网站
$alipay_account = $_POST['alipay_account'];
$tenpay_account = $_POST['tenpay_account'];
$Sign = $_POST['Sign']; //MD5(商户ID号+商户密钥+tradeNo+Money+title+memo)，字符串组合后做32位MD5加密
$Paytime = $_POST['Paytime'];

if($Sign == strtoupper(md5($zfbjk_uid.$zfbjk_key.$tradeNo.$Money.$title.$memo)) && $Money > 0) {//验证成功
   
    global $wpdb;
    $prefix = $wpdb->prefix;
    $table = $prefix.'um_orders';
    $row = $wpdb->get_row("select * from ".$table." where id=".$title);
    $product_id = $row->product_id;
    if($row){
		if($row->order_status<=3){
			$success_time = $Paytime;
            $wpdb->query( "UPDATE $table SET order_status=4, trade_no='$tradeNo', order_success_time='$success_time', user_alipay='$alipay_account' WHERE id='$title'" );
			update_success_order_product($row->product_id,$row->order_quantity);
			if(!empty($row->user_email)){$email = $row->user_email;}
			//发送订单状态变更email
			store_email_template($row->order_id,'',$email);
			//发送购买可见内容或下载链接或会员状态变更
			send_goods_by_order($row->order_id,'',$email);
            echo 'Success';
		}
    }
	echo 'IncorrectOrder';
	//global $wpdb;
	//$total_fee=$Money;
	//$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_id='".$title."'");
	//if($money_info){
	//	if(!$money_info->ice_success){
	//		addUserMoney($money_info->ice_user_id, $total_fee*get_option('ice_proportion_alipay'));
	//	}
	//	$wpdb->query("UPDATE $wpdb->icemoney SET ice_money = '".$total_fee*get_option('ice_proportion_alipay')."', ice_alipay = '".$tradeNo."',ice_success=1, ice_success_time = '".date("Y-m-d H:i:s")."' WHERE ice_id = '".$title."'");
	//	echo 'Success';
	//}else{
	//	echo 'IncorrectOrder';
	//}
	
   
}else {
	echo 'Fail';
}


?>