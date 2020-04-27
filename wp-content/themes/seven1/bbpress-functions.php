<?php
if ( !function_exists( 'ja_filter_bbpress_allowed_tags' ) ) {
	function ja_filter_bbpress_allowed_tags() {
		return array(
			// Links
			'a' => array(
				'href' => array(),
				'title' => array(),
				'rel' => array(),
				'class'=>array(),
			),
			'u'=> array(),
			'i'=>array(
				'class'=>array()
			),
			'b' => array(),
			'br' => array(),
			'font' => array(
				'color' => array()
			),
			'mark'=>array(
				'class'=>array()
			),
			'h2'=>array(
				'class'=>array()
			),
			'hr' => array(),
			// Quotes
			'blockquote' => array(
				'cite' => array()
			),
			'iframe'=>array(),
			// Code
			'code' => array(),
			'pre' => array(
				'class'=>array(),
				'spellcheck'=>array()
			),

			// Formatting
			'em' => array(),
			'strong' => array(
				'style'=>array()
			),
			'del' => array(
				'datetime' => true,
			),
			'span' => array(
				'class' => array(),
				'contenteditable'=>array()
			),
			'p' => array(
				'dir' => array(),
				'style' => array()
			),
			'div' => array(
				'data-video-url'=>array(),
				'data-video-thumb'=>array(),
				'data-video-title'=>array(),
				'style' => array(),
				'class'=>array()
			),
			// Lists
			'ul' => array(),
				'ol' => array(
				'start' => true,
			),
			'li' => array(),

			// Images
			'img' => array(
				'class'=> true,
				'src' => true,
				'border' => true,
				'alt' => true,
				'height' => true,
				'width' => true,
			)

		);
	}
	add_filter( 'bbp_kses_allowed_tags', 'ja_filter_bbpress_allowed_tags' );
}


//最后一个回复的人
function zrz_get_last_topic_author($topic_id){
	$user_id = get_post_field( 'post_author', $topic_id );
	if($user_id){
		$user_link = zrz_get_user_page_link($user_id);
	}else{
		$user_link = '';
	}
	return $user_link;
}

//回复按钮
function zrz_get_reply_to_link($id){
	if(!$id) return;
	$reply = bbp_get_reply( $id );

	if ( empty( $reply ) || ! bbp_current_user_can_access_create_reply_form() ) {
		return;
	}

	$uri = add_query_arg( array( 'bbp_author_id' => $reply->post_author ) );
	$display_name = get_the_author_meta('display_name',$reply->post_author);
	return '<button class="reply-link mar10-r text" data-name="'.$display_name.'" data-url="'.zrz_get_user_page_url($reply->post_author).'">回复</button>';
}

//编辑按钮
function zrz_get_reply_edit_link($id){
	if(!$id) return;
	$reply = bbp_get_reply( $id );
	$uri = '';
	if($reply){
		if ( ! current_user_can( 'edit_others_replies' ) ) {
			if ( empty( $reply ) || ! current_user_can( 'edit_reply', $reply->ID ) || bbp_past_edit_lock( $reply->post_date_gmt ) ) {
				return;
			}
		}

		$uri = bbp_get_reply_edit_url($reply->ID );
			if ( empty( $uri ) ) {
				return;
			}
	}

	return '<a class="reply-edit-button" href="'.esc_url($uri).'">编辑</a>';
}

//帖子列表缩略图
function zrz_get_bbp_content_img(){
	global $post;
	preg_match_all('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',$post->post_content, $pics );
	$pics_count = count($pics[1]);
	$html = '';
	$type = '';
	$home_url = home_url('/');
	$media_setting = zrz_get_media_settings('media_place');
	$imgs = array();

	foreach ($pics[1] as $pic) {
		if(strpos($pic,'images/smilies') === false){
			$imgs[] = $pic;
		}
	}

	if ( count($imgs) > 0 ){
		$html .= '<div class="topic-list-img mar10-t mar10-b">';
		$i = 0;
		foreach ($imgs as $img) {
			if($i >= 4) break;
			if($media_setting == 'localhost'){
				$url = zrz_get_thumb($img,122,78);
			}else{
				$c_url = wp_get_upload_dir();
			    $param = zrz_upload_dir(wp_get_upload_dir());
			    if(strpos($img,$param['baseurl']) !== false){
			        $url = zrz_get_thumb($img,122,78);
			    }else{
			        $url = zrz_get_thumb(str_replace($c_url['baseurl'],$param['baseurl'],$img),122,78);;
			    }
			}
			$html .= '<div class="fd" style="background-image:url('.$url.')"></div>';
			$i++;
		}
		$html .= '</div>';
	}

	return $html;
}

