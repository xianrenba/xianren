<?php
/* AJAX登录验证*/
function zrz_ajax_login(){
    check_ajax_referer('zrz-sign', 'security' );
    if (!isset($_SESSION)) {
        session_start();
    }

    //检查验证
    if(zrz_get_social_settings('open_check_code')){
        $check_code = isset($_POST['checkcode']) ? strtolower($_POST['checkcode']) : FALSE;
        if(!$check_code || $check_code != $_SESSION['zrz_check_code']){
            print json_encode( array('status'=>401,'msg'=>__('验证码错误','ziranzhi2')) );
            exit;
        }
    }

    $result = array();
    $creds = array();
    $creds['user_login'] = sanitize_user($_POST['phoneEmail']);
    $creds['user_password'] = $_POST['pass'];
    $creds['remember'] = true;
    $login = wp_signon($creds);

    if ( ! is_wp_error( $login ) ){
        $result['status'] = 200;
        $result['msg'] = __('登陆成功','ziranzhi2');
    }else{
        $result['status'] = 401;
        $result['msg'] = __( '请检查用户名或者密码', 'ziranzhi2' );
    }
    
    unset($_SESSION['zrz_check_code']);
    unset($_SESSION['zrz_inv_id']);
    print json_encode( $result );
    exit;
}
add_action( 'wp_ajax_nopriv_zrz_ajax_login', 'zrz_ajax_login' );

/*AJAX 注册*/
add_action( 'wp_ajax_nopriv_zrz_ajax_register', 'zrz_ajax_register' );
function zrz_ajax_register(){
    if(!get_option('users_can_register')) exit;
    if (!isset($_SESSION)) {
        session_start();
    }
    $msg = '';
	check_ajax_referer('zrz-sign', 'security' );

	$user_name = sanitize_text_field($_POST['name']);
	$user_pass = $_POST['pass'];
	$recaptcha = sanitize_text_field($_POST['code']);

    $type = zrz_get_social_settings('type');

    $has_invitation = zrz_get_social_settings('has_invitation');
    $invitation_must = zrz_get_social_settings('invitation_must');

    $inv_code = isset($_POST['invCode']) ? esc_sql($_POST['invCode']) : '';

    if($has_invitation){
        //如果邀请码必填
        if($invitation_must && !$inv_code){
            $msg .= __( '请输入邀请码。','ziranzhi2' ) ;
        }

        $inv_check = zrz_check_invitation_code($inv_code);

        if($inv_code && !$inv_check){
            $msg .= __( '邀请码错误或者已被使用。','ziranzhi2' ) ;
        }

    }

    if(!$user_name || !$user_pass){
        $msg .= __( '请输入完整的信息','ziranzhi2' ) ;
    }
    
    if($type == 4){
        $phoneEmail = isset($_POST['phoneEmail']) ? $_POST['phoneEmail'] : '';
        if(!isset($_SESSION['zrz_check_code']) || !isset($_POST['checkcode']) || $_SESSION['zrz_check_code'] != strtolower($_POST['checkcode'])){
            $msg .= __( '验证码错误。','ziranzhi2' ) ;
        }

        if(!is_email($phoneEmail)){
            $msg .= __( '邮箱格式错误','ziranzhi2' ) ;
        }

        if(email_exists($phoneEmail)){
            $msg .= __( '此邮箱已经存在','ziranzhi2' ) ;
        }
       
    }else{
        $phoneEmail = isset($_SESSION['zrz_ep']) ? $_SESSION['zrz_ep'] : '';
        if(time() - $_SESSION['pass_time'] > 300){
            $msg .= __( '验证码过期。','ziranzhi2' ) ;
        }
        
        if($recaptcha != $_SESSION['sign_code']){
            $msg .= __( '验证码错误。','ziranzhi2' ) ;
        }
    }
   
    if(strlen($phoneEmail) < 5){
        $msg .= __( '用户名应该大于5个字符。','ziranzhi2' ) ;
    }

    if(strlen($user_pass)<6){
         $msg .= __( '密码必须大于5个字符。','ziranzhi2' );
    }

	if($msg){
        print json_encode( array('status'=>401,'msg'=>$msg) );
    	exit;
	}else{
        unset($_SESSION['sign_code']);
        unset($_SESSION['pass_time']);
        unset($_SESSION['zrz_ep']);
        unset($_SESSION['zrz_check_code']);
        
        if(is_email($phoneEmail)){
            $user_id = wp_create_user( wp_create_nonce($phoneEmail.rand(1,999)), $user_pass, $phoneEmail );
        }else{
            $user_id = wp_create_user($phoneEmail, $user_pass);
        }

		if (isset($user_id['errors'])) {
			$result['status'] = 401;
			$result['msg'] = __( '注册失败','ziranzhi2' );
		}else{
			update_user_option( $user_id, 'default_password_nag', true, true );
			$result['status'] = 200;
			$result['msg'] = __( '注册成功','ziranzhi2' );

            //更新用户昵称
            $arr = array(
                'display_name'=>$user_name,
                'ID'=>$user_id
            );
            wp_update_user($arr);

			//自动登录
			wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            //更新邀请码
            zrz_sign_invitation($user_id,$inv_code);
		}
        print json_encode($result);
        exit;
	}
    exit;

}

