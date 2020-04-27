<?php
// Require theme functions
require get_stylesheet_directory() . '/functions-theme.php';

// Customize your functions

/* 自动给页面的站外链接添加nofollow属性和新窗口打开 开始*/
add_filter( 'the_content', 'cn_nf_url_parse');
 
function cn_nf_url_parse( $content ) {
 
	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
		if( !empty($matches) ) {
 
			$srcUrl = get_option('siteurl');
			for ($i=0; $i < count($matches); $i++)
			{
 
				$tag = $matches[$i][0];
				$tag2 = $matches[$i][0];
				$url = $matches[$i][0];
 
				$noFollow = '';
 
				$pattern = '/target\s*=\s*"\s*_blank\s*"/';
				preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
				if( count($match) < 1 )
					$noFollow .= ' target="_blank" ';
 
				$pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
				preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
				if( count($match) < 1 ) $noFollow .= ' rel="nofollow" '; $pos = strpos($url,$srcUrl); if ($pos === false) { $tag = rtrim ($tag,'>');
					$tag .= $noFollow.'>';
					$content = str_replace($tag2,$tag,$content);
				}
			}
		}
	}
 
	$content = str_replace(']]>', ']]>', $content);
	return $content;
 
}
/* 自动给页面的站外链接添加nofollow属性和新窗口打开 结束*/


