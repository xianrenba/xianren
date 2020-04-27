<!--登录弹窗-->
<div id="sign" class="um_sign">
    <div class="part loginPart">
    <form id="login" action="<?php echo site_url(); ?>/wp-login.php" method="post" novalidate="novalidate">
        <div id="register-active" class="switch"><?php if(get_option('users_can_register')==1){ ?><i class="fa fa-toggle-on"></i>切换注册<?php } ?></div>
        <h3>登录</h3><p class="status"></p>
        <p>
            <label class="icon" for="username"><i class="fa fa-user"></i></label>
            <input class="input-control" id="username" type="text" placeholder="请输入用户名或邮箱" name="username" required="" aria-required="true">
        </p>
        <p>
            <label class="icon" for="password"><i class="fa fa-lock"></i></label>
            <input class="input-control" id="password" type="password" placeholder="请输入密码" name="password" required="" aria-required="true">
        </p>
        <p class="safe">
            <label class="remembermetext" for="rememberme"><input name="rememberme" type="checkbox" checked="checked" id="rememberme" class="rememberme" value="forever">记住我的登录</label>
            <a class="lost" href="<?php echo get_option('home'); ?>/wp-login.php?action=lostpassword"><?php _e('忘记密码 ?','tinection'); ?></a>
        </p>
        <p>
            <input class="submit" type="submit" value="登录" name="submit">
        </p>
        <a class="close"><i class="fa fa-times"></i></a>
        <input type="hidden" id="security" name="security" value="<?php echo  wp_create_nonce( 'security_nonce' );?>">
		<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
	</form>
    <?php if(_hui('um_open_qq')||_hui('um_open_weibo')||_hui('um_open_weixin')){ ?>
    <div class="other-sign">
	<?php 
	$qq_login = home_url('/oauth/qq?action=login&redirect='.urlencode(um_get_redirect_uri()));
	$weibo_login = home_url('/oauth/weibo?action=login&redirect='.urlencode(um_get_redirect_uri()));
	$weixin_login = home_url('/oauth/weixin?action=login&redirect='.urlencode(um_get_redirect_uri()));
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
    </div>
    <div class="part registerPart">
    <form name="register" id="register" action="<?php bloginfo('url'); ?>/wp-login.php?action=register" method="post" novalidate="novalidate">
        <div id="login-active" class="switch"><i class="fa fa-toggle-off"></i>切换登录</div>
        <h3>注册</h3><p class="status"></p>
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
            <input class="input-control inline" type="text" id="um_captcha" name="um_captcha" placeholder="输入验证码" required>
            <img src="<?php echo add_query_arg('t', str_replace(' ', '_', microtime()), _url_for('captcha')); ?>" class="captcha_img inline" title="点击刷新验证码">
        </p>
        <?php } ?>
        <p>
        	<input class="submit inline" type="submit" value="注册" name="submit">
        </p>
        <a class="close"><i class="fa fa-close"></i></a>	
        <input type="hidden" id="user_security" name="user_security" value="<?php echo  wp_create_nonce( 'user_security_nonce' );?>"><input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
    </form>
    </div>
</div>