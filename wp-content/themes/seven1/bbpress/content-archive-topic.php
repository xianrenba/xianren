<?php

/**
 * Archive Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
//分页
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
?>

<div id="bbpress-forums" class="bbpress-wrapper box">

	<?php if ( bbp_has_topics() ) : ?>
		<?php echo zrz_bbp_forum_info(); ?>
		<?php bbp_get_template_part('loop','topics'); ?>

		<?php 
			
			$bbp = bbpress();
			if ( empty( $bbp->topic_query ) ) {
				$pages =  0;
			}

			$count = (int) ! empty( $bbp->topic_query->found_posts ) ? $bbp->topic_query->found_posts : $bbp->topic_query->post_count;

			if($count){
				$per_page = get_option( '_bbp_topics_per_page', 15 );
				$pages = ceil( $count / $per_page);
			}
		?>

		<page-nav class="b-t" nav-type="bbp-home-0" :paged="'<?php echo $paged; ?>'" :pages="'<?php echo $pages; ?>'" :show-type="'p'"></page-nav>

	<?php else : ?>
		<?php echo zrz_bbp_forum_info(); ?>
		<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_topics_index' ); ?>

</div>
