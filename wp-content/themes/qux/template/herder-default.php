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
			<?php get_template_part('template/user-info');	?>
		</div>
		<?php } ?>
		<i class="fa fa-bars m-icon-nav"></i>
	</div>
</header>