//禁用自带编辑器编辑器
function zrz_bbp_use_wp_editor( $default = 1 ) {
	$default = 0;
	return (bool)$default;
}
add_filter('bbp_use_wp_editor','zrz_bbp_use_wp_editor');

//禁止游客发帖
function zrz_allow_anonymous( $default = 0 ) {
	$default = 0;
	return (bool)$default;
}
add_filter('bbp_allow_anonymous','zrz_allow_anonymous');

//编辑回帖时自动添加P标签
function zrz_get_form_reply_content() {
	if (isset( $_POST['bbp_reply_content'] ) ) {
		$reply_content = wp_unslash( $_POST['bbp_reply_content'] );
	} elseif ( bbp_is_reply_edit() ) {
		$reply_content = wpautop(bbp_get_global_post_field( 'post_content', 'raw' ));
	} else {
		$reply_content = '';
	}
	return $reply_content;
}
add_filter( 'bbp_get_form_reply_content', 'zrz_get_form_reply_content' );

//编辑话题自动添加P标签
function zrz_get_form_topic_content() {
	if (isset( $_POST['bbp_topic_content'] ) ) {
		$topic_content = wp_unslash( $_POST['bbp_topic_content'] );
	} elseif ( bbp_is_topic_edit() ) {
		$topic_content = wpautop(bbp_get_global_post_field( 'post_content', 'raw' ));
	} else {
		$topic_content = '';
	}

	return $topic_content;
}
add_filter( 'bbp_get_form_topic_content', 'zrz_get_form_topic_content' );

function zrz_bbp_current_user_can_access_create_topic_form() {

	$retval = false;

	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	} elseif ( ( bbp_is_single_forum() || is_page() || is_single() || zrz_is_page(false,'newtopic')) && bbp_is_forum_open() ) {
		$retval = bbp_current_user_can_publish_topics();

	} elseif ( bbp_is_topic_edit() ) {
		$retval = current_user_can( 'edit_topic', bbp_get_topic_id() );
	}

	return (bool) $retval;
}
add_filter( 'bbp_current_user_can_access_create_topic_form', 'zrz_bbp_current_user_can_access_create_topic_form' );

//重定义编辑提示样式
function zrz_get_topic_revision_log( $topic_id = 0 ) {
	// Create necessary variables
	$topic_id     = bbp_get_topic_id( $topic_id );
	$revision_log = bbp_get_topic_raw_revision_log( $topic_id );

	if ( empty( $topic_id ) || empty( $revision_log ) || ! is_array( $revision_log ) ) {
		return false;
	}

	$revisions = bbp_get_topic_revisions( $topic_id );
	if ( empty( $revisions ) ) {
		return false;
	}

	$retval = "\n\n" . '<ul id="bbp-topic-revision-log-' . esc_attr( $topic_id ) . '" class="bbp-topic-revision-log b-t pd10">' . "\n\n";

	// Loop through revisions
	foreach ( (array) $revisions as $revision ) {

		if ( empty( $revision_log[ $revision->ID ] ) ) {
			$author_id = $revision->post_author;
			$reason    = '';
		} else {
			$author_id = $revision_log[ $revision->ID ]['author'];
			$reason    = $revision_log[ $revision->ID ]['reason'];
		}

		$author = zrz_get_user_page_link($author_id);
		$since  = zrz_time_ago($revision->ID);

		$retval .= "\t" . '<li id="bbp-topic-revision-log-' . esc_attr( $topic_id ) . '-item-' . esc_attr( $revision->ID ) . '" class="bbp-topic-revision-log-item">' . "\n";
		if ( ! empty( $reason ) ) {
			$retval .= "\t\t" . sprintf( __( 'This topic was modified %1$s by %2$s. Reason: %3$s', 'bbpress' ), $since, $author, esc_html( $reason ) ) . "\n";
		} else {
			$retval .= "\t\t" . sprintf( __( 'This topic was modified %1$s by %2$s.',              'bbpress' ), $since , $author ) . "\n";
		}
		$retval .= "\t" . '</li>' . "\n";
	}

	$retval .= "\n" . '</ul>' . "\n\n";

	return $retval;
}
add_filter( 'bbp_get_topic_revision_log', 'zrz_get_topic_revision_log' );

