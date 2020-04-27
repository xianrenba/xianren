<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ziranzhi2
 */
// $address = get_user_meta(5,'zrz_user_custom_data',true);
// $address['address'] = array();
// update_user_meta(5,'zrz_user_custom_data',$address);

//邮箱设置debug
// wp_mail('','测试','测试');
// add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
// function onMailError( $wp_error ) {
//     echo "<pre>";
//     print_r($wp_error);
//     echo "</pre>";
// } 

//允许文章中使用的标签
function allow_data_event_content() {
    global $allowedposttags, $allowedtags;
    $newattribute = "data-video-url";
    $datavideothumb = "data-video-thumb";
    $datavideotitle = "data-video-title";

    $allowedposttags["div"][$newattribute] = true;
    $allowedtags["div"][$newattribute] = true;

    $allowedposttags["div"][$datavideothumb] = true;
    $allowedtags["div"][$datavideothumb] = true;

    $allowedposttags["div"][$datavideotitle] = true;
    $allowedtags["div"][$datavideotitle] = true;

    $allowedtags["a"]['class'] = true;
}
add_action( 'init', 'allow_data_event_content' );

 //自定义页面
function zrz_init_globals() {
    global $zrz_page_type;
    $zrz_page_type = apply_filters( 'zrz_page_type',array('directmessage','gold','hot','newtopic','notifications',
    'pass','write','signin','signup','sticky','top','maopao','links','add-labs','collections',
    'pay','cart','notify-pay','return-pay','new-topic','open','gold','wxpay','weixinpay-notify','vips','announcements','xunhu-success','xunhu-error','withdraw','task'
    ));
    //注册主题自定页面
    add_rewrite_tag('%zrz_page_type%','([^&]+)');
    //注册商品类型页面
    add_rewrite_tag('%zrz_shop_type%','([^&]+)');
    //私信页面用户
    add_rewrite_tag('%zrz_muser%','([^&]+)');
    //商品类型页面
    $zrz_shop_type = array('buy','exchange','lottery');
    foreach ($zrz_shop_type as $page) {
        add_rewrite_rule('^shop/'.$page.'/page/([0-9]+)/?','index.php?zrz_shop_type='.$page.'&paged=$matches[1]','top');
        add_rewrite_rule('^shop/'.$page.'/?','index.php?zrz_shop_type='.$page,'top');
    }
}
add_action( 'init','zrz_init_globals',10,0 );

 //注册路由规则
function zrz_rewrite_rules( $wp_rewrite ) {
    global $zrz_page_type;
    $new_rules = array();
    $new_rules['directmessage/([0-9]+)/?'] = 'index.php?zrz_page_type=directmessage&zrz_muser=$matches[1]';
    foreach ($zrz_page_type as $page) {
        $new_rules[$page] = 'index.php?zrz_page_type='.$page;
    }
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    return $wp_rewrite;
}
add_filter('generate_rewrite_rules','zrz_rewrite_rules');

 //加载自定义模板
 function zrz_template_redirects() {
    $type = get_query_var('zrz_page_type');
    if ($type) {
        get_template_part( 'template-bases/zrz',$type);
        exit;
    }

    //商品类型页面
    $shop = get_query_var('zrz_shop_type');
    if ($shop) {
        get_template_part( 'template-bases/zrz','shop-type');
        exit;
    }
}
add_filter( 'template_redirect', 'zrz_template_redirects' );

 //去掉反斜杠