//忘记密码
add_action( 'wp_ajax_nopriv_lost_password', 'lost_password' );
function lost_password(){
    check_ajax_referer('zrz-sign', 'security' );
    $code = isset($_POST['code']) ? $_POST['code'] : '';

    if (!isset($_SESSION)) {
        session_start();
    }

    if(!$code){
        print json_encode( array('status'=>401,'msg'=>__('验证失败','ziranzhi2')) );
        exit;
    }

    $_code = $_SESSION['sign_code'];
    if($code == $_code){
        print json_encode( array('status'=>200,'msg'=>__('验证成功','ziranzhi2')) );
        exit;
    }else{
        print json_encode( array('status'=>401,'msg'=>__('验证失败','ziranzhi2')) );
        exit;
    }
}

//修改密码
add_action( 'wp_ajax_nopriv_re_password', 're_password' );
function re_password(){
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
    $code = isset($_POST['code']) ? $_POST['code'] : '';
    check_ajax_referer('zrz-sign', 'security' );
    if(!$pass || !$code){
        print json_encode( array('status'=>401,'msg'=>__('验证错误，请重试','ziranzhi2')) );
        exit;
    }

    if (!isset($_SESSION)) {
        session_start();
    }

    if(time() - $_SESSION['pass_time'] > 300){
        print json_encode( array('status'=>401,'msg'=>__('验证码过期','ziranzhi2')) );
        exit;
    }

    $_code = $_SESSION['sign_code'];
    $email_or_phone = $_SESSION['zrz_ep'];

    unset($_SESSION['sign_code']);
    unset($_SESSION['pass_time']);
    unset($_SESSION['zrz_ep']);

    if($code == $_code){
        if(is_email($email_or_phone)){
            $user = get_user_by( 'email', $email_or_phone );
        }else{
            $user = get_user_by( 'login', $email_or_phone );
        }

        if(!$user){
            print json_encode( array('status'=>200,'msg'=>__('手机或邮箱不存在','ziranzhi2')) );
            exit;
        }
        $arr = array(
            'user_pass'=>$pass,
            'ID'=>$user->ID
        );
        $user_id = wp_update_user($arr);
        if(is_numeric($user_id)){
            print json_encode( array('status'=>200,'msg'=>__('修改成功','ziranzhi2')) );
            exit;
        }
    }

    print json_encode( array('status'=>200,'msg'=>__('修改失败','ziranzhi2')) );
    exit;
}

//有用户注册的时候，添加积分
function user_register_update_zrz_credit( $user_id ) {
	$init = new Zrz_Invitation_Reg();
	$init->do_credit($user_id);

	if($user_id){
        $credit = zrz_get_credit_settings('zrz_credit_signup');
        $init = new Zrz_Credit_Message($user_id,4);
        $add_msg = $init->add_message('',$credit,$user_id,'');
	}
}
add_action( 'user_register', 'user_register_update_zrz_credit');

