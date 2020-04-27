jQuery('#img_file').live('change', function(){	
	var file = jQuery('#img_file');
	if(file.get(0).files.leight === 0 || file.val() == '') {
		alert('Bạn chưa chọn ảnh nào!');
	} else {
		onsubmit(jQuery("#form_file"));
	}
});								

function onsubmit(e) {
	jQuery( '.mce-panel' ).find('iframe').after('<div class="imgur-upload-loading"><div class="loading"></div></div>');
	var formdata = new FormData(this);
	formdata.append('action', 'imgur_uploader_action');
	formdata.append('img_file', jQuery("#img_file")[0].files[0]);
	 jQuery.ajax({
		url: sb_imgur_ajax.url, 
		type: "POST",             
		data: formdata, 
		contentType: false,      
		cache: false,            
		processData:false,       
		success: function(data)  
		{
			jQuery(  '.mce-panel'  ).find('.imgur-upload-loading').remove();
			tinymce.activeEditor.execCommand('mceInsertContent', false, data);						
			return false;
		}
	}); 
}