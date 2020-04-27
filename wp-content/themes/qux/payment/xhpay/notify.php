<?php
/**
 * 支付成功异步回调接口
 *
 * 当用户支付成功后，支付平台会把订单支付信息异步请求到本接口(最多5次)
 *
 * @date 2017年3月13日
 * @copyright 重庆迅虎网络有限公司
 */

require_once 'config.php';


/**
 * 回调数据
 * @var array(
 *       'trade_order_id'，商户网站订单ID
         'total_fee',订单支付金额
         'transaction_id',//支付平台订单ID
         'order_date',//支付时间
         'plugins',//自定义插件ID,与支付请求时一致
         'status'=>'OD'//订单状态，OD已支付，WP未支付
 *   )
 */


$data = $_POST;

foreach ($data as $k=>$v){
    $data[$k] = stripslashes($v);
}
if(!isset($data['hash'])||!isset($data['trade_order_id'])){
   echo 'failed';exit;
}

//自定义插件ID,请与支付请求时一致
if(isset($data['plugins'])&&$data['plugins']!=$my_plugin_id){
    echo 'failed';exit;
}

//APP SECRET
$appkey = $data['appid'] == $appid ? $appsecret : $aliappsecret;
$hash = XH_Payment_Api::generate_xh_hash($data,$appkey);
if($data['hash']!=$hash){
    //签名验证失败
    echo 'failed';exit;
}

//商户订单ID
$trade_order_id = $data['trade_order_id'];

//平台订单号
$transaction_id = $data['transaction_id'];

//完成时间
$success_time = date("Y-m-d h:i:s",$data['time']);

//渠道ID
$openid = $data['appid'];

if($data['status']=='OD'){  //处理完成订单
  
    global $wpdb;
	$prefix = $wpdb->prefix;
	$table = $prefix.'um_orders';
	$row = $wpdb->get_row("select * from ".$table." where order_id=".$trade_order_id);
	if($row){
		if($row->order_status<=3){

			$wpdb->query( "UPDATE $table SET order_status=4, trade_no='$transaction_id', order_success_time='$success_time', user_alipay='$openid' WHERE order_id='$trade_order_id'" );
			//更新资源剩余量
			update_success_order_product($row->product_id,$row->order_quantity);
			if(!empty($row->user_email)){$email = $row->user_email;}
			//发送订单状态变更email
			store_email_template($trade_order_id,'',$email);
			//发送购买可见内容或下载链接或会员状态变更
			send_goods_by_order($trade_order_id,'',$email);
		}
		
	}
    echo 'success';
    exit; 

//}else{
    //处理未支付的情况
}

//以下是处理成功后输出，当支付平台接收到此消息后，将不再重复回调当前接口

?>