<?php
/*
* 与用户相关的函数
* zrz_get_user_page_url() 获取用户页面的 url 网址
* zrz_get_user_page_link() 获取用户页面的 html 链接
*/

/*
* 获取用户页面的 url 网址
* $user_id 用户的ID，必要项（int）
* $type 用户页面的参数，非必要项 （array）,例如  array('key','value')
*/
function zrz_get_user_page_url($user_id = 0,$type = ''){

    if(!$user_id) $user_id = get_the_author_meta( 'ID' );

    $url = home_url('/user/'.$user_id);

    if(!$type){
        return esc_url($url);
    }else{
        return esc_url($url.'/'.$type);
    }
}

/*
* 获取用户页面的 html 链接
* $user_id 用户的ID，如果未指定，则为当前登录用户
*/
function zrz_get_user_page_link($user_id = 0){

    if(!is_numeric($user_id)) return '<span class="guest">'.$user_id.'</span>';

    if(!$user_id) $user_id = get_the_author_meta( 'ID' );

    $display_name = get_the_author_meta('display_name',$user_id);

    $display_name = zrz_get_content_ex($display_name,16);

    $url = zrz_get_user_page_url($user_id);

    if($display_name){
        $link = '<a id="user-'.$user_id.'" class="users" href="'.$url.'">'.esc_html($display_name).'</a>';
    }else{
        $link = '<span class="gray">'.esc_html__('已注销','ziranzhi2').'</span>';
    }
    return $link;
}

//将用户页面的 author 改为 user
function change_author_permalinks() {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'user';
    //$wp_rewrite->flush_rules();
}
add_action('init','change_author_permalinks');

add_filter('query_vars', 'users_query_vars');
function users_query_vars($vars) {
    // add lid to the valid list of variables
    $vars[] = 'user';
    $vars[] = 'zrz_user_page';
    return $vars;
}

add_filter('author_link', 'zrz_author_url_with_id', 1000, 2);
function zrz_author_url_with_id($link, $author_id) {
  $link_base = trailingslashit(get_option('home'));
  $link = "user/".$author_id;
  return $link_base . $link;
}