add_filter('redirect_canonical', 'zrz_cancel_redirect_canonical');
function zrz_cancel_redirect_canonical($redirect_url){
    if( get_query_var('zrz_page_type') || get_query_var('zrz_shop_type')) return false;
    return $redirect_url;
}

 function zrz_custom_page_document_title($title_parts){
     $type = get_query_var('zrz_page_type') ? get_query_var('zrz_page_type') : get_query_var('zrz_shop_type');

     if ($type){
         $name = zrz_custom_name('labs_name');
         $zrz_page_name = apply_filters( 'zrz_page_name',array(
             'directmessage'=>__('私信','ziranzhi2'),
             'gold'=>__('财富明细','ziranzhi2'),
             'hot'=>__('活跃用户','ziranzhi2'),
             'newtopic'=>__('发起话题','ziranzhi2'),
             'notifications'=>__('消息','ziranzhi2'),
             'open'=>__('社交登录','ziranzhi2'),
             'pass'=>__('完善注册资料','ziranzhi2'),
             'write'=>__('写文章','ziranzhi2'),
             'signin'=>__('登录','ziranzhi2'),
             'signup'=>__('注册','ziranzhi2'),
             'sticky'=>__('精华文章','ziranzhi2'),
             'top'=>__('财富排行','ziranzhi2'),
             'maopao'=>__('冒泡','ziranzhi2'),
             'links'=>__('导航链接','ziranzhi2'),
             'add-labs'=>$name,
             'collections'=>__('专题中心','ziranzhi2'),
             'pay'=>__('支付','ziranzhi2'),
             'cart'=>__('我的购物车','ziranzhi2'),
             'notify-pay'=>__('支付结果','ziranzhi2'),
             'return-pay'=>__('支付结果','ziranzhi2'),
             'buy'=>__('出售商品','ziranzhi2'),
             'exchange'=>__('兑换商品','ziranzhi2'),
             'lottery'=>__('抽奖商品','ziranzhi2'),
             'new-topic'=>__('发起话题','ziranzhi2'),
             'wxpay'=>__('微信支付','ziranzhi2'),
             'vips'=>__('成为VIP会员','ziranzhi2'),
             'announcements'=>__('公告','ziranzhi2'),
             'xunhu-success'=>__('支付成功','ziranzhi2'),
             'xunhu-error'=>__('支付失败','ziranzhi2'),
             'withdraw'=>__('提现管理','ziranzhi2'),
             'task'=>__('我的任务','ziranzhi2'),
         ));
         $title_parts['tagline'] = $title_parts['title'];
         foreach ($zrz_page_name as $key => $value) {
             if ( $type ==  $key) {
                 $title_parts['title'] = $value;
                 break;
             }
         }
     }
     
    return $title_parts;
 }
 add_filter( 'document_title_parts', 'zrz_custom_page_document_title' );

 //自定义 body 标签 class
 add_filter('body_class', 'zrz_multisite_body_classes');
 function zrz_multisite_body_classes($classes) {
    $cp = zrz_is_page();
    if($cp){
        $classes[0] = 'zrz-custom-page '.$cp;
    }
    return $classes;
 }

 /*
 * 获取logo
 * 如果主题设置了 logo ，则显示 logo ，否者显示网站名称
 */
 function zrz_get_logo($type = ''){
 	$logo = zrz_get_theme_settings('logo');
 	return $logo ? '<img class="logo '.$type.'" src='.esc_url($logo).' alt="'.esc_html(get_bloginfo( 'name' )).'"/>' : '<span class="site-text-title">'.esc_html(get_bloginfo( 'name' )).'</span>';
 }

/*获取当前网址*/
 function zrz_get_curl(){
 	global $wp;
 	return home_url(add_query_arg(array(),$wp->request));
 }

 /*顶部表情*/
 function zrz_get_face_head(){
    $array = zrz_get_face_array();
 	$html = '<div class="fr mobile-hide">';
 	foreach ($array as $key => $val) {
 		$html .= '
 		   <a class="face pos-r '.(isset($val['class']) ? $val['class'] : '').'" href="#">
 		   		<img src="'.ZRZ_THEME_URI.'/images/face/'.$key.'.svg">
 		   </a>'.($key != 'wtf' ? ZRZ_THEME_DOT : '');
 	}
 	$html .= '</div>';
 	return $html;
 }


