<?php

/**
 * Archive Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
//分页
$count = wp_count_posts('topic' );
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$pages = 0;
if($count){
	$count = isset($count->publish) ? $count->publish : 1;
	$per_page = get_option( '_bbp_topics_per_page', 15 );
	$pages = ceil( $count / $per_page);
}

?>

<div id="bbpress-forums" class="bbpress-wrapper box">

	<?php if ( bbp_has_topics() ) : ?>
		<?php echo zrz_bbp_forum_info(); ?>
		<?php bbp_get_template_part('loop','topics'); ?>

		<page-nav class="b-t" nav-type="bbp-home-0" :paged="'<?php echo $paged; ?>'" :pages="'<?php echo $pages; ?>'" :show-type="'p'"></page-nav>

	<?php else : ?>
		<?php echo zrz_bbp_forum_info(); ?>
		<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_topics_index' ); ?>

</div>