function user_rewrite_rules( $wp_rewrite ) {
    $new_rules = array();
    $new_rules['user/([0-9]+)/([^&]+)?'] = 'index.php?author=$matches[1]&zrz_user_page=$matches[2]';
    $new_rules['user/([0-9]+)?'] = 'index.php?author=$matches[1]';
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules','user_rewrite_rules');

add_filter('manage_users_columns','remove_users_columns');
function remove_users_columns($column_headers) {
    unset($column_headers['name']);
    $column_headers['lv'] = '权限等级';
    return $column_headers;
}

add_action('manage_users_custom_column',  'pippin_show_user_id_column_content', 10, 3);
function pippin_show_user_id_column_content($value, $column_name, $user_id) {

	if ( 'lv' == $column_name ) return zrz_get_lv($user_id,'name');

    return $value;
}

//更新用户信息
class zrz_update_user_data{
    public static $type;
    public static $val;//存入的值
    public static $user_id;//用户ID
    public static $avatar_type;//头像类型
    public static $avatar_new;//社交头像原始图片链接，用以对比用户的社交网络上的头像是否更换

    public function __construct($type,$val,$user_id = 0,$avatar_type = 'defalut',$avatar_new = ''){
        self::$type = $type;

        self::$val = $val;

        if($user_id){
            self::$user_id = $user_id;
        }else{
            self::$user_id = get_current_user_id();
        }

        self::$avatar_type = $avatar_type;
        self::$avatar_new = $avatar_new;

    }

    public static function update_user_meta(){
        $value = get_user_meta(self::$user_id,'zrz_open',true);
        $value = is_array($value) ? $value : array();
        switch (self::$type) {
            //更新用户封面图片 cover
            case 'cover':
                foreach (self::$val as $key => $val) {
                    $value['cover'][$key] = $val;
                }
                $resout = update_user_meta(self::$user_id,'zrz_open',$value);
                break;
            //更新用户头像 avatar
            case 'avatar':
                $value['avatar_set'] = self::$avatar_type;
                if(self::$avatar_type === 'default'){
                    $value['avatar'] = self::$val;
                }elseif(self::$avatar_type === 'qq'){
                    $value['qq_avatar'] = self::$val;
                    $value['qq_avatar_new'] = self::$avatar_new;
                }elseif(self::$avatar_type === 'weibo'){
                    $value['weibo_avatar'] = self::$val;
                    $value['weibo_avatar_new'] = self::$avatar_new;
                }
                $resout = update_user_meta(self::$user_id,'zrz_open',$value);
                break;
            default:
                $resout = false;
                break;
        }

        if($resout){
            return true;
        }else{
            return false;
        }
    }
}

class zrz_get_user_data{
    public static $user_id;//用户ID
    public static $user_open;//用户的自定义信息，包括封面图片，头像，社交登陆的信息等等
    public static $size;//头像尺寸

    public function __construct($user_id,$size = 0){
        self::$user_id = $user_id;
        self::$user_open = get_user_meta($user_id, 'zrz_open',true);
        self::$size = $size;
    }

    //获取用户封面图片
    public static function get_cover(){

        //如果没有封面图片，返回空
        if(!isset(self::$user_open['cover'])) return '';

        $cover_arr = self::$user_open['cover'];
        $width = (int)zrz_get_theme_settings('page_width');
        return zrz_get_thumb(zrz_get_media_path().'/'.$cover_arr['key'],$width,ceil(240*$width/1140),abs($cover_arr['top']),true);

    }

    public static function get_avatar($type = false){
        $avatar_host = zrz_get_media_settings('avatar_host');

        $avatar_set = $type ? $type : (isset(self::$user_open['avatar_set']) ? self::$user_open['avatar_set'] : 'default');

        $gif = zrz_get_media_settings('avatar_gif');

        if($avatar_set == 'default'){
            if(isset(self::$user_open['avatar']) && !empty(self::$user_open['avatar'])){
                return zrz_get_thumb(zrz_get_media_path().'/'.self::$user_open['avatar'],self::$size,self::$size,0,false,$gif);
            }else{
                if(zrz_get_media_settings('auto_avatar')){
                    $user_name = get_the_author_meta('display_name',self::$user_id);
                    $avatar = new Get_Letter_Avatar($user_name,self::$size);
                    return zrz_get_thumb($avatar->get_letter_avatar(),self::$size,self::$size,0,false);
                }else{
                    return get_avatar_url(self::$user_id,self::$size);
                }
            }
        }elseif($avatar_set == 'qq'){
            if($avatar_host == 0){
                return isset(self::$user_open['qq_avatar_new']) ? self::$user_open['qq_avatar_new'] : '';
            }
            return isset(self::$user_open['qq_avatar']) && !empty(self::$user_open['qq_avatar']) ?
            zrz_get_thumb(zrz_get_media_path().'/'.self::$user_open['qq_avatar'],self::$size,self::$size,0,false,$gif)
            : '';
        }elseif($avatar_set == 'weibo'){
            if($avatar_host == 0){
                return isset(self::$user_open['weibo_avatar_new']) ? self::$user_open['weibo_avatar_new'] : '';
            }
            return isset(self::$user_open['weibo_avatar']) && !empty(self::$user_open['weibo_avatar']) ?
            zrz_get_thumb(zrz_get_media_path().'/'.self::$user_open['weibo_avatar'],self::$size,self::$size,0,false,$gif)
            : '';
        }elseif($avatar_set == 'weixin'){
            if($avatar_host == 0){
                $wx_av = isset(self::$user_open['weixin_avatar_new']) ? self::$user_open['weixin_avatar_new'] : '';
                if(!empty($wx_av)){
                    return zrz_is_weixin() ? $wx_av['in_wx'] : $wx_av['in_pc'];
                }
            }else{
                return isset(self::$user_open['weixin_avatar']) && !empty(self::$user_open['weixin_avatar']) ?
                zrz_get_thumb(zrz_get_media_path().'/'.self::$user_open['weixin_avatar'],self::$size,self::$size,0,false,$gif)
                : '';
            }
        }elseif($type == 'set'){
            return isset(self::$user_open['avatar_set']) ? self::$user_open['avatar_set'] : 'default';
        }

    }
}

//设置默认地址
add_action('wp_ajax_zrz_set_default_address','zrz_set_default_address');
function zrz_set_default_address($key = ''){
    if(!$key){
        $key = isset($_POST['key']) ? $_POST['key'] : '';
    }

    $user_id = get_current_user_id();

    if($key){
        if(update_user_meta($user_id,'zrz_default_address',$key)){
            print json_encode(array('status'=>200,'msg'=>__('设置成功','ziranzhi2')));
            exit;
        }
    }

    print json_encode(array('status'=>401,'msg'=>__('设置失败','ziranzhi2')));
    exit;
}

//添加地址
add_action('wp_ajax_zrz_add_address','zrz_add_address');
function zrz_add_address(){
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $key = isset($_POST['key']) ? $_POST['key'] : '';

    $user_id = get_current_user_id();

    if($data){
        $user_data = get_user_meta($user_id,'zrz_user_custom_data',true);
        $user_data = is_array($user_data) ? $user_data : array();
        $user_data['address'] = $data;
        update_user_meta($user_id,'zrz_user_custom_data',$user_data);
        zrz_set_default_address($key);
        print json_encode(array('status'=>200,'msg'=>__('设置成功','ziranzhi2')));
        exit;
    }
    print json_encode(array('status'=>401,'msg'=>__('设置失败','ziranzhi2')));
    exit;
}

//获取默认地址
function zrz_get_default_address($user_id){
    $default = get_user_meta($user_id,'zrz_default_address',true);
    $user_data = get_user_meta($user_id,'zrz_user_custom_data',true);
    $address = is_array($user_data) ? $user_data : array();
    $add = '';
    if($address){
        $add = isset($address['address']) ? $address['address'] : '';
        if($add){
            $add = isset($add[$default]) ? $add[$default] : reset($add);
            $add = $add['address'] .' '.$add['name'].' '.$add['phone'];
        }
    }
    return apply_filters('zrz_get_default_address_filters',$add);
}


//用户资料更新
add_action('wp_ajax_zrz_ajax_update_user_data','zrz_ajax_update_user_data');
function zrz_ajax_update_user_data(){

    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    if($type != 'address'){
        $data = sanitize_text_field($data);
    }

    if(!$user_id || !$type || (get_current_user_id() != $user_id && !current_user_can('delete_users'))){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    if($type == 'mail' && !is_email($data)){
        print json_encode(array('status'=>401,'msg'=>__('邮箱格式错误','ziranzhi2')));
        exit;
    }

    if($type == 'mail' && email_exists( $data )){
        print json_encode(array('status'=>401,'msg'=>__( '此邮箱已被注册','ziranzhi2' )));
        exit;
    }

    if($type == 'phone' && !zrz_isMobile($data)){
        print json_encode(array('status'=>401,'msg'=>__('手机格式错误','ziranzhi2')));
        exit;
    }

    if($type == 'phone' && username_exists( $data )){
        print json_encode(array('status'=>401,'msg'=>__('手机已被注册','ziranzhi2')));
        exit;
    }

    //开启了何种验证方式
    $sign_type = zrz_get_social_settings('type');
    $pass = '';

    //修改邮箱或者电话
    if (!isset($_SESSION)) {
        session_start();
    }

    if(isset($_SESSION['zrz_ep']) && isset($_SESSION['sign_code']) && (($type == 'mail' && ($sign_type == 1 || $sign_type == 3)) || ($type == 'phone' && ($sign_type == 2 || $sign_type == 3)))){

        $msg = '';

        //邮箱或电话
        $phoneEmail = isset($_SESSION['zrz_ep']) ? $_SESSION['zrz_ep'] : '';

        //验证码
        $recaptcha = sanitize_text_field($_POST['code']);

        //密码
        $pass = isset($_POST['pass']) ? $_POST['pass'] : '';

        if($phoneEmail != $data){
            $msg = ($type == 'mail' && ($sign_type == 1 || $sign_type == 3)) ? __( '没有正确验证邮箱','ziranzhi2' ) : __( '没有正确验证电话','ziranzhi2' );
        }elseif(time() - $_SESSION['pass_time'] > 300){
            $msg = __( '验证码过期','ziranzhi2' );
        }elseif($recaptcha != $_SESSION['sign_code']){
            $msg = __( '验证码错误','ziranzhi2' );
        }elseif(!$pass){
            $msg = __( '请输入密码','ziranzhi2' );
        }elseif(strlen($pass) < 6){
            $msg = __( '密码必须大于5个字符','ziranzhi2' );
        }

        unset($_SESSION['sign_code']);
        unset($_SESSION['pass_time']);
        unset($_SESSION['zrz_ep']);

        if($msg){
            print json_encode( array('status'=>401,'msg'=>$msg) );
            exit;
        }
    }

    $success = false;

    if($type == 'phone'){

        global $wpdb;
        $resout = $wpdb->update($wpdb->users, array('user_login' => $data), array('ID' => $user_id));
        if($resout){
            $arr = array(
                'user_pass'=>$pass,
                'ID'=>$user_id,
            );
            $user_id = wp_update_user($arr);

            wp_cache_delete($user_id, 'users');
            $creds = array('user_login' => $user_id, 'user_password' => $pass, 'remember' => true);
            wp_signon($creds);
            wp_cache_flush();
            $success = true;
        }

    }elseif($type == 'gender' || $type == 'address'){

        $user_data = get_user_meta($user_id,'zrz_user_custom_data',true);
        $user_data = is_array($user_data) ? $user_data : array();
        $user_data[$type] = $data;
        update_user_meta($user_id,'zrz_user_custom_data',$user_data);
        $success = true;

    }elseif($type == 'bio' || $type == 'nickname' || $type == 'mail' || $type == 'site' || $type == 'pass'){
        $arr = array();

        switch ($type) {
            case 'bio':
                $arr['description'] = $data;
                break;
            case 'nickname':
                $arr['display_name'] = $data;
                break;
            case 'mail':
                $arr['user_email'] = $data;
                break;
            case 'site':
                $arr['user_url'] = $data;
                break;
            case 'pass':
                $arr['user_pass'] = $data;
                break;
            default:
                break;
        }

        $arr['ID'] = $user_id;
        if($pass){
            $arr['user_pass'] = $pass;
        }
        $re_user_id = wp_update_user($arr);
        if($user_id == $re_user_id){
            $success = true;
        } else {
            $success = false;
        }
    }

    if($success == true){
        print json_encode(array('status'=>200,'msg'=>__('更新成功','ziranzhi2')));
        exit;
    }

    print json_encode(array('status'=>401,'msg'=>__('更新失败','ziranzhi2')));
    exit;
}

//解除社交账户绑定
add_action('wp_ajax_zrz_unbind_social','zrz_unbind_social');
function zrz_unbind_social(){
    $type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : '';

    if(!$type || !$user_id || (!current_user_can('delete_users') && $user_id != get_current_user_id())){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_data = get_userdata( $user_id );

    if(get_the_author_meta('user_email', $user_id) == '' || !zrz_isMobile($user_data->user_login) ){
        print json_encode(array('status'=>401,'msg'=>__('请先绑定邮箱或者手机号码','ziranzhi2')));
        exit;
    }

    $user_info = get_user_meta($user_id,'zrz_open',true);
    $user_info = is_array($user_info) && !empty($user_info) ? $user_info : array();
    $set = '';
    if($user_info['avatar_set'] == $type){
        $user_info['avatar_set'] = 'default';
        $set = 'default';
    }

    $user_info[$type.'_access_token'] = '';
    $user_info[$type.'_avatar'] = '';
    $user_info[$type.'_avatar_new'] = '';
    update_user_meta($user_id,'zrz_open',$user_info);

    delete_user_meta( $user_id, 'zrz_'.$type.'_uid');
    if($type == 'weixin'){
        delete_user_meta($user_id ,'zrz_'.$type.'_unionid');
    }
    print json_encode(array('status'=>200,'name'=>$name,'msg'=>'解除绑定成功！您可以使用邮箱和密码来登录','set'=>$set));
    exit;
}

//设置项中选择头像
add_action('wp_ajax_zrz_setting_set_avatar','zrz_setting_set_avatar');
function zrz_setting_set_avatar(){
    $type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : '';

    if(!$type || !$user_id || (!current_user_can('delete_users') && $user_id != get_current_user_id())){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_info = get_user_meta($user_id,'zrz_open',true);
    $user_info = is_array($user_info) && !empty($user_info) ? $user_info : array();

    $user_info['avatar_set'] = $type;
    update_user_meta($user_id,'zrz_open',$user_info);
    print json_encode(array('status'=>200,'name'=>$name,'msg'=>'选择成功'));
    exit;
}

//管理员财富变更
add_action('wp_ajax_zrz_setting_change_nub','zrz_setting_change_nub');
function zrz_setting_change_nub(){
    if(!current_user_can('delete_users')){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : '';
    $nub = isset($_POST['nub']) ? esc_attr($_POST['nub']) : '';
    $why = isset($_POST['why']) ? esc_attr($_POST['why']) : '';

    if(!$type || !$user_id || !$nub){
        print json_encode(array('status'=>401,'msg'=>__('参数不全','ziranzhi2')));
        exit;
    }

    if(!$why){
        $why = '未说明变更原因。';
    }

    //积分变更
    if($type == 'credit'){
        //添加通知
        $init = new Zrz_Credit_Message($user_id,14);
		$add_msg = $init->add_message(get_current_user_id(), $nub,str_shuffle(current_time('timestamp')),$why);
        if($add_msg){
            print json_encode(array('status'=>200,'msg'=>__('积分变更成功','ziranzhi2')));
            exit;
        }
    }

    //财富变更
    if($type == 'rmb'){
        $rmb = get_user_meta($user_id,'zrz_rmb',true);
        $rmb = $rmb + $nub;

        $init = new Zrz_Credit_Message($user_id,37);
		$add_msg = $init->add_message(get_current_user_id(), 0,str_shuffle(current_time('timestamp')),$why);

        if($add_msg){
            update_user_meta($user_id,'zrz_rmb',$rmb);
            print json_encode(array('status'=>200,'msg'=>__('余额变更成功','ziranzhi2')));
            exit;
        }
    }

    print json_encode(array('status'=>401,'msg'=>__('更新失败','ziranzhi2')));
    exit;
}

function zrz_get_rmb($user_id){
    $rmb = get_user_meta($user_id,'zrz_rmb',true);
    $rmb = $rmb ? $rmb : 0;
    return '<span class="coin l1 rmb">¥ '.$rmb.'</span>';
}

//通知消息
add_action('wp_ajax_zrz_get_message','zrz_get_message');
function zrz_get_message(){
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : false;

    $not = " AND ((NOT msg_type = 2 AND NOT msg_type = 9 AND NOT msg_type = 13 AND NOT msg_type = 16 AND NOT msg_type = 17 AND NOT msg_type = 18 AND NOT msg_type = 21 AND NOT msg_type = 24 AND NOT msg_type = 25 AND NOT msg_type = 38 AND NOT msg_type = 41 
    AND NOT msg_type = 42 AND NOT msg_type = 43 AND NOT msg_type = 45 AND NOT msg_type = 46 AND NOT msg_credit_total=0) OR (msg_credit_total=0 AND (msg_type=35 OR msg_type=31 OR msg_type=32 OR msg_type=41)))";
    $all_sql = "( msg_read=0 OR msg_read=1)".$not;

    //身份验证
    if(!is_user_logged_in()) {
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_id = get_current_user_id();

    //页码验证
    if(!$paged){
        print json_encode(array('status'=>401,'msg'=>__('参数不全，请重试','ziranzhi2')));
        exit;
    }
    $number = (int)get_option('posts_per_page',true);
    $offset = ($paged-1)*$number;

    $init = new Zrz_Credit_Message($user_id);

    $count = $count_new = $pages = 0;

    if($offset == 0){
        $count = $init->get_message(true,$all_sql);
        $count_new = $init->get_message(true,'msg_read=0'.$not);
        $pages = ceil($count/$number);
    }

    $datas = $init->get_message(false,$all_sql,$number,$offset);

    //如果没有消息，返回空
    if(!$datas){
        print json_encode(array('status'=>401,'msg'=>__('没有消息','ziranzhi2')));
        exit;
    }

    $users = '';
    $post_id = 0;
    $resout = array();

    foreach( $datas as $data ){

        //获取用户
        if($data->msg_users){
            $users = json_decode($data->msg_users);
            $users = get_user_array($users);
        }

        //获取标题
        $post_type = get_post_type($data->msg_key);
        if($post_type){
            $post_title = ($post_type == 'post' ? '文章：' : ($post_type == 'pps' ? sprintf( '%1$s：',zrz_custom_name('bubble_name')) : '')).'<a class="post-title" href="'.get_permalink( $data->msg_key ).'">'.get_the_title( $data->msg_key ).'</a>';
        }

        if($data->msg_type == 35){
            $msg_data = unserialize($data->msg_value);
            $text = '<p class="fs12 mar10-b">'.zrz_get_user_page_link($msg_data['user']).' 给你打赏了 ¥'.$msg_data['price'].'元。';
            $text .= $msg_data['text'] ? '并对你说：</p><p>'.$msg_data['text'].'</p>' : '</p>';
        }
        $resout[] = array(
            'type'=>$data->msg_type,
            'users'=>$users,
            'post_title'=>$post_title,
            'comment'=>$data->msg_type == 29 || $data->msg_type == 14 || $data->msg_type == 37 || $data->msg_type == 23 ? $data->msg_value : ($data->msg_type == 35 ? $text : zrz_get_comment_content($data->msg_value)),
            'date'=>$data->msg_date,
            'credit'=>$data->msg_credit,
            'new'=> $data->msg_read
        );
    }

    print json_encode(array('status'=>200,'msg'=>$resout,'count'=>$count,'countNew'=>$count_new,'pages'=>$pages));
    exit;
}

add_action('wp_ajax_zrz_get_new_msg_count','zrz_get_new_msg_count');
function zrz_get_new_msg_count(){
    $user_id = get_current_user_id();

    $not = " AND ((NOT msg_type = 2 AND NOT msg_type = 9 AND NOT msg_type = 13 AND NOT msg_type = 16 AND NOT msg_type = 17 AND NOT msg_type = 18 AND NOT msg_type = 21 AND NOT msg_type = 24 AND NOT msg_type = 25 AND NOT msg_type = 38 AND NOT msg_type = 41 AND NOT msg_type = 42 AND NOT msg_type = 43 AND NOT msg_type = 45 AND NOT msg_type = 46 AND NOT msg_credit_total=0) OR (msg_credit_total=0 AND (msg_type=35 OR msg_type=31 OR msg_type=32 OR msg_type=41)))";

    $init = new Zrz_Credit_Message($user_id);
    $count_new = $init->get_message(true,'msg_read=0'.$not);

    $mission = is_user_logged_in() ? (int)get_user_meta($user_id,'zrz_mission',true) : 0;

    if($count_new){
        print json_encode(array('status'=>200,'count'=>$count_new,'mission'=>$mission));
        exit;
    }else{
        print json_encode(array('status'=>401,'count'=>0,'mission'=>$mission));
        exit;
    }

}

//标记已读
add_action('wp_ajax_zrz_msg_read','zrz_msg_read');
function zrz_msg_read(){
    $user_id = get_current_user_id();

    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_message';

    $resout = $wpdb->update(
        $table_name,
        array('msg_read'=>1),
        array('user_id'=>$user_id,'msg_read'=>0)
    );
    if($resout){
        print json_encode(array('status'=>200));
        exit;
    }

    print json_encode(array('status'=>401));
    exit;
}

//标记消息已读
add_action('wp_ajax_zrz_dmsg_read','zrz_dmsg_read');
function zrz_dmsg_read(){
    $user_id = get_current_user_id();
    $cuser_id = isset($_POST['cuser_id']) ? (int)$_POST['cuser_id'] : '';

    if($cuser_id){
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_message';

        $resout = $wpdb->update(
            $table_name,
            array('msg_read'=>1),
            array('user_id'=>$cuser_id,'msg_users'=>'['.$user_id.']','msg_type'=>13)
        );
        if($resout){
            print json_encode(array('status'=>200));
            exit;
        }
    }
    print json_encode(array('status'=>401));
    exit;
}

//财富消息
add_action('wp_ajax_zrz_get_gold_message','zrz_get_gold_message');
function zrz_get_gold_message(){
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : false;
    $uid = isset($_POST['uid']) ? (int)$_POST['uid'] : false;

    $not = " AND (msg_type = 1 OR msg_type = 2 OR msg_type = 3 OR msg_type = 4 OR msg_type = 5 OR msg_type = 9 OR msg_type = 10 OR msg_type = 11 OR msg_type = 15 OR msg_type = 14 OR msg_type = 16 OR msg_type = 17 OR msg_type = 18 OR msg_type = 19 OR msg_type = 20 OR msg_type = 21 OR msg_type = 24 OR msg_type = 33 OR msg_type = 34 OR msg_type = 28 OR msg_type = 29 OR msg_type = 30 OR msg_type = 36 OR msg_type = 38 OR msg_type = 39 OR msg_type = 40 OR msg_type = 42 OR msg_type = 43 OR msg_type = 45 OR msg_type = 44 OR msg_type = 46) AND NOT (msg_credit = 0 OR msg_credit_total = 0)";
    $all_sql = "( msg_read=0 OR msg_read=1)".$not;

    //身份验证
    if(!is_user_logged_in()) {
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_id = get_current_user_id();

    if($uid && current_user_can('delete_users')){
        $user_id = $uid;
    }

    //页码验证
    if(!$paged){
        print json_encode(array('status'=>401,'msg'=>__('参数不全，请重试','ziranzhi2')));
        exit;
    }
    $number = (int)get_option('posts_per_page',true);
    $offset = ($paged-1)*$number;

    $init = new Zrz_Credit_Message($user_id);

    $count = $count_new = $pages = 0;

    if($offset == 0){
        $count = $init->get_message(true,$all_sql);
        $count_new = $init->get_message(true,'msg_read=0'.$not);
        $pages = ceil($count/$number);
    }

    $datas = $init->get_message(false,$all_sql,$number,$offset);

    //如果没有消息，返回空
    if(!$datas){
        print json_encode(array('status'=>401,'msg'=>__('没有消息','ziranzhi2')));
        exit;
    }

    $users = '';
    $post_id = 0;
    $resout = array();

    foreach( $datas as $data ){

        //获取用户
        if($data->msg_users){
            $users = json_decode($data->msg_users);
            $users = get_user_array($users,false);
        }

        //获取标题
        $post_type = get_post_type($data->msg_key);
        if($post_type){
            $post_title = '<a class="post-title" href="'.get_permalink( $data->msg_key ).'">'.get_the_title( $data->msg_key ).'</a>';
        }

        $resout[] = array(
            'type'=>$data->msg_type,
            'users'=>$users,
            'post_title'=>$post_title,
            'comment'=>$data->msg_type == 38 ? $data->msg_value : wpautop($data->msg_value),
            'date'=>$data->msg_date,
            'new'=> $data->msg_read,
            'credit'=>$data->msg_credit,
            'credit_total'=>$data->msg_credit_total,
        );
    }

    print json_encode(array('status'=>200,'msg'=>$resout,'count'=>$count,'countNew'=>$count_new,'pages'=>$pages));
    exit;
}

//提现消息
add_action('wp_ajax_zrz_get_tx_message','zrz_get_tx_message');
function zrz_get_tx_message(){
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : false;
    $uid = isset($_POST['uid']) ? (int)$_POST['uid'] : false;
    $edit = isset($_POST['edit']) ? (int)$_POST['edit'] : false;

    //身份验证
    if(!is_user_logged_in()) {
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_id = get_current_user_id();

    if($uid && current_user_can('delete_users')){
        $user_id = $uid;
    }

    if($edit){
        $and = "user_id=$user_id";
    }else{
        $and = "msg_users=$user_id";
    }

    $all_sql = "( msg_read=0 OR msg_read=1) AND $and AND msg_type=41";

    //页码验证
    if(!$paged){
        print json_encode(array('status'=>401,'msg'=>__('参数不全，请重试','ziranzhi2')));
        exit;
    }

    $number = (int)get_option('posts_per_page',true);
    $offset = ($paged-1)*$number;

    $tx_allowed = zrz_get_credit_settings('zrz_tx_allowed');

    $init = new Zrz_Credit_Message($tx_allowed);

    $count = $pages = 0;

    if($offset == 0){
        $count = $init->get_message(true,$all_sql);
        $pages = ceil($count/$number);
    }

    $datas = $init->get_message(false,$all_sql,$number,$offset);

    //如果没有消息，返回空
    if(!$datas){
        print json_encode(array('status'=>401,'msg'=>__('没有消息','ziranzhi2')));
        exit;
    }

    $users = '';
    $post_id = 0;
    $resout = array();

    foreach( $datas as $data ){

        //获取用户
        if($data->msg_users){
            $users = '<a href="'.zrz_get_user_page_url($data->msg_users).'" target="_blank">'.get_the_author_meta('display_name',$data->msg_users).'</a>';
        }

        //获取标题
        $post_type = get_post_type($data->msg_key);
        if($post_type){
            $post_title = '<a class="post-title" href="'.get_permalink( $data->msg_key ).'">'.get_the_title( $data->msg_key ).'</a>';
        }

        $qcode = get_user_meta($data->msg_users,'zrz_qcode',true);

        $resout[] = array(
            'id'=>$data->msg_id,
            'users'=>$users,
            'date'=>$data->msg_date,
            'credit'=>$data->msg_value,
            'status'=>$data->msg_key,
            'user_code'=>array(
                'weixin'=>isset($qcode['weixin']) ? zrz_get_media_path().'/'.$qcode['weixin'] : '',
                'alipay'=>isset($qcode['alipay']) ? zrz_get_media_path().'/'.$qcode['alipay'] : '',
            ),
            'user_id'=>$data->msg_users
        );
    }

    print json_encode(array('status'=>200,'msg'=>$resout,'count'=>$count,'pages'=>$pages));
    exit;
}

//获取用户动态数据
function zrz_get_user_activities($user_id,$paged,$count = false){

    $all_sql = '( msg_type=5 OR msg_type=24 OR msg_type=17 OR msg_type=18 OR msg_type=2 OR msg_type=4)';
    $number = (int)get_option('posts_per_page',true);

    $init = new Zrz_Credit_Message((int)$user_id);

    //总页数
    if($count){
        $count = $init->get_message(true,$all_sql);
        if($count){
            return ceil($count/$number);
        }
        return 0;
    }

    //用户动态数据
    $offset = ($paged-1)*$number;
    $msg = $init->get_message(false,$all_sql,$number,$offset);

    return $msg;
}

//获取用户动态
add_action('wp_ajax_nopriv_zrz_get_user_activities_fn', 'zrz_get_user_activities_fn');
add_action('wp_ajax_zrz_get_user_activities_fn','zrz_get_user_activities_fn');
function zrz_get_user_activities_fn($user_id = 0,$paged = 0,$return = false){
    if(!$user_id && !$paged){
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : 0;
    }

    if(!$user_id || !$paged){
        if($return){
            return '';
        }else{
            print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
            exit;
        }
    }

    $msg = zrz_get_user_activities($user_id,$paged);

    if($msg){
        $html = '';
        foreach ($msg as $val) {
            $post_status = get_post_status($val->msg_key);

            //发表文章
            if($val->msg_type == 5){
                if($post_status == 'publish'){
                    $html .= '
                        <div class="act-list pd10-b pd10-t">
                            <div class="clarfix fs15"><span class="act-ac"><i class="iconfont zrz-icon-font-write1"></i> 发表了文章</span> <span class="fr"><time class="timeago gray" datetime="'.$val->msg_date.'" data-timeago="'.$val->msg_date.'" ref="timeAgo">'.$val->msg_date.'</time></span></div>
                            <div class="pos-r">
                                <h2 class="mar10-t mar10-b"><a href="'.get_permalink($val->msg_key).'">'.get_the_title($val->msg_key).'</a></h2>
                                <p class="des fs13 gray">'.zrz_get_post_des($val->msg_key).'</p>
                            </div>
                        </div>
                    ';
                }
            }

            //冒泡
            if($val->msg_type == 24){
                if($post_status == 'publish'){
                    $img = zrz_get_pp_content_img($val->msg_key);
                    $video = zrz_get_bubble_video($val->msg_key);
                    $html .= '
                        <div class="act-list pd10-b pd10-t">
                            <div class="clarfix fs15"><span class="act-ac"><i class="iconfont zrz-icon-font-iocnqipaotu"></i> 发起了'.sprintf( '%1$s：',zrz_custom_name('bubble_name')).'</span> <span class="fr"><time class="timeago gray" datetime="'.$val->msg_date.'" data-timeago="'.$val->msg_date.'" ref="timeAgo">'.$val->msg_date.'</time></span></div>
                            <div class="pos-r fs14 mar10-t">
                                <a href="'.get_permalink($val->msg_key).'">'.apply_filters('the_content',convert_smilies(strip_tags(wpautop(get_post_field('post_content',$val->msg_key))))).($img ? '<p>[图片]</p>' : ($video ? '<p>[视频]</p>' : '')).'</a>
                            </div>
                        </div>
                    ';
                }
            }

            //发帖
            if($val->msg_type == 17){
                if($post_status == 'publish'){
                    $html .= '
                        <div class="act-list pd10-b pd10-t">
                            <div class="clarfix fs15"><span class="act-ac"><i class="iconfont iconfont zrz-icon-font-tiezi"></i> 发起了话题</span> <span class="fr"><time class="timeago gray" datetime="'.$val->msg_date.'" data-timeago="'.$val->msg_date.'" ref="timeAgo">'.$val->msg_date.'</time></span></div>
                            <div class="pos-r">
                                <h2 class="mar10-t mar10-b"><a href="'.get_permalink($val->msg_key).'">'.get_the_title($val->msg_key).'</a></h2>
                                <p class="des fs13">'.zrz_get_post_des($val->msg_key).'</p>
                            </div>
                        </div>
                    ';
                }
            }

            //回复帖子
            if($val->msg_type == 18){
                if($post_status == 'publish'){
                    $html .= '
                        <div class="act-list pd10-b pd10-t">
                            <div class="clarfix fs15"><span class="act-ac"><i class="iconfont zrz-icon-font-taolun"></i> 回复了话题</span> <span class="fr"><time class="timeago gray" datetime="'.$val->msg_date.'" data-timeago="'.$val->msg_date.'" ref="timeAgo">'.$val->msg_date.'</time></span></div>
                            <div class="pos-r">
                                <h2 class="mar10-t mar10-b"><a href="'.get_permalink($val->msg_key).'">'.get_the_title($val->msg_key).'</a></h2>
                                <p class="des fs13 act-list-reply">'.zrz_get_post_des($val->msg_value).'</p>
                            </div>
                        </div>
                    ';
                }
            }

            //注册
            if($val->msg_type == 4){
                $html .= '
                    <div class="act-list pd10-b pd10-t">
                        <div class="clarfix fs15"><span class="act-ac"><i class="iconfont zrz-icon-font-29"></i> 注册成功</span> <span class="fr"><time class="timeago gray" datetime="'.$val->msg_date.'" data-timeago="'.$val->msg_date.'" ref="timeAgo">'.$val->msg_date.'</time></span></div>
                        <div class="pos-r">
                            <h2 class="mar10-t mar10-b">成为本站会员，开启一段新的旅程！</h2>
                        </div>
                    </div>
                ';
            }

            //回复文章
            if($val->msg_type == 2){
                if($post_status == 'publish'){
                    $post_type = get_post_type($val->msg_key);
                    if($post_type == 'post'){
                        $title = '<i class="iconfont zrz-icon-font-reply"></i> 评论了文章</span>';
                    }elseif($post_type == 'pps'){
                        $title = '<i class="iconfont zrz-icon-font-iocnqipaotu"></i> 评论了'.sprintf( '%1$s：',zrz_custom_name('bubble_name')).'</span>';
                    }elseif($post_type == 'labs'){
                        $title = '<i class="iconfont zrz-icon-font-shiyan"></i> 评论了'.sprintf( '%1$s：',zrz_custom_name('labs_name')).'</span>';
                    }elseif($post_type == 'shop'){
                        $title = '<i class="iconfont zrz-icon-font-2"></i> 评论了商品</span>';
                    }else{
                        $title = '';
                    }
                    $html .= '
                        <div class="act-list pd10-b pd10-t">
                            <div class="clarfix fs15"><span class="act-ac">'.$title.'</span> <span class="fr"><time class="timeago gray" datetime="'.$val->msg_date.'" data-timeago="'.$val->msg_date.'" ref="timeAgo">'.$val->msg_date.'</time></span></div>
                            <div class="pos-r">
                                <h2 class="mar10-t mar10-b"><a href="'.get_permalink($val->msg_key).'">'.convert_smilies(get_the_title($val->msg_key)).'</a></h2>
                                '.($val->msg_value ? '<p class="des fs13 act-list-reply">'.convert_smilies(get_comment_excerpt($val->msg_value)).'</p>' : '').'
                            </div>
                        </div>
                    ';
                }
            }
        }

        if($return){
            return $html;
        }else{
            print json_encode(array('status'=>200,'msg'=>$html));
            unset($html);
            exit;
        }
    }

    if($return){
        return '';
    }else{
        print json_encode(array('status'=>401,'msg'=>__('没有动态','ziranzhi2')));
        exit;
    }
}

function zrz_post_can_edit( $query ) {

    if ( $query->is_author ){

        //用户页面显示草稿
        $user_id = get_query_var('author');
        $current_id = get_current_user_id();
        if($user_id == $current_id || current_user_can('delete_users')){
            $query->set( 'post_status', 'any' );
        }
        //用户页面研究所
        $type = get_query_var('zrz_user_page');
        if($type && $type == 'labs'){
            $query->set( 'post_status', 'any' );
            $query->set( 'post_type', 'labs' );
        }
    }

    return $query;
}
add_filter( 'pre_get_posts', 'zrz_post_can_edit' );

//关注
add_action('wp_ajax_zrz_follow','zrz_follow');
function zrz_follow(){
    //被关注者的ID
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

    if(!$user_id || !is_user_logged_in()){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    //当前用户ID
    $current_id = (int)get_current_user_id();

    //关注
    $follow = get_user_meta($current_id,'zrz_follow',true);
    $follow = is_array($follow) ? $follow : array();

    //粉丝
    $fans = get_user_meta($user_id,'zrz_followed',true);
    $fans = is_array($fans) ? $fans : array();

    //如果已经关注，则取消关注
    if(in_array($user_id,$follow)){
        foreach ($follow as $key => $val) {
            if($follow[$key] == $user_id){
                unset($follow[$key]);
            }
        }
        foreach ($fans as $key => $val) {
            if($fans[$key] == $current_id){
                unset($fans[$key]);
            }
        }
        $type = 'del';
    }else{
    //如果未关注，则添加关注
        $follow[] = $user_id;
        $fans[] = $current_id;
        $type = 'add';
    }

    $credit = (int)zrz_get_credit_settings('zrz_credit_follow');
    $c_credit = (int)zrz_get_credit_settings('zrz_credit_followed');

    if($type == 'add'){
        $init = new Zrz_Credit_Message($user_id,11);
		$add_msg = $init->add_message($current_id, $credit,0,0);

        //给自己增加积分
        $init = new Zrz_Credit_Message($current_id,42);
		$add_msg = $init->add_message($user_id, $c_credit,0,0);
    }else{
        $init = new Zrz_Credit_Message($user_id,15);
		$add_msg = $init->add_message($current_id, -$credit,0,0);

        //减去自己的积分
        $init = new Zrz_Credit_Message($current_id,43);
		$add_msg = $init->add_message($user_id, -$c_credit,0,0);
    }

    //更新数据
    update_user_meta($current_id,'zrz_follow',array_merge($follow));
    update_user_meta($user_id,'zrz_followed',array_merge($fans));

    print json_encode(array('status'=>200,'msg'=>$type));
    exit;
}

function zrz_is_followed($user_id){

    $current_user_id = get_current_user_id();
    $follow = get_user_meta($current_user_id,'zrz_follow',true);
    $follow = is_array($follow) ? $follow : array();

    if(in_array($user_id,$follow)){
        return true;
    }
    return false;
}

//允许搜索display_name
add_filter( 'user_search_columns',  'zrz_allow_search_disply_name');
function zrz_allow_search_disply_name( $search_columns ) {
    $search_columns[] = 'display_name';
    return $search_columns;
}

//用户搜索
add_action('wp_ajax_zrz_user_search','zrz_user_search');
function zrz_user_search(){
    $str = isset($_POST['str']) ? esc_attr($_POST['str']) : '';
    if($str){
        $users = new WP_User_Query( array(
            'search'         => '*'.$str.'*',
            'search_columns' => array(
                'display_name',
            ),
        ) );
        $users_found = $users->get_results();
        $i = 0;
        $users = array();
        foreach ($users_found as $user) {
            if($i >= 8) break;
            $users[] = array(
                'id'=>$user->ID,
                'name'=>$user->display_name,
                'avatar'=>get_avatar($user->ID,42),
                'des'=>get_the_author_meta( 'description',$user->ID )
            );
            $i++;
        }

        if(!empty($users)){
            print json_encode(array('status'=>200,'msg'=>$users));
            exit;
        }else{
            print json_encode(array('status'=>401,'msg'=>__('没有搜索到用户','ziranzhi2')));
            exit;
        }

    }
}

//发送私信
add_action('wp_ajax_zrz_send_msg','zrz_send_msg');
function zrz_send_msg(){
    $to = isset($_POST['to_id']) ? (int)$_POST['to_id'] : 0;
    $content = isset($_POST['content']) ? esc_attr($_POST['content']) : '';

    $current_user = wp_get_current_user();

    if(!zrz_current_user_can('message')){
        print json_encode(array('status'=>401,'msg'=>__('权限错误','ziranzhi2')));
        exit;
    }

    $current_user_id = $current_user->ID;

    check_ajax_referer($current_user_id, 'security' );

    //检查是不是垃圾
    $user_custom_data = get_user_meta($current_user_id,'zrz_user_custom_data',true);
    $url = preg_replace( '/^https?:\/\//', '', home_url() );
    $url = str_replace('.','_',$url);
    $arg = array(
        'comment_author' => $current_user->display_name,
        'comment_author_email' => isset($current_user->user_email) && !empty($current_user->user_email) ? $current_user->user_email : $url.$current_user_id.'@163.com',
        'comment_author_url' => '',
        'comment_content' => $content,
        'referrer'=>home_url('/directmessage')
    );
    $check = zrz_check_spam($arg);


    //不能给自己发消息
    if(!$to || !$content || $current_user_id == $to || $check){
        print json_encode(array('status'=>401,'msg'=>__('发送失败','ziranzhi2')));
        exit;
    }

    //给被私信人发送通知
    $init = new Zrz_Credit_Message($to,12);
    $add_msg = $init->add_message($current_user_id, 0,0,0);

    //添加消息到被私信人
    $init_msg = new Zrz_Credit_Message($to,13);
    $resout = $init_msg->add_message($current_user_id, 0,$current_user_id,$content);

    if($add_msg && $resout){
        print json_encode(array('status'=>200,'msg'=>convert_smilies(strip_tags($content)),'msgData'=>array(
            'msg_key'=>$current_user_id,
            'user_id'=>$to,
            'msg_date'=> current_time( 'mysql' ),
            'msg_value'=>wpautop(convert_smilies(strip_tags($content))),
            'msg_read'=>0
        )));
        exit;
    }
    print json_encode(array('status'=>401,'msg'=>__('发送失败','ziranzhi2')));
    exit;
}

//获取私信列表用户
add_action('wp_ajax_zrz_get_dmsg_user_list','zrz_get_dmsg_user_list');
function zrz_get_dmsg_user_list(){
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : false;
    $user_id = get_current_user_id();


    //页码验证
    if(!$paged){
        print json_encode(array('status'=>401,'msg'=>__('参数不全，请重试','ziranzhi2')));
        exit;
    }
    $number = (int)get_option('posts_per_page',true);
    $offset = ($paged-1)*$number;

    $count = $count_new = $pages = 0;

    $all_sql = "msg_type=13 AND user_id=".$user_id." AND (msg_read=1 OR msg_read=0)";
    $init = new Zrz_Credit_Message($user_id,13);

    $datas = $init->get_data($all_sql,"msg_key,user_id,msg_date,msg_value,msg_users,msg_read",true,$number,$offset);

    if($offset == 0){
		$count = $init->get_data($all_sql,"msg_key,msg_date","count");
        $count = count($count);
        $pages = ceil($count/$number);
    }

    //如果没有消息，返回空
    if(!$datas){
        print json_encode(array('status'=>401,'msg'=>__('没有消息','ziranzhi2')));
        exit;
    }

    $users = '';
    $post_id = 0;
    $resout = array();

    foreach( $datas as $data ){
        $user_data = new zrz_get_user_data($data->msg_key,50);
        //获取标题
        $resout[] = array(
            'name'=>get_the_author_meta('display_name',$data->msg_key),
            'avatar'=>$user_data->get_avatar(),
            'link'=>zrz_get_user_page_url($data->msg_key),
            'user_id'=>$data->msg_key == $user_id ? $data->user_id : $data->msg_key,
            'msg'=>convert_smilies(strip_tags($data->msg_value)),
            'date'=>$data->msg_date,
            'read'=>$data->msg_read,
            'color'=>!is_numeric($data->msg_key) ? zrz_get_avatar_background_by_id(rand(0,9)) : zrz_get_avatar_background_by_id($data->msg_key)
        );
    }

    print json_encode(array('status'=>200,'msg'=>$resout,'count'=>$count,'pages'=>$pages));
    exit;
}

//删除私信
add_action('wp_ajax_zrz_dmsg_delete','zrz_dmsg_delete');
function zrz_dmsg_delete(){
	$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    if(!$user_id){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_message';
    $current_id = get_current_user_id();
    $resout = $wpdb->update(
        $table_name,
        array('msg_read'=>5),
        array('msg_key'=>$user_id,'user_id'=>(int)$current_id)
    );

    if($resout){
        print json_encode(array('status'=>200,'msg'=>$resout));
        exit;
    }

    print json_encode(array('status'=>401,'msg'=>__('操作失败','ziranzhi2')));
    exit;
}

//获取与私信人的对话
add_action('wp_ajax_zrz_get_dmsg_data','zrz_get_dmsg_data');
function zrz_get_dmsg_data(){
    $user_id = isset($_POST['duser_id']) ? (int)$_POST['duser_id'] : 0;
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : false;

    if(!$user_id){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $cuser_id = get_current_user_id();

    $number = (int)get_option('posts_per_page',true);
    $offset = ($paged-1)*$number;

    $count = $count_new = $pages = 0;

    $all_sql = " ((user_id=".$cuser_id." AND msg_key=".$user_id.") OR (user_id=".$user_id." AND msg_key=".$cuser_id.")) AND msg_type=13";
    $init = new Zrz_Credit_Message($user_id,13);

    $datas = $init->get_data($all_sql,"msg_key,user_id,msg_date,msg_value,msg_read",false,$number,$offset);
    $resout = array();
    if($datas){
        foreach ($datas as $data) {
            $resout[] = array(
                'msg_key'=>$data->msg_key,
                'user_id'=>$data->user_id,
                'msg_date'=>$data->msg_date,
                'msg_value'=>wpautop(convert_smilies(strip_tags($data->msg_value))),
                'msg_read'=>$data->msg_read
            );
        }

        print json_encode(array('status'=>200,'msg'=>$resout));
        exit;
    }else{
        print json_encode(array('status'=>401,'msg'=>__('没有消息','ziranzhi2')));
        exit;
    }
}

//修改用户权限
add_action('wp_ajax_zrz_setting_save_lv','zrz_setting_save_lv');
function zrz_setting_save_lv(){
    if(current_user_can('delete_users')){
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        $lv = isset($_POST['lv']) ? $_POST['lv'] : 0;

        //获取当前的lv
        $c_lv = get_user_meta($user_id,'zrz_lv',true);

        if($user_id && $lv){
            update_user_meta($user_id,'zrz_lv',$lv);
			if(strpos($lv,'vip') !== false){
				//记录vip时间
				$lv_setting = zrz_get_lv_settings($lv);
				update_user_meta($user_id,'zrz_vip_time',array(
					'start'=>date('Y-m-d H:i:s',time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )),
                    'end'=>$lv_setting['time'] == 0 ? 0 : date("Y-m-d H:i:s", get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + strtotime('+'.$lv_setting['time'].' day')),
                    'lv'=>$lv,
                    'oldlv'=>$c_lv
				));
			}
            print json_encode(array('status'=>200,'msg'=>$lv_setting));
            exit;
        }
    }
    print json_encode(array('status'=>401,'msg'=>'修改失败'));
    exit;
}

//关进小黑屋
add_action('wp_ajax_zrz_setting_save_disabled','zrz_setting_save_disabled');
function zrz_setting_save_disabled(){
    if(current_user_can('delete_users')){
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $days = isset($_POST['days']) ? (int)$_POST['days'] : 0;
        $disabled = isset($_POST['disabled']) ? (int)$_POST['disabled'] : 0;
        if(!$disabled){
            delete_user_meta($user_id,'zrz_abled');
        }else{
            update_user_meta($user_id,'zrz_abled',array(
                'abled'=>$disabled,
                'end'=>$days == 0 ? 0 : date("Y-m-d H:i:s", get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + strtotime('+'.$days.' day')),
                'days'=>$days
                )
            );
        }
        
        print json_encode(array('status'=>401,'msg'=>'修改成功'));
        exit;
    }
    print json_encode(array('status'=>401,'msg'=>'修改失败'));
    exit;
}


//个人主页浏览次数
add_action('wp_ajax_zrz_author_page_views','zrz_author_page_views');
add_action('wp_ajax_nopriv_zrz_author_page_views','zrz_author_page_views');
function zrz_author_page_views(){
    $author_id = isset($_POST['uid']) ? $_POST['uid'] : 0;
    if($author_id){
        $views = get_user_meta($author_id,'views',true);
        update_user_meta($author_id,'views',$views+1);
    }
}

add_action('wp_ajax_zrz_mission','zrz_mission');
function zrz_mission(){
    $user_id = get_current_user_id();

    $mission_time = get_user_meta($user_id,'zrz_mission',true);

    $mission = zrz_get_credit_settings('zrz_credit_mission');
    $mission = explode("-", $mission);

    if(!isset($mission[0]) || !isset($mission[1]) || $mission_time > 0){
        print json_encode(array('status'=>401));
        exit;
    }

    if(isset($mission[0]) && isset($mission[1])){
        $mission = (int)rand($mission[0], $mission[1]);
    }else{
        $mission = 1;
    }

    $init_msg = new Zrz_Credit_Message($user_id,16);
    $resout = $init_msg->add_message($user_id, $mission,0,0);

    if($resout){
        update_user_meta($user_id,'zrz_mission',$mission);
        print json_encode(array('status'=>200,'msg'=>$mission));
        exit;
    }else{
        print json_encode(array('status'=>401));
        exit;
    }
}

//获取用户的关注和粉丝
add_action('wp_ajax_zrz_get_user_follow','zrz_get_user_follow');
add_action('wp_ajax_nopriv_zrz_get_user_follow','zrz_get_user_follow');
function zrz_get_user_follow(){
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    $paged = isset($_POST['paged']) ? $_POST['paged'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : 0;

    $follow = get_user_meta($user_id,$type,true);
    $follow = is_array($follow) ? array_merge($follow) : array();
    $follow_count = count($follow);
    $nub = get_option('posts_per_page');
    $ipages = ceil( $follow_count / $nub);
    $offset = ($paged-1)*$nub;

    $arr = array();
    for ($i=$offset; $i < $follow_count; $i++) {
        if($offset + $nub == $i) break;
        $user_data = get_userdata($follow[$i]);
        $fans = get_user_meta($follow[$i],'zrz_followed',true);
        $arr[] = array(
            'url' => zrz_get_user_page_url($follow[$i]),
            'avatar' => get_avatar($follow[$i],60),
            'des' => $user_data->description ? mb_strimwidth(strip_tags($user_data->description), 0, 100 ,"...") : '',
            'post_count' => count_user_posts($follow[$i], 'post' ),
            'topic_count' => count_user_posts($follow[$i], 'topic' ),
            'reply_count' => count_user_posts($follow[$i], 'reply' ),
            'name' => $user_data->display_name,
            'lv' => zrz_get_lv($follow[$i],'name'),
            'id' => $follow[$i],
            'follow'=>$type == 'zrz_follow' ? 1 : (in_array($user_id,$fans) ? 1 : 0)
        );
    }

    if(count($arr) > 0){
        print json_encode(array('status'=>200,'data'=>$arr,'pages'=>$ipages));
        exit;
    }else{
        print json_encode(array('status'=>401));
        exit;
    }
}

//获取用户的收藏列表
add_action('wp_ajax_zrz_get_user_collection','zrz_get_user_collection');
add_action('wp_ajax_nopriv_zrz_get_user_collection','zrz_get_user_collection');
function zrz_get_user_collection(){
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    $paged = isset($_POST['paged']) ? $_POST['paged'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : 0;

    $cols = get_user_meta($user_id,'zrz_user_favorites',true);
    $_cols = isset($cols[$type]) ? array_merge($cols[$type]) : array();
    $cols_count = count($_cols);
    $nub = get_option('posts_per_page');
    $ipages = ceil( $cols_count / $nub);
    $offset = ($paged-1)*$nub;

    $arr = array();
    for ($i=$offset; $i < $cols_count; $i++) {
        if($offset + $nub == $i) break;
        $comment_count = wp_count_comments( $_cols[$i] );
		$comment_count = $comment_count->approved;
        $arr[] = array(
            'title' => '<a href="'.get_permalink($_cols[$i]).'">'.get_the_title($_cols[$i]).'</a>',
            'love' => count(get_post_meta($_cols[$i], 'zrz_favorites', true )),
            'view'=>get_post_meta($_cols[$i],'views',true),
            'comment'=>$comment_count,
            'time'=>zrz_time_ago($_cols[$i])
        );
    }

    if(count($arr) > 0){
        print json_encode(array('status'=>200,'data'=>$arr,'pages'=>$ipages));
        exit;
    }else{
        print json_encode(array('status'=>401));
        exit;
    }
}

//用户页面钩子
function zrz_user_page_link_nav($type,$self,$user_id) {
	$html='<a class="'.($type === 'activities' || !$type ? 'picked' : '').'" href="'.zrz_get_user_page_url($user_id).'/activities'.'">动态</a>
    <a class="'.($type === 'posts' ? 'picked' : '').'" href="'.zrz_get_user_page_url($user_id).'/posts'.'">文章</a>';

    $name = zrz_custom_name('labs_name');
    $html .= zrz_get_display_settings('labs_show') ? '<a class="'.($type === 'labs' ? 'picked' : '').'" href="'.zrz_get_user_page_url($user_id).'/labs'.'">'.$name.'</a>' : '';

    $html .= class_exists( 'bbPress' ) ? '
    <a class="'.($type === 'topic' ? 'picked' : '').'" href="'.zrz_get_user_page_url($user_id).'/topic'.'">话题</a>
    <a class="'.($type === 'reply' ? 'picked' : '').'" href="'.zrz_get_user_page_url($user_id).'/reply'.'">跟帖</a>' : '';

    $html .= ($self && zrz_get_display_settings('shop_show')) ? '<a class="'.($type === 'orders' ? 'picked' : '').'" href="'.zrz_get_user_page_url($user_id).'/orders'.'">订单</a>' : '';
	return apply_filters('zrz_user_page_link_nav_filter', $html,$type,$self,$user_id);
}
