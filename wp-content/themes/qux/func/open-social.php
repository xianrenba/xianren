<?php
// 拒绝搜索引擎索引开放平台登录地址
function um_connect_robots_mod( $output, $public ){
    $output .= "\nDisallow: /oauth";
	return $output;
}
add_filter( 'robots_txt', 'um_connect_robots_mod', 10, 2 );


//获取重定向链接
function um_get_redirect_uri(){
	if( isset($_GET['redirect_uri']) ) return urldecode($_GET['redirect_uri']);
	if( isset($_GET['redirect_to']) ) return urldecode($_GET['redirect_to']);
	if( isset($_GET['redirect']) ) return urldecode($_GET['redirect']);
	if( isset($_SERVER['HTTP_REFERER']) ) return urldecode($_SERVER['HTTP_REFERER']);
	return home_url();
}

//重定向Cookie名
function um_redirect_cookie_name(){
	$home = home_url();
	$home = str_replace('.', '_', $home);
	$cookie_name = 'um_logged_in_redirect_'.$home;
	return $cookie_name;
}

//判断QQ登录
function um_is_open_qq($user_id=0){
	$open_qq = _hui('um_open_qq');
	if(!$open_qq) return false;
	$id = (int)$user_id;
	$O = array(
		'ID'=>_hui('um_open_qq_id'),
		'KEY'=>_hui('um_open_qq_key')
	);		
	if( !$O['ID'] || !$O['KEY'] ) return false;
	if($id){		
		$U = array(
			'ID'=>get_user_meta( $id, 'um_qq_openid', true ),
			'TOKEN'=>get_user_meta( $id, 'um_qq_access_token', true )
		);		
		if( !$U['ID'] || !$U['TOKEN'] ) return false;
	}
	return true;	
}

//判断微博登录
function um_is_open_weibo($user_id=0){
	$open_weibo = _hui('um_open_weibo');
	if(!$open_weibo) return false;
	$id = (int)$user_id;
	$O = array(
		'KEY'=>_hui('um_open_weibo_key'),
		'SECRET'=>_hui('um_open_weibo_secret')
	);	
	if( !$O['KEY'] || !$O['SECRET'] ) return false;
	if($id){		
		$U = array(
			'ID'=>get_user_meta( $id, 'um_weibo_openid', true ),
			'TOKEN'=>get_user_meta( $id, 'um_weibo_access_token', true )
		);		
		if( !$U['ID'] || !$U['TOKEN'] ) return false;		
	}	
	return true;
}

//判断微信登录
function um_is_open_weixin($user_id=0){
	$open_weixin = _hui('um_open_weixin');
	if(!$open_weixin) return false;
	$id = (int)$user_id;
	$O = array(
		'KEY'=>_hui('um_open_weixin_appid'),
		'SECRET'=>_hui('um_open_weixin_secret')
	);	
	if( !$O['KEY'] || !$O['SECRET'] ) return false;
	if($id){		
		$U = array(
			'ID'=>get_user_meta( $id, 'um_weixin_openid', true ),
			'TOKEN'=>get_user_meta( $id, 'um_weixin_access_token', true )
		);		
		if( !$U['ID'] || !$U['TOKEN'] ) return false;		
	}	
	return true;
}

//后台个人资料QQ及微博信息
function um_open_user_contactmethods($user_contactmethods ){
	$user_contactmethods ['um_qq_openid'] = 'QQ OPEN ID';
	$user_contactmethods ['um_qq_access_token'] = 'QQ TOKEN';
	$user_contactmethods ['um_weibo_openid'] = 'WEIBO OPEN ID';
	$user_contactmethods ['um_weibo_access_token'] = 'WEIBO TOKEN';
	return $user_contactmethods ;
}
//~ add_filter('user_contactmethods','um_open_user_contactmethods');


