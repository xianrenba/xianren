<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name() {

	// Change this to use your theme slug
	return 'DUX';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'options_framework_theme'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	// Test data
	$test_array = array(
		'one' => __('One', 'options_framework_theme'),
		'two' => __('Two', 'options_framework_theme'),
		'three' => __('Three', 'options_framework_theme'),
		'four' => __('Four', 'options_framework_theme'),
		'five' => __('Five', 'options_framework_theme')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one' => __('French Toast', 'options_framework_theme'),
		'two' => __('Pancake', 'options_framework_theme'),
		'three' => __('Omelette', 'options_framework_theme'),
		'four' => __('Crepe', 'options_framework_theme'),
		'five' => __('Waffle', 'options_framework_theme')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment'=>'scroll' );

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55' );

	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','12','14','16','20' ),
		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name.'=>' .$category->cat_ID;
	}
	
	//允许投稿分类
	$options_cat = array();
	$options_cat_obj = get_categories(array('hide_empty'=>false));
	foreach ($options_cat_obj as $cat) {
		$options_cat[$cat->cat_ID] = $cat->cat_name.'=>' .$cat->cat_ID;
	}
	
	//社区分类循环
	$options_forum = array();
	$options_forum_terms = get_categories(array('taxonomy'=>'forum_cat','hide_empty'=>false));
	foreach ($options_forum_terms as $forum) {
		$options_forum[$forum->cat_ID] = $forum->cat_name.'=>' .$forum->cat_ID;
	}

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}


	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	// $options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	$options_linkcats = array();
	$options_linkcats_obj = get_terms('link_category');
	foreach ( $options_linkcats_obj as $tag ) {
		$options_linkcats[$tag->term_id] = $tag->name;
	}

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/img/';
	$adsdesc =  __('可添加任意广告联盟代码或自定义代码', 'haoui');
	$nnn = ' / ';
	$rrr = ' - ';
	$home = home_url();
	$name = get_bloginfo('name');
	$notfly = _url_for('mqpaynotify');

	$options = array();

	// ======================================================================================================================
	$options[] = array(
		'name' => __('基本', 'haoui'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Logo', 'haoui'),
		'id' => 'logo_src',
		'desc' => __('Logo不会做？去轻语博客求助！Logo 最大高：32px；建议尺寸：140*32px 格式：png', 'haoui'),
		'std' => 'https://www.qyblog.cn/wp-content/uploads/2016/11/png.png',
		'type' => 'upload');
		
	$options[] = array(
		'name' => __('Logo扫光', 'haoui').' (v9.1+)',
		'id' => 's-lights',
		'desc' => __('开启后logo会有扫光效果', 'haoui'),
		'std' => false,
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('后台登录页图片', 'haoui'),
		'id' => 'bg_img',
		'desc' => __('后台登录登录页背景', 'haoui'),
		'std' => 'https://www.qyblog.cn/wp-content/uploads/2019/12/timg.jpg',
		'type' => 'upload');
		
    $options[] = array(
		'name' => __('头部风格', 'haoui').' (v4.0+)',
		'id' => 'header_style',
		'std' => "2",
		'type' => "select",
		'desc' => __("提供两种头部风格选择，保存后生效。", 'haoui'),
		'options' => array(
			'2' => __('风格1', 'haoui'),
			'1' => __('风格2', 'haoui')
		));

    $options[] = array(
		'name' => __('布局', 'haoui'),
		'id' => "layout",
		'std' => "2",
		'type' => "images",
        'desc' => __("3种布局供选择，点击选择你喜欢的布局方式，保存后前端展示会有所改变。", 'haoui'),
		'options' => array(
			'1' => $imagepath . '1col.png',
            '2' => $imagepath . '2cr.png',
			'3' => $imagepath . '2cl.png'	
		));

	$options[] = array(
		'name' => __("主题风格", 'haoui'),
		'desc' => __("14种颜色供选择，点击选择你喜欢的颜色，保存后前端展示会有所改变。", 'haoui'),
		'id' => "theme_skin",
		'std' => "FF5E52",
		'type' => "colorradio",
		'options' => array(
		    'FF5E52' => 100,
			'45B6F7' => 1,
			'2CDB87' => 2,
			'00D6AC' => 3,
			'16C0F8' => 4,
			'EA84FF' => 5,
			'FDAC5F' => 6,
			'FD77B2' => 7,
			'76BDFF' => 8,
			'C38CFF' => 9,
			'FF926F' => 10,
			'8AC78F' => 11,
			'C7C183' => 12,
			'555555' => 13
		)
	);

	$options[] = array(
		'id' => 'theme_skin_custom',
		'std' => "",
		'desc' => __('不喜欢上面提供的颜色，你好可以在这里自定义设置，如果不用自定义颜色清空即可（默认不用自定义）', 'haoui'),
		'type' => "color");
  
  	$options[] = array(
		'name' => __('上传文件重命名', 'haoui'),
		'id' => 'newfilename',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));
  
  	$options[] = array(
		'name' => __('文章内容、列表描述和侧边栏文章标题两端对齐', 'haoui'),
		'id' => 'text_justify_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
		
	$options[] = array(
		'name' => __('网页最大宽度', 'haoui'),
		'id' => 'site_width',
		'std' => 1200,
		'class' => 'mini',
		'desc' => __('默认：1200，单位：px（像素）', 'haoui'),
		'type' => 'text');
			
	$options[] = array(
		'name' => __('底部友情链接', 'haoui').' (v1.5+)',
		'id' => 'flinks_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('（开启后会在页面底部增加一个链接模块）', 'haoui'));


	$options[] = array(
		// 'name' => __('底部友情链接只在首页', 'haoui'),
		'id' => 'flinks_home_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('只在首页开启', 'haoui'));

	$options[] = array(
		// 'name' => __('底部友情链接链接分类', 'haoui'),
		'id' => 'flinks_cat',
		'options' => $options_linkcats,
		'desc' => __('选择一个底部友情链接的链接分类', 'haoui'),
		'type' => 'select');


	$options[] = array(
		'name' => __('jQuery底部加载', 'haoui'),
		'id' => 'jquery_bom',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('（可提高页面内容加载速度，但部分依赖jQuery的插件可能失效）', 'haoui'));


	/*$options[] = array(
		'name' => __('Gravatar 头像获取', 'haoui'),
		'id' => 'gravatar_url',
		'std' => "ssl",
		'type' => "radio",
		'options' => array(
			'no' => __('原有方式', 'haoui'),
			'ssl' => __('从Gravatar官方ssl获取', 'haoui'),
			'duoshuo' => __('从多说服务器获取', 'haoui')
		));*/

	/*$options[] = array(
		'name' => __('JS文件托管（可大幅提速JS加载）', 'haoui'),
		'id' => 'js_outlink',
		'std' => "no",
		'type' => "radio",
		'options' => array(
			'no' => __('不托管', 'haoui'),
			'baidu' => __('百度', 'haoui'),
			'360' => __('360', 'haoui'),
			'he' => __('框架来源站点（分别引入jquery和bootstrap官方站点JS文件）', 'haoui')
		));*/

	$options[] = array(
		'name' => __('网站整体变灰', 'haoui'),
		'id' => 'site_gray',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('（支持IE、Chrome，基本上覆盖了大部分用户，不会降低访问速度）', 'haoui'));

	$options[] = array(
		'name' => __('分类url去除category字样', 'haoui'),
		'id' => 'no_categoty',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('（主题已内置no-category插件功能，请不要安装插件；开启后请去设置-固定连接中点击保存即可）', 'haoui'));

	$options[] = array(
		'name' => __('品牌文字', 'haoui'),
		'id' => 'brand',
		'std' => "欢迎光临\n我们一直在努力",
		'desc' => __('显示在Logo旁边的两个短文字，请换行填写两句文字（短文字介绍）', 'haoui'),
		'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');

	$options[] = array(
		'name' => __('网站底部信息', 'haoui'),
		'id' => 'footer_seo',
		'std' => '<a href="'.site_url('/sitemap.xml').'">'.__('网站地图', 'haoui').'</a>'."\n",
		'desc' => __('备案号可写于此，网站地图可自行使用sitemap插件自动生成', 'haoui'),
		'type' => 'textarea');

	$options[] = array(
		'name' => __('百度自定义站内搜索', 'haoui'),
		'id' => 'search_baidu',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'id' => 'search_baidu_code',
		'std' => '',
		'desc' => __('此处存放百度自定义站内搜索代码，请自行去 http://zn.baidu.com/ 设置并获取', 'haoui'),
		'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');

	$options[] = array(
		'name' => __('PC端滚动时导航固定', 'haoui').'  (v1.3+)',
		'id' => 'nav_fixed',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('由于网址导航左侧菜单的固定，故对网址导航页面无效', 'haoui'));

	$options[] = array(
		'name' => __('新窗口打开文章', 'haoui'),
		'id' => 'target_blank',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));


	$options[] = array(
		'name' => __('分页无限加载页数', 'haoui'),
		'id' => 'ajaxpager',
		'std' => 5,
		'class' => 'mini',
		'desc' => __('为0时表示不开启该功能', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('列表模式', 'haoui'),
		'id' => 'list_type',
		'std' => "thumb",
		'type' => "radio",
		'options' => array(
			'thumb' => __('图文模式（缩略图尺寸：440*300px，默认已自动裁剪）', 'haoui'),
			'text' => __('文字模式 ', 'haoui'),
			'thumb_if_has' => __('图文模式，无特色图时自动转换为文字模式 ', 'haoui').' (v1.6+)',
		));
	$options[] = array(
		'name' => __('列表购买按钮', 'haoui').' (v9.1+)',
		'id' => 'buy_btm',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
	$options[] = array(
		'name' => __('列表中评论数靠右', 'haoui').' (v1.6+)',
		'id' => 'list_comments_r',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));
    $options[] = array(
		'name' => __('自动使用文章第一张图作为缩略图', 'haoui').' (v1.9+)',
		'id' => 'thumb_postfirstimg_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').'，特别注意：如果文章已经设置特色图像或外链缩略图输入，此功能将无效。');

	$options[] = array(
		'id' => 'thumb_postfirstimg_lastname',
		'std' => '',
		'desc' => __('自动缩略图后缀将自动加入文章第一张图的地址后缀之前。比如：文章中的第一张图地址是“aaa/bbb.jpg”，此处填写的字符是“-440x300”，那么缩略图的实际地址就变成了“aaa/bbb-440x300.jpg”。默认为空。', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('外链缩略图输入', 'haoui').' (v1.8+)',
		'id' => 'thumblink_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').' 开启后会在后台编辑文章时出现外链缩略图地址输入框，填写一个图片地址即可在文章列表中显示。注意：如果文章添加了特色图像，列表中显示的缩略图优先选择该特色图像。');
	
	$options[] = array(
		'name' => __('文章缩略图异步加载', 'haoui').' (v1.4+)',
		'id' => 'thumbnail_src',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启  开启可以提高网站速度，提升浏览体验，但对SEO不太友好', 'haoui'));
		
    $options[] = array(
        'id'   => 'thumbnail_cut',
        'name'  => __('Timthumb.php缩略图裁剪','haoui'),
        'type'  => 'checkbox',
        'std'   => false,
        'desc'  => __('采用Timthumb.php缩略图裁剪，见防止缩略图变形，提升视觉、加载速度。使用教程<a href="https://www.qyblog.cn/2017/04/1229.html" target="_blank">Timthumb.php教程</a>  ','haoui'));
        
    $options[] = array(
        'id'   => 'aliyun_osscat',
        'name'  => __('阿里云对象储存OSS裁剪','haoui'),
        'type'  => 'checkbox',
        'std'   => false,
        'desc'  => __('阿里云对象储存OSS裁剪，需要安装插件 aliyun-oss-support-master  防止缩略图变形，提升视觉、加载速度。','haoui'));

	/*$options[] = array(
		'id' => 'list_thumb_out',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('缩略图使用外链图片 （外链是没有缩略图的，所以不会是小图，浩子不建议外链图，但如果你的文章都是外链图片，这个可以帮你实现以上的列表模式） ', 'haoui'));*/

	/* $options[] = array(
		'name' => __('文章小部件开启', 'haoui'),
		'id' => 'post_plugin',
		'std' => array(
			'view' => 1,
			'comm' => 1,
			'date' => 1,
			'author' => 1,
			'cat' => 1
		),
		'type' => "multicheck",
		'options' => array(
			'view' => __('阅读量（无需安装插件）', 'haoui'),
			'comm' => __('列表评论数', 'haoui'),
			'date' => __('列表时间', 'haoui'),
			'author' => __('列表作者名', 'haoui'),
			'cat' => __('列表分类链接', 'haoui').'  (v1.3+)'
		)); */
		
	$options[] = array(
		'name' => __('禁用古腾堡编辑器', 'haoui'),
		'id' => 'disabled_block_editor',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启之后可以禁用古腾堡编辑器', 'haoui'));	
		
	$options[] = array(
		'name' => __('文章小部件开启', 'haoui'),
		'id' => 'post_plugin_view',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('阅读量（无需安装插件）', 'haoui'));

	$options[] = array(
		'id' => 'post_plugin_comm',
		'type' => "checkbox",
		'std' => true,
		'class' => 'op-multicheck',
		'desc' => __('列表评论数', 'haoui'));

	$options[] = array(
		'id' => 'post_plugin_date',
		'type' => "checkbox",
		'std' => true,
		'class' => 'op-multicheck',
		'desc' => __('列表时间', 'haoui'));

	$options[] = array(
		'id' => 'post_plugin_author',
		'type' => "checkbox",
		'std' => true,
		'class' => 'op-multicheck',
		'desc' => __('列表作者名', 'haoui'));

	$options[] = array(
		'id' => 'post_plugin_cat',
		'type' => "checkbox",
		'std' => true,
		'class' => 'op-multicheck',
		'desc' => __('列表分类链接', 'haoui'));

		
	$options[] = array(
		'name' => __('文章作者名加链接', 'haoui'),
		'id' => 'author_link',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('（列表和文章有作者的地方都会加上链接） ', 'haoui'));
        
	$options[] = array(
		'name' => __('分享功能', 'haoui').' (v1.8+)',
		'id' => 'share_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('分享代码', 'haoui').' (v1.8+)',
		'id' => 'share_code',
		'std' => '<div class="bdsharebuttonbox">
<span>分享到：</span>
<a class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
<a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
<a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
<a class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
<a class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
<a class="bds_bdhome" data-cmd="bdhome" title="分享到百度新首页"></a>
<a class="bds_tqf" data-cmd="tqf" title="分享到腾讯朋友"></a>
<a class="bds_youdao" data-cmd="youdao" title="分享到有道云笔记"></a>
<a class="bds_more" data-cmd="more">更多</a> <span>(</span><a class="bds_count" data-cmd="count" title="累计分享0次">0</a><span>)</span>
</div>
<script>
window._bd_share_config = {
    common: {
		"bdText"     : "",
		"bdMini"     : "2",
		"bdMiniList" : false,
		"bdPic"      : "",
		"bdStyle"    : "0",
		"bdSize"     : "24"
    },
    share: [{
        bdCustomStyle: "'. get_template_directory_uri() .'/css/share.css"
    }]
}
with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion="+~(-new Date()/36e5)];
</script>',
		'desc' => __('默认是百度分享代码，可以改成其他分享代码', 'haoui'),
		'settings' => array(
			'rows' => 8
		),
		'type' => 'textarea');
  
	$options[] = array(
		'name' => '关闭顶部Topbar'.' (v4.1+)',
		'id' => 'topbar_off',
		'type' => "checkbox",
		'std' => false,
		'desc' => '关闭');
	
	$options[] = array(
		'name' => '移动端显示侧边栏',
		'id' => 'm-sidebar',
		'type' => "checkbox",
		'std' => false,
		'desc' => '开启  开启后移动端将在底部显示侧边栏。');
  
	$options[] = array(
        'name' => __( '建站日期', 'haoui' ),
        'desc' => __('网站开放的日期, 使用`YYYY-mm-dd`格式', 'haoui'),
        'id' => 'site_open_date',
        'std' => date('Y-m-d'),//(new DateTime())->format('Y-m-d'),
        //'class' => 'mini',
        'type' => 'text'
    );
    
    $options[] = array(
        'name' => __( '网站token', 'haoui' ),
        'desc' => __('网站私有token，某些私有调用需要用到', 'haoui'),
        'id' => 'private_token',
        'std' => generateRandomStr(5),
        //'class' => 'mini',
        'type' => 'text'
    );
    
    $options[] = array(
		'name' => __( '刷新固定链接', 'theme-textdomain' ),
		'desc' => sprintf(__('如果你遭遇一些404访问错误，请点击 <a href="%1$s/m/refresh?token=%2$s" target="_blank">刷新固定链接规则</a>', 'tt'), $home, _hui('private_token')),
		'type' => 'info'  
	);

	$options[] = array(
		'name' => __('全站底部推广区', 'haoui'),
		'id' => 'footer_brand_s',
		'std' => true,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('推广标题', 'haoui'),
		'id' => 'footer_brand_title',
		'desc' => '建议20字内',
		'std' => '铭宇网络建站 专业 快捷',
		'type' => 'text');

	for ($i=1; $i <= 2; $i++) { 
		
	$options[] = array(
		'name' => __('按钮 ', 'haoui').$i,
		'id' => 'footer_brand_btn_text_'.$i,
		'desc' => '按钮文字',
		'std' => '联系我们',
		'type' => 'text');

	$options[] = array(
		'id' => 'footer_brand_btn_href_'.$i,
		'desc' => __('按钮链接', 'haoui'),
		'std' => 'http://www.minyuweb.com',
		'type' => 'text');

	$options[] = array(
		'id' => 'footer_brand_btn_blank_'.$i,
		'std' => true,
		'desc' => __('新窗口打开', 'haoui'),
		'type' => 'checkbox');

	}

/*
	$options[] = array(
		'name' => __('评论数只显示人为评论数量', 'haoui'),
		'id' => 'comment_number_remove_trackback',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__('（部分文章有trackback导致评论数的增加，这个可以过滤掉） ', 'haoui'));
*/

	// ======================================================================================================================
	$options[] = array(
		'name' => __('SEO', 'haoui'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('全站连接符', 'haoui'),
		'id' => 'connector',
		'desc' => __('一经选择，切勿更改，对SEO不友好，一般为“-”或“_”', 'haoui'),
		'std' => _hui('connector') ? _hui('connector') : '-',
		'type' => 'text',
		'class' => 'mini');
		
	$options[] = array(
		'name' => 'SEO标题(title)'.' (v4.0+)',
		'id' => 'hometitle',
		'std' => '',
		'desc' => '完全自定义的首页标题让搜索引擎更喜欢，该设置为空则自动采用后台-设置-常规中的“站点标题+副标题”的形式',
		'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');
	
	$options[] = array(
		'name' => __('首页关键字(keywords)', 'haoui'),
		'id' => 'keywords',
		'std' => '一个网站, 一个牛x的网站',
		'desc' => __('关键字有利于SEO优化，建议个数在5-10之间，用英文逗号隔开', 'haoui'),
		'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');

	$options[] = array(
		'name' => __('首页描述(description)', 'haoui'),
		'id' => 'description',
		'std' => __('本站是一个高端大气上档次的网站', 'haoui'),
		'desc' => __('描述有利于SEO优化，建议字数在30-70之间', 'haoui'),
		'settings' => array(
			'rows' => 3
		),
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('文章页自定义SEO', 'haoui'),
		'id' => 'post_keywords_description_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));
		
		
	// ======================================================================================================================
	$options[] = array(
		'name' => __('首页', 'haoui'),
		'type' => 'heading');

    $options[] = array(
		'name' => __( '首页显示模式', 'haoui' ),
		'desc' => __( '设置首页显示风格.', 'haoui' ),
		'id' => 'index-s',
		'std' => 'index-blog',
		'type' => 'select',
		'options' => array('index-blog' => __( '博客模式', 'theme-textdomain' ),'index-card' => __( '卡片模式', 'theme-textdomain' ),'index-cms' => __( 'CMS模式', 'haoui' ),)
	);
	
	$options[] = array(
		'name' => __( '卡片模式每页显示数量', 'haoui' ),
		'desc' => __( '首页为卡片模式时每页显示文章数', 'haoui' ),
		'id' => 'card-num',
		'std' => '12',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => __( 'CMS首页排除的分类', 'haoui' ),
		'desc' => __( '排除的分类ID将不会显示在首页，中间用英文逗号隔开例如:1,2,3', 'haoui' ),
		'id' => 'cmsundisplaycats',
		'std' => '',
		'type' => 'text'
	);
	
	
	$options[] = array(
		'name' => __( 'CMS自定义分类排序', 'haoui' ),
		'desc' => __( '自定义首页CMS分类排序，依次填写分类ID，中间用英文逗号隔开例如:1,2,3', 'haoui' ),
		'id' => 'example_text',
		'std' => '',
		'type' => 'text'
	);
  	$options[] = array(
		'name' => __('CMS模式底部显示最新文章', 'haoui'),
		'id' => 'cms_blog',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启后在CMS模式下，最底部会显示最新文章！', 'haoui')
    );
    foreach ($options_categories as $id => $name) {
        $options[] = array(
            'name' => sprintf(__('CMS首页分类模板样式 - %s', 'haoui'), $name),
            'desc' => __('选择该分类在CMS首页的展示的样式', 'haoui'),
            'id' => sprintf('cms_home_cat_style_%d', $id),
            'std' => 'catlist_bar_0',
            'type' => 'select',
            'options' => array(
                'catlist_bar_0' => __('半宽精简样式', 'haoui'),
                'catlist_bar_1' => __('全宽样式,顶部焦点图', 'haoui'),
                'catlist_bar_2' => __('全宽样式,左侧焦点图', 'haoui'),
                'catlist_bar_3' => __('全宽样式,右侧焦点图', 'haoui'),
                'catlist_bar_4' => __('全宽样式,三列三行', 'haoui'),
                'catlist_bar_5' => __('全宽样式,图文对称模板', 'haoui')
            )
        );
    }
    
    $options[] = array(
		'name' => __('首页不显示该分类下文章', 'haoui'),
		'id' => 'notinhome',
		'options' => $options_categories,
		'type' => 'multicheck');

	$options[] = array(
		'name' => __('首页不显示以下ID的文章', 'haoui'),
		'id' => 'notinhome_post',
		'std' => "11245\n12846",
		'desc' => __('每行填写一个文章ID,CMS模式下该设置无效', 'haoui'),
		'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');

	$options[] = array(
		'name' => __('首页最新发布标题', 'haoui'),
		'id' => 'index_list_title',
		'std' => __('最新发布', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('首页最新发布标题右侧', 'haoui'),
		'id' => 'index_list_title_r',
		'std' => '<a href="链接地址">显示文字</a><a href="链接地址">显示文字</a><a href="链接地址">显示文字</a><a href="链接地址">显示文字</a>',
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('首页热门排行', 'haoui'),
		'id' => 'most_list_s',
		'std' => true,
		'desc' => __('开启，在首页增加一个热门文章板块', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('热门排行', 'haoui').$rrr.__('模式', 'haoui').'(v5.3*)',
		'id' => 'most_list_style',
		'std' => "comment",
		'type' => "radio",
		'options' => array(
			'comment' => __('按文章评论数', 'haoui'),
			'view' => __('按文章阅读数', 'haoui'),
		));

	$options[] = array(
		'name' => __('热门排行', 'haoui').$rrr.__('标题', 'haoui'),
		'id' => 'most_list_title',
		'std' => __('一周热门排行', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('热门排行', 'haoui').$rrr.__('最近多少天内的文章', 'haoui'),
		'id' => 'most_list_date',
		'std' => 7,
		'class' => 'mini',
		'desc' => __('留空表示不限制时间', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('热门排行', 'haoui').$rrr.__('显示数量', 'haoui'),
		'id' => 'most_list_number',
		'std' => 5,
		'class' => 'mini',
		'type' => 'text');

		
	$options[] = array(
		'name' => __('首页显示专题模块', 'haoui'),
		'id' => 'home_topic',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
	
	$options[] = array(
		'name' => __('专题', 'haoui').$rrr.__('标题文字', 'haoui'),
		'id' => 'home_topic_title',
		'std' => __('专题列表', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('专题', 'haoui').$rrr.__('标题右侧描述', 'haoui'),
		'id' => 'home_topic_desc',
		'std' => '这里是填入专题列表描述',
		'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');
		
	$options[] = array(
		'name' => __('专题', 'haoui').$rrr.__('显示数量', 'haoui'),
		'id' => 'home_topic_num',
		'std' => 4,
		'class' => 'mini',
		'desc' => __('在首页显示专题的数量，建议使用4倍数', 'haoui'),
		'type' => 'text');
		
	$options[] = array(
		'name' => __('专题', 'haoui').$rrr.__('选择页面', 'haoui'),
		'id' => 'home_topic_page',
		'options' => $options_pages,
		'desc' => __('如果没有合适的页面作为专题页面，你需要去新建一个页面再来选择','haoui'),
		'type' => 'select');
	

		
	// ======================================================================================================================
	$options[] = array(
		'name' => __('文章页', 'haoui'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('面包屑导航', 'haoui'),
		'id' => 'breadcrumbs_single_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('面包屑导航 - 用“正文”替代标题', 'haoui'),
		'id' => 'breadcrumbs_single_text',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));	
  
    $options[] = array(
		'name' => __('热门文章', 'haoui'),
		'id' => 'post_hot_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui').__(' 开启后文章底部会显示4篇热门文章，根据浏览量显示', 'haoui'));
  
  	$options[] = array(
		'desc' => __('热门文章最近多少天内的文章，留空表示不限制时间', 'haoui'),
		'id' => 'post_hot_day',
		'std' => 30,
		'class' => 'mini',
		'type' => 'text');
	$options[] = array(
		'desc' => __('热门文章标题', 'haoui'),
		'id' => 'post_hot_title',
		'std' => __('热门文章', 'haoui'),
		'type' => 'text');
  
	$options[] = array(
		'name' => __('相关文章', 'haoui'),
		'id' => 'post_related_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
  
	$options[] = array(
		'id' => 'post_related_thumb_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启图文模式', 'haoui'));
  
	$options[] = array(
		'desc' => __('相关文章标题', 'haoui'),
		'id' => 'related_title',
		'std' => __('相关推荐', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'desc' => __('相关文章显示数量', 'haoui'),
		'id' => 'post_related_n',
		'std' => 8,
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => __('文章来源', 'haoui'),
		'id' => 'post_from_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
	
	$options[] = array(
		'id' => 'post_from_h1',
		'std' => __('来源：', 'haoui'),
		'desc' => __('来源显示字样', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'id' => 'post_from_link_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('来源加链接', 'haoui'));
  
  	$options[] = array(
		'name' =>__('全屏查看图片模式', 'haoui').'(v4.0+)',
		'id' => 'full_image',
		'desc' => __('开启', 'haoui'),
		'std' => true,
		'type' => "checkbox");
  
    $options[] = array(
		'name' =>__('启用文章列表页面滚动动画', 'haoui').'(v4.0+)',
		'id' => 'switch_wow',
		'desc' => __('开启', 'haoui'),
		'std' => true,
		'type' => "checkbox");

	$options[] = array(
		'name' => __('文章图片延时加载', 'haoui').'(v4.0+)',
		'id' => 'post_loading',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui').__(' 是否启用图片延迟加载', 'haoui'));
  
	$options[] = array(
		'name' => __('内容段落缩进', 'haoui').' (v1.3+)',
		'id' => 'post_p_indent_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__(' 开启后只对前台文章展示有效，对后台编辑器中的格式无效', 'haoui'));
		
	$options[] = array(
		'name' => __('文章二维码', 'haoui').'(v4.0+)',
		'id' => 'erweima_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__(' 开起后会生成文章二维码', 'haoui'));
		
	$options[] = array(
		'name' => __('网站禁止复制', 'haoui').'(v4.0+)',
		'id' => 'copy_b',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__(' 启用后访客无法使用右键复制', 'haoui'));
		
	$options[] = array(
		'name' => __('复制弹窗提醒', 'haoui').'(v4.0+)',
		'id' => 'copydialog_b',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui').__(' 启用后，访客复制之后会弹出提示弹窗', 'haoui'));

	/*$options[] = array(
		'name' => __('文章段落缩进', 'haoui'),
		'id' => 'post_p_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));*/
	$options[] = array(
		'name' => __('文章页阅读全文按钮', 'haoui'),
		'id' => 'p_readmore',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
  
	$options[] = array(
		'name' => __('文章页尾版权提示', 'haoui'),
		'id' => 'post_copyright_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));
		
	$options[] = array(
        'name' => '文章下载提示信息',
        'desc' => '文章下载面板里面的提示信息',
        'id' => 'git_dltable_b',
        'std' => '本站文件大多收集于互联网，如有版权问题，请联系博主及时删除！',
        'settings' => array(
			'rows' => 2
		),
		'type' => 'textarea');

	$options[] = array(
		'name' => __('文章页尾版权提示前缀', 'haoui'),
		'id' => 'post_copyright',
		'std' => __('未经允许不得转载：', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('文章页作者介绍栏目', 'haoui').' (v1.7+)',
		'id' => 'post_author',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui').__('（开启后会在文章页显示作者介绍栏目，作者介绍可以在用户中心或后台用户资料中添加）', 'haoui'));
  $options[] = array(
		'name' => __('打赏开关', 'haoui'),
		'id' => 'btn_shang',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui').$nnn.__('开启打赏按钮', 'haoui'));

    $options[] = array(
		'name' => '打赏（赞助）按钮设置',
		'desc' => '自定义按钮文字，留空则不显示弹窗',
		'id' => 'alipay_name',
		'std' => '打赏',
		'type' => 'text'
	);

	$options[] = array(
		'name' => '打赏：提示文字',
		'desc' => '自定义提示文字',
		'id' => 'alipay_t',
		'std' => '赞助本站',
		'type' => 'text'
	);

	$options[] = array(
		'name' => '打赏：弹窗标题',
		'desc' => '自定义弹窗标题文字，留空则不显示',
		'id' => 'alipay_h',
		'std' => '您可以选择一种方式赞助本站',
		'type' => 'text'
	);

	$options[] = array(
		'name' => '打赏：支付宝收款二维码',
		'desc' => '上传支付宝二维码图片（＜240px）',
		'id' => 'qr_a',
        "std" => "https://www.qyblog.cn/wp-content/uploads/2017/11/zhifubao.jpg",
		'type' => 'upload'
	);

	$options[] = array(
		'name' => '打赏：支付宝提示信息',
		'desc' => '自定义支付宝二维码图片文字说明，留空则不显示',
		'id' => 'alipay_z',
		'std' => '支付宝扫一扫赞助',
		'type' => 'text'
	);

	$options[] = array(
		'name' => '打赏：微信收款二维码',
		'desc' => '上传微信钱包二维码图片（＜250px）',
		'id' => 'qr_b',
        "std" => "https://www.qyblog.cn/wp-content/uploads/2017/11/wechat.jpg",
		'type' => 'upload'
	);

	$options[] = array(
		'name' => '打赏：微信提示信息',
		'desc' => '自定义微信钱包二维码图片文字说明，留空则不显示',
		'id' => 'alipay_w',
		'std' => '微信钱包扫描赞助',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => __('评论标题', 'haoui'),
		'id' => 'comment_title',
		'std' => __('评论', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('评论框默认字符', 'haoui'),
		'id' => 'comment_text',
		'std' => __('你的评论可以一针见血', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('评论提交按钮字符', 'haoui'),
		'id' => 'comment_submit_text',
		'std' => __('提交评论', 'haoui'),
		'type' => 'text');
   $options[] = array(
		'name' => __('评论小工具', 'haoui'),
		'id' => 'comment_tool',
		'std' => true,
        'desc' =>__('开启  开启后评论区将为用户提供表情、彩色字体、外链图片等小工具','haoui'),
		'type' => "checkbox",);
	
	
	// ======================================================================================================================
	$options[] = array(
		'name' => __('社区', 'haoui'),
		'type' => 'heading' );
		
	$options[] = array(
		'name' => __('问答社区必须登录才能查看', 'haoui'),
		'id' => 'forum_login',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	
	$options[] = array(
		'name' => __('问答社区顶部显示分类', 'haoui'),
		'id' => 'forum_top_cat',
        'std' => '',
		'options' => $options_forum,
		'type' => 'multicheck');
		
	$options[] = array(
		'name' => __('问答社区列表页面', 'haoui'),
		'id' => 'list_page',
		'desc' => '选择一个页面作为问答社区页面，如果没有请到页面->新建->选择问答页面模板',
		'options' => $options_pages,
		'type' => 'select');

	$options[] = array(
		'name' => __('问答社区新建问题页面', 'haoui'),
		'id' => 'forum_new_page',
		'desc' => '选择一个页面作为问答发帖页面，如果没有请到页面->新建->选择新建问答模板',
		'options' => $options_pages,
		'type' => 'select');

	$options[] = array(
        'name' =>__('每页显示帖子数量', 'haoui'),
		'id' => 'forum_list_number',
		'std' => 20,
		'type'=>'text',
		'desc'=>'');
		
	$options[] = array(
        'name' =>__('每页显示评论数量', 'haoui'),
		'id' => 'forum_comment_number',
		'std' => 20,
		'type'=>'text',
		'desc'=>'');  	
		
	$options[] = array(
		'name' => __('回复是否需要审核', 'haoui'),
		'id' => 'answer_moderation',
		'std' => '0',
		'options' => array('0'=>'不审核','1'=>'仅首次审核','2'=>'全部需要审核'),
		'type' => 'select');
		
	$options[] = array(
		'name' => __('问题是否需要审核', 'haoui'),
		'id' => 'question_moderation',
		'std' => '0',
		'options' => array('0'=>'不审核','1'=>'仅首次审核','2'=>'全部需要审核'),
		'type' => 'select');
		
	$options[] = array(
		'name' => __('有新问题邮件通知', 'haoui'),
		'id' => 'email_new',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('有新回复邮件通知', 'haoui'),
		'id' => 'email_answer',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('有新回复是否抄送给管理员', 'haoui'),
		'id' => 'email_answer_cc',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

    // ======================================================================================================================
	$options[] = array(
		'name' => __('商城', 'haoui'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('商品归档页别名', 'haoui'),
		'id' => 'store_archive_slug',
		'std' => 'store',
		'type' => 'text',
		'desc' => __('默认为store，即可以以<a href="'.$home.'/store" target="_blank">'.$home.'/store</a>链接访问商品归档页', 'haoui'));
	$options[] = array(
		'name' => __('商品页筛选', 'haoui'),
		'id' => 'shop_filter',
		'std' => false,
		'type' => 'checkbox',
		'desc' => __('开启后在商品页及分类页可以通过分类、价格筛选', 'haoui'));
	$options[] = array(
		'name' => __('商品页固定链接模式', 'haoui'),
		'id' => 'product_link_mode',
		'std' => 'post_id',
		'options' => array('post_name'=>'Postname','post_id'=>'PostID'),
		'type' => 'select',
		'desc' => __('', 'haoui'));
    $options[] = array(
		'name' => __('商品归档页横幅标题', 'haoui'),
		'id' => 'store_archive_title',
		'std' => 'WordPress商城',
		'type' => 'text',
		'desc' => __('可作为页面的title', 'haoui'));
    $options[] = array(
		'name' => __('商品归档页横幅子标题', 'haoui'),
		'id' => 'store_archive_subtitle',
		'std' => 'Themes - Service - Resource',
		'type' => 'text',
		'desc' => __('可作页面关键字', 'haoui'));
	$options[] = array(
		'name' => __('商品归档页描述', 'haoui'),
		'id' => 'store_archive_des',
		'std' => $name.'商城',
		'type' => 'text',
		'desc' => __('页面描述,SEO要素', 'haoui'));
	$options[] = array(
		'name' => __('商品分类链接前缀', 'haoui'),
		'id' => 'store_cat_pre',
		'std' => 'category',
		'type' => 'text',
		'desc' => __('', 'haoui'));
	$options[] = array(
		'name' => __('商品标签链接前缀', 'haoui'),
		'id' => 'store_tag_pre',
		'std' => 'tag',
		'type' => 'text',
		'desc' => __('', 'haoui'));
	$options[] = array(
		'name' => __('推广订单返现百分数', 'haoui'),
		'id' => 'aff_ratio',
		'std' => 10,
		'type' => 'text',
		'desc' => __('单位%，注意数值为10则代表返现10%', 'haoui'));
	$options[] = array(
		'name' => __('申请提现最低账户余额', 'haoui'),
		'id' => 'aff_discharge_lowest',
		'std' => 100,
		'type' => 'text',
		'desc' => __('账户余额达到一定值时方可提现', 'haoui'));
    $options[] = array(
		'name' => __('订单状态维护', 'haoui'),
		'id' => 'maintain_orders_deadline',
		'std' => 0,
      	'class' => 'mini',
		'type' => 'text',
		'desc' => __('自动关闭多少天以上未支付订单, 设置为0则不启用自动状态维护', 'haoui'));

    // ======================================================================================================================
	$options[] = array(
		'name' => __('支付接口', 'haoui'),
		'type' => 'heading');
  
  	$options[] = array(
		'name' => __('支付宝收款方式', 'haoui'),
		'id' => 'alipay_option',
		'std' => "alipay",
		'type' => "select",
		'options' => array(
			'alipay' => __('支付宝签约收款', 'haoui'),
			'f2fpay' => __('支付宝当面付', 'haoui'),
			'codepay' => __('支付宝码支付收款', 'haoui'),
			'alipay_jk' => __('支付宝免签收款', 'haoui'),
			'xhpay' => __('虎皮椒收款', 'haoui'),
		));
	$options[] = array(
		'name' => __('支付宝签约收款账', 'haoui'),
		'id' => 'alipay_account',
		'type' => 'text',
		'desc' => __('支付宝收款帐户邮箱,要收款必填并务必保持正确', 'haoui'));
	$options[] = array(
		'name' => __('', 'haoui'),
		'id' => 'alipay_id',
		'type' => 'text',
		'desc' => __('(mapi网关)支付宝商家身份ID', 'haoui'));
	$options[] = array(
		'name' => __('', 'haoui'),
		'std' => 'trade_create_by_buyer',
		'id' => 'alipay_sign_type',
		'options' => array('create_direct_pay_by_user'=>'即时到账','trade_create_by_buyer'=>'双功能收款','create_partner_trade_by_buyer'=>'担保交易'),
		'type' => 'select',
        'desc' => __('支付宝商家签约类型', 'haoui'));
    $options[] = array(
		'name' => __('', 'haoui'),
		'id' => 'alipay_key',
		'type' => 'text',
		'desc' => __('(mapi网关)支付宝商家身份Key, 请至<a href="https://b.alipay.com" target="_blank">支付宝商家服务</a>进行申请', 'haoui'));
		
		
	$options[] = array(
		'name' => __('支付宝当面付', 'haoui'),
        'desc' => __( 'APPID  申请地址：<a href="https://open.alipay.com/platform/home.htm" target="_blank">支付宝开放平台</a>', 'haoui' ),
        'id' => 'f2f_appid',
        'std' => '',
        'type' => 'text' 
    );
    $options[] = array(
        'desc' => __( '应用私钥', 'haoui' ),
        'id' => 'rsa_private_key',
        'std' => '',
        'settings' => array(
			'rows' => 3
		),
		'type' => 'textarea' 
    );
    
    $options[] = array(
        'desc' => __( '支付宝公钥', 'haoui' ),
        'id' => 'alipay_public_key',
        'std' => '',
        'settings' => array(
			'rows' => 3
		),
		'type' => 'textarea' 
    );
  
    $options[] = array(
		'name' => __('支付宝免签收款', 'haoui'),
		'id' => 'zfbjk_uid',
		'type' => 'text',
		'desc' => __('商户ID 此接口的安全稳定性，请使用者自行把握，我们只提供技术集成，接口申请地址：<a href="http://t.cn/ELEywQ4" target="_blank">点击查看</a>', 'haoui'));
  
    $options[] = array(
		'name' => __('', 'haoui'),
		'id' => 'zfbjk_key',
		'type' => 'text',
		'desc' => __('支付宝免签商户秘钥', 'haoui'));
  
    $options[] = array(
		'name' => __('', 'haoui'),
		'id' => 'zfbjk_alipay',
		'type' => 'text',
		'desc' => __('支付宝收款账号', 'haoui'));
    $options[] = array(
		'name' => __('', 'haoui'),
		'id' => 'zfbjk_name',
		'type' => 'text',
		'desc' => __('收款人姓名,用于校验真实姓名', 'haoui'));
  
	$options[] = array(
		'name' => '',
		'desc' => '支付宝收款二维码,上传支付宝二维码图片',
		'id' => 'alipay_qrcode',
		'type' => 'upload'
	);
  	$options[] = array(
		'name' => __( '支付宝免签通知地址', 'haoui' ),
		'desc' => __( $notfly, 'haoui' ),
		'type' => 'info'
    );
    
    $options[] = array(
		'name' => __('码支付收款', 'haoui'),
		'id' => 'codepay_id',
		'type' => 'text',
		'desc' => __('码支付ID  申请地址：<a href="https://codepay.fateqq.com/i/50919" target="_blank">码支付</a>', 'haoui'));
  
	$options[] = array(
		'name' => '',
		'desc' => '支付密匙',
		'id' => 'codepay_key',
		'type' => 'text'
	);
	
	$options[] = array(
        'name' => __( '虎皮椒收款(支付宝)', 'haoui' ),
		'desc' => __( 'Appid    申请地址：<a href="https://www.xunhupay.com/" target="_blank">虎皮椒</a>', 'haoui' ),
        'id' => 'ali_xhpay_appid',
        'std' => '',
        'type' => 'text' 
    );
	
	$options[] = array(
        'desc' => __( '密匙', 'haoui' ),
        'id' => 'ali_xhpay_secret',
        'std' => '',
        'type' => 'text' 
    );
	
	$options[] = array(
		'name' => __('微信收款方式', 'haoui'),
		'id' => 'wxpay_option',
		'std' => "alipay",
		'type' => "select",
		'options' => array(
			'wxpay' => __('微信签约收款', 'haoui'),
			'payjs' => __('Payjs收款', 'haoui'),
			'xhpay' => __('虎皮椒收款', 'haoui'),
		));


    $options[] = array(
        'name' => __( '微信收款', 'haoui' ),
		'desc' => __( '微信商户号(MCHID)', 'haoui' ),
        'id' => 'wechat_mchid',
        'std' => '',
        'type' => 'text' 
    );
	
	$options[] = array(
        'desc' => __( 'APPID', 'haoui' ),
        'id' => 'wechat_appid',
        'std' => '',
        'type' => 'text' 
    );

    $options[] = array(
        'desc' => __( '商户支付密钥(KEY)    设置地址：<a href="https://pay.weixin.qq.com/index.php/account/api_cert" target="_blank">微信支付</a> ，建议为32位字符串', 'haoui' ),
        'id' => 'wechat_key',
        'std' => '',
        'settings' => array(
			'rows' => 3
		),
		'type' => 'textarea' 
    );
	
	/*$options[] = array(
        'desc' => __( '公众帐号Secret', 'haoui' ),
        'id' => 'wechat_secret',
        'std' => '',
        'type' => 'text' 
    );*/
	
	$options[] = array(
        'name' => __( 'Payjs签约收款', 'haoui' ),
		'desc' => __( '商户号    申请地址：<a href="https://payjs.cn/ref/DXQVLZ" target="_blank">payjs</a>', 'haoui' ),
        'id' => 'payjs_id',
        'std' => '',
        'type' => 'text' 
    );
	
	$options[] = array(
        'desc' => __( '签约密匙', 'haoui' ),
        'id' => 'payjs_key',
        'std' => '',
        'type' => 'text' 
    );
    
    $options[] = array(
        'name' => __( '虎皮椒收款(微信)', 'haoui' ),
		'desc' => __( 'Appid    申请地址：<a href="https://www.xunhupay.com/" target="_blank">虎皮椒</a>', 'haoui' ),
        'id' => 'xhpay_appid',
        'std' => '',
        'type' => 'text' 
    );
	
	$options[] = array(
        'desc' => __( '密匙', 'haoui' ),
        'id' => 'xhpay_secret',
        'std' => '',
        'type' => 'text' 
    );
	
	
	// ======================================================================================================================
	$options[] = array(
		'name' => __('邮件', 'haoui'),
		'type' => 'heading');
	$options[] = array(
		'name' => __('启用SMTP发信', 'haoui'),
		'id' => 'smtp_switch',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('如果主机商禁用了PHP Mail()，请使用SMTP发信，仍有任何问题请参考<a target="_blank" href="https://www.qyblog.cn/2019/03/2136.html">虚拟主机SMTP发信</a>', 'haoui'));
	$options[] = array(
		'name' => __('SMTP发信服务器', 'haoui'),
		'id' => 'smtp_host',
		'type' => 'text',
		'desc' => __('SMTP发信服务器，例如smtp.163.com', 'haoui'));
	$options[] = array(
		'name' => __('SMTP发信服务器端口', 'haoui'),
		'id' => 'smtp_port',
		'type' => 'text',
		'desc' => __('SMTP发信服务器端口，不开启SSL时一般默认25，开启SSL一般为465', 'haoui'));
	$options[] = array(
		'name' => __('SMTP发信服务器SSL连接', 'haoui'),
		'id' => 'smtp_ssl',
		'type' => 'checkbox',
		'desc' => __('SMTP发信服务器SSL连接，请相应修改端口', 'haoui'));
	$options[] = array(
		'name' => __('SMTP发信用户名', 'haoui'),
		'id' => 'smtp_account',
		'type' => 'text',
		'desc' => __('SMTP发信用户名，一般为完整邮箱号', 'haoui'));
	$options[] = array(
		'name' => __('SMTP帐号密码', 'haoui'),
		'id' => 'smtp_pass',
		'type' => 'password',
		'desc' => __('', 'haoui'));	
	$options[] = array(
		'name' => __('SMTP发信人昵称', 'haoui'),
		'id' => 'smtp_name',
		'type' => 'text',
		'desc' => __('', 'haoui'));	
	$options[] = array(
		'name' => __('评论邮件提醒', 'haoui'),
		'id' => 'comment_reply_mail',
		'type' => 'checkbox',
		'desc' => __('开启评论回复邮件提醒', 'haoui'));
	$options[] = array(
		'name' => __('登录成功邮件提醒', 'haoui'),
		'id' => 'login_mail',
		'type' => 'checkbox',
		'desc' => __('登录成功时邮件提醒管理员邮箱', 'haoui'));
	$options[] = array(
		'name' => __('登录错误邮件提醒', 'haoui'),
		'id' => 'login_error_mail',
		'type' => 'checkbox',
		'desc' => __('登陆错误时邮件提醒管理员邮箱', 'haoui'));
	$options[] = array(
		'name' => __('邮件模板Logo', 'haoui'),
		'id' => 'logo_img',
		'type' => 'upload',
		'desc' => __('邮件模板的站点Logo图像，留空则采用站点标题', 'haoui'));
		
	// ======================================================================================================================
	$options[] = array(
		'name' => __('积分', 'haoui'),
		'type' => 'heading');
    $options[] = array(
		'name' => __('现金积分兑换比率', 'haoui'),
		'id' => 'exchange_ratio',
		'type' => 'text',
		'std' => 100,
		'desc' => __('1元所能兑换的积分数，用于计算积分充值', 'haoui'));
    $options[] = array(
		'name' => __('新用户注册奖励', 'haoui'),
		'id' => 'new_reg_credit',
		'type' => 'text',
		'std' => 10,
		'desc' => __('新用户首次注册奖励积分', 'haoui'));
    $options[] = array(
		'name' => __('每日签到积分奖励', 'haoui'),
		'id' => 'daily_sign_credit',
		'type' => 'text',
		'std' => 10,
		'desc' => __('', 'haoui'));
    $options[] = array(
		'name' => __('评论一次奖励积分', 'haoui'),
		'id' => 'comment_credit',
		'type' => 'text',
		'std' => 5,
		'desc' => __('', 'haoui'));	
    $options[] = array(
		'name' => __('每日评论奖励积分次数', 'haoui'),
		'id' => 'comment_credit_times',
		'type' => 'text',
		'std' => 20,
		'desc' => __('每天通过评论获得积分的最大次数，超出后将不再获得积分', 'haoui'));			
    $options[] = array(
		'name' => __('文章互动可得积分,点击喜欢', 'haoui'),
		'id' => 'like_article_credit',
		'type' => 'text',
		'std' => 5,
		'desc' => __('', 'haoui'));		
    $options[] = array(
		'name' => __('文章互动可得积分每日次数', 'haoui'),
		'id' => 'like_article_credit_times',
		'type' => 'text',
		'std' => 5,
		'desc' => __('', 'haoui'));	
	$options[] = array(
		'name' => __('投稿一次奖励积分', 'haoui'),
		'id' => 'contribute_credit',
		'type' => 'text',
		'std' => 100,
		'desc' => __('', 'haoui'));		
    $options[] = array(
		'name' => __('每日投稿获得积分次数', 'haoui'),
		'id' => 'contribute_credit_times',
		'type' => 'text',
		'std' => 5,
		'desc' => __('每天通过投稿获得积分的最大次数，超出后将不再获得积分', 'haoui'));
    $options[] = array(
		'name' => __('访问推广一次奖励积分', 'haoui'),
		'id' => 'aff_visit_credit',
		'type' => 'text',
		'std' => 10,
		'desc' => __('通过专属推广链接推广用户访问一次奖励积分', 'haoui'));
	$options[] = array(
		'name' => __('每日访问推广奖励积分次数', 'haoui'),
		'id' => 'aff_visit_credit_times',
		'type' => 'text',
		'std' => 10,
		'desc' => __('每天通过访问推广获得积分的最大次数，超出后将不再获得积分', 'haoui'));
	$options[] = array(
		'name' => __('注册推广一次奖励积分', 'haoui'),
		'id' => 'aff_reg_credit',
		'type' => 'text',
		'std' => 20,
		'desc' => __('通过专属推广链接推广新用户注册一次所奖励积分', 'haoui'));
	$options[] = array(
		'name' => __('每日注册推广奖励积分次数', 'haoui'),
		'id' => 'aff_reg_credit_times',
		'type' => 'text',
		'std' => 10,
		'desc' => __('每天通过注册推广获得积分的最大次数，超出后将不再获得积分', 'haoui'));
	$options[] = array(
		'name' => __('发布资源被下载奖励积分', 'haoui'),
		'id' => 'source_download_credit',
		'type' => 'text',
		'std' => 10,
		'desc' => __('作者发布的资源被用户下载后获得积分奖励，不限次数', 'haoui'));	

    


	// ======================================================================================================================
	$options[] = array(
		'name' => __('会员中心', 'haoui'),
		'type' => 'heading' );
    $options[] = array(
        'name' =>__('邮箱注册验证', 'haoui'),
		'id' => '_email_oauth',
		'std' => true,
      	'desc' => __('开启后注册需要邮箱验证激活账户链接，能够提高用户质量，预防机器恶意注册！', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
        'name' =>__('注册验证码', 'haoui'),
		'id' => 'reg_captcha',
		'std' => true,
      	'desc' => __('开启注册验证码', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'id' => 'user_page_s',
		'std' => true,
		'desc' => __('开启前台登录', 'haoui'),
		'type' => 'checkbox'); 
    $options[] = array(
	    'name' =>__('开启个人会员中心', 'haoui'),
		'id' => 'open_ucenter',
		'std' => true,
		'desc' => __('开启个人中心，丰富用户个人页功能', 'haoui'),
		'type' => 'checkbox'); 
	$options[] = array(
		'id' => 'user_on_notice_s',
		'std' => true,
		'desc' => __('在首页公告栏显示会员模块', 'haoui'),
		'type' => 'checkbox'
	    );
    $options[] = array(
        'name' =>__('月费会员价格', 'haoui'),
		'id' => 'monthly_mb_price',
		'std' => 5,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('季费会员价格', 'haoui'),
		'id' => 'quarterly_mb_price',
		'std' => 12,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('年费会员价格', 'haoui'),
		'id' => 'annual_mb_price',
		'std' => 45,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('终身会员价格', 'haoui'),
		'id' => 'life_mb_price',
		'std' => 120,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('月费会员默认折扣', 'haoui'),
		'id' => 'monthly_mb_disc',
		'std' => 0.95,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('季费会员默认折扣', 'haoui'),
		'id' => 'quarterly_mb_disc',
		'std' => 0.90,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('年费会员默认折扣', 'haoui'),
		'id' => 'annual_mb_disc',
		'std' => 0.85,
		'type'=>'text',
		'desc'=>'');
	$options[] = array(
        'name' =>__('终身会员默认折扣', 'haoui'),
		'id' => 'life_mb_disc',
		'std' => 0.75,
		'type'=>'text',
		'desc'=>'');
		
	/* $options[] = array(
		'name' => __('选择会员中心页面', 'haoui'),
		'id' => 'user_page',
		'desc' => '如果没有合适的页面作为会员中心，你需要去新建一个页面再来选择',
		'options' => $options_pages,
		'type' => 'select'); */

	$options[] = array(
		'name' => __('选择找回密码页面', 'haoui'),
		'id' => 'user_rp',
		'desc' => '如果没有合适的页面作为找回密码页面，你需要去新建一个页面再来选择',
		'options' => $options_pages,
		'type' => 'select');

	$options[] = array(
		'name' => __('允许用户发布文章', 'haoui').' (v1.6+)',
		'id' => 'tougao_s',
		'std' => true,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('有新投稿邮件通知', 'haoui').' (v1.3+)',
		'id' => 'tougao_mail_send',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'desc' => __('投稿通知接收邮箱', 'haoui').' (v1.3+)',
		'id' => 'tougao_mail_to',
		'type' => 'text');
  
   $options[] = array(
		'name' => __('允许投稿分类', 'haoui'),
		'id' => 'can_post_cat',
        'std' => '',
		'options' => $options_cat,
		'type' => 'multicheck');
/*
	$options[] = array(
		'name' => __('禁止昵称关键字', 'haoui'),
		'desc' => __('一行一个关键字，用户昵称将不能使用或包含这些关键字，对编辑以下职位有效', 'haoui'),
		'id' => 'user_nickname_out',
		'std' => "赌博\n博彩\n彩票\n性爱\n色情\n做爱\n爱爱\n淫秽\n傻b\n妈的\n妈b\nadmin\ntest",
		'type' => 'textarea');*/

    $options[] = array(
		'name' => __('QQ快速登录', 'haoui'),
		'id' => 'um_open_qq',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
    $options[] = array(
		'desc' => __('QQ开放平台ID！请至<a href="https://connect.qq.com" target="_blank">QQ互联</a>进行申请', 'haoui'),
		'id' => 'um_open_qq_id',
		'type' => 'text');
	$options[] = array(
		'desc' => __('QQ开放平台KEY', 'haoui'),
		'id' => 'um_open_qq_key',
		'type' => 'text');
	$options[] = array(
		'name' => __( 'QQ互联回调地址', 'haoui' ),
		'desc' => __( home_url('oauth/qq'), 'haoui' ),
		'type' => 'info');
    $options[] = array(
		'name' => __('微博快速登录', 'haoui'),
		'id' => 'um_open_weibo',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
    $options[] = array(
		'desc' => __('微博开放平台KEY！请至<a href="https://open.weibo.com/" target="_blank">新浪微博开放平台</a>进行申请', 'haoui'),
		'id' => 'um_open_weibo_key',
		'type' => 'text');
	$options[] = array(
		'desc' => __('微博开放平台SECRET', 'haoui'),
		'id' => 'um_open_weibo_secret',
		'type' => 'text');
  	$options[] = array(
		'name' => __( '微博开放平台回调地址', 'haoui' ),
		'desc' => __( home_url('oauth/weibo'), 'haoui' ),
		'type' => 'info');
	$options[] = array(
		'name' => __('微信快速登录', 'haoui'),
		'id' => 'um_open_weixin',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
    $options[] = array(
		'desc' => __('微信开放平台APPID  请至<a href="https://open.weixin.qq.com/" target="_blank">微信开放平台</a>进行申请', 'haoui'),
		'id' => 'um_open_weixin_appid',
		'type' => 'text');
	$options[] = array(
		'desc' => __('微信开放平台SECRET', 'haoui'),
		'id' => 'um_open_weixin_secret',
		'type' => 'text');
	$options[] = array(
		'name' => __( '微信回调地址', 'haoui' ),
		'desc' => __( home_url('oauth/weixin'), 'haoui' ),
		'type' => 'info');
    $options[] = array(
		'name' => __('新登录用户角色', 'haoui'),
		'desc' => __('', 'haoui'),
		'id' => 'um_open_role',
		'std' => 'contributor',
		'options' => array('subscriber'=>'订阅者','contributor'=>'投稿者','author'=>'作者','editor'=>'编辑'),
		'type' => 'select');
  

	// ======================================================================================================================
	$options[] = array(
		'name' => __('微分类', 'haoui'),
		'type' => 'heading' );

	$options[] = array(
		'id' => 'minicat_s',
		'std' => true,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'id' => 'minicat_home_s',
		'std' => true,
		'desc' => __('在首页显示微分类最新文章', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('首页模块前缀', 'haoui'),
		'id' => 'minicat_home_title',
		'desc' => '默认为：今日观点',
		'std' => '今日观点',
		'type' => 'text');

	$options[] = array(
		'name' => __('选择分类设置为微分类', 'haoui'),
		'desc' => __('选择一个使用微分类展示模版，分类下文章将全文输出到微分类列表', 'haoui'),
		'id' => 'minicat',
		'options' => $options_categories,
		'type' => 'select');


	// ======================================================================================================================
	$options[] = array(
		'name' => __('首页公告', 'haoui'),
		'type' => 'heading' );
		
	$options[] = array(
	    'id' => 'dux_tui_s', 'std' => false, 
	    'desc' => __('显示首页滚动公告', 'haoui').'(v4.0+)',
	    'type' => 'checkbox');
	
	/*$options[] = array(
        'name' => '首页滚动公告栏信息',
        'desc' => '最新消息显示在全站导航条下方，非常给力的推广位置',
        'id' => 'dux_tui',
        'std' => '<li>欢迎访问轻语博客，WordPress信息，WordPress教程，热点搞笑分享</li><li>DUXv4.0+主题现已支持滚动公告栏功能，兼容其他浏览器，看到的就是咯，在后台最新消息那里用li标签添加即可。</li>',
		'type' => 'textarea');
    */
  
	$options[] = array(
		'id' => 'site_notice_s',
		'std' => true,
		'desc' => __('显示公告模块', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('显示标题', 'haoui'),
		'id' => 'site_notice_title',
		'desc' => '建议4字内，默认：网站公告',
		'std' => '网站公告',
		'type' => 'text');

	$options[] = array(
		'name' => __('选择分类设置为网站公告', 'haoui'),
		'id' => 'site_notice_cat',
		'options' => $options_categories,
		'type' => 'select');
		
  	
		
	// 添加特别推荐设置选项卡
    $options[] = array(
	    'name' => __('强烈推荐', 'haoui'),
	    'id' => 'site_tuijian_s', 'std' => false, 
	    'desc' => __('显示推荐模块', 'haoui'),
	    'type' => 'checkbox');
    $options[] = array(
	    'name' => __('显示标题', 'haoui'),
	    'id' => 'site_tuijian_title', 
	    'desc' => '建议4字内，默认：强烈推荐',
	    'std' => '强烈推荐', 'type' => 'text');
    $options[] = array(
	    'name' => __('显示文本', 'haoui'), 
	    'id' => 'site_tuijian_text', 
	    'desc' => '可自定义，默认：qyblog.cn强烈推荐：<strong>qyblog.cn分享极致</strong>', 
	    'std' => '<h2>强烈推荐：qyblog.cn<br><strong>qyblog.cn最好的Blog</strong></h2>',
	    'type' => 'textarea');
    $options[] = array(
	    'name' => __('按钮 ', 'haoui') ,
	    'id' => 'site_tuijian_button' , 
	    'desc' => '按钮文字', 'std' => '点击查看',
	    'type' => 'text');
    $options[] = array(
	    'id' => 'site_tuijian_url' ,
	    'desc' => __('按钮链接', 'haoui'), 
	    'std' => 'http://www.qyblog.cn/',
	    'type' => 'text');
    $options[] = array(
	    'id' => 'site_tuijian_blank' , 
	    'std' => false, 'desc' => __('新窗口打开', 'haoui'), 
	    'type' => 'checkbox');
    $options[] = array(
	    'name' => __('联系我们','haoui'), 
	    'id' => 'site_aboutus_s', 
	    'std' => false, 
    	'desc' => __('显示联系我们模块', 'haoui'), 
    	'type' => 'checkbox');
    $options[] = array(
	    'name' => __('显示标题', 'haoui'),
    	'id' => 'site_aboutus_title', 
	    'desc' => '可自定义，默认：联系我们', 
	    'std' => '联系我们', 
	    'type' => 'text');
    $options[] = array(
	    'name' => __('显示文本', 'haoui'),
	    'id' => 'site_aboutus_text', 
	    'desc' => '可自定义，默认：<h2>如有疑问,请留言或邮件咨询 <br>admin@aijzxw.com</h2>', 
	    'std' => '<h2>如有疑问,请留言或邮件咨询 <br><i class="fa fa-envelope-o"></i> admin@aijzxw.com</h2>', 
	    'type' => 'textarea');
		
	// 添加选项卡结束



	// ======================================================================================================================
	$options[] = array(
		'name' => __('首页焦点图', 'haoui'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('焦点图', 'haoui'),
		'id' => 'focus_s',
		'std' => true,
		'desc' => __('开启', 'haoui').$nnn.__('  以下设置将显示在焦点图的第一张，其它位置调用的是置顶文章，设置置顶文章方法：后台-文章-快速编辑-置顶选中即可', 'haoui'),
		'type' => 'checkbox');
		
	$options[] = array(
      	'name' => __('单独焦点图', 'haoui'),
		'id' => 'focusslide_s',
		'std' => false,
		'desc' => __('开启', 'haoui').$nnn.__('  使用单独焦点图请关闭上面焦点图选项，只使用独立的焦点图', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('排序', 'haoui'),
		'id' => 'focusslide_sort',
		'desc' => '默认：1 2 3 4 5',
		'std' => '1 2 3 4 5',
		'type' => 'text');

	for ($i=1; $i <= 5; $i++) { 
		
	$options[] = array(
		'name' => __('图', 'haoui').$i,
		'id' => 'focusslide_title_'.$i,
		'desc' => '标题',
		'std' => 'WordPress主题 - QUX',
		'type' => 'text');

	$options[] = array(
		// 'name' => __('链接到', 'haoui'),
		'id' => 'focusslide_href_'.$i,
		'desc' => __('链接', 'haoui'),
		'std' => 'https://www.qyblog.cn/2017/02/991.html',
		'type' => 'text');

	$options[] = array(
		'id' => 'focusslide_blank_'.$i,
		'std' => true,
		'desc' => __('新窗口打开', 'haoui'),
		'type' => 'checkbox');
	
	$options[] = array(
		// 'name' => __('图片', 'haoui'),
		'id' => 'focusslide_src_'.$i,
		'desc' => __('图片，尺寸：', 'haoui').'410*266   如果使用单独焦点图为820*200',
		'std' => 'https://www.qyblog.cn/wp-content/uploads/2019/10/1111.jpg',
		'type' => 'upload');

	}


	// ======================================================================================================================
	/*$options[] = array(
		'name' => __('侧栏随动', 'haoui'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('首页', 'haoui'),
		'id' => 'sideroll_index_s',
		'std' => true,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'id' => 'sideroll_index',
		'std' => '1 2',
		'class' => 'mini',
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块 ', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('分类/标签/搜索页', 'haoui'),
		'id' => 'sideroll_list_s',
		'std' => true,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'id' => 'sideroll_list',
		'std' => '1 2',
		'class' => 'mini',
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块 ', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'name' => __('文章页', 'haoui'),
		'id' => 'sideroll_post_s',
		'std' => true,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');

	$options[] = array(
		'id' => 'sideroll_post',
		'std' => '1 2',
		'class' => 'mini',
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块 ', 'haoui'),
		'type' => 'text');*/



	// ======================================================================================================================
	$options[] = array(
		'name' => __('直达链接', 'haoui'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('在文章列表显示', 'haoui').' (v1.3+)',
		'id' => 'post_link_excerpt_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('在文章页面显示', 'haoui'),
		'id' => 'post_link_single_s',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('新窗口打开链接', 'haoui'),
		'id' => 'post_link_blank_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('链接添加 nofollow', 'haoui'),
		'id' => 'post_link_nofollow_s',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('自定义显示文字', 'haoui'),
		'id' => 'post_link_h1',
		'type' => "text",
		'std' => '直达链接',
		'desc' => __('默认为“直达链接” ', 'haoui'));


	// ======================================================================================================================
	$options[] = array(
		'name' => __('独立页面', 'haoui'),
		'type' => 'heading');
	
	$options[] = array(
		'name' => __('网址导航 - 标题下描述', 'haoui'),
		'id' => 'navpage_desc',
		'std' => '这里显示的是网址导航的一句话描述...',
		'type' => 'text');

	$options[] = array(
		'name' => __('网址导航 - 选择链接分类到网址导航', 'haoui'),
		'id' => 'navpage_cats',
		'desc' => '注：此处如果没有选项，请去后台-链接中添加链接和链接分类目录，只有不为空的链接分类目录才会显示出来。',
		'options' => $options_linkcats,
		'type' => 'multicheck');

	$options[] = array(
		'name' => __('读者墙', 'haoui'),
		'id' => 'readwall_limit_time',
		'std' => 200,
		'class' => 'mini',
		'desc' => __('限制在多少月内，单位：月', 'haoui'),
		'type' => 'text');

	$options[] = array(
		'id' => 'readwall_limit_number',
		'std' => 200,
		'class' => 'mini',
		'desc' => __('显示个数', 'haoui'),  
		'type' => 'text');
  
    $options[] = array(
		'name' => __('作者墙推荐作者', 'haoui'),
		'id' => 'recommend_user',
		'desc' => __('输入用户ID，用英文逗号隔开，建议推荐3个。', 'haoui'),
		'type' => 'text');

	/*$options[] = array(
		'name' => __('页面左侧菜单设置', 'haoui'),
		'id' => 'page_menu',
		'options' => $options_pages,
		'type' => 'multicheck');*/
  
  	$options[] = array(
		'name' =>__('页面全屏查看图片模式', 'haoui').'(v4.0+)',
		'id' => 'full_image',
		'desc' => __('开启', 'haoui'),
		'std' => true,
		'type' => "checkbox");
  
	$options[] = array(
		'name' => __('友情链接分类选择', 'haoui'),
		'id' => 'page_links_cat',
		'desc' => '注：此处如果没有选项，请去后台-链接中添加链接和链接分类目录，只有不为空的链接分类目录才会显示出来。',
		'options' => $options_linkcats,
		'type' => 'multicheck');

	

	// ======================================================================================================================
   	$options[] = array(
		'name' => __('社交', 'haoui'),
		'type' => 'heading' );
  
    $options[] = array(
		'name' => __('网站顶部右上角关注本站', 'haoui'),
		'id' => 'guanzhu_b',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('显示文字', 'haoui'),
		'id' => 'sns_txt',
		'std' => '关注我们',
		'type' => 'text');

	

	$options[] = array(
		'name' => __('微信 - 标题', 'haoui'),
		'id' => 'wechat',
		'std' => '关注微信',
		'type' => 'text');
	$options[] = array(
		'id' => 'wechat_qr',
		'std' => 'https://www.qyblog.cn/wp-content/uploads/2016/10/629060465448565467.jpg',
		'desc' => __('微信二维码，建议图片尺寸：', 'haoui').'200x200px',
		'type' => 'upload');


	for ($i=1; $i < 10; $i++) { 

		$options[] = array(
			'name' => __('自定义社交 '.$i.' - 标题', 'haoui'),
			'id' => 'sns_tit_'.$i,
			'std' => '',
			'type' => 'text');
		$options[] = array(
			'name' => __('自定义社交 '.$i.' - 链接地址', 'haoui'),
			'id' => 'sns_link_'.$i,
			'std' => '',
			'type' => 'text');

	}

  
    // ======================================================================================================================
  	$options[] = array(
		'name' => __('客服', 'haoui'),
		'type' => 'heading' );

	$options[] = array(
		'name' => __('显示位置', 'haoui').' (v5.1+)',
		'id' => 'kefu',
		'std' => "rb",
		'type' => "radio",
		'desc' => '',
		'options' => array(
			'rb' => __('右侧底部', 'haoui'),
			'rm' => __('右侧中部', 'haoui'),
			'0' => __('不显示', 'haoui')
		));
	
	$options[] = array(
		'name' => __('按钮排序', 'haoui'),
		'id' => 'kefu_px',
		'desc' => '填写需要显示的以下按钮ID，用空格隔开。默认：2 4 1',
		'std' => '2 4 1',
		'type' => 'text');

	$options[] = array(
		'name' => __('手机端', 'haoui'),
		'id' => 'kefu_m',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));

	$options[] = array(
		'name' => __('手机端 - 按钮排序', 'haoui'),
		'id' => 'kefu_m_px',
		'desc' => '填写需要显示的以下按钮ID，用空格隔开。默认：2 3 4',
		'std' => '2 3 4',
		'type' => 'text');

	$options[] = array(
		'name' => __('回顶 （排序ID：1）', 'haoui'),
		'id' => 'kefu_top_tip',
		'desc' => '按钮提示文字。默认：回顶部',
		'std' => '回顶部',
		'type' => 'text');
	$options[] = array(
		'id' => 'kefu_top_tip_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => '回顶',
		'type' => 'text');

	$options[] = array(
		'name' => 'QQ （排序ID：2）',
		'id' => 'fqq_tip',
		'desc' => '按钮提示文字。默认：QQ咨询',
		'std' => 'QQ咨询',
		'type' => 'text');
	$options[] = array(
		'id' => 'fqq_tip_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => 'QQ咨询',
		'type' => 'text');

	$options[] = array(
		'id' => 'fqq_id',
		'desc' => 'QQ号码',
		'std' => '937373201',
		'type' => 'text');

	$options[] = array(
		'name' => '电话 （排序ID：3）',
		'id' => 'kefu_tel_tip',
		'desc' => '按钮提示文字。默认：电话咨询',
		'std' => '电话咨询',
		'type' => 'text');
	$options[] = array(
		'id' => 'kefu_tel_tip_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => '电话咨询',
		'type' => 'text');

	$options[] = array(
		'id' => 'kefu_tel_id',
		'desc' => '电话号码',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('微信/二维码 （排序ID：4）', 'haoui'),
		'id' => 'kefu_wechat_tip',
		'desc' => '按钮提示文字。默认：关注微信',
		'std' => '关注微信',
		'type' => 'text');
	$options[] = array(
		'id' => 'kefu_wechat_tip_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => '微信咨询',
		'type' => 'text');

	$options[] = array(
		'id' => 'kefu_wechat_qr',
		'std' => '',
		'desc' => __('微信二维码，正方形，建议图片尺寸：', 'haoui').'200x200px',
		'type' => 'upload');

	$options[] = array(
		'name' => '自定义客服 （排序ID：5）',
		'id' => 'kefu_sq_tip',
		'desc' => '按钮提示文字。默认：在线咨询',
		'std' => '在线咨询',
		'type' => 'text');
	$options[] = array(
		'id' => 'kefu_sq_tip_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => '在线咨询',
		'type' => 'text');

	$options[] = array(
		'id' => 'kefu_sq_id',
		'desc' => '自定义客服链接',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => '评论 （排序ID：6）',
		'id' => 'kefu_comment_tip',
		'desc' => '按钮提示文字，只在开启评论的文章或页面显示。默认：去评论',
		'std' => '去评论',
		'type' => 'text');
	$options[] = array(
		'id' => 'kefu_comment_tip_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => '去评论',
		'type' => 'text');       
      
    $options[] = array(
		'name' => '简繁切换 （排序ID：7）',
		'id' => 'f_wencode_tip',
		'desc' => '按钮提示文字。默认：简繁切换',
		'std' => '简繁切换',
		'type' => 'text');
	$options[] = array(
		'id' => 'f_wencode_m',
		'desc' => '手机端 - 按钮下显示文字',
		'std' => '简繁切换',
		'type' => 'text');
  	$options[] = array(
		'name' => __('首页模式切换（排序ID：8）', 'haoui'),
		'id' => 'index_layout',
		'type' => "checkbox",
		'std' => false,
		'desc' => __('开启', 'haoui'));
		
  	$options[] = array(
		'name' => __('夜间模式（排序ID：9）', 'haoui'),
		'id' => 'index_layout',
		'type' => "info",
		'desc' => __('夜间模式切换按钮', 'haoui'));

	// ======================================================================================================================
	$options[] = array(
		'name' => __('广告位', 'haoui'),
		'type' => 'heading' );

	$options[] = array(
		'name' => __('文章页正文结尾文字广告', 'haoui'),
		'id' => 'ads_post_footer_s',
		'std' => false,
		'desc' => ' 显示',
		'type' => 'checkbox');
	$options[] = array(
		'desc' => '前标题',
		'id' => 'ads_post_footer_pretitle',
		'std' => '阿里百秀',
		'type' => 'text');
	$options[] = array(
		'desc' => '标题',
		'id' => 'ads_post_footer_title',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'desc' => '链接',
		'id' => 'ads_post_footer_link',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'id' => 'ads_post_footer_link_blank',
		'type' => "checkbox",
		'std' => true,
		'desc' => __('开启', 'haoui') .' ('. __('新窗口打开链接', 'haoui').')');


	$options[] = array(
		'name' => __('首页文章列表上', 'haoui'),
		'id' => 'ads_index_01_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_index_01',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_index_01_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('首页分页下', 'haoui'),
		'id' => 'ads_index_02_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_index_02',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_index_02_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('文章页正文上', 'haoui'),
		'id' => 'ads_post_01_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_post_01',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_post_01_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('文章页正文下', 'haoui'),
		'id' => 'ads_post_02_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_post_02',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_post_02_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('文章页评论上', 'haoui'),
		'id' => 'ads_post_03_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_post_03',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_post_03_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('分类页列表上', 'haoui'),
		'id' => 'ads_cat_01_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_cat_01',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_cat_01_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('标签页列表上', 'haoui'),
		'id' => 'ads_tag_01_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_tag_01',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_tag_01_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');

	$options[] = array(
		'name' => __('搜索页列表上', 'haoui'),
		'id' => 'ads_search_01_s',
		'std' => false,
		'desc' => __('开启', 'haoui'),
		'type' => 'checkbox');
	$options[] = array(
		'desc' => __('非手机端', 'haoui').' '.$adsdesc,
		'id' => 'ads_search_01',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'id' => 'ads_search_01_m',
		'std' => '',
		'desc' => __('手机端', 'haoui').' '.$adsdesc,
		'type' => 'textarea');



	// ======================================================================================================================
	$options[] = array(
		'name' => __('自定义代码', 'haoui'),
		'type' => 'heading' );

	$options[] = array(
		'name' => __('自定义网站底部内容', 'haoui'),
		'desc' => __('该块显示在网站底部版权上方，可已定义放一些链接或者图片之类的内容。', 'haoui'),
		'id' => 'fcode',
		'std' => '',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('自定义CSS样式', 'haoui'),
		'desc' => __('位于</head>之前，直接写样式代码，不用添加&lt;style&gt;标签', 'haoui'),
		'id' => 'csscode',
		'std' => '',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('自定义头部代码', 'haoui'),
		'desc' => __('位于</head>之前，这部分代码是在主要内容显示之前加载，通常是CSS样式、自定义的<meta>标签、全站头部JS等需要提前加载的代码', 'haoui'),
		'id' => 'headcode',
		'std' => '',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('自定义底部代码', 'haoui'),
		'desc' => __('位于&lt;/body&gt;之前，这部分代码是在主要内容加载完毕加载，通常是JS代码', 'haoui'),
		'id' => 'footcode',
		'std' => '',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('网站统计代码', 'haoui'),
		'desc' => __('位于底部，用于添加第三方流量数据统计代码，如：Google analytics、百度统计、CNZZ、51la，国内站点推荐使用百度统计，国外站点推荐使用Google analytics', 'haoui'),
		'id' => 'trackcode',
		'std' => '',
		'type' => 'textarea');
  
  
  	// ======================================================================================================================
    $options[] = array(
		'name' => __('百度熊掌号', 'haoui'),
		'type' => 'heading' );

	$options[] = array(
		'name' => __('百度熊掌号', 'haoui'),
		'id' => 'xzh_on',
		'std' => false,
		'desc' => ' 开启',
		'type' => 'checkbox');

	$options[] = array(
		'name' => '百度熊掌号 Appid',
		'id' => 'xzh_appid',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => '百度熊掌号 推送密钥 token',
		'id' => 'xzh_post_token',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('粉丝关注', 'haoui'),
		'id' => 'xzh_render_head',
		'std' => false,
		'desc' => ' 吸顶bar',
		'type' => 'checkbox');

	$options[] = array(
		'class' => 'op-multicheck',
		'id' => 'xzh_render_body',
		'std' => true,
		'desc' => ' 文章段落间bar',
		'type' => 'checkbox');

	$options[] = array(
		'class' => 'op-multicheck',
		'id' => 'xzh_render_tail',
		'std' => true,
		'desc' => ' 底部bar',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('添加JSON_LD数据', 'haoui'),
		'id' => 'xzh_jsonld_single',
		'std' => true,
		'desc' => ' 文章页',
		'type' => 'checkbox');

	$options[] = array(
		'class' => 'op-multicheck',
		'id' => 'xzh_jsonld_page',
		'std' => false,
		'desc' => ' 页面',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('添加JSON_LD数据 - 不添加图片', 'haoui'),
		'id' => 'xzh_jsonld_img',
		'std' => false,
		'desc' => ' 开启',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('新增文章实时推送', 'haoui'),
		'id' => 'xzh_post_on',
		'std' => false,
		'desc' => ' 开启 （使用此功能，你还需要开启本页中的 百度熊掌号 和 Appid以及token的设置）',
		'type' => 'checkbox');


	/**
	 * For $settings options see:
	 * http://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * 'media_buttons' are not supported as there is no post to attach items to
	 * 'textarea_name' is set by the 'id' you choose
	 */
/*
	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);

	$options[] = array(
		'name' => __('Default Text Editor', 'options_framework_theme'),
		'desc' => sprintf( __( 'You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>', 'options_framework_theme' ), 'http://codex.wordpress.org/Function_Reference/wp_editor' ),
		'id' => 'example_editor',
		'type' => 'editor',
		'settings' => $wp_editor_settings );

*/
  
	return $options;
}

if(!function_exists('allow_data_event_content')){
    function allow_data_event_content() {
        global $allowedposttags, $allowedtags;
        $newattribute = "target";
        $allowedposttags["a"][$newattribute] = true;
        $allowedtags["a"][$newattribute] = true;
    }
}
add_action( 'init', 'allow_data_event_content' );