function zrz_bbp_get_reply_revision_log( $reply_id = 0 ) {

	$reply_id = bbp_get_reply_id( $reply_id );

	if ( bbp_is_topic( $reply_id ) ) {
		return bbp_get_topic_revision_log( $reply_id );
	}


	$revision_log = bbp_get_reply_raw_revision_log( $reply_id );

	if ( empty( $reply_id ) || empty( $revision_log ) || ! is_array( $revision_log ) ) {
		return false;
	}

	$revisions = bbp_get_reply_revisions( $reply_id );
	if ( empty( $revisions ) ) {
		return false;
	}

	$r = "\n\n" . '<ul id="bbp-reply-revision-log-' . esc_attr( $reply_id ) . '" class="bbp-reply-revision-log">' . "\n\n";

	foreach ( (array) $revisions as $revision ) {

		if ( empty( $revision_log[ $revision->ID ] ) ) {
			$author_id = $revision->post_author;
			$reason    = '';
		} else {
			$author_id = $revision_log[ $revision->ID ]['author'];
			$reason    = $revision_log[ $revision->ID ]['reason'];
		}

		$author = zrz_get_user_page_link($author_id);
		$since  = zrz_time_ago($revision->ID);

		$r .= "\t" . '<li id="bbp-reply-revision-log-' . esc_attr( $reply_id ) . '-item-' . esc_attr( $revision->ID ) . '" class="bbp-reply-revision-log-item">' . "\n";
		if ( ! empty( $reason ) ) {
			$r .= "\t\t" . sprintf( esc_html__( 'This reply was modified %1$s by %2$s. Reason: %3$s', 'bbpress' ), $since , $author, esc_html( $reason ) ) . "\n";
		} else {
			$r .= "\t\t" . sprintf( esc_html__( 'This reply was modified %1$s by %2$s.', 'bbpress' ), $since , $author ) . "\n";
		}
		$r .= "\t" . '</li>' . "\n";

	}

	$r .= "\n" . '</ul>' . "\n\n";

	return $r;
}
add_filter( 'bbp_get_reply_revision_log', 'zrz_bbp_get_reply_revision_log' );

//版主
function zrz_get_forum_moderators($forum_id){
	$users_id = get_post_meta($forum_id,'_bbp_moderator_id',false);
	$html = '';
	if(!empty($users_id)){
		foreach ($users_id as $user_id) {
			$html .= '<span class="pos-r">'.get_avatar($user_id,17).zrz_get_user_page_link($user_id).'（版主）</span>';
		}
	}else{
		$html .= '<span class="pos-r">'.get_avatar(1,17).zrz_get_user_page_link(1).'（版主）</span>';
	}
	if($html){
		return '<div class="fr forum-mod fs12">'.$html.'</div>';
	}else{
		return;
	}
}

//获取帖子数量
function zrz_get_reply_count(){
	$bbp = bbpress();
	$total_int = (int) $bbp->reply_query->found_posts;
	$total = bbp_number_format( $total_int );
	return $total_int;
}

