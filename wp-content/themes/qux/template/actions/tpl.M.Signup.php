<?php

//wp_no_robots();

if ( !get_option('users_can_register') ) {
	wp_safe_redirect( add_query_arg('reg', 'disabled', _url_for('signin')));
	exit();
}

// 引入头部
get_header();

?>
    <div class="sign-bg"></div>
    <div id="signin" class="um_sign">
    <div class="part registerPart">
    <div class="login-logo"><a href="<?php echo home_url(); ?>"><img style="max-width: 160px;" src="<?php echo _hui('logo_src'); ?>"></a></div>
    <form name="register" id="register" action="<?php bloginfo('url'); ?>/wp-login.php?action=register" method="post" novalidate="novalidate">
        <p class="status"></p>
        <?php if(_hui('_email_oauth')) echo '<p style="text-align: center;font-size: 13px;">我们将发送一封验证邮件至你的邮箱, 请正确填写以完成账号注册和激活</p>'; ?>
        <p>
        	<label class="icon" for="user_name"><i class="fa fa-user"></i></label>
            <input class="input-control" id="user_name" type="text" name="user_name" placeholder="输入英文用户名" required="" aria-required="true">
        </p>
        <p>
        	<label class="icon" for="user_email"><i class="fa fa-envelope"></i></label>
            <input class="input-control" id="user_email" type="email" name="user_email" placeholder="输入常用邮箱" required="" aria-required="true">
        </p>
        <p>
        	<label class="icon" for="user_pass"><i class="fa fa-lock"></i></label>
            <input class="input-control" id="user_pass" type="password" name="user_pass" placeholder="密码最小长度为6" required="" aria-required="true">
        </p>
        <p>
        	<label class="icon" for="user_pass2"><i class="fa fa-retweet"></i></label>
            <input class="input-control" type="password" id="user_pass2" name="user_pass2" placeholder="再次输入密码" required="" aria-required="true">
        </p>
        <?php if(_hui('reg_captcha')){ ?>
        <p id="captcha_inline">
        	<label class="icon" for="um_captcha"><i class="fa fa-photo"></i></label>
            <input class="input-control inline" type="text" id="um_captcha" name="um_captcha" placeholder="输入验证码" required>
            <img src="<?php echo add_query_arg('t', str_replace(' ', '_', microtime()), _url_for('captcha')); ?>" class="captcha_img inline" title="点击刷新验证码">
        </p>
        <?php } ?>
        <p>
        	<input class="submit inline" type="submit" value="注册" name="submit">
        </p>
        <input type="hidden" id="user_security" name="user_security" value="<?php echo  wp_create_nonce( 'user_security_nonce' );?>"><input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
	</form>
	<div class="foot-copyright">&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url() ?>"><?php echo get_bloginfo('name') ?></a>|<?php if(get_option('users_can_register')==1){ ?><a href="<?php echo _url_for('signin'); ?>">登录</a>|<?php } ?><a href="<?php echo _url_for('findpass'); ?>">忘记密码？</a></div>
    </div>
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