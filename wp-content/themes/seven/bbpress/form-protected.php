<?php

/**
 * Password Protected
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums" class="bbpress-wrapper">
	<fieldset class="bbp-form box-bb" id="bbp-protected">
		<Legend><?php esc_html_e( 'Protected', 'bbpress' ); ?></legend>

		<?php echo get_the_password_form(); ?>

	</fieldset>
</div>
