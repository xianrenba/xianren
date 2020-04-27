<?php
require_once 'constants.php';
require_once THEME_DIR . '/options.php';
function num_of_author_posts($authorID='')
{
	if ($authorID) 
	{
		$author_query = new WP_Query( 'posts_per_page=-1&author='.$authorID );
		$i=0;
		while ($author_query->have_posts()) : $author_query->the_post();
		++$i;
		endwhile;
		wp_reset_postdata();
		return $i;
	}
	return false;
}
function _hui($name, $default = false) 
{
	$option_name = '';
	if ( function_exists( 'optionsframework_option_name' ) ) 
	{
		$option_name = optionsframework_option_name();
	}
	if ( '' == $option_name ) 
	{
		$option_name = get_option( 'stylesheet' );
		$option_name = preg_replace( "/\W/", "_", strtolower( $option_name ) );
	}
	$options = get_option( $option_name );
	if ( isset( $options[$name] ) ) 
	{
		return $options[$name];
	}
	return $default;
}
function um_add_avatar_folder() 
{
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/avatars';
	if (! is_dir($upload_dir)) 
	{
		mkdir( $upload_dir, 0755 );
	}
}
add_action('init','um_add_avatar_folder');
function qux_get_current_page_url()
{
	global $wp;
	$redirect = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
	if( $redirect)
	{
		return $redirect;
	}
	else
	{
		return get_option( 'permalink_structure' ) == '' ? add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) : home_url( add_query_arg( array(), $wp->request ) );
	}
}
function um_script_parameter()
{
	$object = array();
	$object['ajax_url'] = admin_url('admin-ajax.php');
	$object['admin_url'] = admin_url();
	$object['wp_url'] = get_bloginfo('url');
	$object['um_url'] = get_stylesheet_directory_uri();
	$object['uid'] = (int)get_current_user_id();
	$object['is_admin'] = current_user_can('edit_users')?1:0;
	$object['redirecturl'] = qux_get_current_page_url();
	$object['loadingmessage'] = '正在请求中，请稍等...';
	$object['paged'] = get_query_var('paged')?(int)get_query_var('paged'):1;
	$object['cpage'] = get_query_var('cpage')?(int)get_query_var('cpage'):1;
	if(is_single() && get_post_type() == 'store' )
	{
		global $post;
		$object['pid'] = $post->ID;
	}
	$object['timthumb'] = get_stylesheet_directory_uri().'/func/timthumb.php?src=';
	$object_json = json_encode($object);
	return $object_json;
}
function um_ajax_login()
{
	$result = array('loggedin'=>0,'message'=>'');
	if(isset($_POST['security']) && wp_verify_nonce( $_POST['security'], 'security_nonce'))
	{
		$user_name = sanitize_user( $_POST['username'] );
		$user_password = $_POST['password'];
		$creds = array();
		$creds['user_login'] = $user_name;
		$creds['user_password'] = $user_password;
		$creds['remember'] = ( isset( $_POST['remember'] ) ) ? $_POST['remember'] : false;
		$login = wp_signon($creds, is_ssl());
		if ( ! is_wp_error( $login ) )
		{
			$result['loggedin'] = 1;
			$result['message'] = '登录成功！即将为你刷新';
		}
		else
		{
			$result['message'] = ( $login->errors ) ? strip_tags( $login->get_error_message() ) : '<strong>ERROR</strong>: ' . esc_html__( '请输入正确用户名和密码以登录', 'um' );
		}
	}
	else
	{
		$result['message'] = __('安全认证失败，请重试！','um');
	}
	header( 'content-type: application/json; charset=utf-8' );
	echo json_encode( $result );
	exit;
}
add_action( 'wp_ajax_ajax_login', 'um_ajax_login' );
add_action( 'wp_ajax_nopriv_ajax_login', 'um_ajax_login' );
function um_ajax_register()
{
	$result = array();
	if(isset($_POST['security']) && wp_verify_nonce( $_POST['security'], 'user_security_nonce' ))
	{
		$user_login = sanitize_user($_POST['username']);
		$user_pass = $_POST['password'];
		$user_email = apply_filters( 'user_registration_email', $_POST['email'] );
		$captcha = strtolower(trim($_POST['um_captcha']));
		session_start();
		$session_captcha = strtolower($_SESSION['um_captcha']);
		$errors = new WP_Error();
		if( ! validate_username( $user_login ) )
		{
			$errors->add( 'invalid_username', __( '请输入一个有效用户名','um' ) );
		}
		elseif(username_exists( $user_login ))
		{
			$errors->add( 'username_exists', __( '此用户名已被注册','um' ) );
		}
		elseif(email_exists( $user_email ))
		{
			$errors->add( 'email_exists', __( '此邮箱已被注册','um' ) );
		}
		do_action( 'register_post', $user_login, $user_email, $errors );
		$errors = apply_filters( 'registration_errors', $errors, $user_login, $user_email );
		if ( $errors->get_error_code() )
		{
			$result['success'] = 0;
			$result['message'] = $errors->get_error_message();
		}
		else 
		{
			if(_hui(_email_oauth))
			{
				$link = _generate_registration_activation_link ($user_login, $user_email, $user_pass);
				if($link)
				{
					$message = '<h2 style="color: #333;font-size: 30px;font-weight: 400;line-height: 34px;margin-top: 0;text-align: center;">欢迎, '.$user_login.'</h2>'. '<p style="color: #444;font-size: 17px;line-height: 24px;margin-bottom: 0;text-align: center;">要完成注册, 请点击下面的激活按钮确认你的账户</p>'. '<div id="cta" style="border: 1px solid #e14329; border-radius: 3px; display: block; margin: 20px auto; padding: 12px 24px;max-width: 120px;text-align: center;">'. '<a href="'.$link .'" style="color: #e14329; display: inline-block; text-decoration: none" target="_blank">确认账户</a></div>';
					if(um_basic_mail('',$user_email,'你的账户激活链接',$message,'你的账户激活链接'))
					{
						$result['success'] = 1;
						$result['message'] = __( '注册成功，请前往邮箱激活！','um' );
					}
					else
					{
						$result['success'] = 0;
						$result['message'] = __( '邮件发送失败，请联系管理员！','um' );
					}
				}
				else
				{
					$result['success'] = 0;
					$result['message'] = __( '注册失败，请重试或联系管理员！','um' );
				}
			}
			else
			{
				$user_id = wp_create_user( $user_login, $user_pass, $user_email );
				if ( ! $user_id ) 
				{
					$errors->add( 'registerfail', sprintf( __( '无法注册，请联系管理员','um' ), get_option( 'admin_email' ) ) );
					$result['success'] = 0;
					$result['message'] = $errors->get_error_message();
				}
				else
				{
					update_user_option( $user_id, 'default_password_nag', true, true );
					wp_new_user_notification( $user_id, $user_pass );
					$result['success'] = 1;
					$result['message'] = __( '注册成功，正在自动登录！','um' );
					wp_set_current_user($user_id);
					wp_set_auth_cookie($user_id);
					$result['loggedin'] = 1;
				}
			}
		}
	}
	else
	{
		$result['message'] = __('安全认证失败，请重试！','um');
	}
	header( 'content-type: application/json; charset=utf-8' );
	echo json_encode( $result );
	exit;
}
add_action( 'wp_ajax_ajax_register', 'um_ajax_register' );
add_action( 'wp_ajax_nopriv_ajax_register', 'um_ajax_register' );
function handle_banned_user()
{
	if($user_id = get_current_user_id()) 
	{
		if (current_user_can('administrator')) 
		{
			return;
		}
		$ban_status = get_user_meta($user_id, 'um_banned', true);
		if($ban_status) 
		{
			wp_die(sprintf(__('你的账户已被封禁, 理由为: %s ', 'um'), get_user_meta($user_id, 'um_banned_reason', true)), __('账户冻结', 'um'), 404);
		}
	}
}
add_action('template_redirect', 'handle_banned_user');
add_action('admin_menu', 'handle_banned_user');
function get_account_status($user_id, $return = 'bool') 
{
	$ban = get_user_meta($user_id, 'um_banned', true);
	if($ban) 
	{
		if($return == 'bool') 
		{
			return true;
		}
		$reason = get_user_meta($user_id, 'um_banned_reason', true);
		$time = get_user_meta($user_id, 'um_banned_time', true);
		return array( 'banned' => true, 'banned_reason' => strval($reason), 'banned_time' => strval($time) );
	}
	return $return == 'bool' ? false : array( 'banned' => false );
}
function ban_user($user_id, $reason = '', $return = 'bool') 
{
	$user = get_user_by('ID', $user_id);
	if(!$user) 
	{
		return $return == 'bool' ? false : array( 'success' => false, 'message' => __('指定的用户不存在', 'um') );
	}
	if(update_user_meta($user_id, 'um_banned', 1)) 
	{
		update_user_meta($user_id, 'um_banned_reason', $reason);
		update_user_meta($user_id, 'um_banned_time', current_time('mysql'));
		return $return == 'bool' ? true : array( 'success' => true, 'message' => __('指定的用户已被封禁', 'um') );
	}
	return $return == 'bool' ? false : array( 'success' => false, 'message' => __('当封禁用户时发生了错误', 'um') );
}
function unban_user($user_id, $return = 'bool') 
{
	$user = get_user_by('ID', $user_id);
	if(!$user) 
	{
		return $return == 'bool' ? false : array( 'success' => false, 'message' => __('指定的用户不存在', 'um') );
	}
	if(update_user_meta($user_id, 'um_banned', 0)) 
	{
		return $return == 'bool' ? true : array( 'success' => true, 'message' => __('指定的用户已解禁', 'um') );
	}
	return $return == 'bool' ? false : array( 'success' => false, 'message' => __('当解禁用户时发生了错误', 'um') );
}
function ajax_ban_user()
{
	$action = $_POST['type'] ? $_POST['type'] : 'ban';
	$uid = $_POST['uid'] ? $_POST['uid'] : 0;
	$ico = 'error';
	if(current_user_can('edit_users') && is_user_logged_in())
	{
		if($action == 'ban')
		{
			if(get_account_status($uid) == true)
			{
				$msg = '指定用户不存在或已被封禁';
			}
			else
			{
				if(ban_user($uid , '你的账号已被管理员封禁') == true)
				{
					$ico = 'success';
					$msg = '指定用户已被封禁';
				}
				else
				{
					$msg = '指定用户不存在或已被封禁';
				}
			}
		}
		else if($action == 'unban')
		{
			if(get_account_status($uid) == false)
			{
				$msg = '指定用户不存在或未被封禁';
			}
			else
			{
				if(unban_user($uid))
				{
					$ico = 'success';
					$msg = '指定用户已解除封禁';
				}
				else
				{
					$msg = '指定用户不存在或未被封禁';
				}
			}
		}
	}
	else
	{
		$msg = '抱歉, 你无权更新用户账户状态';
	}
	header( 'content-type: application/json; charset=utf-8' );
	$return = array('msg'=>$msg,'ico'=>$ico);
	echo json_encode($return);
	exit;
}
add_action( 'wp_ajax_ban_user', 'ajax_ban_user' );
function um_load_author_template($template_path)
{
	if(!_hui('open_ucenter',1))return $template_path;
	if(is_author())
	{
		$template_path = UM_DIR.'/template/author.php';
	}
	return $template_path;
}
add_filter( 'template_include', 'um_load_author_template', 1 );
function um_catch_first_image()
{
	global $post, $posts;
	$first_img = '';
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = isset($matches [1] [0]) ? $matches [1] [0] : '';
	if(empty($first_img))
	{
		$random = mt_rand(1, 15);
		$first_img = get_stylesheet_directory_uri();
		$first_img .= '/img/rand/'.$random.'.jpg';
	}
	return $first_img;
}
function um_timthumb($src,$width=240,$height=170)
{
	return get_stylesheet_directory_uri().'/func/timthumb.php?src='.$src.'&w='.$width.'&h='.$height.'&zc=1';
}
function um_get_user_url( $type='', $user_id=0 )
{
	$user_id = intval($user_id);
	if( $user_id==0 )
	{
		$user_id = get_current_user_id();
	}
	$url = add_query_arg( 'tab', $type, get_author_posts_url($user_id) );
	return $url;
}
function um_author_tab_no_robots()
{
	if( is_author() && isset($_GET['tab']) ) wp_no_robots();
}
add_action('wp_head', 'um_author_tab_no_robots');
function um_profile_page( $url ) 
{
	return is_admin() ? $url : um_get_user_url('profile');
}
add_filter( 'edit_profile_url', 'um_profile_page' );
function um_redirect_wp_admin()
{
	$url = qux_get_current_page_url();
	if( (is_admin()&&!stripos($url,'media-upload.php')) && is_user_logged_in() && !current_user_can('edit_users') && ( !defined('DOING_AJAX') || !DOING_AJAX ) )
	{
		wp_redirect( um_get_user_url('profile') );
		exit;
	}
}
add_action( 'init', 'um_redirect_wp_admin' );
function um_edit_post_link($url, $post_id)
{
	if( !current_user_can('edit_users') )
	{
		$url = add_query_arg(array('action'=>'edit', 'id'=>$post_id), um_get_user_url('post'));
	}
	return $url;
}
add_filter('get_edit_post_link', 'um_edit_post_link', 10, 2);
function um_login_logo_url() 
{
	return home_url();
}
add_filter( 'login_headerurl', 'um_login_logo_url' );
function um_login_logo_url_title() 
{
	return get_bloginfo('name');
}
/***
add_filter( 'login_headertitle', 'um_login_logo_url_title' );
function qux_add_admin_bar_menu() 
{
	global $wp_admin_bar;
	$status = get_option('qux-status');
	if($status == 'fake')
	{
		$wp_admin_bar->add_menu( array( 'id' => 'oauth','title' => '前往获取授权','href' => 'https://www.qyblog.cn/oauth') );
	}
}
add_action( 'admin_bar_menu', 'qux_add_admin_bar_menu', 99 );
function qux_check_oauth_this_daily() 
{
	if (is_admin() || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') 
	{
		return true;
	}
	global $themeid;
	$last = get_option('qux-oauth-time');
	$token = get_option('dux-secret-key');
	$theme_id = get_option('theme_id');
	$oauthcode = $token ? $token : $themeid;
	if(!$themeid)
	{
		wp_die(sprintf(base64_decode('5Li76aKY57y65bCR5b+F6KaB5Y+C5pWwdGhlbWVpZO+8jOivt+WJjeW+gDxhIGhyZWY9IiUxJHMiIHRhcmdldD0iX2JsYW5rIiB0aXRsZT0i5LiL6L295Lit5b+DIj7kuIvovb3kuK3lv4M8L2E+6YeN5paw5LiL6L29'),base64_decode('aHR0cHM6Ly93d3cucXlibG9nLmNuL29hdXRo')),base64_decode('5Li76aKY5o6I5p2D6ZSZ6K+v'));
	}
	elseif(!$theme_id)
	{
		$result = wp_remote_get('https://www.qyblog.cn/oauth/oc.php?themeid='.$themeid.'&url='.$_SERVER['HTTP_HOST']);
		if ( is_array( $result ) && $result['response']['code'] == '200' ) 
		{
			$body = json_decode($result['body']);
			if(isset($body->code) && $body->code == 1)
			{
				update_option("theme_id", $themeid);
			}
			else
			{
				wp_die(base64_decode('5Li76aKY6YeH55So5LqG6Z2e5rOVdGhlbWVpZO+8jOivt+WJjeW+gDxhIGhyZWY9Imh0dHBzOi8vd3d3LnF5YmxvZy5jbi9vYXV0aCIgdGFyZ2V0PSJfYmxhbmsiIHRpdGxlPSLkuIvovb3kuK3lv4MiPuS4i+i9veS4reW/gzwvYT7ph43mlrDkuIvovb0='),base64_decode('5Li76aKY5o6I5p2D6ZSZ6K+v'));
			}
		}
		else
		{
			wp_die(base64_decode('5LiO5pyN5Yqh5Zmo6YCa6K6v5aSx6LSl77yM5qOA5p+l5pyN5Yqh5Zmo572R57uc5piv5ZCm5q2j5bi477yM5Y+v5bCd6K+V5YWz6Zet5Li75py65a6J5YWo57uE5oiW6Ziy54Gr5aKZ44CC'),base64_decode('5pyN5Yqh5Zmo6ZSZ6K+v'));
		}
	}
	$now = time();
	if(!$last || (( $now - $last ) > 86400))
	{
		$result = wp_remote_get('https://www.qyblog.cn/oauth/check.php?url='.$_SERVER['HTTP_HOST'].'&authcode='.$oauthcode);
		if ( is_array( $result ) && $result['response']['code'] == '200' ) 
		{
			$body = json_decode($result['body']);
			if(isset($body->code) && $body->code == 1)
			{
				update_option( "qux-status", 'certified' );
			}
			else
			{
				update_option( "qux-status", 'fake' );
			}
		}
		else
		{
			update_option( "qux-status", 'fake' );
		}
		update_option( "qux-oauth-time" , time());
	}
}
 ***/