//保存开放平台信息到用户信息
function saveOpenInfoAndProfile($user_id, $data ,$type){
	update_user_meta($user_id, 'um_'.$type.'_openid', $data['openid']);
	update_user_meta($user_id, 'um_'.$type.'_access_token', $data['token']);
	//update_user_meta($user_id, 'um_'.$type.'_refresh_token', $data['refresh_token']);
    //update_user_meta($user_id, 'um_'.$type.'_token_expiration', $data['expiration']);
	if($type === 'weixin'){
		update_user_meta($user_id, 'um_weixin_avatar', set_url_scheme($data['headimgurl'], 'https'));
		update_user_meta($user_id, 'um_weixin_unionid', $data['unionid']);
		update_user_meta($user_id, 'um_user_country', $data['country']); // 国家，如中国为CN
		update_user_meta($user_id, 'um_user_province', $data['province']); // 普通用户个人资料填写的省份
		update_user_meta($user_id, 'um_user_city', $data['city']); // 普通用户个人资料填写的城市
		update_user_meta($user_id, 'um_user_sex', $data['sex']==2 ? 'female' : 'male'); // 普通用户性别，1为男性，2为女性
	}
	if($type === 'weibo'){
		update_user_meta($user_id, 'um_weibo_avatar', $data['avatar_large']);
		update_user_meta($user_id, 'um_weibo_profile_img', $data['profile_image_url']);
		update_user_meta($user_id, 'um_weibo_id', $data['id']);
		update_user_meta($user_id, 'um_user_description', $data['description']);
		update_user_meta($user_id, 'um_user_location', $data['location']);
		update_user_meta($user_id, 'um_user_sex', $data['sex']!='m' ? 'female' : 'male'); // 普通用户性别，m为男性，f为女性
	}
}


function generateRandomStr($len=10, $char_type = 'letter_and_number'){
    $hash = '';
    switch ($char_type){
        case 'letter':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'number':
            $chars = '01234567890';
            break;
        default:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    }
    $max = strlen($chars) - 1;
    mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $len; $i++){
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}


