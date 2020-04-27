<?php

/**
 * Split Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums" class="bbpress-wrapper box-bb">

	<?php if ( is_user_logged_in() && current_user_can( 'edit_topic', bbp_get_topic_id() ) ) : ?>

		<div id="split-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-split">

			<form id="split_topic" name="split_topic" method="post" action="<?php the_permalink(); ?>">

				<fieldset class="bbp-form">

					<div class="pd10 b-b b-t"><?php printf( esc_html__( 'Split topic "%s"', 'bbpress' ), bbp_get_topic_title() ); ?></div>

					<div>

						<div class="bbp-template-notice info pd10">
							<ul>
								<li><?php esc_html_e( 'When you split a topic, you are slicing it in half starting with the reply you just selected. Choose to use that reply as a new topic with a new title, or merge those replies into an existing topic.', 'bbpress' ); ?></li>
							</ul>
						</div>

						<div class="bbp-template-notice pd10">
							<ul>
								<li><?php esc_html_e( 'If you use the existing topic option, replies within both topics will be merged chronologically. The order of the merged replies is based on the time and date they were posted.', 'bbpress' ); ?></li>
							</ul>
						</div>

						<fieldset class="bbp-form">
							<div class="pd10 fs14"><?php esc_html_e( 'Split Method', 'bbpress' ); ?></div>

							<div class="pd10">
								<input name="bbp_topic_split_option" id="bbp_topic_split_option_reply" class="radio" type="radio" checked="checked" value="reply" />
								<label for="bbp_topic_split_option_reply"><?php printf( esc_html__( '分割成一个新话题在 %s 板块中:', 'bbpress' ), bbp_get_forum_title( bbp_get_topic_forum_id( bbp_get_topic_id() ) ) ); ?></label>
								<input type="text" id="bbp_topic_split_destination_title" class="dis-b mar10-t" value="<?php printf( esc_html__( 'Split: %s', 'bbpress' ), bbp_get_topic_title() ); ?>" size="35" name="bbp_topic_split_destination_title" />
							</div>

							<?php if ( bbp_has_topics( array( 'show_stickies' => false, 'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ), 'post__not_in' => array( bbp_get_topic_id() ) ) ) ) : ?>

								<div class="pd10">
									<input name="bbp_topic_split_option" id="bbp_topic_split_option_existing" class="radio" type="radio" value="existing" />
									<label for="bbp_topic_split_option_existing"><?php esc_html_e( 'Use an existing topic in this forum:', 'bbpress' ); ?></label>

									<?php
										bbp_dropdown( array(
											'post_type'   => bbp_get_topic_post_type(),
											'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ),
											'post_status' => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
											'selected'    => -1,
											'exclude'     => bbp_get_topic_id(),
											'select_id'   => 'bbp_destination_topic',
											'select_class' => 'dis-b mar10-t'
										) );
									?>

								</div>

							<?php endif; ?>

						</fieldset>

						<div class="bbp-template-notice error b-t" role="alert" tabindex="-1">
							<ul>
								<li><?php esc_html_e( 'This process cannot be undone.', 'bbpress' ); ?></li>
							</ul>
						</div>

						<div class="bbp-submit-wrapper pd10 t-r">
							<button type="submit" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" class="button submit"><?php esc_html_e( 'Submit', 'bbpress' ); ?></button>
						</div>
					</div>

					<?php bbp_split_topic_form_fields(); ?>

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
