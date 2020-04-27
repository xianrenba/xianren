<?php
require_once("codepay/codepay_config.php"); //导入配置文件
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


$data = array(
    "id" => $codepay_id,//你的码支付ID
    "pay_id" => $order->order_id, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
    "type" => 1,//1支付宝支付 3微信支付 2QQ钱包
    "price" => $order->order_total_price,//金额100元
    "param" => "",//自定义参数
    "notify_url"=> CODEPAY_URI . "/notify.php",//通知地址
    "return_url"=> add_query_arg('tab', 'orders', esc_url( get_author_posts_url( $order->user_id) )).'&order='.$order->id,//跳转地址
); //构造需要传递的参数

ksort($data); //重新排序$data数组
reset($data); //内部指针指向数组中的第一个元素

$sign = ''; //初始化需要签名的字符为空
$urls = ''; //初始化URL参数为空

foreach ($data AS $key => $val) { //遍历需要传递的参数
    if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
    if ($sign != '') { //后面追加&拼接URL
        $sign .= "&";
        $urls .= "&";
    }
    $sign .= "$key=$val"; //拼接为url参数形式
    $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值

}
$query = $urls . '&sign=' . md5($sign .$codepay_key); //创建订单所需的参数
$url = "https://api.xiuxiu888.com/creat_order/?{$query}"; //支付页面

header("Location:{$url}"); //跳转到支付页面