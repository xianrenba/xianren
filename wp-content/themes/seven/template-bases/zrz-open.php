<?php
//QQ
define('ZRZ_QQ_APPID',zrz_get_social_settings('open_qq_key'));
define('ZRZ_QQ_APPSECRET',zrz_get_social_settings('open_qq_secret'));

//微博
define('ZRZ_WEIBO_APPID',zrz_get_social_settings('open_weibo_key'));
define('ZRZ_WEIBO_APPSECRET',zrz_get_social_settings('open_weibo_secret'));

//微信
define('ZRZ_WEIXIN_APPID',zrz_get_social_settings('open_weixin_key'));
define('ZRZ_WEIXIN_APPSECRET',zrz_get_social_settings('open_weixin_secret'));

//微信公众号登录
define('ZRZ_WEIXIN_GZ_APPID',zrz_get_social_settings('open_weixin_gz_key'));
define('ZRZ_WEIXIN_GZ_APPSECRET',zrz_get_social_settings('open_weixin_gz_secret'));

$type = (isset($_GET['open_type']) && !empty($_GET['open_type'])) ? $_GET['open_type'] : false;
$code = (isset($_GET['code']) && !empty($_GET['code'])) ? $_GET['code'] : false;
$re_url = (isset($_GET['url']) && !empty($_GET['url'])) ? $_GET['url'] : false;
if($type && $code){
    all_oauth($type,$code,$re_url);
}

function ouath_redirect($userid,$type,$avatar,$re_url){
    echo '<div class="fs14 pos-r" style="width:100%;height:100%">跳转中，请不要关闭此窗口...</div>';
	zrz_check_vip($userid);
    $user_info = get_user_meta($userid,'zrz_open',true);
    $user_info = is_array($user_info) ? $user_info : array();
    $has_avatar = isset($user_info[$type.'_avatar_new']) ? $user_info[$type.'_avatar_new'] : false;

    //如果是微信登录，分别记录获取储存的头像
    if($type == 'weixin'){
        if(zrz_is_weixin()){
            $has_avatar = isset($has_avatar['in_wx']) ? $has_avatar['in_wx'] : '';
        }else{
            $has_avatar = isset($has_avatar['in_pc']) ? $has_avatar['in_pc'] : '';
        }
    }

    $avatar_host = zrz_get_media_settings('avatar_host');


    if(($avatar != $has_avatar || !isset($user_info[$type.'_avatar'])) && $avatar_host){
        $url = $avatar;
        $file_contents = wp_remote_post($url, array(
    		'method' => 'GET',
    		'timeout' => 1000,
    		'redirection' => 5,
    		'httpversion' => '1.0',
    		'blocking' => true,
    		'headers' => array( 'Accept-Encoding' => '' ),
    		'sslverify' => true,
    	    )
    	);

        //上传头像
        if (!isset($file_contents->errors) && isset($file_contents['body']) && $file_contents['body'] != ''){

            //头像 base64 编码
            add_filter('upload_dir', 'zrz_upload_dir', 100, 1);
            $img_base64 = base64_encode($file_contents['body']);

            $url = str_shuffle(uniqid()).'.jpg';

            $img             = str_replace( 'data:image/jpeg;base64,', '', $img_base64 );
            $img             = str_replace( ' ', '+', $img );
            $decoded         = base64_decode( $img );

            $url = wp_upload_bits($url, null, $decoded);

            if($url){
                $url = str_replace(zrz_get_media_path().'/','',$url['url']);
                $user_info[$type.'_avatar'] = $url;
                $user_info['avatar_set'] = $type;
                if($type == 'weixin'){
                    $wx_avatar = isset($user_info[$type.'_avatar_new']) && is_array($user_info[$type.'_avatar_new']) ? $user_info[$type.'_avatar_new'] : array();
                    if(zrz_is_weixin()){
                        $wx_avatar['in_wx'] = $avatar;
                    }else{
                        $wx_avatar['in_pc'] = $avatar;
                    }
                    $avatar = $wx_avatar;
                }
                $user_info[$type.'_avatar_new'] = $avatar;
                update_user_meta($userid , 'zrz_open',$user_info);
            }
        }
    }elseif($avatar != $has_avatar || !$has_avatar){
        $user_info['avatar_set'] = $type;
        $user_info[$type.'_avatar_new'] = $avatar;
        update_user_meta($userid , 'zrz_open',$user_info);
    }
        echo '<body>
        <script>
        if (window.opener) {
        window.opener.location.reload();
        window.close();
        } else {
            window.location.href="'.$re_url.'";
        }</script>
        </body>';
    exit;
}

