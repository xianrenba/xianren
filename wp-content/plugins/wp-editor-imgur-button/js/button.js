(function() {
	tinymce.PluginManager.add('imgur_tc_button', function(editor, url) {
		jQuery('body').append('<form action="" method="POST" id="form_file"><input type="file" accept="image/gif,.gif,image/jpeg,.jpg,image/png,.png,.jpeg" style="position:absolute;left:-9999px;" id="img_file" name="img_file"/></form>');
		editor.addButton('imgur_tc_button', {
			title: 'Imgur Uploader',
			icon: 'icon imgur-icon',
			onclick: function() {
				jQuery('#img_file').click();
			}
		});
	});
})();