<?php

/**
 * Single View Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
$bbp = bbpress();
$total_int = (int) !empty( $bbp->topic_query->found_posts ) ? $bbp->topic_query->found_posts : $bbp->topic_query->post_count;
$count = ceil( $total_int / get_option( '_bbp_topics_per_page', 'forums' ));
$current = get_query_var('paged') ? get_query_var('paged') : 1;
?>

<div id="bbpress-forums" class="bbpress-wrapper box-bb">

	<?php bbp_set_query_name( bbp_get_view_rewrite_id() ); ?>

	<?php if ( bbp_view_query() ) : ?>

		<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

		<div id="zrz-pager"><?php echo zrz_pager($current,$count) ?></div>

	<?php else : ?>

		<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

	<?php endif; ?>

	<?php bbp_reset_query_name(); ?>

</div>
