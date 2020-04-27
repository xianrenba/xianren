<?php
/*
 * Plugin Name: WP Editor Imgur Button
 * Plugin URI: http://codehay.net/q/plugin-them-button-upload-anh-len-imgur-com-cho-tinymce-editor/
 * Description: Plugin Upload ảnh lên Imgur.com cho wordpress
 * Author: Sơn Trần
 * Version: 1.1
 * Author URI: http://codehay.net
 */
 

include 'option-file.php';
 
if(get_option('addcomment')) {
	function gk_comment_form( $fields ) {
		ob_start();
	   wp_editor( '', 'comment', array(
			 'quicktags' => false,
			 'media_buttons' => false,
			 'textarea_rows' => 10
			 ));
		$fields['comment_field'] = ob_get_clean();
		return $fields;
	}
	add_filter( 'comment_form_defaults', 'gk_comment_form' );
	add_action('init', 'imgur_add_my_tc_button');
	add_action('init', 'imgur_tc_css');
	add_action('init', 'imgur_action_js');
	function imgur_add_my_tc_button() {
		 //if( !current_user_can('edit_posts') && !current_user_can('edit_pages')) {return;}
		 //if( get_user_option('rich_editing') == 'true' ) {
			 add_filter('mce_external_plugins', 'imgur_add_tiny_plugin');
			 add_filter('mce_buttons', 'imgur_register_my_tc_button');
		 //}
	}
} else {
	add_action('admin_head', 'imgur_add_my_tc_button');
	add_action('admin_enqueue_scripts', 'imgur_tc_css');
	add_action('init', 'imgur_action_js');
	
	function imgur_add_my_tc_button() {
		global $typenow;
		 if( !current_user_can('edit_posts') && !current_user_can('edit_pages')) {return;}
		 // verify the post type
		if( ! in_array( $typenow, array( 'post', 'page' ) ) )
			return;
		 if( get_user_option('rich_editing') == 'true' ) {
			 add_filter('mce_external_plugins', 'imgur_add_tiny_plugin');
			 add_filter('mce_buttons', 'imgur_register_my_tc_button');
		 }
	}
}

 


 
 function imgur_add_tiny_plugin($plugin_array) {
	 $plugin_array['imgur_tc_button'] = plugins_url('/js/button.js', __FILE__);
	 return $plugin_array;
 }
 
 function imgur_register_my_tc_button($buttons) {
	 array_push($buttons, 'imgur_tc_button');
	 return $buttons;
 }
 
 function imgur_tc_css() {
	wp_enqueue_style('imgur-tc', plugins_url('/style/style.css', __FILE__));
 }
 
 
function imgur_action_js() {
    wp_enqueue_script('ajax-action', plugins_url('/js/ajax-action.js',__FILE__), array('jquery'), false, true);
    wp_localize_script('ajax-action', 'sb_imgur_ajax', array('url' => admin_url('/admin-ajax.php')));
}


function imgur_uploader_action() {
	if(isset($_FILES["img_file"]["type"])) {
		$img=$_FILES['img_file']; 
		 if($img['name']==''){   
		 }else{ 
			  $filename = $img['tmp_name']; 
			  $client_id = esc_attr(get_option('clientid'));
			  $handle = fopen($filename, "r"); 
			  $data = fread($handle, filesize($filename)); 
			  $pvars   = array('image' => base64_encode($data)); 
			  $timeout = 30; 
			  $curl = curl_init(); 
			  curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json'); 
			  curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); 
			  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id)); 
			  curl_setopt($curl, CURLOPT_POST, 1); 
			  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars); 
			  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
			  $out = curl_exec($curl); 
			  curl_close ($curl); 
			  $pms = json_decode($out,true); 
			  $url=$pms['data']['link']; 
			  if($url!=""){ 
					echo "<img src='$url'/><br>"; 
					exit();
			  } 
			  exit();
		 }
		 exit();
	}
	exit();
}
add_action('wp_ajax_imgur_uploader_action','imgur_uploader_action');
add_action('wp_ajax_nopriv_imgur_uploader_action', 'imgur_uploader_action');

