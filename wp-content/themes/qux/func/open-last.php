<?php
//oauth last
if(isset($_POST['username']) && isset($_POST['userpass'])){
    $is_new = true;
    $user_login = $_POST['username'];
    $password = $_POST['userpass'];
    $type = $_POST['type'];
    $_data_transient_key = isset($_GET['key']) ? $_GET['key'] : '';
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : home_url();
    $id_field = 'um_'.$type.'_openid';
    $token_field = 'um_'.$type.'_access_token';
    $ico = 'error';
    $msg = '未知错误，请重试';
    $success = 0;
    if($_data_transient_key && $cache_data = get_transient($_data_transient_key)){
        $data = (array)maybe_unserialize($cache_data);
        if(!$data){
            $msg = '开放平台登录失败, 必要资料未完成';
     	    $return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
	        echo json_encode($return);
	        exit;
        }
        //$user_login = strtoupper(static::$_type) . $data['openid']; 
        //$user_login = strtoupper(static::$_type) . Utils::generateRandomStr(6, 'letter'); // 使用随机字符
        //$data['user_login'] = $user_login;
	}else{
        $msg = '无法获取 OAuth数据, 请重试授权步骤';
		$return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
	    echo json_encode($return);
	    exit;
    }
    if(is_email($user_login)){
        $user = get_user_by('email', $user_login);
    }else{
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
        ));
        $is_new = false;
    }else{    
        // 开放平台连接并需要新建一个本地用户绑定
        $insert_user_id = wp_insert_user( array(
            'user_login'  => $user_login,
            'user_email' => $user_login,
            'nickname'  => $data['name'],
            'display_name'  => $data['name'],
            'user_pass' => $password
        ));
    }
    if( is_wp_error($insert_user_id) ) {
        $msg = '创建新用户失败'.$insert_user_id->get_error_message();
    }else{
        //update_user_meta($insert_user_id, $id_field, $data['openid']);
	    //update_user_meta($insert_user_id, $token_field, $data['token']);
	    saveOpenInfoAndProfile($insert_user_id, $data ,$type);
	    if( $is_new){
		    update_user_meta($insert_user_id, 'um_avatar', $type);
		    wp_update_user( array ('ID' => $insert_user_id, 'role' => _hui('um_open_role') ) );
		    add_um_message( $insert_user_id, 'unread', current_time('mysql'), __('请完善账号信息','um'), sprintf(__('欢迎来到%1$s，请<a href="%2$s">完善资料</a>，其中电子邮件尤为重要，许多信息都将通过电子邮件通知您！','um') , get_bloginfo('name'), admin_url('profile.php')) );
        }
	    update_user_meta( $insert_user_id, 'um_latest_login', current_time( 'mysql' ) );
	    wp_set_current_user( $insert_user_id, $user_login );
	    wp_set_auth_cookie( $insert_user_id );
	    do_action( 'wp_login', $user_login );
        //header('Location:'.$redirect);
        $ico = 'success';
        $success = 1;
        $msg = '开放平台连接登录成功！';
    }
    $return = array('success'=>$success,'msg'=>$msg,'redirect'=>$redirect,'ico'=>$ico);
	echo json_encode($return);
	exit;
}

get_header();

$open_type = strtolower(get_query_var('oauth'));
?>
<style>
.wrapper{   
    position: relative;
    height: 650px;
    overflow: hidden;
}
.oauthlast .form-account input {
    margin: 10px auto;
}
.form-control {
    display: inline-block;
    width: 100%;
    background-color: #fff;
    background-image: none;
    border: 1px solid #bdc3c7;
    color: #34495e;
    font-size: 1.6rem;
    line-height: 1.467;
    padding: 8px 12px;
    height: 42px;
    border-radius: 3px;
    box-shadow: none;
    transition: border .25s linear,color .25s linear,background-color .25s linear;
}
/*.container .content-wrap .oauthlast {
    position: relative;
    top: 40%;
    left: 50%;
    width: 320px;
    height: auto;
    margin-top: -180px;
    margin-left: -160px;
    text-align: center;
}*/
.content.oauthlast {
    padding: 20px;
    text-align: center;
    margin-right: 0;
    background-color: #fff;
    border: 1px solid #EAEAEA;
    border-radius: 4px;
}
.oauthlast .form-account {
    width: 320px;
    margin: 50px auto 0;
    text-align: left;
}
</style>
<section class="container">	
<div class="content-wrap">
	<div class="content oauthlast">
            <div class="form-account">
                <h2 class="form-account-heading"><?php _e('最后一步 完善账号信息', 'tt'); ?></h2>
                <input type="hidden" id="oauthType" value="<?php echo $open_type; ?>">
                <label for="inputUsername" class="sr-only"><?php _e('邮箱', 'tt'); ?></label>
                <input type="text" id="inputUsername" class="form-control" placeholder="<?php _e('邮箱', 'tt'); ?>" required="required">
                <label for="inputPassword" class="sr-only"><?php _e('重复密码', 'tt'); ?></label>
                <input type="password" id="inputPassword" class="form-control" placeholder="<?php _e('密码', 'tt'); ?>" required="required">
                <button class="btn btn-lg btn-primary btn-block" id="bind-account" type="submit"><?php _e('确定', 'tt'); ?></button>
            </div>
	</div>
</div>
</section>
<?php
get_footer();