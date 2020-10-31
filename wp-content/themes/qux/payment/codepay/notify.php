<?php
require_once("codepay_config.php"); //导入配置文件
ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$sign = '';//初始化

foreach ($_POST AS $key => $val) { //遍历POST参数
    if ($val == '' || $key == 'sign') continue; //跳过这些不签名
    if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
    $sign .= "$key=$val"; //拼接为url参数形式
}

if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
    exit('fail');  //返回失败 继续补单
} else { //合法的数据
    //业务处理
    $pay_id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
    $money = (float)$_POST['money']; //实际付款金额
    $price = (float)$_POST['price']; //订单的原价
    $param = $_POST['param']; //自定义参数
    $pay_no = $_POST['pay_no'];
    $openid = 'codepay';
    $success_time = date("Y-m-d h:i:s",$_POST['pay_time']);

	global $wpdb;
	$prefix = $wpdb->prefix;
	$table = $prefix.'um_orders';
	$row = $wpdb->get_row("select * from ".$table." where order_id=".$pay_id);
	if($row && $money == $row->order_total_price ){
		if($row->order_status<=3){

			$wpdb->query( "UPDATE $table SET order_status=4, trade_no='$pay_no', order_success_time='$success_time', user_alipay='$openid' WHERE order_id='$pay_id'" );
			//更新资源剩余量
			update_success_order_product($row->product_id,$row->order_quantity);
			if(!empty($row->user_email)){$email = $row->user_email;}
			//发送订单状态变更email
			store_email_template($pay_id,'',$email);
			//发送购买可见内容或下载链接或会员状态变更
			send_goods_by_order($pay_id,'',$email);
		}
        exit('ok'); //返回成功 不要删除哦		
	}else{
		exit('fail'); //金额不足
	}
	
}
?>