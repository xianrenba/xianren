<?php
//欲思@添加钮Download
function DownloadUrl($atts, $content = null) {
	extract(shortcode_atts(array("href" => 'http://'), $atts));
	return '<a class="dl" href="'.$href.'" target="_blank" rel="nofollow"><i class="fa fa-cloud-download"></i>'.$content.'</a>';
	}
add_shortcode("dl", "DownloadUrl");
//欲思@添加钮git
function GithubUrl($atts, $content=null) {
   extract(shortcode_atts(array("href" => 'http://'), $atts));
	return '<a class="dl" href="'.$href.'" target="_blank" rel="nofollow"><i class="fa fa-github-alt"></i>'.$content.'</a>';
}
add_shortcode('gt' , 'GithubUrl' );
//欲思@添加钮Demo
function DemoUrl($atts, $content=null) {
   extract(shortcode_atts(array("href" => 'http://'), $atts));
	return '<a class="dl" href="'.$href.'" target="_blank" rel="nofollow"><i class="fa fa-external-link"></i>'.$content.'</a>';
}
add_shortcode('dm' , 'DemoUrl' );

//折叠板
function xcollapse($atts, $content = null) {
    extract(shortcode_atts(array("title" => "") , $atts));
    return '<div class="collapse-wrap"><div class="xControl  collapseButton"><i class="fa fa-angle-right"></i>'.$title.'</div><div class="xContent" >'.do_shortcode($content).'</div></div>';
}
add_shortcode('collapse', 'xcollapse');

// Pproduct shortcode
function product_shortcode($atts, $content = null){
	extract(shortcode_atts(array('size'=>'lg','id'=>''),$atts));
	if(!empty($id)) {
		$currency = get_post_meta($id,'pay_currency',true);
		if($currency==1) $preice = '￥'; else $preice = '<i class="fa fa-gift">&nbsp;</i>';
        $money = um_get_product_price($id);
        $amount = get_post_meta($id,'product_amount',true) ? (int)get_post_meta($id,'product_amount',true):0; //echo $amount; 
        $sales = get_post_meta($id,'product_sales',true) ? (int)get_post_meta($id,'product_sales',true):0; //echo $sales; 
		$href = get_permalink($id);
		$title = get_post_field('post_title',$id);
		$content = !empty($content) ? $content : '立即购买';
	    return '<div class="embed-product">'._get_post_thumbnail(100, 100, 'post-thumbnail','product-img').'<div class="product-info"><h4><a href="'.$href.'">'.$title.'</a></h4><div class="price">'.$preice.'<span>'.$money.'</span></div><div class="post-meta">&nbsp;数量：<span>'.$amount.'</span>&nbsp;&nbsp;&nbsp;销量：<span>'.$sales.'</span></div><a class="btn btn-success btn-buy" href="'.$href.'"><i class="fa fa-shopping-cart"></i>'.$content.'</a></div></div>';	
	}else{
		return '<button type="button" class="btn btn-product btn-'.$size.'">'.$content.'</button>';
	}
}
add_shortcode('product', 'product_shortcode');


/*短代码信息框 开始*/
/*绿色提醒框*/
function toz($atts, $content = null) {
    return '<div id="sc_notice">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_notice', 'toz');
/*红色提醒框*/
function toa($atts, $content = null) {
    return '<div id="sc_error">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_error', 'toa');
/*黄色提醒框*/
function toc($atts, $content = null) {
    return '<div id="sc_warn">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_warn', 'toc');
/*灰色提醒框*/
function tob($atts, $content = null) {
    return '<div id="sc_tips">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_tips', 'tob');
/*蓝色提醒框*/
function tod($atts, $content = null) {
    return '<div id="sc_blue">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_blue', 'tod');
/*蓝边文本框*/
function toe($atts, $content = null) {
    return '<div  class="sc_act">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_act', 'toe');
/*橙色文本框*/
function tof($atts, $content = null) {
    return '<div id="sc_organge">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_organge', 'tof');
/*青色文本框*/
function tog($atts, $content = null) {
    return '<div id="sc_qing">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_qing', 'tog');
/*粉色文本框*/
function toh($atts, $content = null) {
    return '<div id="sc_pink">' . do_shortcode($content) . '</div>';
}
add_shortcode('v_pink', 'toh');

/*添加音乐按钮*/
function tol($atts, $content = null) {
    return '<audio style="width:100%;max-height:40px;" src="' . $content . '" controls preload loop>您的浏览器不支持HTML5的 audio 标签，无法为您播放！</audio>';
}
add_shortcode('music', 'tol');
/*灵魂按钮*/
function tom($atts, $content = null) {
    extract(shortcode_atts(array(
        "href" => 'http://'
    ) , $atts));
    return '<a class="lhb" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('lhb', 'tom');
/*添加视频按钮*/
function too($atts, $content = null) {
    return '<video style="width:100%;" src="' . $content . '" controls preload >您的浏览器不支持HTML5的 video 标签，无法为您播放！</video>';
}
add_shortcode('video', 'too');

//简单的下载面板
function xdltable($atts, $content = null) {
    extract(shortcode_atts(array("file" => "","size" => "" ) , $atts));
    return '<table class="dltable"><tbody><tr><td style="width: 20px;"rowspan="3">文件下载</td><td><i class="fa fa-list-alt"></i>&nbsp;&nbsp;文件名称：' . $file . '</td><td><i class="fa fa-th-large"></i>&nbsp;&nbsp;文件大小：' . $size . '</td></tr><tr><td colspan="2"><i class="fa fa-volume-up"></i>&nbsp;&nbsp;下载声明：'._hui("git_dltable_b").'</td></tr><tr><td colspan="2"><i class="fa fa-download"></i>&nbsp;&nbsp;下载地址：' . $content . '</td></tr></tbody></table>';
}
add_shortcode('dltable', 'xdltable');

//添加编辑器快捷按钮
add_action('admin_print_scripts', 'my_quicktags');
function my_quicktags() {
    wp_enqueue_script('my_quicktags', get_stylesheet_directory_uri() . '/js/my_quicktags.js', array(
        'quicktags'
    ));
};

//使用短代码添加回复后可见内容开始
function reply_to_read($atts, $content = null) {
    extract(shortcode_atts(array(
        "notice" => '<div id="sc_warningbox">注意：本段内容须成功“<a href="' . get_permalink() . '#respond" title="回复本文">回复本文</a>”后“<a href="javascript:window.location.reload();" title="刷新本页">刷新本页</a>”方可查看！</div>'
    ) , $atts));
    $email = null;
    $user_ID = (int)wp_get_current_user()->ID;
    if ($user_ID > 0) {
        $email = get_userdata($user_ID)->user_email;
        //对博主直接显示内容
        $admin_email = get_bloginfo('admin_email');
        if ($email == $admin_email) {
            return $content;
        }
    } else if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
        $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
    } else {
        return $notice;
    }
    if (empty($email)) {
        return $notice;
    }
    global $wpdb;
    $post_id = get_the_ID();
    $query = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
    if ($wpdb->get_results($query)) {
        return do_shortcode($content);
    } else {
        return $notice;
    }
}
add_shortcode('reply', 'reply_to_read');