/*获取自定义页面的网址*/
function zrz_get_custom_page_link($name){

    if(get_option('permalink_structure')){
     return esc_url(home_url('/'.$name));
    }else{
     return esc_url(home_url('?zrz_page_type='.$name));
    }
}

/*自定义顶部搜索框*/
function zrz_search_form() {
 $form = '
    <form role="search" method="get" id="searchform" class="search-form-top" action="' . home_url( '/' ) . '" >
        <input type="text" value="' . esc_attr(get_search_query()) . '" name="s" id="ss" placeholder="搜索"/><button class="text"><i class="iconfont zrz-icon-font-sousuo"></i></button>
    </form>';
 return $form;
}

function zrz_search_form_side( $form ) {
    $form = '<form role="search" method="get" id="searchform2" class="search-form" action="' . home_url( '/' ) . '" >
    <div class="search-input pos-r">
    <input type="text" class="pd5" value="' . get_search_query() . '" name="s" id="s" placeholder="'.__('搜索','ziranzhi2').'"/><input
    type="submit" id="searchsubmit" class="mouh pos-a" value="'. __( '搜索','ziranzhi2' ) .'" />
    </div>
    </form>';
    return $form;
}
add_filter( 'get_search_form', 'zrz_search_form_side' );
/**
 * 时间 timeago
 * Prints HTML with meta information for the current post-date/time and author.
*/
function zrz_time_ago($post_id = 0) {
	$time_string = '<time class="timeago" datetime="%1$s" data-timeago="%2$s" ref="timeAgo">%2$s</time>';

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ,$post_id) ),
		esc_html( get_the_date('Y-n-j G:i:s',$post_id) )
	);

    return $time_string;
}

/*
* 获取文章摘要
* $content 需要截断的字符串 (string)
* $size 截断的长度 (int)
*/
function zrz_get_content_ex($content = '',$size = 130){

    if(!$content){
        global $post;
        $excerpt = $post->post_excerpt;
        $content = $excerpt ? $excerpt : $post->post_content;
    }

    return mb_strimwidth(zrz_clear_code(strip_tags(strip_shortcodes($content))), 0, $size,'...');

}

/*
* 清除字符串中的标签
*/
function zrz_clear_code($string){
    $string = trim($string);
    if(!$string)
        return '';
    $string = preg_replace('/[#][1-9]\d*/','',$string);//清除图片索引（#n）
    $string = str_replace("\r\n",' ',$string);//清除换行符
    $string = str_replace("\n",' ',$string);//清除换行符
    $string = str_replace("\t",' ',$string);//清除制表符
    $pattern = array("/> *([^ ]*) *</","/[\s]+/","/<!--[^!]*-->/","/\" /","/ \"/","'/\*[^*]*\*/'","/\[(.*)\]/");
    $replace = array(">\\1<"," ","","\"","\"","","");
    return preg_replace($pattern,$replace,$string);
}

/*移动端排除平板*/
function zrz_wp_is_mobile() {
    static $is_mobile;

    if ( isset($is_mobile) )
        return $is_mobile;

    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        $is_mobile = false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false ) {
            $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
            $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
        $is_mobile = false;
    } else {
        $is_mobile = false;
    }

    return $is_mobile;
}

