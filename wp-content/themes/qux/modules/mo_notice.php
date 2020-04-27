<?php 
/**
 * For site notice and user welcome
 */
if(  ( is_home() ||is_front_page() )&& (_hui('site_notice_s')||_hui('user_on_notice_s')) ) { 
	$s_notice = _hui('site_notice_s');
	$s_user = _hui('user_page_s') && _hui('user_on_notice_s');

	//添加推荐栏
	$s_tuijian = _hui("site_tuijian_s");
	$s_aboutus = _hui("site_aboutus_s");
	//添加推荐栏
?>
	<div class="widget widget-tops">
		<ul class="widget-nav">
			<?php if( $s_notice ){ ?><li<?php echo ($s_notice) ? ' class="active"' : '' ?>><?php echo _hui('site_notice_title') ? _hui('site_notice_title') : '网站公告' ?></li><?php } ?>
			<?php if( $s_user ){ ?><li<?php echo ($s_user && !$s_notice ) ? ' class="active"' : '' ?>>会员中心</li><?php } ?>
		    <!--添加推荐栏-->
			<?php if ($s_tuijian) { ?><li<?php echo ($s_tuijian && !$s_notice ) ? ' class="active"' : '' ?>><?php echo _hui('site_tuijian_title') ? _hui('site_tuijian_title') : '真诚推荐' ?></li><?php } ?>
			<?php if ($s_aboutus) { ?><li<?php echo ($s_aboutus && !$s_notice ) ? ' class="active"' : '' ?>><?php echo _hui('site_aboutus_title') ? _hui('site_aboutus_title') : '真诚推荐' ?></li><?php } ?>
			<!--添加推荐栏-->
		</ul>
		<ul class="widget-navcontent">
			<?php if( $s_notice && _hui('site_notice_cat') ){ ?>
				<li class="item item-01<?php echo ($s_notice) ? ' active' : '' ?>">
					<ul>
						<?php  
							$args = array(
							    'ignore_sticky_posts' => 1,
							    'showposts' => 5,
							    'cat' => _hui('site_notice_cat')
							);
							query_posts($args);
							while ( have_posts() ) : the_post(); 
								echo '<li><time>'.get_the_time('m-d').'</time><a target="_blank" href="'.get_permalink().'">'.get_the_title().get_the_subtitle().'</a></li>';
							endwhile; 
							wp_reset_query();
						?>
					</ul>
				</li>
			<?php } ?>
			<?php if( $s_user ){ ?>
				<li class="item item-02<?php echo ($s_user && !$s_notice) ? ' active' : '' ?>">
					<?php 
					if( is_user_logged_in() ){
						global $current_user; 
						if(_hui('open_ucenter')){
					?>						
						<ul class='qucenter'><?php  echo um_user_profile(); ?></ul>
						<?php }else{ ?>
						<dl>
							<dt><?php echo _get_the_avatar($user_id=$current_user->ID, $user_email=$current_user->user_email, true); ?></dt>
							<dd><?php echo $current_user->display_name ?><span class="text-muted"><?php echo $current_user->user_email ?></span></dd>
						</dl>
						<ul>
							<li><a href="<?php echo _url_for('user') . '#posts/all' ?>">我的文章</a></li>
							<li><a href="<?php echo _url_for('user') . '#comments' ?>">我的评论</a></li>
							<li><a href="<?php echo _url_for('user') . '#info' ?>">修改资料</a></li>
							<li><a href="<?php echo _url_for('user') . '#password' ?>">修改密码</a></li>
						</ul>
						<?php } ?>
					<?php }else{ ?>
						<h4>需要登录才能进入会员中心</h4>
						<p>
							<a href="javascript:;" class="btn btn-primary user-reg" data-sign="0">立即登录</a>
							<a href="javascript:;" class="btn btn-default user-reg" data-sign="1">现在注册</a>
						</p>
					<?php } ?>
				</li>
			<?php } ?>
						<!---------------添加推荐栏------------------->
			<?php if ($s_tuijian && _hui("site_tuijian_s")) { ?>
			    <li class="item item-03<?php echo ($s_tuijian && !$s_notice) ? ' active' : '' ?>">
				  <?php echo _hui("site_tuijian_text") ? _hui("site_tuijian_text") : "真诚推荐"; ?>
				  <?php if (_hui("site_tuijian_button" ) && _hui("site_tuijian_url" )) {
					  echo "<a" . (_hui("site_tuijian_blank" ) ? " target=\"blank\"" : "") . " class=\"btn btn-primary\" href=\"" . _hui("site_tuijian_url" ) . "\">" . _hui("site_tuijian_button" ) . "</a>";
                  } ?>
				</li>
		    <?php } ?>
            <?php if ($s_aboutus && _hui("site_aboutus_s")) { ?>
			    <li class="item item-04<?php echo ($s_aboutus && !$s_notice) ? ' active' : '' ?>">
				  <?php echo _hui("site_aboutus_text") ? _hui("site_aboutus_text") : "博主邮箱"; ?>
				</li>
			<?php } ?>
						<!---------------添加推荐栏------------------->
		</ul>
	</div>
<?php 
} 