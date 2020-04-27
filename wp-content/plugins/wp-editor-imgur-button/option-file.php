<?php

add_action('admin_menu', function() {
	add_options_page('Imgur Uploader Settings', 'Imgur Uploader Settings', 'manage_options', 'imgur-uploader', 'imgur_uploader_plugin_page');
});

add_action('admin_init', function() {
	register_setting('imgur-uploader-plugin-settings', 'clientid');
	register_setting('imgur-uploader-plugin-settings', 'addcomment');
});

function imgur_uploader_plugin_page() {
	?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
		<h2>Imgur Uploader Settings</h2>
		<?php settings_errors(); ?>
		<div class="clearfix paddingtop20">
			<div class="last threecol">
				<div class="side-block">
					Like the plugin? Don't forget to give it a good rating on WordPress.org <a href="https://wordpress.org/plugins/wp-editor-imgur-button">in Here.</a>
				</div>
				<div class="side-block">
					<form action="options.php" method="post">
						<?php settings_fields('imgur-uploader-plugin-settings');
						do_settings_sections('imgur-uploader-plugin-settings');
						?>
						<br><br>
						<table>
							<tr>
								<td>
									<strong><label for="clientid">Enter Your Imgur Client ID: </label></strong>
								</td>
								<td>
									<input type="text" name="clientid" value="<?php echo esc_attr(get_option('clientid')); ?>" />
									<i><a href="https://api.imgur.com/">Get it now</a></i>
								</td>
							<tr>
							<br>
							<br>
							<tr>
								<td>
									<strong><label for="clientid">Add TinyMCE to Comments box: </label></strong>
								</td>
								<td>
									<input type="checkbox" name="addcomment" value="1" <?php checked(get_option('addcomment'),1); ?> />
									<i>If checked this field will add tinymce editor in the comment box in front end.</i>
								</td>
							<tr>
						</table>
						<br>
						<br>
						<input type="submit" value="Save Change" class="button button-primary" />
					</form>
					<div style="text-align:center;"><a class="button button-primary" href="http://codehay.net">Try It Now!</a></div>
					<div class="paypal">
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="UKZ5ELJTU8PC6">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}