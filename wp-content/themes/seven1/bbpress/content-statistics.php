<?php

/**
 * Statistics Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Get the statistics
$stats = bbp_get_statistics();?>

<div role="main">

	<?php do_action( 'bbp_before_statistics' ); ?>

	<div class="stats-row pd10 pd10">
		<div class="stats-l fd"><?php esc_html_e( 'Registered Users', 'bbpress' ); ?>
		</div><div class="stats-r fd">
			<?php echo esc_html( $stats['user_count'] ); ?>
		</div>
	</div>

	<div class="stats-row pd10">
  <div class="stats-l fd">板块
	</div><div class="stats-r fd">
		<?php echo esc_html( $stats['forum_count'] ); ?>
	</div>
</div>

	<div class="stats-row pd10">
  <div class="stats-l fd"><?php esc_html_e( 'Topics', 'bbpress' ); ?>
	</div><div class="stats-r fd">
		<?php echo esc_html( $stats['topic_count'] ); ?>
	</div>
</div>

	<div class="stats-row pd10">
  <div class="stats-l fd"><?php esc_html_e( 'Replies', 'bbpress' ); ?>
	</div><div class="stats-r fd">
		<?php echo esc_html( $stats['reply_count'] ); ?>
	</div>
</div>

	<?php do_action( 'bbp_after_statistics' ); ?>

</div>

<?php unset( $stats );
