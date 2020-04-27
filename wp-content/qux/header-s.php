<?php 
/*
*  NAME:知更鸟头部
*  URL：http://www.qyblog.cn
*/
?>
<div class="uc-header" id="ucheader">
<?php if( !_hui('topbar_off') ){ ?>
<div class="top-bar">
<div class="bar">
	<div class="user-info">
    <?php if( is_user_logged_in() ): global $current_user; ?>
	<?php _moloader('mo_get_user_page', false) ?>
	Hi, <?php echo $current_user->display_name ?>
	<?php if( _hui('user_page_s') ){ ?>
		<a href="<?php echo um_get_user_url('index') ?>"><i class="fa fa-user"></i>进入用户中心</a><a href="<?php echo wp_logout_url(_get_current_page_url()); ?>"><i class="fa fa-power-off"></i>注销登录</a>
	<?php } ?>
	<?php if( is_super_admin() ){ ?>
		<a href="<?php echo _url_for('manage_status'); ?>"><i class="fa fa-th"></i>站务管理</a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('/wp-admin/') ?>">后台管理</a>
	<?php } ?>
	<?php elseif( _hui('user_page_s') ): ?>
	<?php _moloader('mo_get_user_rp', false) ?>
	<a href="javascript:;" class="user-reg" data-sign="0">Hi, 请登录</a>
	<a href="javascript:;" class="user-reg" data-sign="1">我要注册</a>
	<a href="<?php echo mo_get_user_rp() ?>">找回密码</a>
	<?php endif; ?>
	</div>			   
    <div class="site-nav topmenu">
        <ul>
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
    </div>
</div>
</div>
<?php } ?>
<div class="wp">
	<div class="uc-logo">
		<?php _the_logo(); ?>
		<?php  
			$_brand = _hui('brand');
			if( $_brand ){
				$_brand = explode("\n", $_brand);
				echo '<div class="brand">' . $_brand[0] . '<br>' . $_brand[1] . '</div>';
			}
		?>
		</div>
		<div class="site-nav site-navbar uc-menu">
			<ul class="uc-menu-ul" id="header_menu">
				<?php _moloader('mo_mb_list', false); ?>
			</ul>
		</div>
		<i class="fa fa-bars m-icon-nav"></i>
	</div>
</div>