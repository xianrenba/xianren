<?php

/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */
	$reply_id = bbp_get_reply_id();
    $user_id = get_post_field('author',$reply_id);
    $topic_id = wp_get_post_parent_id( $reply_id );
    $forum_id = wp_get_post_parent_id($topic_id);
?>
<li class="pos-r type-reply pd10" id="post-<?php echo $reply_id;?>">
	<div class="reply-meta mar5-b gray pos-r fs12">
        <span>
            <a class="topic-list-forum" href="<?php echo bbp_get_forum_permalink( $forum_id ); ?>"><?php echo bbp_get_forum_title( $forum_id ); ?></a>
        </span>
        <span class="dot">❯</span>
        <span><a href="<?php echo get_permalink($topic_id); ?>"><?php echo get_the_title($topic_id); ?></a> 中说：</span>
		<span class="pos-a" style="right:0;top:0"><?php echo zrz_time_ago($reply_id);?></span>
	</div>
	<div class="mar20-t mar10-b">
		<?php echo apply_filters('the_content',convert_smilies(strip_tags(wpautop(get_post_field('post_content',$reply_id))))) ?>
	</div><!-- .bbp-reply-content -->
</li>
