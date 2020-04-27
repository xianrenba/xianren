<?php

// 页码start
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

// Item html
$item_html = '<li class="tip">'.__('没有找到记录','um').'</li>';

//~ 私信start
	$get_pm = isset($_POST['pm']) ? trim($_POST['pm']) : '';
	if( isset($_POST['pmNonce']) && $get_pm && is_user_logged_in() ){
		if ( ! wp_verify_nonce( $_POST['pmNonce'], 'pm-nonce' ) ) {
			$message = __('安全认证失败，请重试！','um');
		}else{
			$pm_title = json_encode(array(
				'pm' => $curauth->ID,
				'from' => $current_user->ID
			));
			if( add_um_message( $curauth->ID, 'unrepm', '', $pm_title, $get_pm ) ) $message = __('发送成功！','um');
		}
	}
	
//~ 私信end

?>
<div class="page-wrapper">
    <div class="dashboard-main">
        <div class="page-header">
	         <h3 id="info">站内消息</h3>
        </div>
		<!-- Page global message -->
		<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
        <div class="dashboard-wrapper select-message">
    <?php
	if($is_me){
		$all_sql = "( msg_type='read' OR msg_type='unread' OR msg_type='repm' OR msg_type='unrepm' )";
		$all = get_um_message($curauth->ID, 'count', $all_sql);	
		$pages = ceil($all/$number);	
		$mLog = get_um_message($curauth->ID, '', $all_sql, $number,$offset);
		$unread = intval(get_um_message($curauth->ID, 'count', "msg_type='unread' OR msg_type='unrepm'"));	
		if($mLog){
			$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 条消息，其中 %2$s 条是新消息（绿色标注）。','um'), $all, $unread) . '</li>';
			foreach( $mLog as $log ){
				$unread_tip = $unread_class = '';
				if(in_array($log->msg_type, array('unread', 'unrepm'))){
					$unread_tip = '<span class="tag">'.__('新！', 'um').'</span>';
					$unread_class = ' class="unread"';
					update_um_message_type( $log->msg_id, $curauth->ID , ltrim($log->msg_type, 'un') );
				}
				$msg_title =  $log->msg_title;
				if(in_array($log->msg_type, array('repm', 'unrepm'))){
					$msg_title_data = json_decode($log->msg_title);
					$msg_title = get_the_author_meta('display_name', intval($msg_title_data->from));
					$msg_title = sprintf(__('%s发来的私信','um'), $msg_title).' <a href="'.add_query_arg('tab', 'message', get_author_posts_url(intval($msg_title_data->from))).'#'.$log->msg_id.'">'.__('查看对话','um').'</a>';
				}
				$item_html .= '<li'.$unread_class.'><div class="message-content">'.strip_tags(convert_smilies(htmlspecialchars_decode($log->msg_content)),"<a><strong><em><blockquote><del><u><code><p><img><style>").'</div><p class="info">'.$unread_tip.' '.$msg_title.'   '.$log->msg_date.'</p></li>';
			}
			if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','um'),$paged, $pages, $number).'</li>';
		}
		
		echo '<div id="message"><ul class="user-msg">'.$item_html.'</ul>'.um_pager($paged, $pages).'</div>';
	}else{
		if( is_user_logged_in() ){
			$item_html = '<li class="tip">'.sprintf(__('与 %s 对话','um'), $user_info->display_name).'</li><li><form id="pmform" role="form" method="post"><input type="hidden" name="pmNonce" value="'.wp_create_nonce( 'pm-nonce' ).'" ><p><textarea class="form-control" rows="3" name="pm" required></textarea></p><p class="clearfix"><a class="btn btn-link pull-left" href="'.add_query_arg('tab', 'message', get_author_posts_url($current_user->ID)).'">'.__('查看我的消息','um').'</a><button type="submit" class="btn btn-primary pull-right">'.__('确定发送','um').'</button></p></form></li>';
			$all = get_um_pm( $curauth->ID, $current_user->ID, true );
			$pages = ceil($all/$number);
			$pmLog = get_um_pm( $curauth->ID, $current_user->ID, false, false, $number, $offset );
			if($pmLog){
				foreach( $pmLog as $log ){
					$pm_data = json_decode($log->msg_title);
					if( $pm_data->from==$curauth->ID ){
						update_um_message_type( $log->msg_id, $curauth->ID , 'repm' );
					}
					$item_html .= '<li class="msg" id="'.$log->msg_id.'"><div class="msg-content '.( $pm_data->from==$current_user->ID ? 'right' : 'left' ).'"><a href="'.get_author_posts_url($pm_data->from).'">'.um_get_avatar( $pm_data->from , '35' , um_get_avatar_type($pm_data->from), false ).'</a><div class="pm-box"><div class="pm-content'.( $pm_data->from==$current_user->ID ? '' : ' highlight' ).'">'.$log->msg_content.'</div><p class="pm-date">'._get_time_ago($log->msg_date).'</p></div></div></li>';
				}
			}
			if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','um'),$paged, $pages, $number).'</li>';
		}else{
			$item_html = '<li class="tip">'.sprintf(__('私信功能需要<a href="javascript:;" class="user-reg" data-sign="0"> 登录 </a>才可使用！','um') ).'</li>';
		}
		
		echo '<div class="author-chat"><ul class="user-msg">'.$item_html.'</ul>'.um_pager($paged, $pages).'</div>';
	}
	?>
	     </div>
    </div>
</div>