/*cookie 操作*/
//设置
function zrz_setcookie($key,$val,$time = 86400) {
    setcookie( $key, $val, time() + $time, COOKIEPATH, COOKIE_DOMAIN );
}
//获取
function zrz_getcookie($key) {
    return isset( $_COOKIE[$key] ) ? $_COOKIE[$key] : false;
}
//销毁
function zrz_deletecookie($key) {
    setcookie( $key, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
}

/*做点有趣的事情*/
add_action('wp_ajax_nopriv_zrz_hello_get_lyric', 'zrz_hello_get_lyric');
add_action('wp_ajax_zrz_hello_get_lyric', 'zrz_hello_get_lyric');
function zrz_hello_get_lyric($echo = false) {
	$lyrics = zrz_get_display_settings('hello');

	// Here we split it into lines
	$lyrics = explode( "\n", $lyrics );

	// And then randomly choose a line
	$lyrics = wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
    if($echo){
        return $lyrics;
    }else{
        print json_encode(array('status'=>200,'msg'=>$lyrics));
        exit;
    }
}
//积分换算成金币
function zrz_coin($user_id,$type='0',$custom_credit = ''){
    if(!$custom_credit){
        $credit = get_user_meta( $user_id, 'zrz_credit_total', true );
    }else{
        $credit = $custom_credit;
    }

	if($credit == 0){
		update_user_meta($user_id,'zrz_credit_total',1);
		$credit = 1;
	}

	if($type === 'nub'){
		return $credit;
	}
    if(!zrz_get_credit_settings('zrz_credit_display')){
        return '<span class="coin l1 rmb">'.$credit.'</span>';
    }
	$tong_n=(string)substr($credit,-2);
	$yin_f = substr($credit,-4);
	$yin_n=(string)substr($yin_f,0,-2);
	$jin_r=substr($credit,0,-4);
	$html ='';
	if($tong_n != '00'){
		$tong = '<span class="tong">'.ltrim($tong_n, "0").'<b></b></span>';
	}else{
		$tong = '';
	};
	if($yin_n != '00' && !empty($yin_n)){
		$yin = '<span class="yin">'.ltrim($yin_n, "0").'<b></b></span>';
	}else{
		$yin = '';
	};
	if(!empty($jin_r)){
		$jin = '<span class="jin">'.$jin_r.'<b></b></span>';
	}else{
		$jin = '';
	}
	$html .= '<div class="coin fs12 l1">'.$jin.$yin.$tong.($custom_credit ? '<span class="hide" data-j="'.($jin_r ? $jin_r : 0).'" data-y="'.($yin_n ? $yin_n : 0).'" data-t="'.($tong_n ? $tong_n : 0).'" ref="credit"></span>' : '').'</div>';
	return $html;
}

//移除存档页面标题中的多余字
add_filter( 'get_the_archive_title', 'zrz_remove_archive_title');
function zrz_remove_archive_title($title) {
    $title = single_cat_title( '', false );
    return $title;
}

//邮件发送函数
function mail_smtp($phpmailer){
    if(zrz_get_mail_settings('open') == 0) return;
    $phpmailer->IsSMTP();
    $phpmailer->isHTML(true);
    $phpmailer->SMTPAuth = zrz_get_mail_settings('SMTPAuth'); //SMTP认证（true/flase）
    $phpmailer->FromName = zrz_get_mail_settings('FromName'); //发信人昵称
    $phpmailer->From = zrz_get_mail_settings('From'); //显示的发信邮箱
    $phpmailer->Host = zrz_get_mail_settings('Host'); //邮箱的SMTP服务器地址
    $phpmailer->Port = (int)zrz_get_mail_settings('Port'); //SMTP服务器端口
    $phpmailer->SMTPSecure = zrz_get_mail_settings('SMTPSecure'); //SMTP加密方式tls/ssl/no(不填)
    $phpmailer->Username = zrz_get_mail_settings('Username'); //邮箱地址
    $phpmailer->Password = zrz_get_mail_settings('Password'); //邮箱密码
}
add_action('phpmailer_init','mail_smtp');

//侧边栏class
function zrz_sidebar_hide(){
    return (zrz_get_theme_style() === 'pinterest' && !is_singular() && !zrz_is_page(false,'write') && !zrz_is_page(false,'add-labs') && !is_post_type_archive('shop')) ? 'hide' : '';
}

//当前是否是自定义页面
function zrz_is_page($all = true,$type = ''){
    global $wp_query;
    $page = $wp_query->get('zrz_page_type');
    $page = $page ? $page : $wp_query->get('zrz_shop_type');
    if($all){
        return $page;
    }else{
        return $page === $type;
    }
    return false;
}

//隐藏SEO相关的自定义栏目
add_filter('is_protected_meta', 'zrz_is_protected_meta_filter', 10, 2);
function zrz_is_protected_meta_filter($protected, $meta_key) {
    if($meta_key === 'zrz_seo_description' || $meta_key === 'zrz_seo_keywords'){
        return true;
    }else{
        return $protected;
    }
}

function number($str){
    return preg_replace('/\D/s', '', $str);
}

//从数组中删除某个元素
function zrz_array_unset($arr, $value){
    if(!is_array($arr)){
        return $arr;
    }
    foreach($arr as $k=>$v){
        if($v == $value){
            unset($arr[$k]);
        }
    }
    return $arr;
}

remove_filter('term_description','wpautop');

//首页排除某些分类
function zrz_exclude_category_home( $query ) {
	$cats = zrz_get_theme_settings('post_exclude');
    $_cats = array();
    foreach ($cats as $val) {
        $_cats[] = -$val;
    }
	if ( $query->is_home() && $query->is_main_query() ) {
		//排除某些分类
		$query->set( 'cat', $_cats);
        //$query->set( 'post_type', array('post','bubble'));
	}
	return $query;
}
add_filter( 'pre_get_posts', 'zrz_exclude_category_home' );

//移除多余meta
function zrz_clean_theme_meta(){
    if(zrz_get_theme_settings('clear_head')){
        remove_action( 'wp_head', 'print_emoji_detection_script', 7, 1);
        remove_action( 'wp_print_styles', 'print_emoji_styles', 10, 1);
        remove_action( 'wp_head', 'rsd_link', 10, 1);
        remove_action( 'wp_head', 'wp_generator', 10, 1);
        remove_action( 'wp_head', 'feed_links', 2, 1);
        remove_action( 'wp_head', 'feed_links_extra', 3, 1);
        remove_action( 'wp_head', 'index_rel_link', 10, 1);
        remove_action( 'wp_head', 'wlwmanifest_link', 10, 1);
        remove_action( 'wp_head', 'start_post_rel_link', 10, 1);
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0);
        remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10, 0);
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10, 1);
        remove_action( 'wp_head', 'rel_canonical', 10, 0);
    }
}
add_action( 'after_setup_theme', 'zrz_clean_theme_meta' ); //清除wp_head带入的meta标签
function zrz_deregister_embed_script(){
  wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'zrz_deregister_embed_script' ); //清除wp_footer带入的embed.min.js