function get_token($code,$type){

    if($type == 'qq'){
        $url = "https://graph.qq.com/oauth2.0/token";
        $client_id = ZRZ_QQ_APPID;
        $client_secret = ZRZ_QQ_APPSECRET;
    }elseif($type == 'weibo'){
        $url = "https://api.weibo.com/oauth2/access_token";
        $client_id = ZRZ_WEIBO_APPID;
        $client_secret = ZRZ_WEIBO_APPSECRET;
    }elseif($type = 'weixin'){
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    if($type == 'weixin'){
        if(zrz_is_weixin()){
            $data = array(
                'grant_type' => 'authorization_code',
                'redirect_uri' => home_url('/'),
                'code' => $code,
                'appid'=>ZRZ_WEIXIN_GZ_APPID,
                'secret'=>ZRZ_WEIXIN_GZ_APPSECRET
            );
        }else{
            $data = array(
                'grant_type' => 'authorization_code',
                'redirect_uri' => home_url('/'),
                'code' => $code,
                'appid'=>ZRZ_WEIXIN_APPID,
                'secret'=>ZRZ_WEIXIN_APPSECRET
            );
        }

    }else{
        $data = array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => home_url('/'),
            'code' => $code,
        );
    }
    $response = wp_remote_post( $url, array(
            'method' => 'POST',
            'body' => $data,
        )
    );
    $body = $response['body'];
    $output = false;
    if($type == 'qq'){
        $params = array ();
        parse_str( $body, $params );
        if(empty($params ["access_token"])){
            die('服务器响应错误');
        }
        $output = $params ["access_token"];
    }else{
        $output = json_decode($body,true);
    }

    return $output;
}

function all_oauth($type,$code,$re_url){
    $output = get_token($code,$type);

    if(!$output || (isset($output['error']) && !empty($output['error'])) || (isset($output['errcode']) && !empty($output['errcode']))){
        wp_die('社交登录设置有误，请联系管理员！');
    }

    if($type == 'weibo'){
        $access_token = $output['access_token'];
    }else{
        $access_token = $output;
    }

    $unionid = '';

    if($type == 'qq'){
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $access_token;
        $str = wp_remote_get( $graph_url );
        $str = $str['body'];

        if (strpos ( $str, "callback" ) !== false) {
            $lpos = strpos ( $str, "(" );
            $rpos = strrpos ( $str, ")" );
            $str = substr ( $str, $lpos + 1, $rpos - $lpos - 1 );
        }

        $user = json_decode ( $str,true );
        if (isset ( $user->error )) {
            echo "<h3>错误代码:</h3>" . $user->error;
            echo "<h3>信息  :</h3>" . $user->error_description;
            exit ();
        }

        $qq_openid = $user['openid'];
        if(!$qq_openid){
            wp_redirect(home_url());
            exit;
        }

        $get_user_info_url = "https://graph.qq.com/user/get_user_info?" . "access_token=" . $access_token . "&oauth_consumer_key=" . ZRZ_QQ_APPID . "&openid=" . $qq_openid . "&format=json";
        $uid = $qq_openid;
        $username = 'nickname';
        $avatar = 'figureurl_qq_2';

    }elseif($type == 'weibo'){
        $uid = $output['uid'];
        $get_user_info_url = "https://api.weibo.com/2/users/show.json?uid=".$uid."&access_token=".$access_token;
        $username = 'screen_name';
        $avatar = 'avatar_large';
    }elseif($type == 'weixin'){

        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $output['access_token'] . '&openid=' . $output['openid'];
        $username = 'nickname';
        $avatar = 'headimgurl';
        $uid = $output['openid'];
        $access_token = $output['access_token'];
        $unionid = isset($output['unionid']) ?  $output['unionid'] : '';
    }

    $user = wp_remote_get( $get_user_info_url );

    $userinfo  = json_decode($user['body'] , true);
    $username = $userinfo[$username];
    $avatar = $userinfo[$avatar];
    if($type == 'weixin'){
        $avatar = str_replace('/132','/0',$avatar);
    }
    $avatar = str_replace("http","https",$avatar);
    $user_info = array(
        'user_id' => $uid,
        'token' =>$access_token,
        'avatar'=>$avatar,
        'username' =>$username,
        'unionid'=>$unionid
    );
    save_login($type,$user_info,$re_url);
}

