<?php

/**
 * Archive Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
//分页
$count = wp_count_posts( $post_type = 'topic' );
$current = get_query_var('paged') ? get_query_var('paged') : 1;
if($count){
	$count = isset($count->publish) ? $count->publish : 1;
	$per_page = get_option( '_bbp_topics_per_page', 15 );
	$count = ceil( $count / $per_page);
}

?>

<div id="bbpress-forums" class="bbpress-wrapper box">

	<?php

	if ( bbp_has_topics() ) : ?>

		<?php bbp_get_template_part('loop','topics'); ?>

		<div id="zrz-pager"></div>

	<?php else : ?>

		<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_topics_index' ); ?>

</div>
