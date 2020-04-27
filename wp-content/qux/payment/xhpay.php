<?php
/**
 * 调用支付
 *
 * 实现微信、支付宝支付的接口
 * @date 2019年6月22日
 * @copyright 重庆迅虎网络有限公司
 */

require_once 'xhpay/config.php';  //设置

$product_id = $_POST['product_id']; //商品ID
$order_id = $_POST['order_id']; //订单号
$xhpayment = isset($_POST['payment']) ? $_POST['payment'] : 'wechat';
$success = 1;
$msg = '';

if (!is_user_logged_in()) {
    $msg = '请先登录！';
} else if(!$appid || !$appsecret ){
	$msg = '后台设置错误,请联系管理员！';
} else if(!$product_id || !$order_id ){
	wp_die('获取订单信息出错');
} else {
    global $wpdb;
    $current_user = wp_get_current_user(); 
    $prefix = $wpdb ->prefix;
    $table = $prefix.'um_orders';
    $order = $wpdb ->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
    if (!$order) {
        $msg = '获取订单信息出错！';
    } else {

        $trade_order_id = $order_id; //商户网站内部ID，此处time()是演示数据

        $data = array(
          'version' => '1.1', //固定值，api 版本，目前暂时是1.1
          'lang' => 'zh-cn', //必须的，zh-cn或en-us 或其他，根据语言显示页面
          'plugins' => $my_plugin_id, //必须的，根据自己需要自定义插件ID，唯一的，匹配[a-zA-Z\d\-_]+
          'appid' => $appid, //必须的，APPID
          'trade_order_id' => $trade_order_id, //必须的，网站订单ID，唯一的，匹配[a-zA-Z\d\-_]+
          'payment' => $xhpayment, //必须的，支付接口标识：wechat(微信接口)|alipay(支付宝接口)
          'total_fee' => $order->order_price, //人民币，单位精确到分(测试账户只支持0.1元内付款)
          'title' => $order->product_name, //必须的，订单标题，长度32或以内
          'time' => time(), //必须的，当前时间戳，根据此字段判断订单请求是否已超时，防止第三方攻击服务器
          'notify_url' => XHPAY_URI. '/notify.php', //必须的，支付成功异步回调接口
          'return_url' => add_query_arg('tab', 'orders', esc_url( get_author_posts_url( $current_user->ID ) )).'&order='.$order->id, //必须的，支付成功后的跳转地址
          'callback_url' => add_query_arg('tab', 'orders', esc_url( get_author_posts_url( $current_user->ID ) )), //必须的，支付发起地址（未支付或支付失败，系统会会跳到这个地址让用户修改支付信息）
          'modal' => null, //可空，支付模式 ，可选值( full:返回完整的支付网页; qrcode:返回二维码; 空值:返回支付跳转链接)
          'nonce_str' => str_shuffle(time()) //必须的，随机字符串，作用：1.避免服务器缓存，2.防止安全密钥被猜测出来
        );

        $hashkey = $appsecret;
        $data['hash'] = XH_Payment_Api::generate_xh_hash($data, $hashkey);
        /**
        * 个人支付宝/微信官方支付，支付网关：https://api.xunhupay.com
        * 微信支付宝代收款，需提现，支付网关：https://pay.wordpressopen.com
        */
        $url = 'https://api.xunhupay.com/payment/do.html';

        try {
            $response = XH_Payment_Api::http_post($url, json_encode($data));
            /**
            * 支付回调数据
            * @var array(
            *      order_id,//支付系统订单ID
            *      url//支付跳转地址
            *  )
            */
            $result = $response ? json_decode($response, true) : null;
			
            if (!$result) {
                throw new Exception('Internal server error', 500);
            }

            $hash = XH_Payment_Api::generate_xh_hash($result, $hashkey);
			
            if (!isset($result['hash']) || $hash != $result['hash']) {
                throw new Exception('Invalid sign!', 40029);
            }

            if ($result['errcode'] != 0) {
                throw new Exception($result['errmsg'], $result['errcode']);
            }
            $success = $result['errcode'];
            $pay_url = $result['url'];
            //header("Location: $pay_url");
            //exit;
        } catch(Exception $e) {
            $msg = $e->getMessage();//echo "errcode:{$e->getCode()},errmsg:{$e->getMessage()}";
            //TODO:处理支付调用异常的情况
        }
    }
}

$return = array('payment'=>'xhpay', 'success' =>$success, 'msg' =>$msg, 'pay_url' =>$pay_url, 'order_id' =>$order->order_id);
echo json_encode($return);
exit;
?>