function zrz_get_html_code($str){
    return str_replace('\\','',$str);
}

//距离今天，几天前
function zrz_time_days($startdate){
    if(!$startdate) return;

    $enddate = current_time( 'mysql' );

    return floor((strtotime($enddate)-strtotime($startdate))/86400);
}

//用户名星号处理
function zrz_str_encryption($str){
    if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
        //按照中文字符计算长度
        $len = mb_strlen($str, 'UTF-8');
        //echo '中文';
        if($len >= 3){
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = mb_substr($str, 0, 1, 'UTF-8') . '***' . mb_substr($str, -1, 1, 'UTF-8');
        } elseif($len == 2) {
            //两个字符
            $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
        }
    } else {
        //按照英文字串计算长度
        $len = strlen($str);
        //echo 'English';
        if($len >= 3) {
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = substr($str, 0, 1) . '***' . substr($str, -1);
        } elseif($len == 2) {
            //两个字符
            $str = substr($str2, 0, 1) . '*';
        }
    }
    return $str;
}

//字符串分割无乱码
function zrz_mb_chunk_split($string, $length, $end, $once = false){
    //$string = iconv('gb2312', 'utf-8//ignore', $string);
    $array = array();
    $strlen = mb_strlen($string);
    while($strlen){
        $array[] = mb_substr($string, 0, $length, "utf-8");
        if($once)
            return $array[0] . $end;
        $string = mb_substr($string, $length, $strlen, "utf-8");
        $strlen = mb_strlen($string);
    }
    return implode($end, $array);
}