// vip权限查看全部内容
add_action('the_content','vip_content_show');
function vip_content_show($content){
	global $post;
	$type = get_post_meta($post->ID,'post_vip_type',true);
	$post_auth = get_post_meta($post->ID,'post_vip_auth',true);
	if($type == '1'){
		if (intval($post_auth) == 1) {
        	$vip_infotext= '月费会员及以上权限可查看，';
        }elseif (intval($post_auth) == 2) {
        	$vip_infotext= '季费会员及以上权限可查看，';
        }elseif (intval($post_auth) == 3) {
        	$vip_infotext= '年费会员及以上权限可查看，';
        }elseif (intval($post_auth) == 4) {
        	$vip_infotext= '终生会员可查看';
        }

		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$vip_type = getUserMemberType($user_id);
		if(is_singular() && $post_auth <= $vip_type){
			return do_shortcode($content);
		}else{
			if (!is_user_logged_in()) {
				$wpayc = '<div id="sc_warningbox">注意：该内容仅，'. $vip_infotext .'<a href="javascript:;" class="user-reg" data-sign="0">请登录</a></div>';
			}else{
				$wpayc = '<div id="sc_warningbox">注意：该内容仅，'. $vip_infotext .'<a href="'.add_query_arg('tab', 'membership', esc_url( get_author_posts_url( wp_get_current_user()->ID ) )).'" title="">开通会员</a></div>';
			}				
			$content = $wpayc;
		}
	}
	return $content;
}

// vip权限查看部分内容
add_shortcode('vipshow','vipshow_shortcode');
function vipshow_shortcode($atts, $content){ 
	$atts = shortcode_atts( array(
        'id' => 0
    ), $atts, 'vipshow' );
	global $post,$wpdb;
	$post_id = $post->ID;
	if($atts['id']){
		$post_id = $atts['id'];
	}

	$type = get_post_meta($post_id,'post_vip_type',true);
	$post_auth = get_post_meta($post_id,'post_vip_auth',true);	
	if($type == '2'){
		if (intval($post_auth) == 1) {
        	$vip_infotext= '[月费会员及以上权限] 可查看，';
        }elseif (intval($post_auth) == 2) {
        	$vip_infotext= '[季费会员及以上权限] 可查看，';
        }elseif (intval($post_auth) == 3) {
        	$vip_infotext= '[年费会员及以上权限] 可查看，';
        }elseif (intval($post_auth) == 4) {
        	$vip_infotext= '[终生会员可查看] ';
        }

		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$vip_type = getUserMemberType($user_id);
		if(is_singular() && $post_auth <= $vip_type){
			return '<div class="vipshow">'. do_shortcode($content) .'</div>';
		}else{
			if (!is_user_logged_in()) {
				$wpayc = '<div id="sc_warningbox">注意：该内容仅 '. $vip_infotext .'<a href="javascript:;" class="user-reg" data-sign="0">请登录</a></div>';
			}else{
				$wpayc = '<div id="sc_warningbox">注意：该内容仅 '. $vip_infotext .'<a href="'.add_query_arg('tab', 'membership', esc_url( get_author_posts_url( wp_get_current_user()->ID ) )).'" title="">开通会员</a></div>';
			}
				
			return $wpayc;
		}
	}

}

function _is_administrator(){
	$users = wp_get_current_user();
	if (!empty($users->roles) && in_array('administrator', $users->roles)) {
		return 1;
	} else {
		return 0;
	}
}

//购买产品可见内容
function salong_buy_content($atts, $content = null) {
    global $current_user;
    extract(shortcode_atts(array('product_id'  => ''),$atts));
    $buy = false;
    $product_html = '';
    $product_herf = '';
    $product_arr = explode(',', $product_id);
    if($product_arr){
    	$i = 0;
    	foreach ($product_arr as $product){
    		$i++;
    		if(get_specified_user_and_product_orders($product, $current_user->ID)){
    			$buy = true; continue;
    		}
    		$product_html .= '<span style="color:#F64540;">【'.get_the_title($product).'】</span>';
    		$product_herf .= '&nbsp;&nbsp;<a href="'.get_permalink($product).'" target="_blank" title="前往购买">前往购买</a>';
    		if($i>=1 && $i!=count($product_arr) ){ $product_html .= '或';}
    	}
    }
    
    
    if ( $buy || _is_administrator() || empty($product_id) ) {
        $items .= do_shortcode($content);
    }else{
        if ( is_user_logged_in() ) {
            $items .= sprintf('<div id="sc_warningbox">'.__('当前内容只有购买了 %s 产品的用户才能查看，点击%s。','qux').'</div>',$product_html,$product_herf);
        }else{
            $items .= sprintf('<div id="sc_warningbox">'.__('当前内容只有购买了 %s 产品的用户才能查看，点击%s，如果您已经购买，<a href="javascript:;" class="user-reg" data-sign="0">请登录</a>。','qux').'</div>',$product_html,$product_herf);
        }
    }
    return $items;
}
add_shortcode('buy', 'salong_buy_content');


//取当前主题下img\smilies\下表情图片路径
function custom_gitsmilie_src($old, $img) {
    return get_stylesheet_directory_uri() . '/img/smilies/' . $img;
}