//强制完善资料
add_action('wp_ajax_zrz_complete_user_data','zrz_complete_user_data');
function zrz_complete_user_data(){
    check_ajax_referer('zrz-sign', 'security' );
    if (!isset($_SESSION)) {
        session_start();
    }

    $email_or_phone = $_SESSION['zrz_ep'];
    $code = $_SESSION['sign_code'];

    $_code = isset($_POST['code']) ? $_POST['code'] : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';

    if(!$_code || !$pass){
        print json_encode( array('status'=>401,'msg'=>__('请填写完整的信息','ziranzhi2')) );
        exit;
    }

    if(time() - $_SESSION['pass_time'] > 300){
        print json_encode( array('status'=>401,'msg'=>__('验证码过期，请重试','ziranzhi2')) );
        exit;
    }

    unset($_SESSION['sign_code']);
    unset($_SESSION['pass_time']);
    unset($_SESSION['zrz_ep']);

    if($code == $_code){
        $user_id = get_current_user_id();
        $_user_id = $user_id;

        if(is_email($email_or_phone)){
            $arr = array(
                'user_pass'=>$pass,
                'user_email'=>$email_or_phone,
                'ID'=>$user_id,
            );
            $user_id = wp_update_user($arr);
        }else{
            global $wpdb;
            $resout = $wpdb->update($wpdb->users, array('user_login' => $email_or_phone), array('ID' => $user_id));
            if($resout){
                $user_id = $_user_id;
                $arr = array(
                    'user_pass'=>$pass,
                    'ID'=>$_user_id,
                );
                $user_id = wp_update_user($arr);
            }else{
                $user_id = 'fail';
            }
        }

        if(is_numeric($user_id)){

            if(zrz_isMobile($email_or_phone)){
                $user_data = get_user_meta($user_id,'zrz_user_custom_data',true);
                $user_data = is_array($user_data) ? $user_data : array();
                $user_data['phone'] = $email_or_phone;
                update_user_meta($user_id,'zrz_user_custom_data',$user_data);
                wp_cache_delete($user_id, 'users');
                $creds = array('user_login' => $email_or_phone, 'user_password' => $pass, 'remember' => true);
                wp_signon($creds);
            }

            print json_encode( array('status'=>200,'msg'=>__('修改成功','ziranzhi2')) );
            exit;
        }
    }else{
        print json_encode( array('status'=>401,'msg'=>__('验证码错误','ziranzhi2')) );
        exit;
    }

    print json_encode( array('status'=>401,'msg'=>__('修改失败','ziranzhi2')) );
    exit;
}

//验证是否为手机号码
function zrz_isMobile($mobile='') {
    return preg_match('#^13[\d]{9}$|^14[5,6,7,8,9]{1}\d{8}$|^15[^4]{1}\d{8}$|^16[6]{1}\d{8}$|^17[0,1,2,3,4,5,6,7,8]{1}\d{8}$|^18[\d]{9}$|^19[8,9]{1}\d{8}$#', $mobile) ? true : false;
}

