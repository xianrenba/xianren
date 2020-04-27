<?php

/**
 * Topics Loop - Single
 *
 * @package bbPress
 * @subpackage Theme
 */
 $views = get_post_meta(get_the_id(),'views',true);
 $views = '<span class="dot">•</span>'.( $views ? $views : '0').' 次点击';
 $user_id = bbp_get_topic_author_id();
?>
<li <?php echo bbp_get_topic_class('','pos-r topic'); ?>>
	<div class="topic-list clearfix pd10 b-b">
        <div class="topic-list-left">
            <?php echo get_avatar($user_id,36); ?>
            <?php echo zrz_get_lv($user_id,'lv'); ?>
        </div>

		<div class="topic-user-meta mar10-b fs12 gray">
			<?php echo zrz_get_user_page_link($user_id); ?>
			<span class="dot">•</span>
            <?php echo zrz_time_ago(bbp_get_topic_last_active_id()); ?>
		</div>
		<div class="topic-list-center">

			<h2 class="topic-title mar10-b">
				<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>
			</h2>

			<?php if(zrz_get_reading_settings('show_topic_thumb'))
				echo zrz_get_bbp_content_img();
			?>
			<div class="topic-meta gray fs12">
				<?php echo bbp_get_topic_voice_count() ? '<span>'.bbp_get_topic_voice_count().'人参与</span>' : ''; ?><?php echo $views; ?> <?php echo (bbp_get_topic_voice_count() >= 2) ?  '<span class="mobile-hide"><span class="dot">•</span>最后回复来自 '.zrz_get_last_topic_author(bbp_get_topic_last_active_id()).'</span>' : ''; ?><?php echo bbp_get_topic_status() != 'publish' ? '<span class="mobile-hide"><span class="dot">•</span><span class="red">('.bbp_get_topic_status().')</span></span>' : ''; ?>

			</div>
		</div>

		<div class="topic-list-right pos-a fs12">
			<a class="topic-list-forum mar10-r mobile-hide" href="<?php echo bbp_get_forum_permalink( bbp_get_topic_forum_id() ); ?>"><?php echo bbp_get_forum_title( bbp_get_topic_forum_id() ); ?></a>
			<span class="topic-list-count"><?php bbp_show_lead_topic() ? bbp_topic_reply_count() : bbp_topic_post_count(); ?></span>
		</div>
	</div>
</li>