function zrz_is_custom_tax($type){

    if($type == 'shop'){
        if(!zrz_get_display_settings('shop_show')) return false;
        return is_tax('shoptype') || zrz_is_page(false,'cart') || is_post_type_archive('shop') || is_singular('shop') || zrz_is_page(false,'buy') || zrz_is_page(false,'exchange') || zrz_is_page(false,'lottery');
    }

    if($type == 'labs'){
        if(!zrz_get_display_settings('labs_show')) return false;
        return zrz_is_page(false,'add-labs') || is_post_type_archive('labs') || is_singular('labs');
    }

    if($type == 'collection'){
        return is_tax('collection') || zrz_is_page(false,'collections');
    }

    if($type == 'bubble'){
        if(!zrz_get_display_settings('bubble_show')) return false;
        return is_post_type_archive('pps') || is_tax('mp') || is_singular('pps');
    }

    if($type == 'bbpress'){
        if(class_exists( 'bbPress' )){
            return is_bbpress() || zrz_is_page(false,'new-topic');
        }
        return false;
    }

    return false;
}

function zrz_display_menu(){
    if(!zrz_is_page(false,'write') && !zrz_is_custom_tax('labs') && !is_author() && !zrz_is_custom_tax('bubble') && !zrz_is_page(false,'links') && !zrz_is_page(false,'gold') && !zrz_is_page(false,'directmessage') && !zrz_is_page(false,'notifications') && !zrz_is_page(false,'announcements')){
        return true;
    }
    return false;
}

/*社交登录*/
function weibo_oauth_url(){
    $url = 'https://api.weibo.com/oauth2/authorize?client_id=' .zrz_get_social_settings('open_weibo_key'). '&response_type=code&redirect_uri=' . urlencode (home_url('/open?open_type=weibo&url='.zrz_get_curl()));
    return $url;
}

//社交登陆
function qq_oauth_url(){
    $url = "https://graph.qq.com/oauth2.0/authorize?client_id=" .zrz_get_social_settings('open_qq_key'). "&state=".md5 ( uniqid ( rand (), true ) )."&response_type=code&redirect_uri=" . urlencode (home_url('/open?open_type=qq&url='.zrz_get_curl()));
    return $url;
}

//微信登录
function weixin_oauth_url(){
    if(zrz_is_weixin()){
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='. zrz_get_social_settings('open_weixin_gz_key') .'&redirect_uri='. urlencode (home_url('/open?open_type=weixin&url='.zrz_get_curl())).'&response_type=code&scope=snsapi_userinfo#wechat_redirect';
    }

    return 'https://open.weixin.qq.com/connect/qrconnect?appid='. zrz_get_social_settings('open_weixin_key') .'&redirect_uri='. urlencode (home_url('/open?open_type=weixin&url='.zrz_get_curl())).'&response_type=code&scope=snsapi_login';

}

function zrz_change_user_rol() {
    $user_id = get_current_user_id();
    $user_meta = get_userdata($user_id);
    $user_roles = isset($user_meta->roles) && is_array($user_roles) ? $user_meta->roles : array();
    if (in_array("subscriber", $user_roles)){
        wp_update_user( array( 'ID' => $user_id, 'role' => 'contributor' ) );
    }
}
add_action('wp_login', 'zrz_change_user_rol', 10, 2);

function zrz_sanitize_pagination($content) {
    // Remove h2 tag
    $content = preg_replace('#<h2.*?>(.*?)<\/h2>#si', '', $content);
    return $content;
}
add_action('navigation_markup_template', 'zrz_sanitize_pagination');

//判断是不是微信浏览器
function zrz_is_weixin(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}

//获取URL的扩展名
function zrz_getExt($url){
    //解析url
    $arr=parse_url($url);
    //获取路径中的文件名
    $filename=basename($arr['path']);
    //使用一个字符串分割另一个字符串
    $ext=explode('.', $filename);
    return $ext[count($ext)-1];
}

