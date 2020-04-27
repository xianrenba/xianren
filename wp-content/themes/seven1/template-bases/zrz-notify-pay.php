<?php
$weixin = zrz_wx_pay_type();
$alipay = zrz_alipay_type();

if($alipay == 'xunhu' || $weixin == 'xunhu'){
    unset($alipay);
    unset($alipay);
    require ZRZ_THEME_DIR.'/inc/SDK/Xunhu/api.php';

    $xunhu = zrz_get_pay_settings('xunhu');

    $appid = isset($xunhu['appid']) ? $xunhu['appid'] : '';
    $appsecret = isset($xunhu['appsecret']) ? $xunhu['appsecret'] : '';
    $my_plugin_id = isset($xunhu['plugins']) ? $xunhu['plugins'] : '';

    $data = array_map('stripslashes_deep', $_POST);

    if(!isset($data['hash'])||!isset($data['trade_order_id'])){
       echo 'failed';exit;
    }

    //自定义插件ID,请与支付请求时一致
    if(isset($data['plugins'])&&$data['plugins']!=$my_plugin_id){
        echo 'failed';exit;
    }

    //APP SECRET
    $appkey =$appsecret;
    $hash =XH_Payment_Api::generate_xh_hash($data,$appkey);
    if($data['hash']!=$hash){
        //签名验证失败
        echo 'failed';exit;
    }

    if($data['status']=='OD'){
        //支付单号
        $pay_order_id = $data['trade_order_id'];
        //支付的金额
        $total_amount = $data['total_fee'];

        //更新订单数据
        zrz_notify_data_update($pay_order_id,$total_amount);
    }else{
        echo 'error';
        exit;
    }

    echo 'success';
    exit;

}else{
    require ZRZ_THEME_DIR . '/inc/SDK/PayAll/init.php';

    // 加载配置参数
    $config = require(ZRZ_THEME_DIR . '/inc/SDK/PayConfig.php');

    $_POST = array_map('stripslashes_deep', $_POST);

    $pay = new \Pay\Pay($config);

    if ($pay->driver('alipay')->gateway()->verify($_POST)) {
        $pay_order_id = $_POST['out_trade_no'];
        //支付的金额
        $total_amount = $_POST['total_amount'];

        //更新订单数据
        $res = zrz_notify_data_update($pay_order_id,$total_amount);
        if($res){
            echo 'success';
            exit;
        }
    } else {
        echo 'error';
        exit;
    }
}
