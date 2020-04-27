<?php

/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */
	$reply_id = bbp_get_reply_id();
	$user_id = bbp_get_reply_author_id( $reply_id );

	global $reply_count;

	if(!$reply_count) {
	   $page =  get_query_var('paged') ? get_query_var('paged')-1 : 0;
	   $cpp = get_option('_bbp_replies_per_page');
	   $reply_count = $cpp * $page;
	}
	$reply_count++;
	$args = array(
		'id' => $reply_id,
		'before' => '<div class="admin-liks-drop fs12 t-r">',
		'after' => '</div>',
		'sep' => ''
	);
?>
<li class="b-t pos-r type-reply" id="post-<?php echo $reply_id;?>">

	<div class="reply-center pd10 clearfix">
		<div class="reply-left">
			<?php echo get_avatar($user_id,'46'); ?>
			<?php echo zrz_get_lv($user_id,'lv'); ?>
		</div>
		<div class="reply-right">
			<div class="reply-meta mar5-b gray pos-r fs12">
				<?php
					echo zrz_get_user_page_link($user_id).'<span class="dot">•</span>'.zrz_time_ago($reply_id).(bbp_get_reply_status() == 'publish' ? '' : '<span class="dot">•</span>
					<span class="red">('.bbp_get_reply_status($reply_id).')</span>')
					.(zrz_get_reply_edit_link($reply_id) &&  !current_user_can( 'edit_others_replies' ) ? '<span class="dot">•</span>'.zrz_get_reply_edit_link($reply_id) :'').'<div class="reply-list-r pos-a">'.zrz_get_reply_to_link($reply_id).'<span class="comment-floor">'.$reply_count.'</span></div>';
				?>
			</div>
			<div class="bbp-reply-content entry-content">
				<?php bbp_reply_content(); ?>
			</div><!-- .bbp-reply-content -->
		</div>
	</div>
	<?php
		if(current_user_can( 'moderate', $reply_id )){
			echo bbp_get_reply_admin_links($args);
		}
	?>
</li>