//移除emoji
function theme_init_smilies(){
 global $wpsmiliestrans;
 $wpsmiliestrans = array(
 ':mrgreen:' => 'icon_mrgreen.gif',
 ':neutral:' => 'icon_neutral.gif',
 ':twisted:' => 'icon_twisted.gif',
 ':arrow:' => 'icon_arrow.gif',
 ':shock:' => 'icon_eek.gif',
 ':smile:' => 'icon_smile.gif',
 ':???:' => 'icon_confused.gif',
 ':cool:' => 'icon_cool.gif',
 ':evil:' => 'icon_evil.gif',
 ':grin:' => 'icon_biggrin.gif',
 ':idea:' => 'icon_idea.gif',
 ':oops:' => 'icon_redface.gif',
 ':razz:' => 'icon_razz.gif',
 ':roll:' => 'icon_rolleyes.gif',
 ':wink:' => 'icon_wink.gif',
 ':cry:' => 'icon_cry.gif',
 ':eek:' => 'icon_surprised.gif',
 ':lol:' => 'icon_lol.gif',
 ':mad:' => 'icon_mad.gif',
 ':sad:' => 'icon_sad.gif',
 '8-)' => 'icon_cool.gif',
 '8-O' => 'icon_eek.gif',
 ':-(' => 'icon_sad.gif',
 ':-)' => 'icon_smile.gif',
 ':-?' => 'icon_confused.gif',
 ':-D' => 'icon_biggrin.gif',
 ':-P' => 'icon_razz.gif',
 ':-o' => 'icon_surprised.gif',
 ':-x' => 'icon_mad.gif',
 ':-|' => 'icon_neutral.gif',
 ';-)' => 'icon_wink.gif',
 '8O' => 'icon_eek.gif',
 ':(' => 'icon_sad.gif',
 ':)' => 'icon_smile.gif',
 ':?' => 'icon_confused.gif',
 ':D' => 'icon_biggrin.gif',
 ':P' => 'icon_razz.gif',
 ':o' => 'icon_surprised.gif',
 ':x' => 'icon_mad.gif',
 ':|' => 'icon_neutral.gif',
 ';)' => 'icon_wink.gif',
 ':!:' => 'icon_exclaim.gif',
 ':?:' => 'icon_question.gif',
 );
 
 remove_action( 'wp_head' , 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts' , 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles' , 'print_emoji_styles' );
 remove_action( 'admin_print_styles' , 'print_emoji_styles' );
 remove_filter( 'the_content_feed' , 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss' , 'wp_staticize_emoji' );
 remove_filter( 'wp_mail' , 'wp_staticize_emoji_for_email' );
} 
add_action( 'init', 'theme_init_smilies', 10 );



/*相关图片文章图片调取*/
function catch_that_image() {
    global $post, $posts;
    $first_img = '';
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];
    if(empty($first_img)){
       $popimg= get_stylesheet_directory_uri() . '/img/thumbnail.png';
       $first_img = "$popimg";
    }
    return $first_img;
}
 
function mmimg($postID) {
    $cti = catch_that_image();
    $showimg = $cti;
    if ( has_post_thumbnail() ) { 
        $thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');
        $shareimg = $thumbnail_image_url[0];
    }else{ 
        $shareimg = $showimg;
    };
    return $shareimg;
} 

/* 屏蔽垃圾评论 */  
function refused_spam_comments( $comment_data ) {  
    $pattern = '/[一-龥]/u';  
    if(!preg_match($pattern,$comment_data['comment_content'])) {  
      err('写点汉字吧，博主外语很捉急！You should type some Chinese word!');  
    }  
    return( $comment_data );  
}  
add_filter('preprocess_comment','refused_spam_comments');

//获取访客VIP样式  
function get_author_class($user_email, $user_id){  
    global $wpdb;  
    $author_count = count($wpdb->get_results("SELECT comment_ID as author_count FROM $wpdb->comments WHERE comment_author_email = '$user_email' and user_id = '$user_id' "));
    if(!empty(get_userdata($user_id)->roles) && in_array('administrator',get_userdata($user_id)->roles)) { 
    	echo "<a class='vip' title='博主认证'></a>"; 
    }else{
    	if($author_count>=1 && $author_count<3)  
    	echo '<a class="vip1" title="评论达人 LV.1"></a>';  
    	else if($author_count>=3 && $author_count<5)   
    	echo '<a class="vip2" title="评论达人 LV.2"></a>';  
    	else if($author_count>=5 && $author_count<10)  
    	echo '<a class="vip3" title="评论达人 LV.3"></a>';   
    	else if($author_count>=10 && $author_count<20)   
    	echo '<a class="vip4" title="评论达人 LV.4"></a>';   
    	else if($author_count>=20 &&$author_count<50)   
    	echo '<a class="vip5" title="评论达人 LV.5"></a>';   
    	else if($author_count>=50 && $author_count<100)   
    	echo '<a class="vip6" title="评论达人 LV.6"></a>';   
    	else if($author_count>=100)   
    	echo '<a class="vip7" title="评论达人 LV.7"></a>';
    }
    
}


/* 代码实现评论显示国家、浏览器、系统*/
/* 显示国家 */
function qux_get_country($ip) {
	require_once(dirname(__FILE__).'/ip2c/ip2c.php');
	if (isset($GLOBALS['ip2c'])) {
		global $ip2c;
	} else {
		$ip2c = new ip2country(dirname(__FILE__).'/ip2c/ip-to-country.bin');
		$GLOBALS['ip2c'] = $ip2c;
	}
	return $ip2c->get_country($ip);
}

function CID_get_flag($ip) {
	if($ip == '127.0.0.1'){
		$code = 'wordpress';
		$name = 'Localhost';
	}else{
		$country = qux_get_country($ip);
		if (!$country) return "";
		
		$code = strtolower($country['id2']);
		$name = $country['name'];
	}
	if($name=='China'){
	    $name = '国内网友';
	}
	if($name=='United States'){
	    $name = '来自美国的网友';
	}
	if($name=='Reserved'){
	    $name = '未探索到的地区';
	}
	if($name=='Japan'){
	    $name = '来自日本的网友';
	}
	$output = stripslashes('<span class="country-flag"><img src="%IMAGE_BASE%/%COUNTRY_CODE%.png" title="%COUNTRY_NAME%" alt="%COUNTRY_NAME%" /></span>');
	
	if (!$output) return "";
	
	$output = str_replace("%COUNTRY_CODE%", $code, $output);
	$output = str_replace("%COUNTRY_NAME%", $name, $output);
	$output = str_replace("%COMMENTER_IP%", $ip, $output);
	$output = str_replace("%IMAGE_BASE%", get_stylesheet_directory_uri().'/img/flags', $output);
	
	return $output;
}

function CID_get_flag_without_template($ip, $show_image = true, $show_text = true, $before = '', $after = '') {
	if($ip == '127.0.0.1'){
		$code = 'wordpress';
		$name = 'Localhost';
	}else{
		
		$country = CID_get_country($ip);
		if (!$country) return "";
		
		$code = strtolower($country['id2']);
		$name = $country['name'];
	}
	
	$output = '';
	
	if ($show_image)
		$output = '<img src="'.get_stylesheet_directory_uri().'/img/flags/' . $code . '.png" title="' . $name . '" alt="' . $name . '" class="country-flag" />';
	if ($show_text)
		$output .= ' ' . $name;
	
	return $before . $output . $after;
}

function CID_get_comment_flag() {
	$ip = get_comment_author_IP();
	return CID_get_flag($ip);
}

function CID_get_comment_flag_without_template() {
	$ip = get_comment_author_IP();
	return CID_get_flag_without_template($ip);
}

	
/* 浏览器 */
$CID_image_url = get_stylesheet_directory_uri()."/img/browsers/";
$CID_width_height = "14px";

