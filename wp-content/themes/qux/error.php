<!DOCTYPE html>
<html style="height:100%">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui=yes">
    <title><?php  $die_title = get_query_var('die_title'); if(isset($die_title)) { echo $die_title; }else{ _e('发生了错误！', 'um'); } ?></title>
    <meta name='robots' content='noindex,follow' >
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp"> <!-- 禁止移动端百度转码 -->
    <meta http-equiv="Cache-Control" content="private">
    <meta name="format-detection" content="telephone=no, email=no"> <!-- 禁止自动识别电话号码和邮箱 -->
    <?php wp_head(); ?>
</head>
<body class="error error-page wp-die" style="height:100%">
<header class="header special-header">

</header>
<div class="wrapper container no-aside">
    <div class="row">
        <div class="main inner-wrap">
            <h1>
                <?php  $die_title = get_query_var('die_title'); if(isset($die_title)) { echo $die_title; }else{ _e('发生了错误！', 'um'); } ?>
            </h1>
            <p class="die-msg">
                <?php  $die_msg = get_query_var('die_msg'); if(isset($die_msg)) { echo $die_msg; }else{ _e('页面发生了一个错误.', 'um'); } ?>
            </p>
            <p>
                <a class="btn btn-lg btn-success link-home" id="linkBackHome" href="<?php echo home_url(); ?>" title="<?php _e('返回首页', 'um'); ?>" role="button"><?php _e('返回首页', 'um'); ?></a>
                <a class="btn btn-lg btn-success link-home" onClick="javascript :history.back(-1);" id="linkBackHome" href="#" title="返回上一页" role="button" style="margin-left: 50px;">返回上一页</a>
            </p>
        </div>
    </div>
</div>
<footer class="footer special-footer">
    <p>&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url() ?>"><?php echo get_bloginfo('name') ?></a></p>
</footer>
</body>
</html>