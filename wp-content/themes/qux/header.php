<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<link rel="dns-prefetch" href="//apps.bdimg.com">
<meta http-equiv="X-UA-Compatible" content="IE=11,IE=10,IE=9,IE=8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-title" content="<?php echo get_bloginfo('name') ?>">
<meta http-equiv="Cache-Control" content="no-siteapp">
<title><?php echo _title(); ?></title>
<?php wp_head(); ?>
<link rel="shortcut icon" href="<?php echo home_url() . '/favicon.ico' ?>">
<link rel="stylesheet"  href="<?php echo get_stylesheet_directory_uri() ?>/css/animate.min.css" type="text/css" media="all">
<!--[if lt IE 9]><script src="<?php echo get_stylesheet_directory_uri() ?>/js/libs/html5.min.js"></script><![endif]-->
</head>
<body <?php body_class(_bodyclass()); ?>>
<?php
//var_dump(_hui('test100'));
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
<?php if( is_single() && get_post_type() == 'post' && _hui('breadcrumbs_single_s') ){ ?>
    <div class="breadcrumb-box"><div class="crumbs"><?php echo get_breadcrumb(); ?></div></div>
<?php } ?>
<?php 
	$ads_site_switch = true;
	if(_hui('ads_site_home_s') && !is_home()){
	    $ads_site_switch = false;
	}else if( is_category() ){
		_moloader('mo_is_minicat', false);
		if( mo_is_minicat() ){
			$ads_site_switch = false;
		}
	}else if( is_page_template( 'pages/navs.php' ) || is_page_template( 'pages/user.php' ) || is_page_template( 'pages/resetpassword.php' ) || $paged>1 ){
		$ads_site_switch = false;
	}
	$ads_site_switch && _the_ads($name='ads_site_01', $class='asb-site asb-site-01');
?>