add_action( 'wp', 'qux_check_oauth_this_daily', 99);
function um_display_name_column( $columns ) 
{
	$columns['um_display_name'] = '显示名称';
	unset($columns['name']);
	return $columns;
}
add_filter( 'manage_users_columns', 'um_display_name_column' );
function um_display_name_column_callback( $value, $column_name, $user_id ) 
{
	if( 'um_display_name' == $column_name )
	{
		$user = get_user_by( 'id', $user_id );
		$value = ( $user->display_name ) ? $user->display_name : '';
	}
	return $value;
}
add_action( 'manage_users_custom_column', 'um_display_name_column_callback', 10, 3 );
function um_update_latest_login( $login ) 
{
	$user = get_user_by( 'login', $login );
	$latest_login = get_user_meta( $user->ID, 'um_latest_login', true );
	$latest_ip = get_user_meta( $user->ID, 'um_latest_ip', true );
	update_user_meta( $user->ID, 'um_latest_login_before', $latest_login );
	update_user_meta( $user->ID, 'um_latest_ip_before', $latest_ip );
	update_user_meta( $user->ID, 'um_latest_login', current_time( 'mysql' ) );
	update_user_meta( $user->ID, 'um_latest_ip', $_SERVER['REMOTE_ADDR'] );
}
add_action( 'wp_login', 'um_update_latest_login', 10, 1 );
function um_latest_login_column( $columns ) 
{
	$columns['um_latest_login'] = '上次登录';
	return $columns;
}
add_filter( 'manage_users_columns', 'um_latest_login_column' );
function um_latest_login_column_callback( $value, $column_name, $user_id ) 
{
	if('um_latest_login' == $column_name)
	{
		$user = get_user_by( 'id', $user_id );
		$value = ( $user->um_latest_login ) ? $user->um_latest_login : $value = __('没有记录','um');
	}
	return $value;
}
add_action( 'manage_users_custom_column', 'um_latest_login_column_callback', 10, 3 );
function um_get_recent_user($number=10)
{
	$user_query = new WP_User_Query( array ( 'orderby' => 'meta_value', 'order' => 'DESC', 'meta_key' => 'um_latest_login', 'number' => $number ) );
	if($user_query) return $user_query->results;
	return;
}
function um_create_nonce_callback()
{
	echo wp_create_nonce( 'check-nonce' );
	die();
}
add_action( 'wp_ajax_um_create_nonce', 'um_create_nonce_callback' );
add_action( 'wp_ajax_nopriv_um_create_nonce', 'um_create_nonce_callback' );
function um_tracker_ajax_callback()
{
	if ( ! wp_verify_nonce( trim($_POST['wp_nonce']), 'check-nonce' ) )
	{
		echo 'NonceIsInvalid';
		die();
	}
	if( $_POST['pid']=='' ) return;
	$pid = sanitize_text_field($_POST['pid']);
	if(!empty($pid))
	{
		$views = get_post_meta($pid,'um_post_views',true)?(int)get_post_meta($pid,'um_post_views',true):0;
		$views++;
		update_post_meta($pid,'um_post_views',$views);
	}
	echo $views;
	die();
}
add_action( 'wp_ajax_um_tracker_ajax', 'um_tracker_ajax_callback' );
add_action( 'wp_ajax_nopriv_um_tracker_ajax', 'um_tracker_ajax_callback' );
function um_paginate($wp_query='')
{
	if(empty($wp_query)) global $wp_query;
	$pages = $wp_query->max_num_pages;
	if ( $pages >= 2 ): $big = 999999999;
	$paginate = paginate_links( array( 'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ), 'format' => '?paged=%#%', 'current' => max( 1, get_query_var('paged') ), 'total' => $pages, 'type' => 'array', 'prev_next' => true, 'prev_text' => __('上一页', 'um'), 'next_text' => __('下一页', 'um') ) );
	echo '<div class="pagination">';
	foreach ($paginate as $value) 
	{
		echo '<span class="pg-item">'.$value.'</span>';
	}
	echo '</div>';
	endif;
}
function um_pager($current, $max)
{
	$paged = intval($current);
	$pages = intval($max);
	if($pages<2) return '';
	$pager = '<div class="pagination clx">';
	$pager .= '<div class="btn-group">';
	if($paged>1) $pager .= '<a class="btn btn-default" style="float:left;padding:6px 12px;" href="' . add_query_arg('page',$paged-1) . '">'.__('上一页','um').'</a>';
	if($paged<$pages) $pager .= '<a class="btn btn-default" style="float:left;padding:6px 12px;" href="' . add_query_arg('page',$paged+1) . '">'.__('下一页','um').'</a>';
	if ($pages>2 )
	{
		$pager .= '<div class="btn-group pull-right"><select class="form-control pull-right" onchange="document.location.href=this.options[this.selectedIndex].value;">';
		for( $i=1; $i<=$pages; $i++ )
		{
			$class = $paged==$i ? 'selected="selected"' : '';
			$pager .= sprintf('<option %s value="%s">%s</option>', $class, add_query_arg('page',$i), sprintf(__('第 %s 页','um'), $i));
		}
		$pager .= '</select></div>';
	}
	$pager .= '</div></div>';
	return $pager;
}
function um_like_article()
{
	$pid = $_POST['pid'];
	$uid = get_current_user_id();
	$return = '';
	if($uid)
	{
		$likes = get_post_meta($pid,'um_post_likes',true);
		if(empty($likes) || $likes == '0')
		{
			$likes = $uid;
			update_post_meta($pid,'um_post_likes',$likes);
		}
		else
		{
			$likes_arr = explode(',', $likes);
			if(in_array($uid, $likes_arr))return;
			$likes .= ','.$uid;
			update_post_meta($pid,'um_post_likes',$likes);
		}
		$meta = get_user_meta($uid,'um_article_interaction',true);
		$meta = json_decode($meta);
		$now_date = date('Y-m-j');
		$credit = _hui('like_article_credit',5);
		$times = _hui('like_article_credit_times',5);
		$get = 0;
		if(!isset($meta->dated)||$now_date!=$meta->dated)
		{
			update_um_credit( $uid , $credit , 'add' , 'um_credit' , sprintf( __('参与文章互动，获得%s积分','um') , $credit ) );
			$new_times = 1;
			$new_meta = json_encode(array('dated'=>$now_date,'times'=>$new_times));
			update_user_meta($uid,'um_article_interaction',$new_meta);
			$get = 1;
		}
		else if($meta->times<$times)
		{
			update_um_credit( $uid , $credit , 'add' , 'um_credit' , sprintf( __('参与文章互动，获得%s积分','um') , $credit ) );
			$new_times = $meta->times;
			$new_times++;
			$new_meta = json_encode(array('dated'=>$now_date,'times'=>$new_times));
			update_user_meta($uid,'um_article_interaction',$new_meta);
			$get = 1;
		}
		//else{}
$return = json_encode(array('get'=>$get,'credit'=>$credit));
	}
	echo $return;
	exit;
}
add_action( 'wp_ajax_like', 'um_like_article' );
function um_collect()
{
	$pid = $_POST['pid'];
	$uid = $_POST['uid'];
	$action = $_POST['act'];
	if($action!='remove')
	{
		$collect = get_user_meta($uid,'um_collect',true);
		$plus=1;
		if(!empty($collect))
		{
			$collect_arr = explode(',', $collect);
			if(in_array($pid, $collect_arr))
			{
				$plus=0;
				return;
			}
			$collect .= ','.$pid;
			update_user_meta($uid,'um_collect',$collect);
		}
		else
		{
			$collect = $pid;
			update_user_meta($uid,'um_collect',$collect);
		}
		$collects = get_post_meta($pid,'um_post_collects',true);
		$collects += $plus;
		$plus!=0?update_post_meta($pid,'um_post_collects',$collects):'';
	}
	else
	{
		$plus = -1;
		$collect = get_user_meta($uid,'um_collect',true);
		$collect_arr = explode(',', $collect);
		if(!in_array($pid, $collect_arr))
		{
			$plus=0;
			return;
		}
		$collect = um_delete_string_specific_value(',',$collect,$pid);
		update_user_meta($uid,'um_collect',$collect);
		$collects = get_post_meta($pid,'um_post_collects',true);
		$collects--;
		update_post_meta($pid,'um_post_collects',$collects);
	}
	echo $plus;
	exit;
}
add_action( 'wp_ajax_collect', 'um_collect' );
function um_delete_string_specific_value($separator,$string,$value)
{
	$arr = explode($separator,$string);
	$key =array_search($value,$arr);
	array_splice($arr,$key,1);
	$str_new = implode($separator,$arr);
	return $str_new;
}
function um_get_avatar( $id , $size='40' , $type='',$src = false)
{
	if($type==='qq')
	{
		$O = array( 'ID'=>_hui('um_open_qq_id'), 'KEY'=>_hui('um_open_qq_key') );
		$U = array( 'ID'=>get_user_meta( $id, 'um_qq_openid', true ), 'TOKEN'=>get_user_meta( $id, 'um_qq_access_token', true ) );
		if( $O['ID'] && $O['KEY'] && $U['ID'] && $U['TOKEN'] )
		{
			$avatar_url = 'https://q.qlogo.cn/qqapp/'.$O['ID'].'/'.$U['ID'].'/100';
		}
	}
	else if($type==='weibo')
	{
		$O = array( 'KEY'=>_hui('um_open_weibo_key'), 'SECRET'=>_hui('um_open_weibo_secret') );
		$U = array( 'ID'=>get_user_meta( $id, 'um_weibo_openid', true ), 'TOKEN'=>get_user_meta( $id, 'um_weibo_access_token', true ) );
		if( $O['KEY'] && $O['SECRET'] && $U['ID'] && $U['TOKEN'] )
		{
			$avatar_url = 'https://tp3.sinaimg.cn/'.$U['ID'].'/180/1.jpg';
		}
	}
	else if($type==='customize')
	{
		$avatar_url = get_bloginfo('url').'/wp-content/uploads/avatars/'.get_user_meta($id,'um_customize_avatar',true);
	}
	else
	{
		return get_avatar($id, $size, _get_default_avatar(),'avatar');
	}
	$avatar_size = $size <= 96 ? 'small' : 'medium';
	if($src)
	{
		$avatar = '<img src="'.$avatar_url.'" class="avatar" width="'.$size.'" height="'.$size.'" />';
	}
	else
	{
		$avatar ='<img src="'.THEME_URI.'/img/avatar/avatar_'.$avatar_size.'.png" data-src="'.$avatar_url.'" class="avatar" width="'.$size.'" height="'.$size.'" />';
	}
	return $avatar;
}
function um_get_avatar_type($user_id)
{
	$id = (int)$user_id;
	if($id===0) return 'default';
	$avatar = get_user_meta($id,'um_avatar',true);
	$customize = get_user_meta($id,'um_customize_avatar',true);
	if( $avatar=='qq' && um_is_open_qq($id) ) return 'qq';
	if( $avatar=='weibo' && um_is_open_weibo($id) ) return 'weibo';
	if( $avatar=='customize' && !empty($customize) ) return 'customize';
	return 'default';
}
function um_resize( $ori )
{
	if( preg_match('/^https:\/\/[a-zA-Z0-9]+/', $ori ) )
	{
		return $ori;
	}
	$info = um_getImageInfo( AVATARS_PATH . $ori );
	if( $info )
	{
		$dst_width = 100;
		$dst_height = 100;
		$scrimg = AVATARS_PATH . $ori;
		if( $info['type']=='jpg' || $info['type']=='jpeg' )
		{
			$im = imagecreatefromjpeg( $scrimg );
		}
		if( $info['type']=='gif' )
		{
			$im = imagecreatefromgif( $scrimg );
		}
		if( $info['type']=='png' )
		{
			$im = imagecreatefrompng( $scrimg );
		}
		if( $info['type']=='bmp' )
		{
			$im = imagecreatefromwbmp( $scrimg );
		}
		if( $info['width']<=$dst_width && $info['height']<=$dst_height )
		{
			return;
		}
		else 
		{
			if( $info['width'] > $info['height'] )
			{
				$height = intval($info['height']);
				$width = $height;
				$x = ($info['width']-$width)/2;
				$y = 0;
			}
			else 
			{
				$width = intval($info['width']);
				$height = $width;
				$x = 0;
				$y = ($info['height']-$height)/2;
			}
		}
		$newimg = imagecreatetruecolor( $width, $height );
		imagecopy($newimg,$im,0,0,$x,$y,$info['width'],$info['height']);
		$scale = $dst_width/$width;
		$target = imagecreatetruecolor($dst_width, $dst_height);
		$final_w = intval($width*$scale);
		$final_h = intval($height*$scale);
		imagecopyresampled( $target, $newimg, 0, 0, 0, 0, $final_w, $final_h, $width, $height );
		imagejpeg( $target, AVATARS_PATH . $ori );
		imagedestroy( $im );
		imagedestroy( $newimg );
		imagedestroy( $target );
	}
	return;
}
function um_getImageInfo( $img )
{
	$imageInfo = getimagesize($img);
	if( $imageInfo!== false) 
	{
		$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
		$info = array( "width" =>$imageInfo[0], "height" =>$imageInfo[1], "type" =>$imageType, "mime" =>$imageInfo['mime'], );
		return $info;
	}
	else 
	{
		return false;
	}
}
function get_cat_ids()
{
	global $wpdb;
	$request = "SELECT $wpdb->terms.term_id FROM $wpdb->terms ";
	$request .= " LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id ";
	$request .= " WHERE $wpdb->term_taxonomy.taxonomy = 'category' ";
	$request .= " ORDER BY term_id asc";
	$categorys = $wpdb->get_results($request,ARRAY_N);
	$ids = array();
	foreach ($categorys as $category)
	{
		$ids[] .= $category[0];
	}
	return $ids;
}
function um_post_source_price($postid)
{
	$price = product_smallest_price($postid);
	$currency = get_post_meta($postid,'pay_currency',true);
	$ico = $currency == 1 ? '<em>¥</em>' : '<em><i class="fa fa-gift"></i></em>';
	$type = $currency == 1 ? '<em>(元)</em>' : '<em>(积分)</em>';
	$content = '<div id="post-price">';
	if($price[3]==0 && $price[4]==0)
	{
		$content .= '<li class="summary-price"><span class="dt">售价 :</span>';
		$content .= '<strong><del>'.$ico.sprintf('%0.2f',$price[0]).$type.'</del></strong>';
		$content .= '</li>';
	}
	else
	{
		$content .= '<li class="summary-price"><span class="dt">售价 :</span>';
		$content .= (getUserMemberType() || $price[4]!=0) ? '<strong><del>'.$ico.sprintf('%0.2f',$price[0]).$type.'</del></strong>' : '<strong>'.$ico.sprintf('%0.2f',$price[0]).$type.'</strong>';
		if($price[4]!=0)
		{
			$content .= '<strong>&nbsp;'.sprintf('%0.2f',$price[2]).'</strong><span>(限时优惠)</span>';
		}
		$content .= '</li>';
		if($price[3]!=0)
		{
			$content .= '<li class="summary-vip-price"><span class="dt">会员价格 :</span>';
			if(getUserMemberType()) 
			{
				$content .= '<strong>'.$ico.sprintf('%0.2f',$price[1]).$type.'</strong>';
			}
			else 
			{
				$content .= get_product_vip_price($ico,$price[0],$postid);
			}
			$content .= '</li>';
		}
	}
	$content .= '</div>';
	return $content;
}
function qux_post_paycontent($content)
{
	if(get_post_status(get_the_ID())!='publish'||!get_post_meta(get_the_ID(),'pay_switch',true))return $content;
	$hidden_content = '';
	if(is_single() && get_post_type()=='post')
	{
		$user_id = get_current_user_id();
		$price = product_smallest_price(get_the_ID());
		$dl_links = get_post_meta(get_the_ID(),'product_download_links',true);
		$pay_content = get_post_meta(get_the_ID(),'product_pay_content',true);
		$hidden_content .= !get_specified_user_and_product_orders(get_the_ID(),$user_id) ? um_post_source_price(get_the_ID()) : '';
		$hidden_content .= '<div id="pay-content">';
		if(!empty($dl_links)): $hidden_content .= '<li class="summary-content"><span class="dt" style="position:absolute;top:0;left:0;">资源信息 :</span>';
		$arr_links = explode(PHP_EOL,$dl_links);
		$i = 0;
		foreach($arr_links as $arr_link)
		{
			$i++;
			$arr_link = explode('|',$arr_link);
			$arr_link[0] = isset($arr_link[0]) ? $arr_link[0]:'';
			$arr_link[1] = isset($arr_link[1]) ? $arr_link[1]:'';
			$arr_link[2] = isset($arr_link[2]) ? $arr_link[2]:'';
			$hidden_content .= '<p style="margin:0 0 0 72px;">'.$i.'. '.$arr_link[0].'&nbsp;&nbsp;';
			if($price[5] < 0.01 || get_specified_user_and_product_orders(get_the_ID(),$user_id))
			{
				$hidden_content .= '下载链接：<a href="'._url_for('download' ,$arr_link[1]).'" title="'.$arr_link[0].'" target="_blank"><i class="fa fa-cloud-download"></i>点击下载</a>&nbsp;&nbsp;密码：'.$arr_link[2];
			}
			else
			{
				$hidden_content .= '*** 隐藏内容购买后可见 ***';
			}
			$hidden_content .= '</p>';
		}
		$hidden_content .= '</li>';
		endif;
		if($price[5] < 0.01 || get_specified_user_and_product_orders(get_the_ID(),$user_id)) $hidden_content .= '<div class="hidden-content">'.$pay_content.'</div>';
		if($price[5] > 0 && !get_specified_user_and_product_orders(get_the_ID(),$user_id))
		{
			$amount = (int)get_post_meta(get_the_ID(),'product_amount',true);
			$btn = $amount>0 ? '<a class="inner-buy-btn" data-top="false"><i class="fa fa-shopping-cart"></i>立即购买</a>' : '<a class="inner-soldout" href="javascript:"><i class="fa fa-shopping-cart">&nbsp;</i>缺货不可购买</a>';
			$hidden_content .= '<div id="pay"><p style="margin:0 0 0 72px;">此处内容需要购买后可见！'.$btn.'</p></div>';
		}
		$hidden_content .= '</div>';
		$see_content = empty($hidden_content) ? $content : $content.'<div class="label-title post"><span id="title"><i class="fa fa-paypal"></i>&nbsp;付费资源</span>'.wpautop($hidden_content).'</div>';
	}
	else
	{
		$see_content = $content;
	}
	return $see_content;
}
add_filter('the_content','qux_post_paycontent',10);
function _post_activity_button(/*$content*/)
{
	$content = '';
	$uid = get_current_user_id();
	$umlikes = get_post_meta(get_the_ID(),'um_post_likes',true);
	$umlikes_array = explode(',',$umlikes);
	$umlikes_count = $umlikes!=0?count($umlikes_array):0;
	$umcollects = get_post_meta(get_the_ID(),'um_post_collects',true);
	if(empty($umlikes)):$umlikes_count=0;
	endif;
	if(empty($umcollects)):$umcollects=0;
	endif;
	if( is_user_logged_in() && in_array($uid, $umlikes_array))
	{
		$unlike = ' love-yes';
		$text = '您已赞';
		$likeico = ' fa-heart';
	}
	else
	{
		$unlike = '';
		$text = '赞一个';
		$likeico = ' fa-heart-o';
	}
	$value = 0;
	if($umlikes_count == 0)
	{
		$liake_author = '';
	}
	else
	{
		$liake_author = '';
		foreach ($umlikes_array as $id) 
		{
			$value++;
			$liake_author .= '<li class="like-user" title="'.get_userdata($id)->display_name.'">'.um_get_avatar($id,40,um_get_avatar_type($id), false).'</li>';
			if($value == 8)
			{
				break;
			}
		}
	}
	if(!empty($uid)&&$uid!=0)
	{
		$content .= '<div class="activity-btn"><ul class="like-author">'.$liake_author.'<li class="post-like-counter"><span><span class="js-article-like-count num">'.$umlikes_count.'</span> 个人</span>已赞</li></ul><a class="like-btn'.$unlike.'" pid="'.get_the_ID().'" uid="'.get_current_user_id().'" href="javascript:;" title="'.$text.'"><i class="fa'.$likeico.'">&nbsp;</i>'.$text.'</a>';
	}
	else
	{
		$content .= '<div class="activity-btn"><ul class="like-author">'.$liake_author.'<li class="post-like-counter"><span><span class="js-article-like-count num">'.$umlikes_count.'</span> 个人</span>已赞</li></ul><a class="like-btn user-reg" title="你必须注册并登录才能点赞"><i class="fa fa-heart-o">&nbsp;</i>赞一个</a>';
	}
	if(!empty($uid)&&$uid!=0)
	{
		$mycollects = get_user_meta($uid,'um_collect',true);
		$mycollects = explode(',',$mycollects);
		$match = 0;
		foreach ($mycollects as $mycollect)
		{
			if ($mycollect == get_the_ID()):$match++;
			endif;
		}
		if ($match==0)
		{
			$content .= '<a class="collect-btn collect-no" pid="'.get_the_ID().'" href="javascript:;" uid="'.get_current_user_id().'" title="点击收藏"><i class="fa fa-star-o">&nbsp;</i>收藏 (<span>'.$umcollects.'</span>)</a>';
		}
		else
		{
			$content .= '<a class="collect-btn collect-yes remove-collect" href="javascript:;" pid="'.get_the_ID().'" uid="'.get_current_user_id().'" title="你已收藏，点击取消"><i class="fa fa-star">&nbsp;</i>收藏 (<span>'.$umcollects.'</span>)</a>';
		}
	}
	else
	{
		$content .= '<a class="collect-btn collect-no" title="你必须注册并登录才能收藏"><i class="fa fa-star-o">&nbsp;</i>收藏 (<span>'.$umcollects.'</span>)</a>';
	}
	if(_hui('btn_shang'))
	{
		if ( _hui('alipay_name') == '' )
		{
			$content .='';
		}
		else
		{
			$content .= '<a href="javascript:;" class="action-rewards" etap="rewards"><i class="fa fa-jpy">&nbsp;</i>' . _hui('alipay_name').'</a>
         <div class="rewards-popover-mask" etap="rewards-close"></div>
         <div class="rewards-popover">
		   <h3>'._hui('alipay_h').'</h3>
		   <div class="rewards-popover-item">
			 <h4>'. _hui('alipay_z').'</h4>
			 <img src="'. _hui('qr_a').'">
		   </div>
		   <div class="rewards-popover-item">
			 <h4>'. _hui('alipay_w'). '</h4>
			 <img src="'. _hui('qr_b'). '">
		   </div>
		 <span class="rewards-popover-close" etap="rewards-close"><i class="fa fa-times"></i></span>
         </div>';
		}
		$content .= '';
	}
	$content .= '</div>';
	echo $content;
}
function um_canonical_url()
{
	switch(TRUE)
	{
		case is_home() : case is_front_page() : $url = home_url('/');
		break;
		case is_single() : $url = get_permalink();
		break;
		case is_tax() : case is_tag() : case is_category() : $term = get_queried_object();
		$url = get_term_link( $term, $term->taxonomy );
		break;
		case is_post_type_archive() : $url = get_post_type_archive_link( get_post_type() );
		break;
		case is_author() : $url = get_author_posts_url( get_query_var('author'), get_query_var('author_name') );
		break;
		case is_year() : $url = get_year_link( get_query_var('year') );
		break;
		case is_month() : $url = get_month_link( get_query_var('year'), get_query_var('monthnum') );
		break;
		case is_day() : $url = get_day_link( get_query_var('year'), get_query_var('monthnum'), get_query_var('day') );
		break;
		default : $url = qux_get_current_page_url();
	}
	if ( get_query_var('paged') > 1 ) 
	{
		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() ) 
		{
			$url = user_trailingslashit( trailingslashit( $url ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var('paged'), 'archive' );
		}
		else 
		{
			$url = add_query_arg( 'paged', get_query_var('paged'), $url );
		}
	}
	return $url;
}
function um_user_profile()
{
	$current_user = wp_get_current_user();
	$li_output = '';
	$li_output .= '<li class="author-link">'.um_get_avatar( $current_user->ID , '36' , um_get_avatar_type($current_user->ID), false ) . sprintf(__('登录者 <a class="author-name" href="%1$s">%2$s</a>','um'), get_edit_profile_url($current_user->ID), $current_user->display_name) . '<a href="'.wp_logout_url(qux_get_current_page_url()).'" title="'.esc_attr__('登出本帐号').'">' . __('登出') . '</a></li>';
	$shorcut_links[] = array( 'icon' => '<i class="fa fa-home"></i>', 'title' => __('个人主页','um'), 'url' => get_author_posts_url($current_user->ID) );
	$shorcut_links[] = array( 'icon' => '<i class="fa fa-edit"></i>', 'title' => __('编辑资料','um'), 'url' => um_get_user_url('profile') );
	if( current_user_can( 'manage_options' ) ) 
	{
		$shorcut_links[] = array( 'icon' => '<i class="fa fa-dashboard"></i>', 'title' => __('管理后台','um'), 'url' => admin_url() );
	}
	$shorcut_html = '<li class="active">';
	foreach( $shorcut_links as $shorcut )
	{
		$shorcut_html .= isset($shorcut['prefix'])?$shorcut['prefix']:'';
		$shorcut_html .= '<a href="'.$shorcut['url'].'">'.$shorcut['icon'].$shorcut['title'].'</a>';
	}
	if(!current_user_can( 'manage_options' ))
	{
		$shorcut_html .= um_whether_signed($current_user->ID);
	}
	$shorcut_html .= '</li>';
	$credit = intval(get_user_meta( $current_user->ID, 'um_credit', true ));
	$follower = um_follower_count($current_user->ID);
	$following = um_following_count($current_user->ID);
	$credit_void = intval(get_user_meta( $current_user->ID, 'um_credit_void', true ));
	$unread_count = intval(get_um_message($current_user->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )"));
	$collects = get_user_meta($current_user->ID,'um_collect',true) ? get_user_meta($current_user->ID,'um_collect',true) : 0;
	$collects_array = explode(',',$collects);
	$collects_count = $collects != 0 ? count($collects_array) : 0;
	$info_array = array( array( 'title' => __('文章','um'), 'url' => um_get_user_url('post'), 'count' => count_user_posts($current_user->ID) ), array( 'title' => __('评论','um'), 'url' => um_get_user_url('comment'), 'count' => get_comments( array('status' => '1', 'user_id'=>$current_user->ID, 'count' => true) ) ), array( 'title' => __('收藏','um'), 'url' => um_get_user_url('collect'), 'count' => intval($collects_count) ), array( 'title' => __('积分','um'), 'url' => um_get_user_url('credit'), 'count' => ($credit) ), array( 'title' => __('关注','um'), 'url' => um_get_user_url('follower'), 'count' => ($follower) ), array( 'title' => __('粉丝','um'), 'url' => um_get_user_url('following'), 'count' => ($following) ), );
	$info_html = '<li class="meta">';
	foreach( $info_array as $info )
	{
		$info_html .= '<span class="meta-info">'.$info['count'].'<a href="'.$info['url'].'"> '.$info['title'].'</a></span>';
	}
	$info_html .= '</li>';
	if(!filter_var($current_user->user_email, FILTER_VALIDATE_EMAIL))
	{
		$friend_html = '';
	}
	else
	{
		$friend_html = '
	<li>
		<div class="input-group">
			<span class="input-group-addon">'.__('推广链接','um').'</span>
			<input class="um_aff_url form-control" type="text" class="form-control" value="'.add_query_arg('aff',$current_user->ID,um_canonical_url()).'">
		</div>
	</li>
	';
	}
	return $li_output.$shorcut_html.$info_html;
}
function um_add_contact_fields($contactmethods)
{
	$contactmethods['um_gender'] = '性别';
	$contactmethods['um_qq'] = 'QQ';
	$contactmethods['um_qq_weibo'] = __('腾讯微博','um');
	$contactmethods['um_sina_weibo'] = __('新浪微博','um');
	$contactmethods['um_weixin'] = __('微信二维码','um');
	$contactmethods['um_twitter'] = __('Twitter','um');
	$contactmethods['um_googleplus'] = 'Google+';
	$contactmethods['um_donate'] = __('支付宝收款二维码','um');
	$contactmethods['wechat_pay'] = __('微信收款二维码','um');
	$contactmethods['um_alipay_email'] = __('支付宝帐户','um');
	return $contactmethods;
}
add_filter('user_contactmethods', 'um_add_contact_fields');
function um_author_link($link, $author_id)
{
	global $wp_rewrite;
	$author_id = (int)$author_id;
	$link = $wp_rewrite->get_author_permastruct();
	if(empty($link))
	{
		$file = home_url('/');
		$link = $file.'?author='.$author_id;
	}
	else
	{
		$link = str_replace('%author%', $author_id, $link);
		$link = home_url(user_trailingslashit($link));
	}
	return $link;
}
add_filter('author_link','um_author_link',10,2);
function um_author_link_request($query_vars)
{
	if(array_key_exists('author_name', $query_vars))
	{
		global $wpdb;
		$author_id = $query_vars['author_name'];
		if($author_id)
		{
			$query_vars['author'] = $author_id;
			unset($query_vars['author_name']);
		}
	}
	return $query_vars;
}
add_filter('request','um_author_link_request');
function um_alipay_post_gather($alipay_email,$amount=10,$hide=0)
{
	if(empty($alipay_email))$alipay_email = _hui('alipay_account');
	if($hide==0)
	{
		$style='display:inline-block;';
		$button = '<input name="pay" type="image" value="转帐" src="https://img.alipay.com/sys/personalprod/style/mc/btn-index.png" />';
	}
	else
	{
		$style='display:none;';
		$button = '<input name="pay" type="hidden" value="转帐"  />';
	}
	$html = '<form id="alipay-gather" style="'.$style.'" action="https://shenghuo.alipay.com/send/payment/fill.htm" method="POST" target="_blank" accept-charset="GBK"><input name="optEmail" type="hidden" value="'.$alipay_email.'" /><input name="payAmount" type="hidden" value="'.$amount.'" /><input id="title" name="title" type="hidden" value="支持一下" /><input name="memo" type="hidden" value="" />'.$button.'</form>';
	return $html;
}
function um_author_page_title()
{
	$author = get_queried_object();
	$name = $author->data->display_name;
	if(isset($_GET['tab']))
	{
		switch($_GET['tab'])
		{
			case 'comment': $title = '评论';
			break;
			case 'forum': $title = '问答社区';
			break;
			case 'collect': $title = '文章收藏';
			break;
			case 'credit': $title = '个人积分';
			break;
			case 'message': $title = '站内消息';
			break;
			case 'profile': $title = '个人资料';
			break;
			case 'orders': $title = '个人订单';
			break;
			case 'siteorders': $title = '订单管理';
			break;
			case 'membership': $title = '会员信息';
			break;
			case 'affiliate': $title = '推广信息';
			break;
			case 'promote': $title = '优惠码管理';
			break;
			case 'following': $title = '我的关注';
			break;
			case 'follower': $title = '我的粉丝';
			break;
			case 'index': $title = '用户中心';
			break;
			default: $title = '文章';
		}
	}
	else
	{
		$title = '用户中心';
	}
	return $name._get_delimiter().$title._get_delimiter().get_bloginfo('name');
}
function um_ob_replace_title()
{
	ob_start('um_replace_title');
}
add_action('wp_loaded', 'um_ob_replace_title');
function um_replace_title($html)
{
	$blogname = get_bloginfo('name');
	$partten = array('/<title>(.*?)<\/title>/i');
	$title = '';
	if($action = get_query_var('action')) 
	{
		switch ($action) 
		{
			case 'signin': $title = __('登录', 'tt');
			break;
			case 'signup': $title = __('注册', 'tt');
			break;
			case 'activate': $title = __('激活注册', 'tt');
			break;
			case 'signout': $title = __('注销', 'tt');
			break;
			case 'findpass': $title = __('找回密码', 'tt');
			break;
			case 'resetpass': $title = __('重置密码', 'tt');
			break;
			case 'new': $title = __('发布文章', 'tt');
			break;
			case 'edit': $title = __('编辑文章', 'tt');
			break;
		}
		if($title)
		{
			$replacement = array('<title>'.$title._get_delimiter().get_bloginfo('name').'</title>');
			$html = preg_replace($partten, $replacement, $html);
		}
	}
	if($oauth = get_query_var('oauth') && get_query_var('oauth_last')) 
	{
		switch ($oauth) 
		{
			case 'qq': $title = __('完善账户信息-连接QQ', 'tt');
			break;
			case 'weibo': $title = __('完善账户信息-连接微博', 'tt');
			break;
		}
		if($title)
		{
			$replacement = array('<title>'.$title._get_delimiter().get_bloginfo('name').'</title>');
			$html = preg_replace($partten, $replacement, $html);
		}
	}
	if($site_manage = get_query_var('manage_child_route')) 
	{
		switch ($site_manage) 
		{
			case 'status': $title = __('站点统计', 'tt');
			break;
			case 'posts': $title = __('文章管理', 'tt');
			break;
			case 'comments': $title = __('评论管理', 'tt');
			break;
			case 'users': $title = __('用户管理', 'tt');
			break;
			case 'orders': $title = __('订单管理', 'tt');
			break;
			case 'coupons': $title = __('优惠码管理', 'tt');
			break;
			case 'members': $title = __('会员管理', 'tt');
			break;
			case 'products': $title = __('商品列表', 'tt');
			break;
			case 'editpost': $title = __('编辑文章', 'tt');
			break;
			default: $title = __('找不到该页面', 'tt');
			break;
		}
		if($title)
		{
			$replacement = array('<title>'.$title._get_delimiter().get_bloginfo('name').'</title>');
			$html = preg_replace($partten, $replacement, $html);
		}
	}
	if(is_author())
	{
		$title = um_author_page_title();
		$replacement = array('<title>'.$title.'</title>');
		if(_hui('open_ucenter')): $html = preg_replace($partten, $replacement, $html);
		else: $html = $html;
		endif;
	}
	if(get_post_type() == 'store')
	{
		if ( is_single() )
		{
			$title = get_the_title(get_the_ID())._get_delimiter().$blogname;
			global $post;
			$post_ID = $post->ID;
			$seo_title = trim(get_post_meta($post_ID, 'title', true));
			if($seo_title) $title = $seo_title. _get_delimiter().get_bloginfo('name');
			$replacement = array('<title>'.$title.'</title>');
		}
		else
		{
			$title = _hui('store_archive_title','商城')._get_delimiter().$blogname;
			$description = _hui('store_archive_des');
			$keywords = _hui('store_archive_subtitle');
			$keywords = explode('-', $keywords);
			$keywords = implode(',', $keywords);
			$replacement = array('<title>'.$title.'</title>');
			$partten[] = '/<meta name=\"description\" content=\"(.*?)\"(.*?)>/i';
			$replacement[] = '<meta name="description" content="'.$description.'"$2>';
			$partten[] = '/<meta name=\"keywords\" content=\"(.*?)\"(.*?)>/i';
			$replacement[] = '<meta name="keywords" content="'.$keywords.'"$2>';
		}
	}
	if($title)
	{
		$html = preg_replace($partten, $replacement, $html);
	}
	else
	{
		$html = $html;
	}
	return $html;
}
function um_convertip($ip)
{
	$url = 'http://ip.taobao.com/service/getIpInfo.php';
	$data = array('ip' => $ip );
	$location = um_curl_post($url,$data);
	$location = json_decode($location);
	if($location && !empty($location->data->country) && (!empty($location->data->region) || !empty($location->data->city)))
	{
		$data = $location->data;
		if($data->country =='中国')
		{
			return $data->region.$data->city.' '.$data->isp;
		}
		else
		{
			return $data->country.$data->region;
		}
	}
	else
	{
		return '火星';
	}
}
function um_curl_post($url,$data)
{
	$post_data = http_build_query($data);
	$post_url= $url;
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_URL, $post_url );
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$return = curl_exec($ch);
	if (curl_errno($ch)) 
	{
		return '';
	}
	curl_close($ch);
	return $return;
}
function um_comment_url_to_author_homepage($content)
{
	global $comment;
	$comment_ID = $comment->comment_ID;
	$user_id = (int)$comment->user_id;
	$url = get_comment_author_url( $comment_ID );
	$author = get_comment_author( $comment_ID );
	if ( $user_id>0 )
	{
		$author_home = um_get_user_url('post',$user_id);
		$return = "<a href='".$author_home."' rel='external nofollow' class='url author_home' title='访问".$author."的个人主页'>$author</a>";
	}
	else
	{
		$return = $author;
	}
	return $return;
}
add_filter('get_comment_author_link','um_comment_url_to_author_homepage',99);
function _get_user_cover ($user_id, $size = 'full', $default = '') 
{
	if(!in_array($size, array('full', 'small'))) 
	{
		$size = 'full';
	}
	if($cover = get_user_meta($user_id, 'um_cover', true)) 
	{
		if($size =='full')
		{
			return um_timthumb($cover,1200,300);
		}
		else
		{
			return um_timthumb($cover,350,145);
		}
	}
	return $default ? $default : UM_URI . '/img/cover/1-' . $size . '.jpg';
}
add_action('wp_insert_comment','inlojv_sql_insert_qq_field',10,2);
function inlojv_sql_insert_qq_field($comment_ID,$commmentdata) 
{
	$qq = isset($_POST['new_field_qq']) ? $_POST['new_field_qq'] : false;
	update_comment_meta($comment_ID,'new_field_qq',$qq);
}
add_filter( 'manage_edit-comments_columns', 'add_comments_columns' );
add_action( 'manage_comments_custom_column', 'output_comments_qq_columns', 10, 2 );
function add_comments_columns( $columns )
{
	$columns[ 'new_field_qq' ] = __( 'QQ号' );
	return $columns;
}
function output_comments_qq_columns( $column_name, $comment_id )
{
	switch( $column_name ) 
	{
		case "new_field_qq" : echo get_comment_meta( $comment_id, 'new_field_qq', true );
		break;
	}
}
add_filter( 'get_avatar', 'inlojv_change_avatar', 10, 3 );
function inlojv_change_avatar($avatar)
{
	global $comment;
	if($comment)
	{
		if( get_comment_meta( $comment->comment_ID, 'new_field_qq', true ) )
		{
			$qq_number = get_comment_meta( $comment->comment_ID, 'new_field_qq', true );
			$qqavatar = file_get_contents('http://ptlogin2.qq.com/getface?appid=1006102&imgtype=3&uin='.$qq_number);
			preg_match('/https:(.*?)&t/',$qqavatar,$m);
			return '<img src="'.stripslashes($m[1]).'" class="avatar avatar-40 photo" width="40" height="40"  alt="qq_avatar" />';
		}
		else
		{
			return $avatar ;
		}
	}
	else
	{
		return $avatar ;
	}
}
function _new_get_excerpt($limit = 150, $after = '...') 
{
	$excerpt = get_the_excerpt();
	if (_new_strlen($excerpt) > $limit) 
	{
		return _str_cut(strip_tags($excerpt), 0, $limit, $after);
	}
	else 
	{
		return $excerpt;
	}
}
function the_layout()
{
	$layout = 'index-blog';
	if(isset($_GET['layout']))
	{
		$layout = 'index-'. $_GET['layout'];
	}
	elseif(_hui('index-s'))
	{
		$layout = _hui('index-s');
	}
	else
	{
		$layout = 'index-blog';
	}
	return $layout;
}
add_filter('the_content', 'fancybox');
function fancybox ($content)
{
	global $post;
	$pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png|swf)('|\")(.*?)>(.*?)<\/a>/i";
	$replacement = '<a$1href=$2$3.$4$5 data-fancybox="images"$6>$7</a>';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}
function new_excerpt_more($more) 
{
	global $post;
	$readmore = '...';
	return '<a rel="nofollow" class="more-link" style="text-decoration:none;" href="'. get_permalink($post->ID) . '">'.$readmore.'</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');
function _get_cms_cat_template ($cat_id) 
{
	$default = 'catlist_bar_0';
	$key = sprintf('cms_home_cat_style_%d', $cat_id);
	$option = _hui($key, $default);
	if (in_array($option, array('catlist_bar_0', 'catlist_bar_1', 'catlist_bar_2', 'catlist_bar_3', 'catlist_bar_4', 'catlist_bar_5', 'catlist_bar_6'))) 
	{
		return $option;
	}
	return $default;
}
function upload_media($filename) 
{
	$parts = explode('.', $filename);
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	foreach ( (array) $parts as $part) $filename .= '.' . $part;
	if(preg_match('/[\x{4e00}-\x{9fa5}]+/u', $filename))
	{
		$filename = substr(md5($filename), 0, 8);
	}
	$filename .= '.' . $extension;
	return $filename ;
}
add_filter('sanitize_file_name', 'upload_media', 5,1);
function _login_header() 
{
	echo '<style type="text/css">
       .login h1 a { background-image:url('._hui('logo_src').');height: 32px;background-size: auto 32px;background-position: center center; }
   </style>';
	echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/css/login.css" />';
	echo "<script type='text/javascript' src='https://apps.bdimg.com/libs/jquery/1.7.2/jquery.min.js'></script>";
	echo "<script type='text/javascript' src='https://cdn.bootcss.com/particles.js/2.0.0/particles.min.js'></script>";
	remove_action('login_head', 'wp_shake_js', 12);
}
add_action('login_head', '_login_header');
function _ligin_particle() 
{
	echo '<div id="particles-js"></div>';
}
add_action('login_header', '_ligin_particle');
function myQaptcha_wp_login() 
{
	echo '<div id="autologin" name="autologin"></div>';
	$url = get_stylesheet_directory_uri();
	$outer.= '<script type="text/javascript" src="' . $url . '/js/libs/jquery-ui.min.js"></script>'."\n";
	$outer.= '<script type="text/javascript" src="' . $url . '/js/libs/jquery.ui.touch.js"></script>'."\n";
	$outer.= '<script type="text/javascript">var myQaptchaJqueryPage="' . $url . '/template/Qaptcha.php";</script>'."\n";
	$outer.= '<script type="text/javascript" src="' . $url . '/js/libs/myqaptcha.jquery.js"></script>'."\n";
	$outer.= '<script type="text/javascript">var newQapTcha = document.createElement("div");newQapTcha.className="QapTcha";var tagIDComment=document.getElementById("autologin");if(tagIDComment){tagIDComment.parentNode.insertBefore(newQapTcha,tagIDComment);}else{var allTagP = document.getElementsByTagName("p");for(var p=0;p<allTagP.length;p++){var allTagTA = allTagP[p].getElementsByTagName("autologin");if(allTagTA.length>0){allTagP[p].parentNode.insertBefore(newQapTcha,allTagP[p]);}}}jQuery(document).ready(function(){jQuery(\'.QapTcha\').QapTcha({disabledSubmit:true,autoRevert:true});});</script>'."\n";
	echo $outer;
}
add_action('login_form', 'myQaptcha_wp_login' );
function _login_footer()
{
	echo "<script type='text/javascript' src='".get_stylesheet_directory_uri()."/js/login.js'></script>";
}
add_filter('login_footer', '_login_footer' );
function um_change_cover()
{
	$uid = isset($_POST['user'])?(int)$_POST['user']:0;
	if(!$uid) $uid = (int)get_current_user_id();
	if(!$uid) return;
	$cover = $_POST['cover'];
	update_user_meta($uid,'um_cover',$cover);
	echo json_encode(array('success'=>1));
	exit;
}
add_action( 'wp_ajax_author_cover', 'um_change_cover' );
function get_true_ip()
{
	static $realIP;
	if (isset($_SERVER))
	{
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$realIP = explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"]);
			$realIP = $realIP[0];
		}
		else if (isset($_SERVER["HTTP_CLIENT_IP"])) 
		{
			$realIP = $_SERVER["HTTP_CLIENT_IP"];
		}
		else 
		{
			$realIP = $_SERVER["REMOTE_ADDR"];
		}
	}
	else 
	{
		if (getenv("HTTP_X_FORWARDED_FOR"))
		{
			$realIP = getenv("HTTP_X_FORWARDED_FOR");
		}
		else if (getenv("HTTP_CLIENT_IP")) 
		{
			$realIP = getenv("HTTP_CLIENT_IP");
		}
		else 
		{
			$realIP = getenv("REMOTE_ADDR");
		}
	}
	$_SERVER['REMOTE_ADDR'] = $realIP;
	return $realIP;
}
add_action( 'init', 'get_true_ip' );
function my_upload_media( $wp_query_obj ) 
{
	global $current_user, $pagenow;
	if( !is_a( $current_user, 'WP_User') ) return;
	if( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' ) return;
	if( !current_user_can( 'manage_options' ) && !current_user_can('manage_media_library') ) $wp_query_obj->set('author', $current_user->ID );
	return;
}
add_action('pre_get_posts','my_upload_media');
function my_media_library( $wp_query ) 
{
	if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) 
	{
		if ( !current_user_can( 'manage_options' ) && !current_user_can( 'manage_media_library' ) ) 
		{
			global $current_user;
			$wp_query->set( 'author', $current_user->id );
		}
	}
}
add_filter('parse_query', 'my_media_library' );
function allow_contributor_uploads() 
{
	$contributor = get_role('contributor');
	$contributor->add_cap('upload_files');
	$contributor->add_cap('edit_published_posts');
}
if ( current_user_can('contributor') && !current_user_can('upload_files') ) 
{
	add_action('init', 'allow_contributor_uploads');
}
function add_register_captcha()
{
	$captcha = THEME_URI.'/template/captcha.php';
	?>
	<p style="overflow:hidden;">
		<label for="um_captcha">验证码<br>
		<input type="text" name="um_captcha" id="um_captcha" aria-describedby="" class="input" value="" size="20" style="float:left;margin-right:10px;width:175px;">
		<img src="<?php echo $captcha;
	?>" class="captcha_img inline" title="点击刷新验证码" onclick="this.src='<?php echo $captcha;
	?>';" style="float:right;margin-top: 5px;"></label>
	</p>
	<?php
}
function add_register_captcha_verify($sanitized_user_login,$user_email,$errors)
{
	if(!isset($_POST['um_captcha'])||empty($_POST['um_captcha']))
	{
		return $errors->add( 'empty_captcha', __( '请填写验证码','um' ) );
	}
	else
	{
		$captcha = strtolower(trim($_POST['um_captcha']));
		session_start();
		$session_captcha = strtolower($_SESSION['um_captcha']);
		if($captcha!=$session_captcha)
		{
			return $errors->add( 'wrong_captcha', __( '验证码错误','tinection' ) );
		}
	}
}
if(_hui('reg_captcha'))
{
	add_action('register_form','add_register_captcha');
	add_action('register_post','add_register_captcha_verify',10,3);
}
function set404()
{
	global $wp_query;
	$wp_query->is_home = false;
	$wp_query->is_404 = true;
	$wp_query->query = array('error'=>'404');
	$wp_query->query_vars['error'] = '404';
}
function _force_permalink()
{
	global $pagenow;
	if(!get_option('permalink_structure'))
	{
		update_option('permalink_structure', '/%postname%.html');
	}
	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) 
	{
		wp_redirect( admin_url( 'admin.php?page=options-framework' ) );
	}
}
add_action('load-themes.php', '_force_permalink');
function _url_for($key, $arg = null, $relative = false)
{
	$routes = (array)json_decode(SITE_ROUTES);
	if(array_key_exists($key, $routes))
	{
		return $relative ? '/' . $routes[$key] : home_url('/' . $routes[$key]);
	}
	$get_uid = function($var)
	{
		if($var instanceof WP_User)
		{
			return $var->ID;
		}
		else
		{
			return intval($var);
		}
	}
	;
	$endpoint = null;
	switch ($key)
	{
		case 'manage_user': $endpoint = 'management/users/' . intval($arg);
		break;
		case 'manage_order': $endpoint = 'management/orders/' . intval($arg);
		break;
		case 'edit_post': $endpoint = 'management/editpost/' . absint($arg);
		break;
		case 'download': $endpoint = 'site/download?url=' . urlencode(_lock_url($arg, _hui('private_token')));
		break;
	}
	if($endpoint)
	{
		return $relative ? '/' . $endpoint : home_url('/' . $endpoint);
	}
	return false;
}
function _filter_default_login_url($login_url, $redirect) 
{
	$login_url = _url_for('signin');
	if ( !empty($redirect) ) 
	{
		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
	}
	return $login_url;
}
add_filter('login_url', '_filter_default_login_url', 10, 2);
function _filter_default_logout_url($logout_url, $redirect) 
{
	$logout_url = _url_for('signout');
	if ( !empty($redirect) ) 
	{
		$logout_url = add_query_arg('redirect_to', urlencode($redirect), $logout_url);
	}
	return $logout_url;
}
add_filter('logout_url', '_filter_default_logout_url', 10, 2);
function _filter_default_register_url() 
{
	return _url_for('signup');
}
add_filter('register_url', '_filter_default_register_url');
if(!_hui('open_ucenter'))
{
	add_action('generate_rewrite_rules', 'user_handle_site_page_rewrite_rules');
	add_filter('query_vars', 'user_add_payment_page_query_vars');
	add_action('template_redirect', 'user_handle_site_page_template', 5);
}
function user_handle_site_page_rewrite_rules($wp_rewrite)
{
	if($ps = get_option('permalink_structure'))
	{
		$new_rules['u/([A-Za-z_-]+)$'] = 'index.php?user=$matches[1]';
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}
function user_add_payment_page_query_vars($public_query_vars) 
{
	if(!is_admin())
	{
		$public_query_vars[] = 'user';
	}
	return $public_query_vars;
}
function user_handle_site_page_template()
{
	$site = strtolower(get_query_var('user'));
	$allowed_routes = (array)json_decode(USER_ROUTES);
	if($site && in_array($site, array_keys($allowed_routes)))
	{
		global $wp_query;
		$wp_query->is_home = false;
		$wp_query->is_page = true;
		$template = THEME_DIR . '/pages/'.$allowed_routes[$site].'.php';
		load_template($template);
		exit;
	}
	elseif ($site) 
	{
		set404();
		return;
	}
}
function _handle_site_page_rewrite_rules($wp_rewrite)
{
	if($ps = get_option('permalink_structure'))
	{
		$new_rules['site/([A-Za-z_-]+)$'] = 'index.php?site_util=$matches[1]';
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}
add_action('generate_rewrite_rules', '_handle_site_page_rewrite_rules');
function _add_payment_page_query_vars($public_query_vars) 
{
	if(!is_admin())
	{
		$public_query_vars[] = 'site_util';
	}
	return $public_query_vars;
}
add_filter('query_vars', '_add_payment_page_query_vars');
function _handle_site_page_template()
{
	$site = strtolower(get_query_var('site_util'));
	$allowed_routes = (array)json_decode(ALLOWED_SITE_ROUTES);
	if($site && in_array($site, array_keys($allowed_routes)))
	{
		global $wp_query;
		$wp_query->is_home = false;
		$wp_query->is_page = true;
		if($site == 'mqpaynotify')
		{
			$template = THEME_DIR . '/payment/alipay_jk/'. $allowed_routes[$site] .'.php';
		}
		elseif($site == 'download' || $site == 'qrcode')
		{
			$template = THEME_DIR . '/func/'.$allowed_routes[$site].'.php';
		}
		elseif($site == 'captcha')
		{
			$template = THEME_DIR . '/template/'.$allowed_routes[$site].'.php';
		}
		else
		{
			$template = THEME_DIR . '/payment/'.$allowed_routes[$site].'.php';
		}
		load_template($template);
		exit;
	}
	elseif ($site) 
	{
		set404();
		return;
	}
}
add_action('template_redirect', '_handle_site_page_template', 5);
function _handle_action_page_rewrite_rules($wp_rewrite)
{
	if($ps = get_option('permalink_structure'))
	{
		$new_rules['m/([A-Za-z_-]+)$'] = 'index.php?action=$matches[1]';
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}
add_action('generate_rewrite_rules', '_handle_action_page_rewrite_rules');
function _add_action_page_query_vars($public_query_vars) 
{
	if(!is_admin())
	{
		$public_query_vars[] = 'action';
	}
	return $public_query_vars;
}
add_filter('query_vars', '_add_action_page_query_vars');
function _handle_action_page_template()
{
	$action = strtolower(get_query_var('action'));
	$allowed_actions = (array)json_decode(ALLOWED_M_ACTIONS);
	if($action && in_array($action, array_keys($allowed_actions)))
	{
		global $wp_query;
		$wp_query->is_home = false;
		$wp_query->is_page = true;
		$template = THEME_TPL . '/actions/tpl.M.' . ucfirst($allowed_actions[$action]) . '.php';
		load_template($template);
		exit;
	}
}
add_action('template_redirect', '_handle_action_page_template', 5);
function handle_oauth_page_rewrite_rules($wp_rewrite)
{
	if($ps = get_option('permalink_structure'))
	{
		$new_rules['oauth/([A-Za-z]+)$'] = 'index.php?oauth=$matches[1]';
		$new_rules['oauth/([A-Za-z]+)/last$'] = 'index.php?oauth=$matches[1]&oauth_last=1';
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
}
add_action('generate_rewrite_rules', 'handle_oauth_page_rewrite_rules');
function add_oauth_page_query_vars($public_query_vars) 
{
	if(!is_admin())
	{
		$public_query_vars[] = 'oauth';
		$public_query_vars[] = 'oauth_last';
	}
	return $public_query_vars;
}
add_filter('query_vars', 'add_oauth_page_query_vars');
function redirect_management_main_route()
{
	if(preg_match('/^\/management([^\/]*)$/i', $_SERVER['REQUEST_URI']))
	{
		if(current_user_can('administrator'))
		{
			wp_redirect(_url_for('manage_status'), 302);
		}
		elseif(!is_user_logged_in()) 
		{
			wp_redirect(_url_for('signin'), 302);
			exit;
		}
		elseif(!current_user_can('edit_users')) 
		{
			wp_die(__('你没有权限访问该页面', 'um'), __('错误: 没有权限', 'um'), 403);
		}
		else
		{
			set404();
			return;
		}
		exit;
	}
	if(preg_match('/^\/management\/orders$/i', $_SERVER['REQUEST_URI']))
	{
		if(current_user_can('administrator'))
		{
			wp_redirect(_url_for('manage_orders'), 302);
		}
		elseif(!is_user_logged_in()) 
		{
			wp_redirect(_url_for('signin'), 302);
			exit;
		}
		elseif(!current_user_can('edit_users')) 
		{
			wp_die(__('你没有权限访问该页面', 'um'), __('错误: 没有权限', 'um'), 403);
		}
		else
		{
			set404();
			return;
		}
		exit;
	}
}
add_action('init', 'redirect_management_main_route');
function handle_management_child_routes_rewrite($wp_rewrite)
{
	if(get_option('permalink_structure'))
	{
		$new_rules['management/([a-zA-Z]+)$'] = 'index.php?manage_child_route=$matches[1]&is_manage_route=1';
		$new_rules['management/orders/([a-zA-Z0-9]+)$'] = 'index.php?manage_child_route=orders&manage_grandchild_route=$matches[1]&is_manage_route=1';
		$new_rules['management/users/([a-zA-Z0-9]+)$'] = 'index.php?manage_child_route=users&manage_grandchild_route=$matches[1]&is_manage_route=1';
		$new_rules['management/editpost/([0-9]{1,})$'] = 'index.php?manage_child_route=editpost&manage_grandchild_route=$matches[1]&is_manage_route=1';
		$new_rules['management/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
		$new_rules['management/orders/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=orders&manage_grandchild_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
		$new_rules['management/users/([a-zA-Z]+)/page/([0-9]{1,})$'] = 'index.php?manage_child_route=users&manage_grandchild_route=$matches[1]&is_manage_route=1&paged=$matches[2]';
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
	return $wp_rewrite;
}
add_filter('generate_rewrite_rules', 'handle_management_child_routes_rewrite');
function handle_manage_child_routes_template()
{
	$is_manage_route = strtolower(get_query_var('is_manage_route'));
	$manage_child_route = strtolower(get_query_var('manage_child_route'));
	$manage_grandchild_route = strtolower(get_query_var('manage_grandchild_route'));
	if($is_manage_route && $manage_child_route)
	{
		global $wp_query;
		$wp_query->is_home = false;
		if($wp_query->is_404()) 
		{
			return;
		}
		if(!is_user_logged_in()) 
		{
			wp_redirect(_url_for('signin'), 302);
			exit;
		}
		if(!current_user_can('edit_users')) 
		{
			wp_die(__('你没有权限访问该页面', 'tt'), __('错误: 没有权限', 'tt'), 403);
		}
		$allow_routes = (array)json_decode(ALLOWED_MANAGE_ROUTES);
		$allow_child = array_keys($allow_routes);
		if(!in_array($manage_child_route, $allow_child))
		{
			set404();
			return;
		}
		if($manage_child_route === 'editpost' && (!$manage_grandchild_route || !preg_match('/([0-9]{1,})/', $manage_grandchild_route)))
		{
			set404();
			return;
		}
		if($manage_child_route === 'orders' && $manage_grandchild_route)
		{
			if(preg_match('/([0-9]{1,})/', $manage_grandchild_route))
			{
				$template = THEME_TPL . '/management/tpl.Manage.Order.php';
				load_template($template);
				exit;
			}
			elseif(in_array($manage_grandchild_route, $allow_routes['orders']))
			{
				$template = THEME_TPL . '/management/tpl.Manage.Orders.php';
				load_template($template);
				exit;
			}
			set404();
			return;
		}
		if($manage_child_route === 'users' && $manage_grandchild_route)
		{
			if(preg_match('/([0-9]{1,})/', $manage_grandchild_route))
			{
				$template = THEME_TPL . '/management/tpl.Manage.User.php';
				load_template($template);
				exit;
			}
			elseif(in_array($manage_grandchild_route, $allow_routes['users']))
			{
				$template = THEME_TPL . '/management/tpl.Manage.Users.php';
				load_template($template);
				exit;
			}
			set404();
			return;
		}
		if($manage_child_route !== 'orders' && $manage_child_route !== 'users' && $manage_child_route !== 'editpost')
		{
			if($manage_grandchild_route) 
			{
				set404();
				exit;
			}
		}
		;
		$template_id = ucfirst($manage_child_route);
		$template = THEME_TPL . '/management/tpl.Manage.' . $template_id . '.php';
		load_template($template);
		exit;
	}
}
add_action('template_redirect', 'handle_manage_child_routes_template', 5);
function add_manage_page_query_vars($public_query_vars) 
{
	if(!is_admin())
	{
		$public_query_vars[] = 'is_manage_route';
		$public_query_vars[] = 'manage_child_route';
		$public_query_vars[] = 'manage_grandchild_route';
	}
	return $public_query_vars;
}
add_filter('query_vars', 'add_manage_page_query_vars');
function _wp_die_handler($message, $title = '', $args = array()) 
{
	$defaults = array( 'response' => 500 );
	$r = wp_parse_args($args, $defaults);
	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) 
	{
		if ( empty( $title ) ) 
		{
			$error_data = $message->get_error_data();
			if ( is_array( $error_data ) && isset( $error_data['title'] ) ) $title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) 
		{
			case 0 : $message = '';
			break;
			case 1 : $message = "{$errors[0]}
		";
		break;
		default : $message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
		break;
	}
}
if ( ! did_action( 'admin_head' ) ) : if ( !headers_sent() ) 
{
	status_header( $r['response'] );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
}
if ( empty($title) ) $title = __('WordPress &rsaquo; Error');
$text_direction = 'ltr';
if ( isset($r['text_direction']) && 'rtl' == $r['text_direction'] ) $text_direction = 'rtl';
elseif ( function_exists( 'is_rtl' ) && is_rtl() ) $text_direction = 'rtl';
global $wp_query;
$wp_query->query_vars['die_title'] = $title;
$wp_query->query_vars['die_msg'] = $message;
include_once UM_DIR . '/error.php';
endif;
die();
}
function _wp_die_handler_switch()
{
return '_wp_die_handler';
}
add_filter('wp_die_handler', '_wp_die_handler_switch');
function hui_get_post_like($pid='', $text='')
{
$pid = $pid ? $pid : get_the_ID();
$uid = get_current_user_id();
$umlikes = get_post_meta($pid,'um_post_likes',true);
$umlikes_array = explode(',',$umlikes);
$umlikes_count = $umlikes!=0?count($umlikes_array):0;
$text = $text ? $text : __('赞', 'haoui');
$event = is_user_logged_in() ? '' : ' user-reg';
if(is_user_logged_in() && in_array($uid,$umlikes_array))
{
	$unlike = 'fa-heart';
	$loveyes = ' love-yes';
}
else
{
	$unlike = 'fa-heart-o';
	$loveyes = '';
}
if(!empty($uid) && $uid!=0)
{
	return '<span class="like-btn'.$event.$loveyes.'" pid="'.$pid.'" uid="'.$uid.'"><i class="fa '.$unlike.'"></i>'.$text.'(<span>'.$umlikes_count.'</span>)&nbsp;</span>';
}
else
{
	return '<span class="like-btn'.$event.$loveyes.'"><i class="fa '.$unlike.'"></i>'.$text.'(<span>'.$umlikes_count.'</span>)&nbsp;</span>';
}
}
function _profile_pass_update($id, $email, $pass1, $pass2)
{
if( ! is_email($email) )
{
	return __('请输入一个有效的电子邮件地址！！！','um');
}
$exists_id = email_exists($email);
if( $exists_id && $exists_id != $id )
{
	return sprintf(__('这个电子邮件地址（%s）已经被使用，请换一个。','um'), $email);
}
$data = array();
$data['ID'] = $id;
$data['user_email'] = $email;
if( !empty($pass1) && !empty($pass2) )
{
	if( $pass1 !== $pass2 )
	{
		return __('两次输入的密码不一致！！！','um');
	}
	$data['user_pass'] = sanitize_text_field($pass1);
}
$user_id = wp_update_user( $data );
if ( ! is_wp_error( $user_id ) )
{
	return __('安全信息已更新','um');
}
return false;
}
function _generate_registration_activation_link ($username, $email, $password) 
{
$base_url = _url_for('activate');
$data = array( 'username' => $username, 'email' => $email, 'password' => $password );
$key = base64_encode(_authdata($data, 'ENCODE', _hui('private_token'), 60*10));
$link = add_query_arg('key', $key, $base_url);
return $link;
}
function _activate_registration_from_link($key) 
{
if(empty($key)) 
{
	return new WP_Error( 'invalid_key', __( '注册激活密钥无效.', 'um' ), array( 'status' => 400 ) );
}
$data = _authdata(base64_decode($key), 'DECODE', _hui('private_token'));
if(!$data || !is_array($data) || !isset($data['username']) || !isset($data['email']) || !isset($data['password']))
{
	return new WP_Error( 'invalid_key', __( '注册激活密钥无效.', 'um' ), array( 'status' => 400 ) );
}
$userdata = array( 'user_login' => $data['username'], 'user_email' => $data['email'], 'user_pass' => $data['password'] );
$user_id = wp_insert_user($userdata);
if(is_wp_error($user_id)) 
{
	return $user_id;
}
$result = array( 'success' => 1, 'message' => __('激活注册成功', 'tt'), 'data' => array( 'username' => $data['username'], 'email' => $data['email'], 'id' => $user_id ) );
$blogname = get_option("blogname");
$umessage = '<p>您的注册用户名和密码信息如下:</p>
   <div style="background-color:#fefcc9; padding:10px 15px; border:1px solid #f7dfa4; font-size: 12px;line-height:160%;">
   用户名:'.$data['username'].'<br>登录密码: '.$data['password'].'<br>登录链接: <a href="'._url_for('signin').'">'._url_for('signin').'</a></div>';
$message = '<p>您的站点「'.$blogname.'」有新用户注册:</p>'. '<div style="background-color:#fefcc9; padding:10px 15px; border:1px solid #f7dfa4; font-size: 12px;line-height:160%;">'. '用户名: '.$data['username'].'<br>注册邮箱: '.$data['email'].'<br>注册时间: '.date("Y-m-d H:i:s"). '<br>注册IP: ' . $_SERVER['REMOTE_ADDR'] . '&nbsp;['.um_convertip($_SERVER['REMOTE_ADDR']).']</div>';
qux_async_mail('',get_option('admin_email'),sprintf(__('您的站点「%s」有新用户注册 :', 'um'), $blogname),$message);
return $result;
}
function _authdata($data, $operation = 'DECODE', $key = '', $expire = 0) 
{
if($operation != 'DECODE')
{
	$data = maybe_serialize($data);
}
$ckey_length = 4;
$key = md5($key ? $key : 'null');
$keya = md5(substr($key, 0, 16));
$keyb = md5(substr($key, 16, 16));
$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($data, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
$cryptkey = $keya.md5($keya.$keyc);
$key_length = strlen($cryptkey);
$data = $operation == 'DECODE' ? base64_decode(substr($data, $ckey_length)) : sprintf('%010d', $expire ? $expire + time() : 0) . substr(md5($data . $keyb), 0, 16) . $data;
$string_length = strlen($data);
$result = '';
$box = range(0, 255);
$rndkey = array();
for($i = 0; $i <= 255; $i++) 
{
	$rndkey[$i] = ord($cryptkey[$i % $key_length]);
}
for($j = $i = 0; $i < 256; $i++) 
{
	$j = ($j + $box[$i] + $rndkey[$i]) % 256;
	$tmp = $box[$i];
	$box[$i] = $box[$j];
	$box[$j] = $tmp;
}
for($a = $j = $i = 0; $i < $string_length; $i++) 
{
	$a = ($a + 1) % 256;
	$j = ($j + $box[$a]) % 256;
	$tmp = $box[$a];
	$box[$a] = $box[$j];
	$box[$j] = $tmp;
	$result .= chr(ord($data[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
}
if($operation == 'DECODE') 
{
	if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) 
	{
		return maybe_unserialize(substr($result, 26));
	}
	else 
	{
		return false;
	}
}
else 
{
	return $keyc . str_replace('=', '', base64_encode($result));
}
}
function _download_file($file_dir)
{
if (substr($file_dir, 0, 7) == 'http://' || substr($file_dir, 0, 8) == 'https://' || substr($file_dir, 0, 10) == 'thunder://' || substr($file_dir, 0, 7) == 'magnet:' || substr($file_dir, 0, 5) == 'ed2k:') 
{
	$file_path = chop($file_dir);
	echo "<script type='text/javascript'>window.location='$file_path';</script>";
	exit;
}
$file_dir = chop($file_dir);
if (!file_exists($file_dir)) 
{
	return false;
}
$temp = explode("/", $file_dir);
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . end($temp) . "\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($file_dir));
ob_end_flush();
@readfile($file_dir);
}
function noemail_page()
{
global $current_user;
$current_id = $current_user->ID;
//登录用户 ID
$current_email = $current_user->user_email;
$scheme = is_ssl() && !is_admin() ? 'https' : 'http';
$current_url = $scheme . '://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
//当前页面
$profile_url = get_author_posts_url($current_id).'?tab=profile';
//跳转到编辑资料页面
if (is_user_logged_in())
{
	if (!$current_email && $current_url!=$profile_url)
	{
		wp_redirect($profile_url.'#pass-form');
		exit;
	}
}
}
add_action( 'wp', 'noemail_page', 3 );
require_once THEME_DIR . '/func/topic.php';
require_once THEME_DIR . '/func/zhuanti-images.php';
require_once THEME_DIR . '/func/bulletin.php';
require_once THEME_DIR . '/func/first-letter-avatar.php';
require_once THEME_DIR . '/func/affiliate.php';
require_once THEME_DIR . '/func/follow.php';
require_once THEME_DIR . '/func/membership.php';
require_once THEME_DIR . '/func/shop.php';
require_once THEME_DIR . '/func/credit.php';
require_once THEME_DIR . '/func/message.php';
require_once THEME_DIR . '/func/mail.php';
require_once THEME_DIR . '/func/meta-box.php';
require_once THEME_DIR . '/func/open-social.php';
require_once THEME_DIR . '/func/shortcode.php';
require_once THEME_DIR . '/func/forum.php';
require_once THEME_DIR . '/func/forum-sql.php';
require_once THEME_DIR . '/func/ajax.php';
?>