//原生分页
function zrz_pagenavi($range = 5,$page_data = array('pages'=>0,'paged'=>0)){
    global $paged, $wp_query;

    if($page_data['pages']){
        $max_page = $page_data['pages'];
    }else{
        $max_page = $wp_query->max_num_pages;
    }
	if($page_data['paged']){
        $paged = $page_data['paged'];
    }

    $html = '';
	if($max_page > 1){
        $html .= '<div class="btn-group fl">';
        if(!$paged){
            $paged = 1;
        }
        if($max_page > $range){
    		if($paged < $range){
                for($i = 1; $i <= ($range + 1); $i++){
                    $html .= '<a class="button empty '.($i==$paged ? 'selected disabled' : '').'" href="'. get_pagenum_link($i) .'">'.$i.'</a>';
                }
            }elseif($paged >= ($max_page - ceil(($range/2)))){
        		for($i = $max_page - $range; $i <= $max_page; $i++){
                    $html .= '<a class="button empty '.($i==$paged ? 'selected disabled' : '').'" href="'. get_pagenum_link($i) .'">'.$i.'</a>';
                }
            }elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
    		    for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){
                    $html .= '<a class="button empty '.($i==$paged ? 'selected disabled' : '').'" href="'. get_pagenum_link($i) .'">'.$i.'</a>';
                }
            }
        }else{
            for($i = 1; $i <= $max_page; $i++){
                $html .= '<a class="button empty '.($i==$paged ? 'selected disabled' : '').'" href="'. get_pagenum_link($i) .'">'.$i.'</a>';
            }
        }

        if($max_page > $range){
            $html .= '<button class="empty bordernone">...</button>';
            $html .= '<a class="button empty" href="' . get_pagenum_link($max_page) . '" class="extend" title="跳转到最后一页">'.$max_page.'</a>';
        }
        $html .= '</div>';
        $html .= '<div class="btn-pager fr fs13">';
        $pre = get_pagenum_link($paged-1);
    	$html .= $paged-1 > 0 ? '<button class="empty button"><a href="'.$pre.'">❮</a></button>' : '<button class="empty disabled button">❮</button>';
        $next = get_pagenum_link($paged+1);
        if(zrz_wp_is_mobile()){
            $html .= '<div class="pager-center">'.$paged.'/'.$max_page.'</div>';
        }
    	$html .= $paged+1 < $max_page ? '<button class="empty navbtr button"><a href="'.$next.'">❯</a></button>' : '<button class="empty disabled navbtr button">❯</button>';
        $html .= '</div>';

    }

    if($max_page > 1){
        return '<div class="zrz-pager clearfix pd10 pos-r box custom-search">'.$html.'</div>';
    }else {
        return '';
    }

}

//根据ID给用户的默认头像分配背景颜色
function zrz_get_avatar_background_by_id($user_id){
    $last_nub = substr($user_id,-1);
    switch ($last_nub) {
        case 0:
            $color = '#61c3f9';
            break;
        case 1:
            $color = '#02c793';
            break;
        case 2:
            $color = '#005b00';
            break;
        case 3:
            $color = '#9c3202';
            break;
        case 4:
            $color = '#00c55d';
            break;
        case 5:
            $color = '#f6f601';
            break;
        case 6:
            $color = '#f755f5';
            break;
        case 7:
            $color = '#c80001';
            break;
        case 8:
            $color = '#c22c2a';
            break;
        case 9:
            $color = '#cc019b';
            break;
        default:
            $color = '#000000';
            break;
    }
    return $color;
}

//获取访问者的IP
function zrz_get_client_ip(){
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {

        $ip = getenv('HTTP_CLIENT_IP');

    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {

        $ip = getenv('HTTP_X_FORWARDED_FOR');

    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {

        $ip = getenv('REMOTE_ADDR');

    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {

        $ip = $_SERVER['REMOTE_ADDR'];

    }

    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

function trim_value($value) {
    return str_replace(' ','',$value);
}