//后台个人资料页添加QQ及微博绑定按钮
function um_open_profile_fields( $user ) {
	$qq = um_is_open_qq();
	$weibo = um_is_open_weibo();
	$weixin = um_is_open_weixin();
	if( $qq || $weibo || $weixin) {
    ?>
<table class="form-table">
	<?php if($qq){ ?>
	<tr>
		<th scope="row">QQ登录</th>
		<td>
	<?php  if(um_is_open_qq($user->ID)) { ?>
		<p><?php _e('已绑定','um');?> <a href="<?php echo home_url('/oauth/qq?action=logout'); ?>"><?php _e('点击解绑','um');?></a></p>
		<?php echo um_get_avatar( $user->ID , '100' , 'qq' ); ?>
	<?php }else{ ?>
		<a class="button button-primary" href="<?php echo home_url('/oauth/qq?action=login'); ?>">绑定QQ账号</a>
	<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php if($weibo){ ?>
	<tr>
		<th scope="row">微博登录</th>
		<td>
	<?php if(um_is_open_weibo($user->ID)) { ?>
		<p><?php _e('已绑定','um');?> <a href="<?php echo home_url('/oauth/weibo?action=logout'); ?>"><?php _e('点击解绑','um');?></a></p>
		<?php echo um_get_avatar( $user->ID , '100' , 'weibo' ); ?>
	<?php }else{ ?>
		<a class="button button-primary" href="<?php echo home_url('/oauth/weibo?action=login'); ?>">绑定微博账号</a>
	<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php if($weixin){ ?>
	<tr>
		<th scope="row">微信登录</th>
		<td>
	<?php if(um_is_open_weixin($user->ID)) { ?>
		<p><?php _e('已绑定','um');?> <a href="<?php echo home_url('/oauth/weixin?action=logout'); ?>"><?php _e('点击解绑','um');?></a></p>
		<?php echo um_get_avatar( $user->ID , '100' , 'weixin' ); ?>
	<?php }else{ ?>
		<a class="button button-primary" href="<?php echo home_url('/oauth/weixin?action=login'); ?>">绑定微信账号</a>
	<?php } ?>
		</td>
	</tr>
	<?php } ?>
</table>
    <?php
	}
}
add_action( 'profile_personal_options', 'um_open_profile_fields' );

function um_open_template_redirect(){	
	$redirect = um_get_redirect_uri();	
	$die_title = '请重试或报告管理员';		
	$redirect_text = '<p>'.$die_title.' </p><p><a href="'.$redirect.'">点击返回</a></p>';
	$user_ID = get_current_user_id();
	$oauth = strtolower(get_query_var('oauth'));
	$oauth_last = get_query_var('oauth_last');
    if($oauth && !in_array($oauth, (array)json_decode(ALLOWED_OAUTH_TYPES))){
        set404();
        exit;      
    }else if($oauth_last){
        global $wp_query;
        $wp_query->is_home = false;
        $wp_query->is_page = true; //将该模板改为页面属性，而非首页
	    $template =  UM_DIR . '/func/open-last.php';
        load_template($template);
		exit; 
	}
	
	function um_open_login($openid='',$token='',$type='qq',$info){
		
		$data = (array)$info;
        $data['openid'] = $openid;
        $data['token'] = $token;
        $data['name'] = isset($info->name) ? $info->name : '';
        
		$cookie_name = um_redirect_cookie_name();
		$redirect = isset($_COOKIE[$cookie_name]) ? urldecode($_COOKIE[$cookie_name]) : home_url();	
		$die_title = '请重试或报告管理员';		
		$redirect_text = '<p>'.$die_title.' </p><p><a href="'.$redirect.'">点击返回</a></p>';
		$user_ID = get_current_user_id();
		$id_field = 'um_'.$type.'_openid';
		global $wpdb;
		$user_exist = $wpdb->get_var( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='$id_field' AND meta_value='$openid' " );		
		if(is_user_logged_in()){		
			if( isset($user_exist) && (int)$user_exist>0 ){
				wp_die($data['name'].' 已有绑定账号，请绑定其他账号或先解除原有账号。 '.$redirect_text  , $die_title );	
			}else{
			    //update_user_meta($user_ID, $id_field, $openid);
			    //update_user_meta($user_ID,$token_field, $token);
			    saveOpenInfoAndProfile($user_ID,$data, $type);
			    header('Location:'.$redirect);
			    exit;	
			}	
		}else{		
			if( isset($user_exist) && (int)$user_exist>0 ){	
				$insert_user_id = $user_exist;
				
			}else{
				$_data_transient_key = md5('oauth_temp_data_' . strtolower(generateRandomStr(10, 'letter')));
                set_transient($_data_transient_key, maybe_serialize($data), 60*10); // 10分钟缓存过期时间
                
                wp_safe_redirect(add_query_arg(array('redirect' => $redirect, 'key' => $_data_transient_key), _url_for('oauth_'.$type.'_last')));
                exit;
			}
			
			if( is_wp_error($insert_user_id) ) {
				wp_die('登录失败！ '.$redirect_text  , $die_title );
			}else{
				//update_user_meta($insert_user_id, $id_field, $openid);
				//update_user_meta($insert_user_id, $token_field, $token);
				saveOpenInfoAndProfile($insert_user_id,$data,$type);

				update_user_meta( $insert_user_id, 'um_latest_login', current_time( 'mysql' ) );
				wp_set_current_user( $insert_user_id, $user_login );
				wp_set_auth_cookie( $insert_user_id );
				do_action( 'wp_login', $user_login );
                header('Location:'.$redirect);
				exit;		
			}		
		}
	}
	
	function um_open_logout($type='qq'){
		$redirect = get_edit_profile_url();
		if($type==='qq'){
			$type = 'qq';
			$name = ' <img src="'.UM_URI.'/img/qq_32x32.png" > QQ ';	
		}else{
			$type = 'weibo';
			$name = ' <img src="'.UM_URI.'/img/weibo_32x32.png" > 微博 ';	
		}

		$user_ID = get_current_user_id();
		if($type==='weibo'){
			$token = get_user_meta($user_ID , 'um_weibo_access_token', true );
			$info = wp_remote_retrieve_body(wp_remote_get('https://api.weibo.com/oauth2/revokeoauth2?access_token='.$token));	
		}
		delete_user_meta($user_ID, 'um_'.$type.'_openid');
		delete_user_meta($user_ID, 'um_'.$type.'_access_token');
		header('Location:'.$redirect);
		exit;
	}

	function um_set_redirect_cookie(){
		setcookie(um_redirect_cookie_name(), urlencode(um_get_redirect_uri()), time()+3600);
	}
	
	function um_get_redirect_text(){
		$cookie_name = um_redirect_cookie_name();
		$redirect = isset($_COOKIE[$cookie_name]) ? urldecode($_COOKIE[$cookie_name]) : um_get_redirect_uri();
		return '<a href="'.$redirect.'">点击返回</a>';
	}
	
	function um_connect_check($str=''){
		if(empty($str)){
			wp_die('服务器无法连接开放平台，请重试或联系管理员！'.um_get_redirect_text(), '无法连接开发平台');
		}
		return $str;
	}

    if( $oauth ==='qq' && um_is_open_qq() && ( is_home() ||is_front_page() ) ){
		um_set_redirect_cookie();
		$OPEN_QQ = array(
			'APPID'=>_hui('um_open_qq_id'),
			'APPKEY'=>_hui('um_open_qq_key'),
			'CALLBACK'=>home_url('/oauth/qq')
		);
		$action = isset($_GET['action']) ? trim($_GET['action']) : 'login';

		if( $action==='login' ){
			
			if( is_user_logged_in() && get_user_meta( $user_ID, 'um_qq_openid', TRUE ) ){
				wp_die('你已经绑定了QQ号，一个账号只能绑定一个QQ号，如要更换，请先解绑现有QQ账号，再绑定新的。<p><a href="'.$redirect.'">点击返回</a></p>','不能绑定多个QQ');
			}
			
			if(!isset($_GET['code']) && !isset($_GET['state'])){
				$state = md5(uniqid(rand(), true));
		        $params = array(
			        'response_type'=>'code',
			        'client_id'=>$OPEN_QQ['APPID'],
			        'state'=>$state,
			        'scope'=>'get_user_info,get_info,add_t,del_t,add_pic_t,get_repost_list,get_other_info,get_fanslist,get_idollist,add_idol,del_idol',
			        'redirect_uri'=>$OPEN_QQ['CALLBACK']
		        );
		        setcookie('qq_state', md5($state), time()+600);
		        header('Location:https://graph.qq.com/oauth2.0/authorize?'.http_build_query($params));
		        exit();
			}
			
			if(!isset($_GET['code']) && isset($_GET['state'])){				
				wp_die( "你应该同意授权本应用连接你的开放平台账户".$redirect_text , "用户取消了授权" );
			}
			
			if( isset($_GET['code']) && isset($_GET['state']) && isset($_COOKIE['qq_state']) && $_COOKIE['qq_state']==md5($_GET['state']) ){
			    $params = array(
				    'grant_type'=>'authorization_code',
				    'code'=>$_GET['code'],
				    'client_id'=>$OPEN_QQ['APPID'],
				    'client_secret'=>$OPEN_QQ['APPKEY'],
				    'redirect_uri'=>$OPEN_QQ['CALLBACK']
			    );

			    $response = um_connect_check(wp_remote_retrieve_body(wp_remote_get('https://graph.qq.com/oauth2.0/token?'.http_build_query($params))));

			    if (strpos($response, "callback") !== false){
				    $lpos = strpos($response, "(");
				    $rpos = strrpos($response, ")");
				    $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
				    $msg = json_decode($response);
				    if (isset($msg->error)){
					    wp_die( "<b>error</b> " . $msg->error . " <b>msg</b> " . $msg->error_description.$redirect_text , $die_title );
				    }
			    }
			 
			    $params = array();
			    parse_str($response, $params);
			    $token = $params['access_token'];
			
			    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$token;
			
			    $str = um_connect_check(wp_remote_retrieve_body(wp_remote_get($graph_url)));
	 
			    if (strpos($str, "callback") !== false){
				    $lpos = strpos($str, "(");
				    $rpos = strrpos($str, ")");
				    $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
			    }
			    $user = json_decode($str);
			    if (isset($user->error)){
				    wp_die( "<b>error</b> " . $user->error . " <b>msg</b> " . $user->error_description.$redirect_text , $die_title );
			    }

			    $qq_openid = $user->openid;
			
			    $info_url = "https://graph.qq.com/user/get_user_info?access_token=".$token."&oauth_consumer_key=".$OPEN_QQ['APPID']."&openid=".$qq_openid;
			
			    $info = json_decode(um_connect_check(wp_remote_retrieve_body(wp_remote_get($info_url))));
			
			    if ($info->ret){
				    wp_die( "<b>error</b> " . $info->ret . " <b>msg</b> " . $info->msg.$redirect_text , $die_title );
			    }
			    
			    $info->openid = $qq_openid;
			    
			    um_open_login($qq_openid,$token,'qq',$info);

			    exit;
		    }
			
		}
		
		if($action==='logout' && is_user_logged_in()) um_open_logout('qq');
	
	}
	
	if( $oauth ==='weibo' && um_is_open_weibo() && ( is_home() ||is_front_page() ) ){
		
		um_set_redirect_cookie();
		$OPEN_WEIBO = array(
			'KEY'=>_hui('um_open_weibo_key'),
			'SECRET'=>_hui('um_open_weibo_secret'),
			'CALLBACK'=>home_url('/oauth/weibo')
		);
		
		$action = isset($_GET['action']) ? trim($_GET['action']) : 'login';
			
		if($action==='login'){
			if( is_user_logged_in() && get_user_meta( $user_ID, 'um_weibo_openid', TRUE ) ){
				wp_die('你已经绑定了微博账号，一个账号只能绑定一个微博，如要更换，请先解绑现有微博账号，再绑定新的。<p><a href="'.$redirect.'">点击返回</a></p>','不能绑定多个微博');
			}
			
			if(!isset($_GET['code'])){
		    	$params = array(
				    'response_type'=>'code',
			    	'client_id'=>$OPEN_WEIBO['KEY'],
				    'redirect_uri'=>$OPEN_WEIBO['CALLBACK']
			    );
			    header('Location:https://api.weibo.com/oauth2/authorize?'.http_build_query($params));
			    exit();
			}
			
			if( isset($_GET['code']) ){

			    $access = um_connect_check(wp_remote_retrieve_body(wp_remote_post('https://api.weibo.com/oauth2/access_token?',array( 'body' => array(
			        'grant_type'=>'authorization_code',
			        'client_id'=>$OPEN_WEIBO['KEY'],
			        'client_secret'=>$OPEN_WEIBO['SECRET'],
			        'code'=>trim($_GET['code']),
					'redirect_uri'=>$OPEN_WEIBO['CALLBACK']
			    )))));
			
		        $access = json_decode($access,true);
			
		        if (isset($access->error)){
			        wp_die( "<b>error</b> " . $access->error . " <b>msg</b> " . $access["error_description"].$redirect_text , $die_title );
		        }
	
		        $openid = $access["uid"];
		        $token = $access["access_token"];

		        $info = um_connect_check(wp_remote_retrieve_body(wp_remote_get('https://api.weibo.com/2/users/show.json?access_token='.$token.'&uid='.$openid)));

		        $info = json_decode($info);
			
		        if (isset($info->error)){
			        wp_die( "<b>error</b> " . $info->error_code . " <b>msg</b> " . $info->error.$redirect_text , $die_title );
		        }
			
		        um_open_login($openid,$token,'weibo',$info);
		
		        exit();
	        }
		}
			
		if($action==='logout' && is_user_logged_in()) um_open_logout('weibo');

	}
	
	if( $oauth ==='weixin' && um_is_open_weixin() && ( is_home() ||is_front_page() ) ){
		
		um_set_redirect_cookie();
		$OPEN_WEIXIN = array(
            'APPID'=>_hui('um_open_weixin_appid'),
			'SECRET'=>_hui('um_open_weixin_secret'),
			'CALLBACK'=>home_url('/oauth/weixin')
        );
		$action = isset($_GET['action']) ? trim($_GET['action']) : 'login';
		if($action==='login'){
			if( is_user_logged_in() && get_user_meta( $user_ID, 'ming_weixin_openid', TRUE ) ){
				wp_die('你已经绑定了微信账号，一个账号只能绑定一个微信，如要更换，请先解绑现有微信账号，再绑定新的。<p><a href="'.$redirect.'">点击返回</a></p>','不能绑定多个微信');
			}
			if(!isset($_GET['code']) && !isset($_GET['state'])){
				$state = md5(uniqid(rand(), true));
				$params = array(
				    'response_type' => 'code',
					'appid' => $OPEN_WEIXIN['APPID'],
					'state' => $state,
					'scope'=>'snsapi_login',
                    'redirect_uri' => $OPEN_WEIXIN['CALLBACK']

				);
                setcookie('weixin_state', md5($state), time()+600);
				header('Location:https://open.weixin.qq.com/connect/qrconnect?' . http_build_query($params));
				exit;
			}
			
			if(!isset($_GET['code']) && isset($_GET['state'])){
				wp_die( "你应该同意授权本应用连接你的开放平台账户".$redirect_text , "用户取消了授权" );
			}
			
			if( isset($_GET['code']) && isset($_GET['state']) && isset($_COOKIE['weixin_state']) && $_COOKIE['weixin_state']==md5($_GET['state']) ){
				$params = array(
				    'grant_type' => 'authorization_code',
					'code' => $_GET['code'],
					'appid' => $OPEN_WEIXIN['APPID'],
					'secret' => $OPEN_WEIXIN['SECRET']
				);
              
				$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($params);
				
				$str = wp_remote_retrieve_body(wp_remote_get($url));
				
				$msg = null;
				if(preg_match('/\{(.*)\}/', $str, $matches)){
					$msg = json_decode(trim($matches[0]));
					if(isset($msg->errcode)){
						wp_die( "<b>error</b> " . $msg->errcode . " <b>msg</b> " . $msg->errmsg .$redirect_text , 'Grant WeiXin Access Token Failed' );
					}
				}else{
					wp_die( "<b>error</b>grant_access_token_error<b>msg</b>The open server returned with a incorrect response " .$redirect_text , 'Grant WeiXin Access Token Failed' );
				}  
				
				// 获取的openid等
				$openid = $msg->openid;
				$access_token = $msg->access_token; // 有效期2小时
				$refresh_token = $msg->refresh_token; // 有效期30天
				$expire_in = $msg->expires_in;
				
				//开始抓取开放平台信息
				$params = array(
				    'access_token' => $access_token,
					'openid' => $openid
				);

				$graph_url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($params);
				
				//$info = json_decode(ming_connect_check(wp_remote_retrieve_body(wp_remote_get($graph_url))));
                
                $info = wp_remote_retrieve_body(wp_remote_get($graph_url));
              
				if(preg_match('/\{(.*)\}/', $info, $matches)){
					$msg = json_decode(trim($matches[0]));
					if(isset($msg->errcode)){
						wp_die( "<b>error</b> " . $msg->errcode . " <b>msg</b> " . $msg->errmsg .$redirect_text , 'Refresh WeiXin Token Failed' );
					}
				}else{
					wp_die( "<b>error</b> refresh_token_error <b>msg</b>The open server returned with a incorrect response " .$redirect_text , 'Refresh WeiXin Token Failed' );
				}
                $info = $msg;
				$info->name = $msg->nickname;
				um_open_login($openid,$access_token,'weixin',$info);				
				exit();
			}
			
		}
      
      	if($action==='logout' && is_user_logged_in()) um_open_logout('weixin');
		
	}

}
add_action('template_redirect', 'um_open_template_redirect' );

function oauth_last() {
	$is_new = true;
	$user_login = $_POST['username'];
	$password = $_POST['userpass'];
	$type = $_POST['type'];
	$_data_transient_key = $_GET['key'];
	$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : home_url();
	$ico = 'error';
	$msg = '';
	$success = 0;
	if($_data_transient_key && $cache_data = get_transient($_data_transient_key)) {
		$data = (array)maybe_unserialize($cache_data);
		if(!$data) {
			$msg = 'KEY错误';
			$return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
			echo json_encode($return);
			exit;
		}
		//$user_login = strtoupper(static::$_type) . $data['openid']; 
		//$user_login = strtoupper(static::$_type) . Utils::generateRandomStr(6, 'letter'); // 使用随机字符
		//$data['user_login'] = $user_login;
		if(is_email($user_login)) {
			$user = get_user_by('email', $user_login);
		} else {
			$user = get_user_by('login', $user_login);
		}
		if($user) {
			if(!wp_check_password( $password, $user->data->user_pass, $user->ID)) {
				$msg = '该用户名已存在, 请提供正确的密码以供连接或重选新的用户名';
				$return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
				echo json_encode($return);
				exit;
			}
			// 更新用户数据
			$insert_user_id = wp_update_user( array(
			           'ID'  => $user->ID,
			           'nickname'  => $data['name'],
			           'display_name'  => $data['name']
			        ) );
			$is_new = false;
		} else {
			// 开放平台连接并需要新建一个本地用户绑定
			$insert_user_id = wp_insert_user( array(
			            'user_login'  => $user_login,
			            'user_email' => $user_login,
			            'nickname'  => $data['name'],
			            'display_name'  => $data['name'],
			            'user_pass' => $password
			        ) );
		}
		if( is_wp_error($insert_user_id) ) {
			$msg = '创建新用户失败';
			$return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
			echo json_encode($return);
			exit;
		} else {
			saveOpenInfoAndProfile($user_ID,$data, $type);
			if( $is_new) {
				update_user_meta($insert_user_id, 'um_avatar', $type);
				wp_update_user( array ('ID' => $insert_user_id, 'role' => _hui('um_open_role') ) );
				add_um_message( $insert_user_id, 'unread', current_time('mysql'), __('请完善账号信息','um'), sprintf(__('欢迎来到%1$s，请<a href="%2$s">完善资料</a>，其中电子邮件尤为重要，许多信息都将通过电子邮件通知您！','um') , get_bloginfo('name'), admin_url('profile.php')) );
			}
			update_user_meta( $insert_user_id, 'um_latest_login', current_time( 'mysql' ) );
			wp_set_current_user( $insert_user_id, $user_login );
			wp_set_auth_cookie( $insert_user_id );
			do_action( 'wp_login', $user_login );
			//header('Location:'.$redirect);
			$msg = '登录成功，3秒后为你刷新';
			$return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
			echo json_encode($return);
			exit;
		}
	} else {
		$msg = $_data_transient_key.$redirect;
		$return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
		echo json_encode($return);
		exit;
	}
}
add_action( 'wp_ajax_nopriv_oauth_last', 'oauth_last' );
add_action( 'wp_ajax_oauth_last', 'oauth_last' );
?>