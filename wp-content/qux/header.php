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
<?php if (_hui('header_style')!=='1'){ ?>
<header class="header">
  <div class="container">
		<?php _the_logo(); ?>
		<?php  
			$_brand = _hui('brand');
			if( $_brand ){
				$_brand = explode("\n", $_brand);
				echo '<div class="brand">' . $_brand[0] . '<br>' . $_brand[1] . '</div>';
			}
		?>
		<ul class="site-nav site-navbar">
			<?php _moloader('mo_mb_list', false); ?>
		</ul>
		<?php if( !_hui('topbar_off') ){ ?>
		<div class="topbar">
			<ul class="site-nav topmenu">
				<?php _the_menu('topmenu') ?>
                <?php if( _hui('guanzhu_b') ){ ?>
				<li class="menusns">
					<a href="javascript:;"><?php echo _hui('sns_txt') ?> <i class="fa fa-angle-down"></i></a>
					<ul class="sub-menu">
						<?php if(_hui('wechat')){ echo '<li><a class="sns-wechat" href="javascript:;" title="'._hui('wechat').'" data-src="'._hui('wechat_qr').'">'._hui('wechat').'</a></li>'; } ?>
						<?php for ($i=1; $i < 10; $i++) { 
							if( _hui('sns_tit_'.$i) && _hui('sns_link_'.$i) ){ 
								echo '<li><a target="_blank" rel="external nofollow" href="'._hui('sns_link_'.$i).'">'. _hui('sns_tit_'.$i) .'</a></li>'; 
							}
						} ?>
					</ul>
				</li>
                <?php }?>
			</ul>
			<?php if( is_user_logged_in() ): global $current_user; ?>
			    <?php $unread_count = intval(get_um_message($current_user->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )"));  ?>
				<?php _moloader('mo_get_user_page', false) ?>
				Hi, <?php echo $current_user->display_name;echo um_member_icon($current_user->ID) ?>
				<?php if( _hui('user_page_s') ){ ?>
					&nbsp;&nbsp;<a href="<?php echo um_get_user_url('index') ?>"><i class="fa fa-user"></i>进入用户中心<?php if($unread_count){ echo '&nbsp;&nbsp;<i class="badge">'.$unread_count.'</i>'; } ?></a>&nbsp;&nbsp;<a href="<?php echo add_query_arg('redirect_to', _get_current_page_url(), _url_for('signout')); ?>"><i class="fa fa-power-off"></i>注销登录</a>
				<?php } ?>
				<?php if( is_super_admin() ){ ?>
					&nbsp;&nbsp;<a href="<?php echo _url_for('manage_status'); ?>"><i class="fa fa-th"></i>站务管理</a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('/wp-admin/') ?>">后台管理</a>
				<?php } ?>
			<?php elseif( _hui('user_page_s') ): ?>
				<?php _moloader('mo_get_user_rp', false) ?>
				<a href="javascript:;" class="user-reg" data-sign="0">Hi, 请登录</a>
				<?php if(get_option('users_can_register')==1){ ?>&nbsp;&nbsp;<a href="javascript:;" class="user-reg" data-sign="1">我要注册</a><?php } ?>
				&nbsp;&nbsp;<a href="<?php echo mo_get_user_rp() ?>">找回密码</a>
			<?php endif; ?>
		</div>
		<?php } ?>
		<i class="fa fa-bars m-icon-nav"></i>
	</div>
</header>
<?php }else{  get_header('s'); } ?>
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