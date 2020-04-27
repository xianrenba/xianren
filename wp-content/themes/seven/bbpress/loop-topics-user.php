<?php

/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

 ?>
<ul ref="topicList">

	<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

		<?php bbp_get_template_part( 'loop', 'single-topic-user' ); ?>

	<?php endwhile; ?>

</ul>