function CID_windows_detect_os($ua) {
	$os_name = $os_code = $os_ver = $pda_name = $pda_code = $pda_ver = null;

	if (preg_match('/Windows 95/i', $ua) || preg_match('/Win95/', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "95";
	} elseif (preg_match('/Windows NT 5.0/i', $ua) || preg_match('/Windows 2000/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "2000";
	} elseif (preg_match('/Win 9x 4.90/i', $ua) || preg_match('/Windows ME/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "ME";
	} elseif (preg_match('/Windows.98/i', $ua) || preg_match('/Win98/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "98";
	} elseif (preg_match('/Windows NT 6.0/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows_vista";
		$os_ver = "Vista";
	} elseif (preg_match('/Windows NT 6.1/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows_win7";
		$os_ver = "7";	
	} elseif (preg_match('/Windows NT 6.2/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver = "8";	
	} elseif (preg_match('/Windows NT 6.3/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver = "8.1";
	} elseif (preg_match('/Windows NT 6.4/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver = "10";
	} elseif (preg_match('/Windows NT 10.0/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver = "10";			
	} elseif (preg_match('/Windows NT 5.1/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "XP";
	} elseif (preg_match('/Windows NT 5.2/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		if (preg_match('/Win64/i', $ua)) {
			$os_ver = "XP 64 bit";
		} else {
			$os_ver = "Server 2003";
		}
	}
	elseif (preg_match('/Mac_PowerPC/i', $ua)) {
		$os_name = "Mac OS";
		$os_code = "macos";
	}elseif (preg_match('/Windows Phone/i', $ua)) {
		$matches = explode(';',$ua);
		$os_name = $matches[2];
		$os_code = "windows_phone7";
	} elseif (preg_match('/Windows NT 4.0/i', $ua) || preg_match('/WinNT4.0/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "NT 4.0";
	} elseif (preg_match('/Windows NT/i', $ua) || preg_match('/WinNT/i', $ua)) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "NT";
	} elseif (preg_match('/Windows CE/i', $ua)) {
		list($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_pda_detect_os($ua);
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver = "CE";
		if (preg_match('/PPC/i', $ua)) {
			$os_name = "Microsoft PocketPC";
			$os_code = "windows";
			$os_ver = '';
		}
		if (preg_match('/smartphone/i', $ua)) {
			$os_name = "Microsoft Smartphone";
			$os_code = "windows";
			$os_ver = '';
		}
	} else{
        $os_name = '未知系统';
		$os_code = 'other';
	}
	
	return array($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver);
}

function CID_unix_detect_os($ua) {
	$os_name = $os_ver = $os_code = null;
		if (preg_match('/Linux/i', $ua)) {
		$os_name = "Linux";
		$os_code = "linux";
		if (preg_match('#Debian#i', $ua)) {
			$os_code = "debian";
			$os_name = "Debian GNU/Linux";
		} elseif (preg_match('#Mandrake#i', $ua)) {
			$os_code = "mandrake";
			$os_name = "Mandrake Linux";
		} elseif (preg_match('#Kindle Fire#i',$ua)) {//for Kindle Fire
			$matches = explode(';',$ua);
			$os_code = "kindle";
			$matches2 = explode(')',$matches[4]);
			$os_name = $matches[2].$matches2[0];
		} elseif (preg_match('#Android#i',$ua)) {//Android
			$matches = explode(';',$ua);
			$os_code = "android";
			$matches2 = explode(')',$matches[4]);
			$os_name = $matches[2].$matches2[0];
		} elseif (preg_match('#SuSE#i', $ua)) {
			$os_code = "suse";
			$os_name = "SuSE Linux";
		} elseif (preg_match('#Novell#i', $ua)) {
			$os_code = "novell";
			$os_name = "Novell Linux";
		} elseif (preg_match('#Ubuntu#i', $ua)) {
			$os_code = "ubuntu";
			$os_name = "Ubuntu Linux";
		} elseif (preg_match('#Red ?Hat#i', $ua)) {
			$os_code = "redhat";
			$os_name = "RedHat Linux";
		} elseif (preg_match('#Gentoo#i', $ua)) {
			$os_code = "gentoo";
			$os_name = "Gentoo Linux";
		} elseif (preg_match('#Fedora#i', $ua)) {
			$os_code = "fedora";
			$os_name = "Fedora Linux";
		} elseif (preg_match('#MEPIS#i', $ua)) {
			$os_name = "MEPIS Linux";
		} elseif (preg_match('#Knoppix#i', $ua)) {
			$os_name = "Knoppix Linux";
		} elseif (preg_match('#Slackware#i', $ua)) {
			$os_code = "slackware";
			$os_name = "Slackware Linux";
		} elseif (preg_match('#Xandros#i', $ua)) {
			$os_name = "Xandros Linux";
		} elseif (preg_match('#Kanotix#i', $ua)) {
			$os_name = "Kanotix Linux";
		} 
	} elseif (preg_match('/FreeBSD/i', $ua)) {
		$os_name = "FreeBSD";
		$os_code = "freebsd";
	} elseif (preg_match('/NetBSD/i', $ua)) {
		$os_name = "NetBSD";
		$os_code = "netbsd";
	} elseif (preg_match('/OpenBSD/i', $ua)) {
		$os_name = "OpenBSD";
		$os_code = "openbsd";
	} elseif (preg_match('/IRIX/i', $ua)) {
		$os_name = "SGI IRIX";
		$os_code = "sgi";
	} elseif (preg_match('/SunOS/i', $ua)) {
		$os_name = "Solaris";
		$os_code = "sun";
	} elseif (preg_match('#iPod.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches)) {
		$os_name = "iPod";
		$os_code = "iphone";
		$os_ver = $matches[1];
	} elseif (preg_match('#iPhone.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches)) {
		$os_name = "iPhone";
		$os_code = "iphone";
		$os_ver = $matches[1];
	} elseif (preg_match('#iPad.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches)) {
		$os_name = "iPad";
		$os_code = "ipad";
		$os_ver = $matches[1];
	} elseif (preg_match('/Mac OS X.([0-9. _]+)/i', $ua, $matches)) {
		$os_name = "Mac OS";
		$os_code = "macos";
		if(count(explode(7,$matches[1]))>1) $matches[1] = 'Lion '.$matches[1];
		elseif(count(explode(8,$matches[1]))>1) $matches[1] = 'Mountain Lion '.$matches[1];
		$os_ver = "X ".$matches[1];
	} elseif (preg_match('/Macintosh/i', $ua)) {
		$os_name = "Mac OS";
		$os_code = "macos";
	} elseif (preg_match('/Unix/i', $ua)) {
		$os_name = "UNIX";
		$os_code = "unix";
	} elseif (preg_match('/CrOS/i', $ua)){
		$os_name="Google Chrome OS";
		$os_code="chromeos";
	} elseif (preg_match('/Fedor.([0-9. _]+)/i', $ua, $matches)){
		$os_name="Fedora";
		$os_code="fedora";
		$os_ver = $matches[1];
	} else{
        $os_name = 'Unknow Os';
		$os_code = 'other';
	}
	  
	return array($os_name, $os_code, $os_ver);
}

function CID_pda_detect_os($ua) {
	$os_name = $os_code = $os_ver = $pda_name = $pda_code = $pda_ver = null;
	if (preg_match('#PalmOS#i', $ua)) {
		$os_name = "Palm OS";
		$os_code = "palm";
	} elseif (preg_match('#Windows CE#i', $ua)) {
		$os_name = "Windows CE";
		$os_code = "windows";
	} elseif (preg_match('#QtEmbedded#i', $ua)) {
		$os_name = "Qtopia";
		$os_code = "linux";
	} elseif (preg_match('#Zaurus#i', $ua)) {
		$os_name = "Linux";
		$os_code = "linux";
	} elseif (preg_match('#Symbian#i', $ua)) {
		$os_name = "Symbian OS";
		$os_code = "symbian";
	} elseif (preg_match('#PalmOS/sony/model#i', $ua)) {
		$pda_name = "Sony Clie";
		$pda_code = "sony";
	} elseif (preg_match('#Zaurus ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$pda_name = "Sharp Zaurus " . $matches[1];
		$pda_code = "zaurus";
		$pda_ver = $matches[1];
	} elseif (preg_match('#Series ([0-9]+)#i', $ua, $matches)) {
		$pda_name = "Series";
		$pda_code = "nokia";
		$pda_ver = $matches[1];
	} elseif (preg_match('#Nokia ([0-9]+)#i', $ua, $matches)) {
		$pda_name = "Nokia";
		$pda_code = "nokia";
		$pda_ver = $matches[1];
	} elseif (preg_match('#SIE-([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "Siemens";
		$pda_code = "siemens";
		$pda_ver = $matches[1];
	} elseif (preg_match('#dopod([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "Dopod";
		$pda_code = "dopod";
		$pda_ver = $matches[1];
	} elseif (preg_match('#o2 xda ([a-zA-Z0-9 ]+);#i', $ua, $matches)) {
		$pda_name = "O2 XDA";
		$pda_code = "o2";
		$pda_ver = $matches[1];
	} elseif (preg_match('#SEC-([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "Samsung";
		$pda_code = "samsung";
		$pda_ver = $matches[1];
	} elseif (preg_match('#SonyEricsson ?([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "SonyEricsson";
		$pda_code = "sonyericsson";
		$pda_ver = $matches[1];
	} elseif (preg_match('#Kindle\/([a-zA-Z0-9. ×\(.\)]+)#i',$ua, $matches)) {//for Kindle
		$pda_name = "kindle";
		$pda_code = "kindle";
		$pda_ver = $matches[1];
	} else {
		$pda_name = 'Unknow Os';
		$pda_code = 'other';
	}
	  
	return array($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver);
}


function CID_detect_browser($ua) {
	$browser_name = $browser_code = $browser_ver = $os_name = $os_code = $os_ver = $pda_name = $pda_code = $pda_ver = null;
	$ua = preg_replace("/FunWebProducts/i", "", $ua);
	if (preg_match('#MovableType[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'MovableType';
		$browser_code = 'mt';
		$browser_ver = $matches[1];
	} elseif (preg_match('#WordPress[ /]([a-zA-Z0-9.]*)#i', $ua, $matches)) {
		$browser_name = 'WordPress';
		$browser_code = 'wp';
		$browser_ver = $matches[1];
	} elseif (preg_match('#typepad[ /]([a-zA-Z0-9.]*)#i', $ua, $matches)) {
		$browser_name = 'TypePad';
		$browser_code = 'typepad';
		$browser_ver = $matches[1];
	} elseif (preg_match('#drupal#i', $ua)) {
		$browser_name = 'Drupal';
		$browser_code = 'drupal';
		$browser_ver = count($matches) > 0 ? $matches[1] : "";
	} elseif (preg_match('#symbianos/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$os_name = "SymbianOS";
		$os_ver = $matches[1];
		$os_code = 'symbian';
	} elseif (preg_match('#avantbrowser.com#i', $ua)) {
		$browser_name = 'Avant Browser';
		$browser_code = 'avantbrowser';
	} elseif (preg_match('#(Camino|Chimera)[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Camino';
		$browser_code = 'camino';
		$browser_ver = $matches[2];
		$os_name = "Mac OS";
		$os_code = "macos";
		$os_ver = "X";
	} elseif (preg_match('#anonymouse#i', $ua, $matches)) {
		$browser_name = 'Anonymouse';
		$browser_code = 'anonymouse';
	} elseif (preg_match('#PHP#', $ua, $matches)) {
		$browser_name = 'PHP';
		$browser_code = 'php';
	} elseif (preg_match('#danger hiptop#i', $ua, $matches)) {
		$browser_name = 'Danger HipTop';
		$browser_code = 'danger';
	} elseif (preg_match('#w3m/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'W3M';
		$browser_code = 'w3m';
		$browser_ver = $matches[1];
    } elseif (preg_match('#Shiira[/]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Shiira';
		$browser_code = 'shiira';
		$browser_ver = $matches[1];
		$os_name = "Mac OS";
		$os_code = "macos";
		$os_ver = "X";
	} elseif (preg_match('#Dillo[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Dillo';
		$browser_code = 'dillo';
		$browser_ver = $matches[1];
	} elseif (preg_match('#Epiphany/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Epiphany';
		$browser_code = 'epiphany';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
	} elseif (preg_match('#UP.Browser/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Openwave UP.Browser';
		$browser_code = 'openwave';
		$browser_ver = $matches[1];
	} elseif (preg_match('#DoCoMo/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'DoCoMo';
		$browser_code = 'docomo';
		$browser_ver = $matches[1];
		if ($browser_ver == '1.0') {
			preg_match('#DoCoMo/([a-zA-Z0-9.]+)/([a-zA-Z0-9.]+)#i', $ua, $matches);
			$browser_ver = $matches[2];
		} elseif ($browser_ver == '2.0') {
			preg_match('#DoCoMo/([a-zA-Z0-9.]+) ([a-zA-Z0-9.]+)#i', $ua, $matches);
			$browser_ver = $matches[2];
		}
	} elseif (preg_match('#(SeaMonkey)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Mozilla SeaMonkey';
		$browser_code = 'seamonkey';
		$browser_ver = $matches[2];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#Kazehakase/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Kazehakase';
		$browser_code = 'kazehakase';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#Flock/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Flock';
		$browser_code = 'flock';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/4([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '火狐浏览器';
		$browser_code = 'firefox';
		$browser_ver = '4'.$matches[2];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '火狐浏览器';
		$browser_code = 'firefox';
		$browser_ver = $matches[2];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#Minimo/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Minimo';
		$browser_code = 'mozilla';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#MultiZilla/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'MultiZilla';
		$browser_code = 'mozilla';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '搜狗浏览器';
		$browser_code = 'sogou';
		$browser_ver = '2'.$matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#baidubrowser ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '百度浏览器';
		$browser_code = 'baidubrowser';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '360浏览器';
		$browser_code = '360se';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#QQBrowser/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'QQ浏览器';
		$browser_code = 'qqbrowser';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('/PSP \(PlayStation Portable\)\; ([a-zA-Z0-9.]+)/', $ua, $matches)) {
		$pda_name = "Sony PSP";
		$pda_code = "sony-psp";
		$pda_ver = $matches[1];
	} elseif (preg_match('#Galeon/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Galeon';
		$browser_code = 'galeon';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
	} elseif (preg_match('#iCab/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'iCab';
		$browser_code = 'icab';
		$browser_ver = $matches[1];
		$os_name = "Mac OS";
		$os_code = "macos";
		if (preg_match('#Mac OS X#i', $ua)) {
			$os_ver = "X";
		}
	} elseif (preg_match('#K-Meleon/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'K-Meleon';
		$browser_code = 'kmeleon';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#Lynx/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Lynx';
		$browser_code = 'lynx';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
	} elseif (preg_match('#Links \\(([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Links';
		$browser_code = 'lynx';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
	} elseif (preg_match('#ELinks[/ ]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'ELinks';
		$browser_code = 'lynx';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
	} elseif (preg_match('#ELinks \\(([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'ELinks';
		$browser_code = 'lynx';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
	} elseif (preg_match('#Konqueror/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Konqueror';
		$browser_code = 'konqueror';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		if (!$os_name) {
			list($os_name, $os_code, $os_ver) = CID_pda_detect_os($ua);
		}
	} elseif (preg_match('#NetPositive/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'NetPositive';
		$browser_code = 'netpositive';
		$browser_ver = $matches[1];
		$os_name = "BeOS";
		$os_code = "beos";
	} elseif (preg_match('#OmniWeb#i', $ua)) {
		$browser_name = 'OmniWeb';
		$browser_code = 'omniweb';
		$os_name = "Mac OS";
		$os_code = "macos";
		$os_ver = "X";
	} elseif (preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '谷歌浏览器'; $browser_code = 'chrome'; $browser_ver = $matches[1]; 
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		} 
	} elseif (preg_match('#Arora/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Arora';
		$browser_code = 'arora';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $ua,$matches)) {
		$browser_name = '傲游浏览器';
		$browser_code = 'maxthon';
		$browser_ver = $matches[2];
		if (preg_match('/Win/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#CriOS/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Chrome for iOS';
		$browser_code = 'crios';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
		 	list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}		
	} elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Safari浏览器';
		$browser_code = 'safari';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
		 	list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}		
	} elseif (preg_match('#opera mini#i', $ua)) {
		$browser_name = 'Opera Mini浏览器';
		$browser_code = 'opera';
		preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches);
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#Opera.(.*)Version[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Opera浏览器';
		$browser_code = 'opera';
		$browser_ver = $matches[2];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
		if (!$os_name) {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
		if (!$os_name) {
			list($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_pda_detect_os($ua);
		}
		if (!$os_name) {
			if (preg_match('/Wii/i', $ua)) {
				$os_name = "Nintendo Wii";
				$os_code = "nintendo-wii";
			}
		}
	} elseif (preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Opera Mini';
		$browser_code = 'opera';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#WebPro/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'WebPro';
		$browser_code = 'webpro';
		$browser_ver = $matches[1];
		$os_name = "PalmOS";
		$os_code = "palmos";
	} elseif (preg_match('#WebPro#i', $ua, $matches)) {
		$browser_name = 'WebPro';
		$browser_code = 'webpro';
		$os_name = "PalmOS";
		$os_code = "palmos";
	} elseif (preg_match('#Netfront/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Netfront';
		$browser_code = 'netfront';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_pda_detect_os($ua);
	} elseif (preg_match('#Xiino/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Xiino';
		$browser_code = 'xiino';
		$browser_ver = $matches[1];
	} elseif (preg_match('/wp-blackberry\/([a-zA-Z0-9.]*)/i', $ua, $matches)) {
		$browser_name = "WordPress for BlackBerry";
		$browser_code = "wordpress";
		$browser_ver = $matches[1];
		$pda_name = "BlackBerry";
		$pda_code = "blackberry";
	} elseif (preg_match('#Blackberry([0-9]+)#i', $ua, $matches)) {
		$pda_name = "Blackberry";
		$pda_code = "blackberry";
		$pda_ver = $matches[1];
	} elseif (preg_match('#Blackberry#i', $ua)) {
		$pda_name = "Blackberry";
		$pda_code = "blackberry";
	} elseif (preg_match('#SPV ([0-9a-zA-Z.]+)#i', $ua, $matches)) {
		$pda_name = "Orange SPV";
		$pda_code = "orange";
		$pda_ver = $matches[1];
	} elseif (preg_match('#LGE-([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "LG";
		$pda_code = 'lg';
		$pda_ver = $matches[1];
	} elseif (preg_match('#MOT-([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "Motorola";
		$pda_code = 'motorola';
		$pda_ver = $matches[1];
	} elseif (preg_match('#Nokia ?([0-9]+)#i', $ua, $matches)) {
		$pda_name = "Nokia";
		$pda_code = "nokia";
		$pda_ver = $matches[1];
	} elseif (preg_match('#NokiaN-Gage#i', $ua)) {
		$pda_name = "Nokia";
		$pda_code = "nokia";
		$pda_ver = "N-Gage";
	} elseif (preg_match('#Blazer[ /]?([a-zA-Z0-9.]*)#i', $ua, $matches)) {
		$browser_name = "Blazer";
		$browser_code = "blazer";
		$browser_ver = $matches[1];
		$os_name = "Palm OS";
		$os_code = "palm";
	} elseif (preg_match('#SIE-([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "Siemens";
		$pda_code = "siemens";
		$pda_ver = $matches[1];
	} elseif (preg_match('#SEC-([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "Samsung";
		$pda_code = "samsung";
		$pda_ver = $matches[1];
	} elseif (preg_match('/wp-iphone\/([a-zA-Z0-9.]*)/i', $ua, $matches)) {
		$browser_name = "WordPress for iOS";
		$browser_code = "wordpress";
		$browser_ver = $matches[1];
		$pda_name = "iPhone & iPad";
		$pda_code = "ipad";
	} elseif (preg_match('/wp-android\/([a-zA-Z0-9.]*)/i', $ua, $matches)) {
		$browser_name = "WordPress for Android";
		$browser_code = "wordpress";
		$browser_ver = $matches[1];
		$pda_name = "Android";
		$pda_code = "android";
	} elseif (preg_match('/wp-windowsphone\/([a-zA-Z0-9.]*)/i', $ua, $matches)) {
		$browser_name = "WordPress for Windows Phone 7";
		$browser_code = "wordpress";
		$browser_ver = $matches[1];
		$pda_name = "Windows Phone 7";
		$pda_code = "windows_phone7";
	} elseif (preg_match('/wp-nokia\/([a-zA-Z0-9.]*)/i', $ua, $matches)) {
		$browser_name = "WordPress for Nokia";
		$browser_code = "wordpress";
		$browser_ver = $matches[1];
		$pda_name = "Nokia";
		$pda_code = "nokia";
	} elseif (preg_match('#SAMSUNG-(S.H-[a-zA-Z0-9_/.]+)#i', $ua, $matches)) {
		$pda_name = "Samsung";
		$pda_code = "samsung";
		$pda_ver = $matches[1];
		if (preg_match('#(j2me|midp)#i', $ua)) {
		$browser_name = "J2ME/MIDP Browser";
		$browser_code = "j2me";
		}
	} elseif (preg_match('#SonyEricsson ?([a-zA-Z0-9]+)#i', $ua, $matches)) {
		$pda_name = "SonyEricsson";
		$pda_code = "sonyericsson";
		$pda_ver = $matches[1];
	} elseif (preg_match('#(j2me|midp)#i', $ua)) {
		$browser_name = "J2ME/MIDP Browser";
		$browser_code = "j2me";
	// mice
	} elseif (preg_match('/GreenBrowser/i', $ua)) {
		$browser_name = 'GreenBrowser';
		$browser_code = 'greenbrowser';
		if (preg_match('/Win/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#TencentTraveler ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '腾讯TT浏览器';
		$browser_code = 'tencenttraveler';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#UCWEB([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'UC浏览器';
		$browser_code = 'ucweb';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Internet Explorer';
		$browser_ver = $matches[1];
		if ( strpos($browser_ver, '7') !== false || strpos($browser_ver, '8') !== false)
			$browser_code = 'ie8';
		elseif ( strpos($browser_ver, '9') !== false)
			$browser_code = 'ie9';
		elseif ( strpos($browser_ver, '10') !== false)
			$browser_code = 'ie10';
		else
			$browser_code = 'ie';
		list($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_windows_detect_os($ua);
	} elseif (preg_match('#Universe/([0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Universe';
		$browser_code = 'universe';
		$browser_ver = $matches[1];
		list($os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_pda_detect_os($ua);
	}elseif (preg_match('#Netscape[0-9]?/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Netscape';
		$browser_code = 'netscape';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#^Mozilla/5.0#i', $ua) && preg_match('#rv:([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = '火狐浏览器5.0';
		$browser_code = 'mozilla';
		$browser_ver = $matches[1];
		if (preg_match('/Windows/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	} elseif (preg_match('#^Mozilla/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
		$browser_name = 'Netscape Navigator';
		$browser_code = 'netscape';
		$browser_ver = $matches[1];
		if (preg_match('/Win/i', $ua)) {
			list($os_name, $os_code, $os_ver) = CID_windows_detect_os($ua);
		} else {
			list($os_name, $os_code, $os_ver) = CID_unix_detect_os($ua);
		}
	}else{
        $browser_name = '未知浏览器';
		$browser_code = 'null';
	}
	
	if (!$pda_name && !$os_name){
        $pda_name = 'Unknow Os';
		$pda_code = 'other';
        $os_name = 'Unknow Os';
		$os_code = 'other';
	}
	return array($browser_name, $browser_code, $browser_ver, $os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver);
}

function CID_friendly_string($browser_name = '', $browser_code = '', $browser_ver = '', $os_name = '', $os_code = '', $os_ver = '', $pda_name= '', $pda_code = '', $pda_ver = '') {

	$output = stripslashes('<span class="WB-OS"><img src="%IMAGE_BASE%/%BROWSER_CODE%.png" title="%BROWSER_NAME%" alt="%BROWSER_NAME%" /> <img src="%IMAGE_BASE%/%OS_CODE%.png" title="%OS_NAME% %OS_VERSION%" alt="%OS_NAME% %OS_VERSION%" /> </span>');
	
	if (!$output) return "";
	
	$browser_name	= htmlspecialchars($browser_name);
	$browser_code	= htmlspecialchars($browser_code);
	$browser_ver 	= htmlspecialchars($browser_ver);
	$os_name     	= htmlspecialchars($os_name);
	$os_code     	= htmlspecialchars($os_code);
	$os_ver      	= htmlspecialchars($os_ver);
	$pda_name    	= htmlspecialchars($pda_name);
	$pda_code    	= htmlspecialchars($pda_code);
	$pda_ver     	= htmlspecialchars($pda_ver);
	
	$output = str_replace("%IMAGE_BASE%", get_stylesheet_directory_uri()."/img/browsers", $output);
	
	if ($browser_name && $pda_name) {
		$output = str_replace("[BROWSER]", "", $output);
		$output = str_replace("[/BROWSER]", "", $output);

		$output = str_replace("[OS]", "", $output);
		$output = str_replace("[/OS]", "", $output);
		
		$output = str_replace("%BROWSER_NAME%", $browser_name, $output);
		$output = str_replace("%BROWSER_CODE%", $browser_code, $output);
		$output = str_replace("%BROWSER_VERSION%", $browser_ver, $output);
		
		$output = str_replace("%OS_NAME%", $pda_name, $output);
		$output = str_replace("%OS_CODE%", $pda_code, $output);
		$output = str_replace("%OS_VERSION%", $pda_ver, $output);
	} elseif ($browser_name && $os_name) {
		$output = str_replace("[BROWSER]", "", $output);
		$output = str_replace("[/BROWSER]", "", $output);

		$output = str_replace("[OS]", "", $output);
		$output = str_replace("[/OS]", "", $output);
		
		$output = str_replace("%BROWSER_NAME%", $browser_name, $output);
		$output = str_replace("%BROWSER_CODE%", $browser_code, $output);
		$output = str_replace("%BROWSER_VERSION%", $browser_ver, $output);
		
		$output = str_replace("%OS_NAME%", $os_name, $output);
		$output = str_replace("%OS_CODE%", $os_code, $output);
		$output = str_replace("%OS_VERSION%", $os_ver, $output);	
	} elseif ($browser_name) {
		$output = str_replace("[BROWSER]", "", $output);
		$output = str_replace("[/BROWSER]", "", $output);
		
		$start	= strpos($output, "[OS]");
		$end  	= strpos($output, "[/OS]");
		$temp 	= substr($output, $start, $end - $start + 5);
		
		$output = str_replace($temp, "", $output);
		
		$output = str_replace("%BROWSER_NAME%", $browser_name, $output);
		$output = str_replace("%BROWSER_CODE%", $browser_code, $output);
		$output = str_replace("%BROWSER_VERSION%", $browser_ver, $output);

		$output = str_replace("%OS_NAME%", "", $output);
		$output = str_replace("%OS_CODE%", "", $output);
		$output = str_replace("%OS_VERSION%", "", $output);		
	} elseif ($os_name) {
		$output = str_replace("[OS]", "", $output);
		$output = str_replace("[/OS]", "", $output);
		
		$start	= strpos($output, "[BROWSER]");
		$end  	= strpos($output, "[/BROWSER]");
		$temp 	= substr($output, $start, $end - $start + 10);
		
		$output = str_replace($temp, "", $output);
		
		$output = str_replace("%OS_NAME%", $os_name, $output);
		$output = str_replace("%OS_CODE%", $os_code, $output);
		$output = str_replace("%OS_VERSION%", $os_ver, $output);

		$output = str_replace("%BROWSER_NAME%", "", $output);
		$output = str_replace("%BROWSER_CODE%", "", $output);
		$output = str_replace("%BROWSER_VERSION%", "", $output);		
	} elseif ($pda_name) {
		$output = str_replace("[OS]", "", $output);
		$output = str_replace("[/OS]", "", $output);
		
		$start	= strpos($output, "[BROWSER]");
		$end  	= strpos($output, "[/BROWSER]");
		$temp 	= substr($output, $start, $end - $start + 10);
		
		$output = str_replace($temp, "", $output);
		
		$output = str_replace("%OS_NAME%", $pda_name, $output);
		$output = str_replace("%OS_CODE%", $pda_code, $output);
		$output = str_replace("%OS_VERSION%", $pda_ver, $output);

		$output = str_replace("%BROWSER_NAME%", "", $output);
		$output = str_replace("%BROWSER_CODE%", "", $output);
		$output = str_replace("%BROWSER_VERSION%", "", $output);
	}
	
	return $output;
}

function CID_friendly_string_without_template($browser_name = '', $browser_code = '', $browser_ver = '', $os_name = '', $os_code = '', $os_ver = '', $pda_name= '', $pda_code = '', $pda_ver = '', $show_image = true, $show_text = true, $between = '', $before = '', $after = '') {
	global $CID_width_height, $CID_image_url;
	
	$browser_name = htmlspecialchars($browser_name);
	$browser_code = htmlspecialchars($browser_code);
	$browser_ver = htmlspecialchars($browser_ver);
	$os_name = htmlspecialchars($os_name);
	$os_code = htmlspecialchars($os_code);
	$os_ver = htmlspecialchars($os_ver);
	$pda_name = htmlspecialchars($pda_name);
	$pda_code = htmlspecialchars($pda_code);
	$pda_ver = htmlspecialchars($pda_ver);
	$between = htmlspecialchars($between);
	
	$text1 = ''; $text2 = ''; $image1 = ''; $image2 = '';
	
	if ($browser_name && $pda_name) {
		if ($show_image) {
			$image1 = "<img src='$CID_image_url/$browser_code.png' title='$browser_name $browser_ver' alt='$browser_name' width='$CID_width_height' height='$CID_width_height' class='browser-icon' />";
			$image2 = "<img src='$CID_image_url/$pda_code.png' title='$pda_name $pda_ver' alt='$pda_name' width='$CID_width_height' height='$CID_width_height' class='os-icon' />";
		}
		if ($show_text) {
			$text1 = "$browser_name $browser_ver $between ";
			$text2 = "$pda_name $pda_ver";
		}
	} elseif ($browser_name && $os_name) {
		if ($show_image) {
			$image1 = "<img src='$CID_image_url/$browser_code.png' title='$browser_name $browser_ver' alt='$browser_name' width='$CID_width_height' height='$CID_width_height' class='browser-icon' /> ";
			$image2 = "<img src='$CID_image_url/$os_code.png' title='$os_name $os_ver' alt='$os_name' width='$CID_width_height' height='$CID_width_height' class='os-icon' /> ";
		}
		if ($show_text) {
			$text1 = "$browser_name $browser_ver $between ";
			$text2 = "$os_name $os_ver";
		}
	} elseif ($browser_name) {
		if ($show_image)
			$image1 = "<img src='$CID_image_url/$browser_code.png' title='$browser_name $browser_ver' alt='$browser_name' width='$CID_width_height' height='$CID_width_height' class='browser-icon' />";
		if ($show_text)
			$text1 = "$browser_name $browser_ver";
	} elseif ($os_name) {
		if ($show_image)
			$image1 = "<img src='$CID_image_url/$os_code.png' title='$os_name $os_ver' alt='$os_name' width='$CID_width_height' height='$CID_width_height' class='os-icon' /> ";
		if ($show_text)
			$text1 = "$os_name $os_ver";
	} elseif ($pda_name) {
		if ($show_image)
			$image1 = "<img src='$CID_image_url/$pda_code.png' title='$pda_name $pda_ver' alt='$pda_name' width='$CID_width_height' height='$CID_width_height' class='os-icon' />";
		if ($show_text)
			$text1 = "$pda_name $pda_ver";
	}
	return $before . $image1 . ' ' . $text1 . ' ' . $image2 . ' ' . $text2 . $after;
}

function CID_browser_string($ua) {
	list ($browser_name, $browser_code, $browser_ver, $os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_detect_browser($ua);
	$string = CID_friendly_string($browser_name, $browser_code, $browser_ver, $os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver);
	/*if (!$string) {
		$string = "Unknown browser";
	}*/
	return $string;
}

function CID_browser_string_without_template($ua, $show_image = true, $show_text = true, $between = '', $before = '', $after = '') {
	list ($browser_name, $browser_code, $browser_ver, $os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver) = CID_detect_browser($ua);
	$string = CID_friendly_string_without_template($browser_name, $browser_code, $browser_ver, $os_name, $os_code, $os_ver, $pda_name, $pda_code, $pda_ver, $show_image, $show_text, $between, $before, $after);
	/*if (!$string) {
		$string = "Unknown browser";
	}*/
	return $string;
}

function CID_get_comment_browser() {
	global $comment;
	if (!$comment->comment_agent) return;
	$string = CID_browser_string($comment->comment_agent);
	return $string;
}

function CID_get_comment_browser_without_template() {
	global $comment;
	if (!$comment->comment_agent) return;
	$string = CID_browser_string_without_template($comment->comment_agent);
	return $string;
}
	
?>