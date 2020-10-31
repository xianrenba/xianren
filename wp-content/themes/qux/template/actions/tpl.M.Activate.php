<?php
wp_no_robots();

// 激活注册
if (!isset($_GET['key'])) :
    wp_die('无法激活您的注册, 访问的链接不正确.', '无效的激活链接', array('response' => 404));
else :
    $key = sanitize_text_field($_GET['key']);
    // $api = rest_url('/v1/users');
    // $response = wp_remote_post($api, array('key' => $key)); // 对自身GET POST会timeout
    // $body = wp_remote_retrieve_body($response);
    // $data_obj = is_string($body) ? json_decode($body) : (object)array();
    $result = _activate_registration_from_link($key);
    $data_obj = is_array($result) ? $result : array();
    if(is_wp_error($result) || !$data_obj || !isset($data_obj['success']) || intval($data_obj['success']) != 1) {
       wp_die('不能激活您的注册, 请重试注册步骤.','激活注册失败', array('response' => 200));
    }

// 引入头部
get_header();
?>
<div id="content" class="wrapper container no-aside" style="width:100%;height:100%;">
    <div class="main inner-wrap">

    </div>
</div>
<?php get_footer(); ?>
<script>
jQuery(function(){
	tbquire(['ucenter'],function(){
		swal({
			type: "success",
			title: "激活成功",
			text: "你将在 3 秒内引导至登录页面",
			showConfirmButton: false
		});      
   })
   setTimeout(function(){window.location.href = "<?php echo _url_for('signin'); ?>";}, 3000);
});  
</script>
<?php
endif;
?>