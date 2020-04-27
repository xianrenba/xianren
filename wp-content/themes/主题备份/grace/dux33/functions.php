<?php
// Require theme functions
require get_stylesheet_directory() . '/functions-theme.php';

// Customize your functions
/* 替换图片链接为 https */
function https_image_replacer($content){
	if( is_ssl() ){
		/*已经验证使用 $_SERVER['SERVER_NAME']也可以获取到数据，但是貌似$_SERVER['HTTP_HOST']更好一点*/
		$host_name = $_SERVER['HTTP_HOST'];
		$http_host_name='http://'.$host_name.'/wp-content/uploads';
		$https_host_name='https://'.$host_name.'/wp-content/uploads';
		$content = str_replace($http_host_name, $https_host_name, $content);
	}
	return $content;
}
add_filter('the_content', 'https_image_replacer');