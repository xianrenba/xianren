<?php

// 如果已经登录
if(is_user_logged_in()) {
    if ( isset( $_REQUEST['redirect'] ) || isset( $_REQUEST['redirect_to'] ) ){
        $redirect_to = isset($_REQUEST['redirect']) ?  $_REQUEST['redirect'] : $_REQUEST['redirect_to'];
    } else {
        $redirect_to = '/';
    }
    wp_safe_redirect($redirect_to);
    exit;
}

// 引入头部
get_header();
?>

    <div class="sign-bg"></div>
    <div id="signin" class="um_sign">
    <div class="part loginPart">
    <div class="login-logo"><a href="<?php echo home_url(); ?>"><img style="max-width: 160px;" src="<?php echo _hui('logo_src'); ?>"></a></div>
    <form id="login" action="<?php echo site_url(); ?>/wp-login.php" method="post" novalidate="novalidate">
		<p class="status"></p>
        <p>
        	<label class="icon" for="username"><i class="fa fa-user"></i></label>
            <input class="input-control" id="username" type="text" placeholder="请输入用户名" name="username" required="" aria-required="true">
        </p>
        <p>
        	<label class="icon" for="password"><i class="fa fa-lock"></i></label>
            <input class="input-control" id="password" type="password" placeholder="请输入密码" name="password" required="" aria-required="true">
        </p>
        <p class="safe">
            <label class="remembermetext" for="rememberme"><input name="rememberme" type="checkbox" checked="checked" id="rememberme" class="rememberme" value="forever">记住我的登录</label>
            <a class="lost" href="<?php echo _url_for('findpass'); ?>"><?php _e('忘记密码 ?','tinection'); ?></a>
        </p>
        <p>
            <input class="submit" type="submit" value="登录" name="submit">
        </p>
        <input type="hidden" id="security" name="security" value="<?php echo  wp_create_nonce( 'security_nonce' );?>">
		<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
	</form>
    <?php if(_hui('um_open_qq')||_hui('um_open_weibo')||_hui('um_open_weixin')){ ?>
    <div class="other-sign">
	<?php 
	$qq_login = home_url('oauth/qq?action=login&redirect='.urlencode(um_get_redirect_uri()));
	$weibo_login = home_url('oauth/weibo?action=login&redirect='.urlencode(um_get_redirect_uri()));
	$weixin_login = home_url('oauth/weixin?action=login&redirect='.urlencode(um_get_redirect_uri()));
	?>
      <p>您也可以使用第三方帐号快捷登录</p>
      <div>
	  <?php if(_hui('um_open_qq')) { ?>
      <a class="qqlogin" id="qqlogin"href="<?php echo $qq_login ?>"><i class="fa fa-qq"></i></a>
	  <?php } ?>
	  <?php if(_hui('um_open_weibo')) { ?>
	  <a class="weibologin" id="weibologin"href="<?php echo $weibo_login ?>"><i class="fa fa-weibo"></i></a>
	  <?php } ?>
	  <?php if(_hui('um_open_weixin')) { ?>
	  <a class="weixinlogin" id="weixinlogin"href="<?php echo $weixin_login ?>"><i class="fa fa-weixin"></i></a>
	  <?php } ?>
      </div>
    </div>
	<?php } ?>
    <div class="foot-copyright">&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url() ?>"><?php echo get_bloginfo('name') ?></a>|<?php if(get_option('users_can_register')==1){ ?><a href="<?php echo home_url('/m/signup'); ?>">注册</a>|<?php } ?><a href="<?php echo _url_for('findpass'); ?>">忘记密码？</a></div>
    </div>
    </div>
<script>
window.jsui={
    www: '<?php echo home_url() ?>',
    uri: '<?php echo get_stylesheet_directory_uri() ?>',
    ver: '<?php echo THEME_VERSION ?>',
    ajaxpager: '<?php echo _hui("ajaxpager") ?>'
};
</script>
<?php
// 引入页脚
wp_footer();
if(isset($_GET['reg']) && $_GET['reg']==='disabled') { ?>
	<script>
	jQuery(function(){
		tbquire(['ucenter'],function(){
			swal({
				type:'warning',
				title: "管理员已经禁止注册，如果你已有账号请登录",
				//showConfirmButton: false
			}); 
		})
	}); 
	</script>
<?php }