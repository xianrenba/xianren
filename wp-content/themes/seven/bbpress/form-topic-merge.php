<?php

/**
 * Merge Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums" class="bbpress-wrapper box-bb">

	<?php if ( is_user_logged_in() && current_user_can( 'edit_topic', bbp_get_topic_id() ) ) : ?>

		<div id="merge-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-merge">

			<form id="merge_topic" name="merge_topic" method="post" action="<?php the_permalink(); ?>">

				<fieldset class="bbp-form">

					<div class="pd10 b-b b-t"><?php printf( esc_html__( 'Merge topic "%s"', 'bbpress' ), bbp_get_topic_title() ); ?></div>

					<div>

						<div class="bbp-template-notice info pd10">
							<ul>
								<li><?php esc_html_e( 'Select the topic to merge this one into. The destination topic will remain the lead topic, and this one will change into a reply.', 'bbpress' ); ?></li>
								<li><?php esc_html_e( 'To keep this topic as the lead, go to the other topic and use the merge tool from there instead.',                                  'bbpress' ); ?></li>
							</ul>
						</div>

						<div class="bbp-template-notice pd10">
							<ul>
								<li>这两个主题的回复将会按照时间顺序进行合并，如果时间相同，则其中一个回复会多出或者少出1秒的时间差，以使排序生效</li>
							</ul>
						</div>

						<fieldset class="bbp-form">
							<div class="fs14 pd10"><?php esc_html_e( 'Destination', 'bbpress' ); ?></div>
							<div class="pd10">
								<?php if ( bbp_has_topics( array( 'show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ), 'post__not_in' => array( bbp_get_topic_id() ) ) ) ) : ?>

									<div><?php esc_html_e( 'Merge with this topic:', 'bbpress' ); ?></div>
									<?php
										bbp_dropdown( array(
											'post_type'   => bbp_get_topic_post_type(),
											'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ),
											'post_status' => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
											'selected'    => -1,
											'exclude'     => bbp_get_topic_id(),
											'select_id'   => 'bbp_destination_topic',
											'select_class'   => 'dis-b mar10-t'
										) );
									?>

								<?php else : ?>

									<div class="pd10"><?php esc_html_e( 'There are no other topics in this forum to merge with.', 'bbpress' ); ?></div>

								<?php endif; ?>

							</div>
						</fieldset>


						<div class="bbp-template-notice error b-t">
							<ul>
								<li><?php esc_html_e( 'This process cannot be undone.', 'bbpress' ); ?></li>
							</ul>
						</div>

						<div class="bbp-submit-wrapper pd10 t-r">
							<button type="submit" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" class="button submit"><?php esc_html_e( 'Submit', 'bbpress' ); ?></button>
						</div>
					</div>

					<?php bbp_merge_topic_form_fields(); ?>

				</fieldset>
			</form>
		</div>

	<?php else : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="entry-content"><?php is_user_logged_in()
				? esc_html_e( 'You do not have permission to edit this topic.', 'bbpress' )
				: esc_html_e( 'You cannot edit this topic.',                    'bbpress' );
			?></div>
		</div>

	<?php endif; ?>

</div>
