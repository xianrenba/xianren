<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<link rel="dns-prefetch" href="//apps.bdimg.com">
<meta http-equiv="X-UA-Compatible" content="IE=11,IE=10,IE=9,IE=8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-title" content="<?php echo get_bloginfo('name') ?>">
<meta http-equiv="Cache-Control" content="no-siteapp">
<meta name="referrer" content="never">
<title><?php echo _title(); ?></title>
<?php wp_head(); ?>
<link rel="shortcut icon" href="<?php echo home_url() . '/favicon.ico' ?>">
<link rel="stylesheet"  href="<?php echo get_stylesheet_directory_uri() ?>/css/animate.min.css" type="text/css" media="all">
<!--[if lt IE 9]><script src="<?php echo get_stylesheet_directory_uri() ?>/js/libs/html5.min.js"></script><![endif]-->
<?php tb_xzh_head_var() ?>
</head>
<body <?php body_class(_bodyclass()); ?>>
<?php tb_xzh_render_head() ?>
<?php 
if (_hui('header_style')!=='1'){
	get_template_part('template/herder','default');	
}else{
	get_template_part('template/herder','zgn');	
} 
?>
<div class="site-search">
	<div class="container">
		<?php  
			if( _hui('search_baidu') && _hui('search_baidu_code') ){
				echo '<form class="site-search-form"><input id="bdcsMain" class="search-input" type="text" placeholder="输入关键字"><button class="search-btn" type="submit"><i class="fa fa-search"></i></button></form>';
				echo _hui('search_baidu_code');
			}else{
				echo '<form method="get" class="site-search-form" action="'.esc_url( home_url( '/' ) ).'" ><input class="search-input" name="s" type="text" placeholder="输入关键字" value="'.htmlspecialchars($s).'"><button class="search-btn" type="submit"><i class="fa fa-search"></i></button></form>';
			}
		?>
	</div>
</div>