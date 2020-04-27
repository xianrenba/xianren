<?php
// Require theme functions
require get_stylesheet_directory() . '/func/functions.php';
require get_stylesheet_directory() . '/func/functions-theme.php';
require get_stylesheet_directory() . '/func/functions-xzh.php';
require get_stylesheet_directory() . '/func/functions-video.php';

/**
* 移除底部js代码 Disable embeds
*/
function disable_embeds_init(){
global $wp;
$wp->public_query_vars = array_diff($wp->public_query_vars, array('embed'));
remove_action('rest_api_init', 'wp_oembed_register_route');
add_filter('embed_oembed_discover', '__return_false');
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
add_filter('tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin');
add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
}
add_action('init', 'disable_embeds_init', 9999);
function disable_embeds_tiny_mce_plugin($plugins){
return array_diff($plugins, array('wpembed'));
}
function disable_embeds_rewrites($rules){
foreach ($rules as $rule => $rewrite) {
if (false !== strpos($rewrite, 'embed=true')) {
unset($rules[$rule]);
}
}
return $rules;
}
function disable_embeds_remove_rewrite_rules(){
add_filter('rewrite_rules_array', 'disable_embeds_rewrites');
flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'disable_embeds_remove_rewrite_rules');
function disable_embeds_flush_rewrite_rules(){
remove_filter('rewrite_rules_array', 'disable_embeds_rewrites');
flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'disable_embeds_flush_rewrite_rules');
//移除 WordPress 头部加载 DNS 预获取（dns-prefetch）
function remove_dns_prefetch( $hints, $relation_type ) {
if ( 'dns-prefetch' === $relation_type ) {
return array_diff( wp_dependencies_unique_hosts(), $hints );
}
return $hints;
}
add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );
//WordPress 5.0+移除 block-library CSS
add_action( 'wp_enqueue_scripts', 'fanly_remove_block_library_css', 100 );
function fanly_remove_block_library_css() {
wp_dequeue_style( 'wp-block-library' );
}
//移除顶部多余信息
remove_action('wp_head', 'feed_links', 2); //文章和评论feed
remove_action('wp_head', 'feed_links_extra', 3);// 额外的feed,例如category, tag页
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );//rel=shortlink
remove_action('wp_head', 'rel_canonical' );
remove_action('wp_head','rsd_link');//移除head中的rel="EditURI"
remove_action('wp_head','wlwmanifest_link');//移除head中的rel="wlwmanifest"
remove_action('template_redirect','wp_shortlink_header',11,0);//移除返回 HTTP 头中的 rel=shortlink
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // 上、下篇.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
//完全禁用wp-json
function disable_rest_api( $access ) {
return new WP_Error( '无访问权限', 'Soooooryyyy', array(
'status' => 403
) );
}
add_filter( 'rest_authentication_errors', 'disable_rest_api' );
//移除顶部wp-json禁用REST API
add_filter('json_enabled', '__return_false' );
add_filter('json_jsonp_enabled', '__return_false' );
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action('template_redirect', 'rest_output_link_header', 11 );
// 关闭 Feed RSS
function jlove_disable_feed() {
wp_die(__('<h1>Feed OFF 暂不提供 Feed 服务，请访问本网站<a href="'.get_bloginfo('url').'">首页</a>！</h1>'));
}
add_action('do_feed', 'jlove_disable_feed', 1);
add_action('do_feed_rdf', 'jlove_disable_feed', 1);
add_action('do_feed_rss', 'jlove_disable_feed', 1);
add_action('do_feed_rss2', 'jlove_disable_feed', 1);
add_action('do_feed_atom', 'jlove_disable_feed', 1);

// Customize your functions

// 代码结束
?>