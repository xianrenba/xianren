<?php
    //通过用户ID数组获取用户名
    function get_user_array($users,$type = true){

        $total_users = count($users);
        $last_users = array_slice($users,-3,3);
    	$last_users_count = count($last_users);
        $avatar = '';

		switch ($last_users_count) {
			case 1:
				if($type){
                    $avatar = get_avatar($last_users[0] , 48);
				}
				$user_html = $avatar.zrz_get_user_page_link($last_users[0]).' ';
				break;
			case 2:
				if($type){
	    			$avatar = get_avatar($last_users[1] , 48);
	    		}
	    		$user_html = $avatar.zrz_get_user_page_link($last_users[1]).__(' 和 ','ziranzhi2').zrz_get_user_page_link($last_users[0]).__(' 等人','ziranzhi2');
				break;
			case 3:
				if($type){
					$avatar = get_avatar($last_users[2] , 48);
				}
				$user_html = $avatar.zrz_get_user_page_link($last_users[2]).__('，','ziranzhi2').zrz_get_user_page_link($last_users[1]).__(' 和 ','ziranzhi2').zrz_get_user_page_link($last_users[0]).__(' 等 ','ziranzhi2').$total_users.__(' 人','ziranzhi2');
				break;
			default:
				if($type){
					$avatar = get_avatar(0 , 48);
				}
				$user_html = $avatar.'匿名';
				break;
		}
    	   return $user_html;
    }

/*
*   使用akismet过滤垃圾
*   $content['comment_author'] = $name;
*   $content['comment_author_email'] = $email;
*   $content['comment_author_url'] = $website;
*   $content['comment_content'] = $message;
*/
function zrz_check_spam($content) {

	$isSpam = FALSE;

	$content = (array) $content;

    //如果启用了akismet插件
	if (function_exists('akismet_init')) {

		$wpcom_api_key = get_option('wordpress_api_key');

		if (!empty($wpcom_api_key)) {

			global $akismet_api_host, $akismet_api_port;

			$content['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
			$content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$content['referrer'] = $_SERVER['HTTP_REFERER'];
			$content['blog'] = get_option('home');

			if (empty($content['referrer'])) {
				$content['referrer'] = get_permalink();
			}

			$queryString = '';

			foreach ($content as $key => $data) {
				if (!empty($data)) {
					$queryString .= $key . '=' . urlencode(stripslashes($data)) . '&';
				}
			}

			$response = akismet_http_post($queryString, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);

			if ($response[1] == 'true') {
				$isSpam = TRUE;
			}

		}
	}

	return $isSpam;
}