//发送验证码
add_action( 'wp_ajax_nopriv_zrz_send_code', 'zrz_send_code' );
add_action( 'wp_ajax_zrz_send_code', 'zrz_send_code' );
function zrz_send_code(){
    $email_or_phone = isset($_POST['ep']) ? $_POST['ep'] : 0;
    check_ajax_referer('zrz-sign', 'security' );
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    //是否是在修改资料
    $check_type = isset($_POST['checkType']) ? $_POST['checkType'] : '';

    //当前设置的验证方式
    $sign_type = zrz_get_social_settings('type');


    if($check_type){
        //修改资料时验证
        if($check_type == 'mail' && !is_email($email_or_phone)){
            print json_encode( array('status'=>401,'msg'=>__('邮箱格式错误','ziranzhi2')) );
            exit;
        }

        if($check_type == 'phone' && !zrz_isMobile($email_or_phone)){
            print json_encode( array('status'=>401,'msg'=>__('电话格式错误','ziranzhi2')) );
            exit;
        }
    }else{
        //注册时验证
        if($sign_type == 1 && !is_email($email_or_phone)){
            print json_encode( array('status'=>401,'msg'=>__('邮箱格式错误','ziranzhi2')) );
            exit;
        }

        if($sign_type == 2 && !zrz_isMobile($email_or_phone)){
            print json_encode( array('status'=>401,'msg'=>__('电话格式错误','ziranzhi2')) );
            exit;
        }

        if($sign_type == 3 && !is_email($email_or_phone) && !zrz_isMobile($email_or_phone)){
            print json_encode( array('status'=>401,'msg'=>__('格式错误','ziranzhi2')) );
            exit;
        }
    }

    if (!isset($_SESSION)) {
        session_start();
    }
    if($_SESSION['pass_time'] && zrz_isMobile($email_or_phone) && time() - $_SESSION['pass_time'] < 60){
        print json_encode( array('status'=>401,'msg'=>'请'.(60 - (time() - $_SESSION['pass_time'])).'秒后重试') );
        exit;
    }

    $code = rand ( 100000, 999999 );

    if(is_email($email_or_phone)){
        if(email_exists( $email_or_phone ) && $type == 're'){
            print json_encode( array('status'=>401,'msg'=>__('邮箱已被注册','ziranzhi2')) );
            exit;
        }elseif($type == 'pa' && !email_exists( $email_or_phone )){
            print json_encode( array('status'=>401,'msg'=>__('该邮箱未被注册','ziranzhi2')) );
            exit;
        }
        $site_name = get_bloginfo('name');
        $subject = '['.$site_name.']'.__('：请查收您的验证码','ziranzhi2');
        $message = '<div id="contentDiv" style="position:relative;font-size:14px;height:auto;z-index:1;zoom:1;line-height:1.7;" class="body">    
        <div style="background-color:#f2f2f2;margin:0;padding:20px ;width:100%;height:100%;font-family:Microsoft YaHei;">
            <div style="width:700px;background-color:#fff;margin:0 auto;">
                <div style="height:64px;margin:0;padding:0;width:100%;background-color:#3B97F6;">
                    <a href="'.home_url().'" style="display:block;padding-left:20px;padding-top:13px;" rel="noopener" target="_blank">
                    <img style="background: #fff;padding: 2px 10px;border-radius: 10px;" src="'.zrz_get_theme_settings('logo').'" width="85" height="37" border="0"></a>
                </div>
                <div style="padding:50px;margin:0;"><div style="font-size:32px;color:#87BD29;line-height:260%;border-bottom:1px dotted #E3E3E3;">'.$site_name.'欢迎您</div>
                    <p style="font-size:14px;color:#333;margin-top:30px;">
                        您的邮箱为：<span style="font-size:14px;color:#333;"><a href="'.$email_or_phone.'" rel="noopener" target="_blank">'.$email_or_phone.'</a></span>，验证码为：
                    </p>
                    <p style="font-size:34px;color:#55aeff;font-style:italic;">'.$code.'</p>
                    <p style="font-size:14px;color:#333;">验证码的有效期为5分钟，请在有效期内输入！</p>
                    <p style="font-size:14px;color:#333;">——'.$site_name.'</p>
                    <p style="font-size:12px;color:#999;border-top:1px dotted #E3E3E3;margin-top:30px;padding-top:30px;">
                        本邮件为系统邮件不能回复，请勿回复。
                    </p>
                </div>
            </div>
        </div>
    </div>';
        
        // sprintf(__('<div style="margin-bottom:20px">[%1$s]：您的验证码为 <strong>%2$s</strong>，5分钟后失效，请尽快验证。</div>','ziranzhi2'),$site_name,$code);
        $send = wp_mail( $email_or_phone, $subject, $message );

        if(!$send){
            print json_encode( array('status'=>401,'msg'=>__('发送失败','ziranzhi2')) );
            exit;
        }else{
            $_SESSION['pass_time'] = time();
            $_SESSION['sign_code'] = $code;
            $_SESSION['zrz_ep'] = $email_or_phone;

            print json_encode( array('status'=>200,'msg'=>__('发送成功','ziranzhi2')) );
            exit;
        }
    }

    if(zrz_isMobile($email_or_phone)){
        if(username_exists( $email_or_phone ) && $type == 're'){
            print json_encode( array('status'=>401,'msg'=>__('手机号码已被注册','ziranzhi2')) );
            exit;
        }elseif(!username_exists( $email_or_phone ) && $type == 'pa'){
            print json_encode( array('status'=>401,'msg'=>__('该手机号码未被注册','ziranzhi2')) );
            exit;
        }
        $send = zrz_ali_sms($email_or_phone,$code);
    }

    if(isset($send['body'])){
        $send = json_decode(trim($send['body']));
    }

    if($send === false || (isset($send->error_code) && $send->error_code == 0) || (isset($send->code) && $send->code == 0)){
        $_SESSION['pass_time'] = time();
        $_SESSION['sign_code'] = $code;
        $_SESSION['zrz_ep'] = $email_or_phone;

        print json_encode( array('status'=>200,'msg'=>__('发送成功','ziranzhi2')) );
        exit;
    }else{
        print json_encode( array('status'=>401,'msg'=>__('发送失败','ziranzhi2')) );
        exit;
    }
}