//bbpress 是否启用
function zrz_is_bbp($type){
	if(class_exists( 'bbPress' )){
		if(bbp_is_forum_archive() && $type == 'bbp_front'){
			return true;
		}elseif(is_bbpress() && $type == 'bbp' && !bbp_is_forum_archive()){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

//板块简介
function zrz_bbp_forum_info(){
	$forum_id = bbp_get_topic_forum_id() ? bbp_get_topic_forum_id() : bbp_get_forum_id();
	if(zrz_is_bbp('bbp_front')){
		$stats = bbp_get_statistics();
		$count_topic = $stats['topic_count'];
		$count_reply = $stats['reply_count'];
		unset( $stats );
		$name = '全部';
	}else{
		$count_topic = get_post_meta( $forum_id, '_bbp_topic_count', true );
		$count_reply = get_post_meta( $forum_id, '_bbp_reply_count', true );
		$name = bbp_get_forum_title( $forum_id);
	}
	// <div class="fl">'.get_avatar(1,50).'</div>
	$html = '<div class="forum-top clearfix pd16 pos-r b-b">';
	$html .= '<div class="forum-info fl"><h2>'.$name.'</h2><p class="mar10-t gray fs13"><span>话题 '.$count_topic.'</span><span class="dot">•</span><span class="fs13">回复 '.$count_reply.'</span></p></div>';
	$html .= zrz_get_forum_moderators($forum_id);
	$html .= '</div>';

	return $html;
}

function action_bbp_new_topic_pre_extras( $bbp_clean_post_cache ) {
	if (!isset($_SESSION)) {
		session_start();
	}
	$_SESSION['zrz_credit_add'] = 1;
};
// add the action
add_action( 'bbp_new_topic_pre_extras', 'action_bbp_new_topic_pre_extras', 10, 1 );
add_action( 'bbp_new_reply_pre_extras', 'action_bbp_new_topic_pre_extras', 10, 1 );

//用户页面获取论坛数据
add_action('wp_ajax_zrz_get_topic_or_reply','zrz_get_topic_or_reply');
add_action('wp_ajax_nopriv_zrz_get_topic_or_reply','zrz_get_topic_or_reply');
function zrz_get_topic_or_reply($paged = 0,$user_id = 0,$type = 'topic',$return = false){

	if(!$return){
		$paged = isset($_POST['paged']) ? $_POST['paged'] : 0;
		$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
		$type = isset($_POST['type']) ? $_POST['type'] : 0;
	}

	if($type == 'topic'){
		$number = get_option( '_bbp_topics_per_page', 15 );
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>'publish',
			'post_type'=>'topic',
			'author'=>$user_id,
			'offset'=>($paged-1)*$number
		);
		ob_start();
		if ( bbp_has_topics($arg) ){
	        bbp_get_template_part('loop','topics-user');
	    }
		$list = ob_get_clean();
	}else{
		$number = get_option( '_bbp_replies_per_page', 15 );
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>'publish',
			'post_type'=>'reply',
			'author'=>$user_id,
			'offset'=>($paged-1)*$number
		);
		ob_start();
		if ( bbp_has_replies($arg)){
			bbp_get_template_part( 'loop','replies-user' );
		}
		$list = ob_get_clean();
	}

	if($return){
		return $list;
	}else{
		print json_encode(array('status'=>200,'msg'=>$list));

		exit;
	}
exit;
}

//论坛权限
function zrz_bbp_has_topics_query( $args = array() ) {
	$cap = get_option('zrz_bbp_capabilities',true);
	if(!is_array($cap) || empty($cap)) return $args;

	$user_id = get_current_user_id();

	$user_role = get_user_meta($user_id,'zrz_lv',true);
	$ids = array();
	foreach ($cap as $key => $val) {
		
		if($user_id){
			if(in_array($user_role,$val)){
				continue;
			}
		}
		$ids[] = $key;
	}
	
	$args['post_parent__not_in'] = $ids;

	if(isset($args['post_parent']) && in_array($args['post_parent'],$ids)){
		$args['meta_key'] = 'cccc';
	}

	return $args;
}
add_filter( 'bbp_has_topics_query', 'zrz_bbp_has_topics_query' );

function pw_bbp_shortcodes( $content, $reply_id ) {
	
	$reply_author = bbp_get_reply_author_id( $reply_id );

	if( user_can( $reply_author, pw_bbp_parse_capability() ) )
		remove_all_filters( 'bbp_get_topic_content' );
		return do_shortcode( $content );

	return $content;
}
add_filter('bbp_get_topic_content', 'pw_bbp_shortcodes', 10, 2);

function pw_bbp_parse_capability() {
	return apply_filters( 'pw_bbp_parse_shortcodes_cap', 'publish_forums' );
}