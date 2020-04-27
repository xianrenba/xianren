<?php

/**
 * Replies Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php

	if ( bbp_thread_replies() ) :
		bbp_list_replies();
	else :
		while ( bbp_replies() ) : bbp_the_reply();
			bbp_get_template_part( 'loop', 'single-reply' );
		endwhile;
	endif;
?>