function reply_to_read($atts, $content=null) {   
        extract(shortcode_atts(array("notice" => '<div style="text-align:center;border:1px dashed #FF9A9A;padding:8px;margin:10px auto;color:#FF6666;>
<span class="reply-to-read">温馨提示: 此处内容需要 <a href="#respond" title="评论本文">评论本文</a> 后 <a href="javascript:window.location.reload();" target="_self">刷新本页</a> 才能查看！</span></div>'), $atts));   
        $email = null;   
        $user_ID = (int) wp_get_current_user()->ID;   
        if ($user_ID > 0) {   
            $email = get_userdata($user_ID)->user_email;   
            //对博主直接显示内容   
            $admin_email = "xiang1008huayun@outlook.com"; //博主Email   
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

//添加评论可见快捷标签按钮
function appthemes_add_reply() {
?>
    <script type="text/javascript">
        if ( typeof QTags != 'undefined' ) {
            QTags.addButton( 'reply', '评论可见按钮', '[reply]','[/reply]' );
        } 
    </script>
<?php 
}
add_action('admin_print_footer_scripts', 'appthemes_add_reply' );

//部分内容输入密码可见
function e_secret($atts, $content=null){
 extract(shortcode_atts(array('key'=>null), $atts));
 if(isset($_POST['e_secret_key']) && $_POST['e_secret_key']==$key){
 return '
<div class="e-secret">'.$content.'</div>
';
 }
 elseif(isset($_POST['e_secret_key']) && $_POST['e_secret_key']!=$key){
        return '
			<script>
				alert("密码错误，请仔细核对密码后重试！！！");
				window.location.href="'.get_permalink().'";
			</script>
		';
	
	}else{
 return '
<form class="e-secret" action="'.get_permalink().'" method="post" name="e-secret"><label>请先加入QQ交流群获取密码后，再来查看加密内容吧~~~<br/>QQ群号：234037219</label><input type="password" name="e_secret_key" class="euc-y-i" maxlength="50"><input type="submit" class="euc-y-s" value="确定">
<div class="euc-clear"></div>
</form>
';
 }
}
add_shortcode('secret','e_secret');
//密码可见快捷标签按钮
function appthemes_add_secret() {
?>
    <script type="text/javascript">
        if ( typeof QTags != 'undefined' ) {
            QTags.addButton( 'secret', '输入密码可见', '[secret key="ZYJ0116"]','[/secret]' );
        } 
    </script>
<?php 
}
add_action('admin_print_footer_scripts', 'appthemes_add_secret' );

//添加下载按钮
function appthemes_add_quicktags() {
?><script type="text/javascript">// <![CDATA[
QTags.addButton( 'downs', '下载按钮', '<div class="sg-dl"><span class="sg-dl-span"><a href="','" target=_blank title="文件下载" rel=nofollow><button type="button" class="btn-download"><i class="fa fa-download"></i>&nbsp;本地下载</button></a></span></div>' );
// ]]></script><?php } add_action('admin_print_footer_scripts', 'appthemes_add_quicktags' );


/*文章内容高亮提示框开始*/
/*青色警示文本框*/
function qgg_cyan($atts, $content=null){   
    return '<div id="tbc_cyan">'.$content.'</div>';   
}    
add_shortcode('qgg_cyan','qgg_cyan'); 
/*绿色警示文本框*/   
function qgg_green($atts, $content=null){   
    return '<div id="tbc_green">'.$content.'</div>';   
}    
add_shortcode('qgg_green','qgg_green');     
 /*黄色色警示文本框*/  
function qgg_yellow($atts, $content=null){   
    return '<div id="tbc_yellow">'.$content.'</div>';   
}    
add_shortcode('qgg_yellow','qgg_yellow'); 
 /*粉色警示文本框*/  
function qgg_pink($atts, $content=null){   
    return '<div id="tbc_pink">'.$content.'</div>';   
}    
add_shortcode('qgg_pink','qgg_pink');  
 /*灰色警示文本框*/  
function qgg_gray($atts, $content=null){   
    return '<div id="tbc_gray">'.$content.'</div>';   
}    
add_shortcode('qgg_gray','qgg_gray');   
/*文章内容高亮提示框完毕*/

//添加彩色文本框快捷按钮
function appthemes_add_qgg_cyan() {
?>
    <script type="text/javascript">
    if ( typeof QTags != 'undefined' ) {
        QTags.addButton( 'qgg_cyan', '青色文本框', '[qgg_cyan]','[/qgg_cyan]' );
    } 
    </script>
<?php 
} 
add_action('admin_print_footer_scripts', 'appthemes_add_qgg_cyan' );

function appthemes_add_qgg_green() {
?>
    <script type="text/javascript">
    if ( typeof QTags != 'undefined' ) {
        QTags.addButton( 'qgg_green', '绿色文本框', '[qgg_green]','[/qgg_green]' );
    } 
    </script>
<?php 
} 
add_action('admin_print_footer_scripts', 'appthemes_add_qgg_green' );

function appthemes_add_qgg_yellow() {
?>
    <script type="text/javascript">
    if ( typeof QTags != 'undefined' ) {
        QTags.addButton( 'qgg_yellow', '黄色文本框', '[qgg_yellow]','[/qgg_yellow]' );
    } 
    </script>
<?php 
} 
add_action('admin_print_footer_scripts', 'appthemes_add_qgg_yellow' );

function appthemes_add_qgg_pink() {
?>
    <script type="text/javascript">
    if ( typeof QTags != 'undefined' ) {
        QTags.addButton( 'qgg_pink', '粉色文本框', '[qgg_pink]','[/qgg_pink]' );
    } 
    </script>
<?php 
} 
add_action('admin_print_footer_scripts', 'appthemes_add_qgg_pink' );

function appthemes_add_qgg_gray() {
?>
    <script type="text/javascript">
    if ( typeof QTags != 'undefined' ) {
        QTags.addButton( 'qgg_gray', '灰色文本框', '[qgg_gray]','[/qgg_gray]' );
    } 
    </script>
<?php 
} 
add_action('admin_print_footer_scripts', 'appthemes_add_qgg_gray' );

/*闪光按钮 开始 */
/*添加蓝色闪光按钮*/
function sg_blue($atts, $content = null) {
 extract(shortcode_atts(array(
 "href" => 'http://'
 ) , $atts));
 return '<a class="sgbtn_blue" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('sgbtn_blue', 'sg_blue');
/*添加红色闪光按钮*/
function sg_red($atts, $content = null) {
 extract(shortcode_atts(array(
 "href" => 'http://'
 ) , $atts));
 return '<a class="sgbtn_red" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('sgbtn_red', 'sg_red');
/*添加橙色闪光按钮*/
function sg_orange($atts, $content = null) {
 extract(shortcode_atts(array(
 "href" => 'http://'
 ) , $atts));
 return '<a class="sgbtn_orange" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('sgbtn_orange', 'sg_orange');
/*添加绿色闪光按钮*/
function sg_lv($atts, $content = null) {
 extract(shortcode_atts(array(
 "href" => 'http://'
 ) , $atts));
 return '<a class="sgbtn_lv" href="' . $href . '" target="_blank" rel="nofollow">' . $content . '</a>';
}
add_shortcode('sgbtn_lv', 'sg_lv');
/*闪光按钮 结束*/

/*彩色按钮 开始*/
/*蓝色按钮*/
function toj($atts, $content=null) {
 extract(shortcode_atts(array("href" => 'http://'), $atts));
 return '<a class="bluebtn" href="' . $href . '" target="_blank" rel="nofollow">' .$content.'</a>';
}
add_shortcode('bb' , 'toj' );
/*黄色按钮*/
function tok($atts, $content=null) {
 extract(shortcode_atts(array("href" => 'http://'), $atts));
 return '<a class="yellowbtn" href="' . $href . '" target="_blank" rel="nofollow">' .$content.'</a>';
}
add_shortcode('yb' , 'tok' );
/*绿色按钮*/
function tol($atts, $content=null) {
 extract(shortcode_atts(array("href" => 'http://'), $atts));
 return '<a class="greenbtn" href="' . $href . '" target="_blank" rel="nofollow">' .$content.'</a>';
}
add_shortcode('gb' , 'tol' );

/*彩色按钮 结束*/

/*添加文本编辑自定义快捷标签按钮*/
 add_action('after_wp_tiny_mce', 'bolo_after_wp_tiny_mce');
 function bolo_after_wp_tiny_mce($mce_settings) {
 ?>
 <script type="text/javascript">
 QTags.addButton( 'sgbtn_blue', '蓝色闪光', "[sgbtn_blue href='']点击购买", "[/sgbtn_blue]" );
 QTags.addButton( 'sgbtn_red', '红色闪光', "[sgbtn_red href='']点击购买", "[/sgbtn_red]" );
 QTags.addButton( 'sgbtn_orange', '黄色闪光', "[sgbtn_orange href='']点击购买", "[/sgbtn_orange]" );
 QTags.addButton( 'sgbtn_lv', '绿色闪光', "[sgbtn_lv href='']点击购买", "[/sgbtn_lv]" );
 QTags.addButton( 'gb', '绿色按钮', "[gb href='']点击购买", "[/gb]" );
 QTags.addButton( 'bb', '蓝色按钮', "[bb href='']点击购买", "[/bb]" );
 QTags.addButton( 'yb', '黄色按钮', "[yb href='']点击购买", "[/yb]" );
 function bolo_QTnextpage_arg1() {
 }
 </script>
 <?php
 }
 /*添加文本编辑自定义快捷标签按钮 结束*/
 
 // 文章页添加展开收缩效果
function xcollapse($atts, $content = null){
	extract(shortcode_atts(array("title"=>""),$atts));
	return '<div style="margin: 0.5em 0;">
		    <div class="xControl">
			    <a href="javascript:void(0)" class="collapseButton xButton"><span class="xTitle">'.$title.'</span></a>
			    <div style="clear: both;"></div>
		    </div>
		<div class="xContent" style="display: none;">'.$content.'</div>
	</div>';
}
add_shortcode('collapse', 'xcollapse');

//添加展开/收缩快捷标签按钮
function appthemes_add_collapse() {
?>
    <script type="text/javascript">
        if ( typeof QTags != 'undefined' ) {
            QTags.addButton( 'collapse', '展开/收缩按钮', '[collapse title="点击展开 查看更多"]','[/collapse]' );
        } 
    </script>
<?php 
}
add_action('admin_print_footer_scripts', 'appthemes_add_collapse' );

//文章内外链添加go跳转
function the_content_nofollow($content){
    preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/',$content,$matches);
    if($matches){
        foreach($matches[2] as $val){
            if(strpos($val,'://')!==false && strpos($val,home_url())===false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i',$val)){
                $content=str_replace("href=\"$val\"", "href=\"".home_url()."/go/?url=$val\" ",$content);
            }
        }
    }
 return $content;
}
add_filter('the_content','the_content_nofollow',999);


//评论者链接添加go跳转
function add_redirect_comment_link($text = ''){
    $text=str_replace('href="', 'href="'.get_option('home').'/go/?url=', $text);
    return $text;
}
add_filter('get_comment_author_link', 'add_redirect_comment_link', 5);
add_filter('comment_text', 'add_redirect_comment_link', 99);


// WordPress 添加评论之星
 function get_author_class($comment_author_email,$user_id){
 global $wpdb;
 $author_count = count($wpdb->get_results(
 "SELECT comment_ID as author_count FROM $wpdb->comments WHERE comment_author_email = '$comment_author_email' "));
 // 如果不需要管理员显示VIP标签，就把下面一行的 // 去掉
 // $adminEmail = get_option('admin_email');if($comment_author_email ==$adminEmail) return;
 if($author_count>=1 && $author_count<20)
 echo '<a class="vip1" title="评论达人 LV.1"></a>';
 else if($author_count>=20 && $author_count<40)
 echo '<a class="vip2" title="评论达人 LV.2"></a>';
 else if($author_count>=40 && $author_count<80)
 echo '<a class="vip3" title="评论达人 LV.3"></a>';
 else if($author_count>=80 && $author_count<160)
 echo '<a class="vip4" title="评论达人 LV.4"></a>';
 else if($author_count>=160 && $author_count<320)
 echo '<a class="vip5" title="评论达人 LV.5"></a>';
 else if($author_count>=320 && $author_count<640)
 echo '<a class="vip6" title="评论达人 LV.6"></a>';
 else if($author_count>=640)
 echo '<a class="vip7" title="评论达人 LV.7"></a>';
}

require get_stylesheet_directory() . '/ua-show.php';


//集成auto-highslide灯箱插件
add_filter('the_content', 'addhighslideclass_replace');
function addhighslideclass_replace ($content)
{   global $post;
	$pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
    $replacement = '<a$1href=$2$3.$4$5 class="highslide-image" onclick="return hs.expand(this);"$6>$7</a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}


//图片添加alt属性
function img_alt( $imgalt ){
        global $post;
        $title = $post->post_title;
        $imgUrl = "<img\s[^>]*src=(\"??)([^\" >]*?)\\1[^>]*>";
        if(preg_match_all("/$imgUrl/siU",$imgalt,$matches,PREG_SET_ORDER)){
                if( !empty($matches) ){
                        for ($i=0; $i < count($matches); $i++){
                                $tag = $url = $matches[$i][0];
                                $judge = '/alt=/';
                                preg_match($judge,$tag,$match,PREG_OFFSET_CAPTURE);
                                if( count($match) < 1 )
                                $altURL = ' alt="'.$title.'" ';
                                $url = rtrim($url,'>');
                                $url .= $altURL.'>';
                                $imgalt = str_replace($tag,$url,$imgalt);
                        }
                }
        }
        return $imgalt;
}

add_filter( 'the_content','img_alt');

//说说功能
function my_custom_shuoshuo_init() { 
	$labels = array( 
	'name' => '说说',
	'singular_name' => '说说', 
	'all_items' => '所有说说',
	'add_new' => '发表说说', 
	'add_new_item' => '撰写新说说',
	'edit_item' => '编辑说说', 
	'new_item' => '新说说', 
	'view_item' => '查看说说', 
	'search_items' => '搜索说说', 
	'not_found' => '暂无说说', 
	'not_found_in_trash' => '没有已遗弃的说说', 
	'parent_item_colon' => '',
	'menu_name' => '说说'
	); 
	$args = array( 
	'labels' => $labels, 
	'public' => true, 
	'publicly_queryable' => true, 
	'show_ui' => true, 
	'show_in_menu' => true, 
	'query_var' => true, 
	'rewrite' => true, 
	'capability_type' => 'post', 
	'has_archive' => true, 
	'hierarchical' => false, 
	'menu_position' => null, 
	'supports' => array('title','editor','author') 
	); 
	register_post_type('shuoshuo',$args); 
}
add_action('init', 'my_custom_shuoshuo_init'); 


//添加@评论者功能
function qgg_comment_add_at( $comment_text, $comment = '') {
  if( $comment->comment_parent > 0) {
    $comment_text = '@<a href="'.get_comment_author_url( $comment->comment_parent ) . '">'.get_comment_author( $comment->comment_parent ) . '</a> ' . $comment_text;
  }

  return $comment_text;
}
add_filter( 'comment_text' , 'qgg_comment_add_at', 20, 2);

//显示网站运营版权时间
function auto_copyright(){
    global $wpdb;
    $first = $wpdb->get_results(" 
    SELECT user_registered
    FROM   $wpdb->users  
    ORDER BY  ID ASC 
    LIMIT 0,1
    ");
    $output = '';
    $current = date(Y);
    if ($first) {
        $first = date(Y, strtotime($first[0]->user_registered));
        $copyright = "&copy; " . $first;
        if ($first != $current) {
            $copyright .= '-' . $current;
        }
        $output = $copyright;
    }
    echo $output;
}