function save_login($type,$user_info,$re_url){
    $uid = $user_info['user_id'];
    $access_token = $user_info['token'];
    $avatar = $user_info['avatar'];
    $username = $user_info['username'];
    if($user_info['unionid'] && $user_info['unionid'] != ''){
        $user = get_users(array('meta_key'=>'zrz_'.$type.'_unionid','meta_value'=>$user_info['unionid']));
    }else{
        $user = get_users(array('meta_key'=>'zrz_'.$type.'_uid','meta_value'=>$uid));
    }

    //用户已经登录，则绑定
    if(is_user_logged_in()){
        if(empty($user)){
            $this_user = wp_get_current_user();
            $zrz_open = get_user_meta($this_user->ID,'zrz_open',true);
            $zrz_open = is_array($zrz_open) ? $zrz_open : array();
            $zrz_open[$type.'_access_token'] = $access_token;

            if($user_info['unionid']){
                update_user_meta($this_user->ID ,'zrz_'.$type.'_unionid',$user_info['unionid']);
            }
            zrz_save_weixin_openid($this_user->ID,$type,$uid);
            update_user_meta($this_user->ID ,'zrz_'.$type.'_uid',$uid);
            update_user_meta($this_user->ID ,'zrz_open',$zrz_open);
            ouath_redirect($this_user->ID,$type,$avatar,$re_url);
        }else{
            die('此帐号已被绑定！');
        }
    }else{
        //创建新用户
        if(is_wp_error($user) || empty($user)){
            if(!get_option('users_can_register')){
                die('本站已关闭注册');
            }
            $login_name = wp_create_nonce($uid.rand(1,99));
            $random_password = wp_generate_password( $length=12, false );
            $userdata=array(
               'user_login' => $login_name,
               'display_name' => $username,
               'user_pass' => $random_password,
               'nickname' => $username
            );
            $user_id = wp_insert_user( $userdata );
            wp_signon(array("user_login"=>$login_name,"user_password"=>$random_password),false);
            $meta_val = array(
               $type.'_access_token' => $access_token,
               'avatar_set' => $type
            );
            if(is_wp_error($user_id)){
               die('此用户已经存在！');
            }
            if($user_info['unionid']){
                update_user_meta($user_id ,'zrz_'.$type.'_unionid',$user_info['unionid']);
            }
            zrz_save_weixin_openid($user_id,$type,$uid);
            update_user_meta($user_id ,'zrz_'.$type.'_uid',$uid);
            update_user_meta($user_id ,'zrz_open',$meta_val);
            ouath_redirect($user_id,$type,$avatar,$re_url);
        }else{
            $zrz_open = get_user_meta($user[0]->ID,'zrz_open',true);
            $zrz_open = is_array($zrz_open) ? $zrz_open : array();
            $zrz_open[$type.'_access_token'] = $access_token;
            if($user_info['unionid']){
                update_user_meta($user[0]->ID ,'zrz_'.$type.'_unionid',$user_info['unionid']);
            }
            zrz_save_weixin_openid($user[0]->ID,$type,$uid);
            update_user_meta($user[0]->ID ,'zrz_open',$zrz_open);
            wp_set_auth_cookie($user[0]->ID,true);
            ouath_redirect($user[0]->ID,$type,$avatar,$re_url);
        }
    }
}

function zrz_save_weixin_openid($user_id,$type,$openid){
    if($type == 'weixin' && zrz_is_weixin()){
        update_user_meta($user_id ,'zrz_weixin_open_id',$openid);
    }
}
