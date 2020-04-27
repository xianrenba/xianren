<?php
header("Content-type:text/html;character=utf-8");
$url = isset($_GET['url']) ? $_GET['url'] : false;
$postId = isset($_GET['id']) ? $_GET['id'] : 0; //只解析链接，商品ID暂不获取
if (!$url) {
    wp_die('下载参数信息错误！','下载参数信息错误！');exit();
}

$unlock_down = _unlock_url($url, _hui('private_token'));

if (isset($_COOKIE['unlock_down_time'])) {
    wp_die('您的下载太频繁，请一分钟后再试。切勿重复短时间内下载相同资源，以免被扣除下载次数！','错误');exit();
} else {
    $endtime = 60; // 发送一个 60秒过期的 cookie
    setcookie("unlock_down_time", time(), time() + $endtime);
}

// 用户已经登录  
// 为以后添加下载次数
if (is_user_logged_in()) {
    $user_id  = get_current_user_id();
    $go = _download_file($unlock_down);
    exit();

}else{
	wp_die('你还没有登录！，无法下载','未登录');exit();
}