//短信注册
function zrz_ali_sms($nub,$code){
    
    //生成验证码
    $mobile = $nub;

    if(!$code) return false;

    //选择
    $select = zrz_get_social_settings('sms_select');

    //发送短信
    if($select == 'aliyun'){
        require ZRZ_THEME_DIR. '/inc/SDK/sms.php';
        $sms = zrz_get_social_settings('phone_setting');
        $cofig = array (
                'accessKeyId' => $sms['accessKeyId'],
                'accessKeySecret' => $sms['accessKeySecret'],
                'signName' => $sms['signName'],
                'templateCode' => $sms['templateCode']
        );
    
        $sms = new Sms($cofig);
        $status = $sms->send_verify($mobile, $code);

    }elseif($select == 'yunpian'){
        $sms = zrz_get_social_settings('yunpian');
        $msg = str_replace('#code#',$code,$sms['text']);
        $status = zrz_remote_post('https://sms.yunpian.com/v2/sms/single_send.json',array('apikey'=>$sms['apikey'],'text'=>$msg,'mobile'=>$mobile,'register'=>true));
    }elseif($select == 'juhe'){
        $sms = zrz_get_social_settings('juhe');
        $status = zrz_remote_post('http://v.juhe.cn/sms/send?',array('mobile'=>$mobile,'tpl_id'=>$sms['tpl_id'],'key'=>$sms['key'],'tpl_value'=>urlencode('#code#='.$code)));
    }
  
    return $status;
}

function zrz_remote_post($url,$body){
    $response = wp_remote_post( $url, array(
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => $body,
        'cookies'     => array()
        )
    );
     
    if ( is_wp_error( $response ) ) {
        return $response->get_error_message();
    } else {
        return $response;
    }
}

//检查邀请码
function zrz_check_invitation_code($code){
    if(!$code) return false;
    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_invitation';
    $cards = $wpdb->get_results( "SELECT * FROM $table_name WHERE invitation_nub='$code' AND invitation_status=0" ,ARRAY_A );
    if(count($cards) == 0){
        return false;
    }
    $credit = $cards[0];
    return $credit['invitation_credit'];
}

//使用邀请码
function zrz_sign_invitation($user_id,$inv){
    //检查邀请码是否使用
    $check = zrz_check_invitation_code($inv);
	$resout = false;
    if($check){
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_invitation';
        $resout = $wpdb->update(
            $table_name,
            array(
                'invitation_status'=>1,
                'invitation_user'=>$user_id,
            ),
            array('invitation_nub'=>$inv)
        );
        if($resout){
			
            //增加积分
            $init = new Zrz_Credit_Message($user_id,46);
            $add_msg = $init->add_message('',$check,$user_id,'');
            $resout = true;
        }
    }
    return apply_filters( 'zrz_invitation_ac',$resout,$user_id,$inv);
}