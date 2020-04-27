<?php
//生成卡密
function zrz_create_guid($namespace = null,$inv = false) {
    static $guid = '';
    $uid = uniqid ( "", true );

    $data = $namespace;
    $data .= $_SERVER ['REQUEST_TIME'];     // 请求那一刻的时间戳
    $data .= $_SERVER ['HTTP_USER_AGENT'];  // 获取访问者在用什么操作系统
    $data .= $_SERVER ['SERVER_ADDR'];      // 服务器IP
    $data .= $_SERVER ['SERVER_PORT'];      // 端口号
    $data .= $_SERVER ['REMOTE_ADDR'];      // 远程IP
    $data .= $_SERVER ['REMOTE_PORT'];      // 端口信息

    $hash = strtoupper ( hash ( 'ripemd128', $uid . $guid . md5 ( $data ) ) );

    if($inv){
        $guid = substr ( $hash, 0, 4 ). substr ( $hash, 8, 4 ). substr ( $hash, 12, 4 );
    }else{
        $guid = substr ( $hash, 0, 4 ) . '-' . substr ( $hash, 8, 4 ) . '-' . substr ( $hash, 12, 4 ) . '-' . substr ( $hash, 16, 4 ) . '-' . substr ( $hash, 20, 4 );
    }
    

    return $guid;
}

//卡密支付
add_action( 'wp_ajax_zrz_km_pay', 'zrz_km_pay' );
function zrz_km_pay(){
    $key = isset($_POST['key']) ? esc_sql($_POST['key']) : '';
    $value = isset($_POST['value']) ? esc_sql($_POST['value']) : '';

    //核对卡密
    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_card';
    $cards = $wpdb->get_results( "SELECT * FROM $table_name WHERE card_key='$key' AND card_value='$value' AND card_status=0" ,ARRAY_A );
    if(count($cards) == 0){
        print json_encode(array('status'=>401,'msg' =>__('该卡密错误或者已经失效','ziranzh2')));
        exit;
    }

    $cards = $cards[0];
    $id = $cards['id'];
    $rmb = $cards['card_rmb'];
    $user_id = get_current_user_id();

    //先清除充值信息
    delete_user_meta($user_id,'zrz_ds_resout');

    $order_id = 'pay_cz-1-'.str_shuffle(uniqid()).'-'.$user_id;

    //记录充值信息
    update_user_meta($user_id,'zrz_ds_resout',array('text'=>'cz'));

    //生成一个临时的订单
    $c_order_id = zrz_build_order_no();

    $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
    $resout = $ordre->add_data();
    $out = false;

    if($resout){
        $resout = $wpdb->update(
            $table_name,
            array(
                'card_status'=>1,
                'card_user'=>$user_id,
            ),
            array('card_key'=>$key)
        );
        if($resout){
            $out = zrz_notify_data_update($c_order_id,$rmb);
        }
    }

    if($out){
        print json_encode(array('status'=>200));
        exit;
    }else{
        print json_encode(array('status'=>401,'msg'=>__('充值失败','ziranzhi2')));
        exit;
    }
}
