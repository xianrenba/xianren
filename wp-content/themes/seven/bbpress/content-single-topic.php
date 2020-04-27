<?php

/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

$topic_id = bbp_get_topic_id();
$user_id = bbp_get_reply_author_id( $topic_id );

//翻页
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
?>

<div id="bbpress-forums" class="bbpress-wrapper">
	<?php if ( post_password_required() ) : ?>
		<?php bbp_get_template_part( 'form', 'protected' ); ?>
	<?php else : ?>
		<div class="box mar10-b">
			<?php if ( bbp_show_lead_topic() ) : ?>
				<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>
			<?php endif; ?>
			<header class="pd10 b-b pos-r clearfix topic-header">
				<div class="topic-header-meta">
					<div class="fs13 bbp-breadcrumb"><?php
						$page = bbp_get_page_by_path( bbp_get_root_slug() );
						if ( ! empty( $page ) ) {
							$root_url = get_permalink( $page->ID );
						} else {
							$root_url = get_post_type_archive_link( bbp_get_forum_post_type() );
						}
						echo '<a href="'.$root_url.'">全部</a>'.'<span class="dot">›</span>'.'<a href="'.bbp_get_forum_permalink().'">'.bbp_get_forum_title().'</a>';
					?></div>
					<h1 class="mar10-b mar10-t" ref="title">
						<?php bbp_topic_title(); ?>
					</h1>
					<div class="topic-meta gray fs12">
						<?php echo zrz_get_user_page_link($user_id); ?><span class="dot">•</span><?php echo zrz_time_ago($topic_id); ?>
						<?php echo bbp_get_topic_voice_count() ? '<span class="mobile-hide"><span class="dot">•</span>'.bbp_get_topic_voice_count().'人参与</span>' : ''; ?>
						<span class="dot">•</span><span><span ref="postViews">0</span> 次点击</span>
						<?php echo bbp_get_topic_status() != 'publish' ? '<span class="mobile-hide"><span class="dot">•</span><span class="red">('.bbp_get_topic_status().')</span></span>' : ''; ?>
					</div>
				</div>
				<?php echo get_avatar($user_id,'73'); ?>
			</header>
			<div class="entry-content pd10">
				<?php bbp_topic_content();?>
				<?php
					if(current_user_can( 'edit_topic', $topic_id )){
						$args = array(
							'id' => $topic_id,
							'before' => '<div class="admin-liks-drop fs12 t-r">',
							'after' => '</span></div>',
							'sep' => ''
						);
						bbp_reply_admin_links($args);
					}
				?>
			</div>
			<footer class="topic-footer b-t pos-r clearfix box-header pd10">
				<div class="fl"><?php echo zrz_get_share(); ?></div><div class="fr"><button class="text" @click="favorites(<?php echo $topic_id; ?>)"><span v-text="favoritesText">加入收藏</span></button></div>
			</footer><!-- .entry-footer -->

		</div>
		<?php
		$args = array(
			'post_type'=>'reply',
			//'posts_per_page'=>get_option( '_bbp_replies_per_page', 15 ) - 1,
		);
		if ( bbp_has_replies($args) ) : ?>
		<ul class="box content-reply-list mar10-b" id="reply-list">
			<div class="pd10 gray fs12">
				<?php echo zrz_get_reply_count(); ?> 讨论 <?php echo zrz_get_reply_count() ? '<b class="gray mar10-r mar10-l">|</b> 直到 '.get_post_time("Y-m-d g:i:s", false,bbp_get_topic_last_active_id()) : ''; ?>
			</div>
			<div ref="replyList">
				<?php bbp_get_template_part( 'loop','replies' ); ?>
			</div>
			<div id="zrz-pager">
				<?php
					$per_page = get_option( '_bbp_replies_per_page', 15 );
				   	$count = zrz_get_reply_count();
				   	$pages = $count ? ceil( $count / $per_page) : 1;
		    	?>
			</div>
			<div class="bbp-pagination hide">
				<div class="bbp-pagination-links"><?php bbp_topic_pagination_links(); ?></div>
			</div>
			<page-nav class="b-t" nav-type="bbp-reply-<?php echo $topic_id; ?>" :paged="'<?php echo $paged; ?>'" :pages="'<?php echo $pages; ?>'" :locked-nav="1"></page-nav>
		</ul>
		 	<?php endif; ?>
		<?php bbp_get_template_part( 'form', 'reply' ); ?>
	<?php endif; ?>
</div>
