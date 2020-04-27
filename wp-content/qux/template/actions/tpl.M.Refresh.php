<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" >
    <meta name="robots" content="noindex,follow">
    <title><?php _e('刷新固定链接规则', 'haoui'); ?></title>
</head>
<body>
<?php

/**
 * 此为私有模板，用于提供内部刷新固定链接缓存等
 * // 此页面只在开启了主题debug模式有效
 */

if(isset($_GET['token']) && trim($_GET['token']) == _hui('private_token')){
    if($ps = get_option('permalink_structure')){
    	
        //刷新固定链接缓存
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        echo sprintf(__('刷新固定链接成功 <a href="%1$s">返回首页</a>', 'haoui'), home_url());
    }else{
        echo __('请自定义固定链接，其是主题高级功能的基础', 'haoui');
    }
}else{
    echo __('禁止访问', 'haoui');
    // 3秒后重定向至首页
    $home = home_url();
    header("refresh:3;url={$home}");
}
?>
</body>
</html>