<?php

//~ 投稿start

if( isset($_GET['action']) && in_array($_GET['action'], array('new', 'edit')) && $oneself ){
	
	if( isset($_GET['id']) && is_numeric($_GET['id']) && get_post($_GET['id']) && intval(get_post($_GET['id'])->post_author) === get_current_user_id() ){
		$action = 'edit';
		$the_post = get_post($_GET['id']);
        $tags = !empty($_POST['post_tags']) ? $_POST['post_tags'] : '';
		$post_title = $the_post->post_title;
		$post_content = $the_post->post_content;
		foreach((get_the_category($_GET['id'])) as $category) { 
			$post_cat[] = $category->term_id; 
		}
	}else{
		$action = 'new';
		$post_title = !empty($_POST['post_title']) ? $_POST['post_title'] : '';
      	$tags = !empty($_POST['post_tags']) ? $_POST['post_tags'] : '';
		$post_content = !empty($_POST['post_content']) ? $_POST['post_content'] : '';
		$post_cat = !empty($_POST['post_cat']) ? $_POST['post_cat'] : array();
	}

	if( isset($_POST['act']) && trim($_POST['act'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
		
		$title = sanitize_text_field($_POST['post_title']);
		$content = $_POST['post_content'];
        $tags = !empty($_POST['post_tags']) ? $_POST['post_tags'] : '';
		$cat =  !empty($_POST['post_cat']) ? $_POST['post_cat'] : '';
		$fromurl_value = $_POST['fromurl_value'];
		$fromname_value = $_POST['fromname_value'];
		
		if( $title && $content ){
			
			if( mb_strlen($content,'utf8')<120 ){
				
				$message = __('提交失败，文章内容至少120字。','um');
				
			}else{
				
				$status = sanitize_text_field($_POST['post_status']);
				
				if( $action==='edit' ){

					$new_post = wp_update_post( array(
						'ID' => intval($_GET['id']),
						'post_title'    => $title,
						'post_content'  => $content,
						'post_status'   => ( $status==='pending' ? 'pending' : 'draft' ),
						'post_author'   => get_current_user_id(),
                        'tags_input'    => $tags,
						'post_category' => $cat
					) );

				}else{

					$new_post = wp_insert_post( array(
						  'post_title'    => $title,
						  'post_content'  => $content,
						  'post_status'   => ( $status==='pending' ? 'pending' : 'draft' ),
						  'post_author'   => get_current_user_id(),
                          'tags_input'    => $tags,
						  'post_category' => $cat
						) );
                  
                  	if( _hui('tougao_mail_send') ){
                         wp_mail(_hui('tougao_mail_to'), '站长，有新投稿：'.$title, $content);
                    }
                  
				}
				
				if( is_wp_error( $new_post ) ){
					$message = __('操作失败，请重试或联系管理员。','um');
				}else{
					
					if ( !empty($fromurl_value) && mb_strlen($fromurl_value) > 50 ){
						$message = __('操作失败，来源链接不能大于50个字符','um');
					}else{
						//上传来源链接
					    update_post_meta( $new_post, 'fromurl_value', htmlspecialchars($fromurl_value) );
					}
					if ( !empty($fromname_value) && mb_strlen($fromname_value) > 50 ){
						$message = __('操作失败，来源名不能大于50个字符','um');
					}else{						
						//上传来源名
					    update_post_meta( $new_post, 'fromname_value', htmlspecialchars($fromname_value) );
					}
					if(!$message) wp_redirect(um_get_user_url('post'));
				}

			}
		}else{
			$message = __('投稿失败，标题和内容不能为空！','um');
		}
	}
}
//~ 投稿end

?>
<article class="panel panel-default archive" role="main">
	<div class="panel-body">
    <?php
        $can_post_cat = array();
	    if( _hui('can_post_cat')) $can_post_cat = _hui('can_post_cat');
        $cat_count = count($can_post_cat)!= 0 ? count($can_post_cat) : 0;
	    $msg = '';
	    if(!is_user_logged_in()){$msg='你必须<a href="javascript:" class="user-login">登录</a>才能够投稿';}
        if(!_hui('tougao_s') ||!$cat_count){$msg='暂无可投稿分类或站点未允许用户发布文章，请等待管理员开放投稿';}
	    if(!current_user_can('edit_posts')){$msg=__('遗憾的是，你现在登录的账号没有投稿权限！', 'um');}
        if( $msg ) {
		    echo '<p>'.$msg.'</p>';
	    }else{
            if( $action==='edit' ){ $_title = '编辑文章'; $_title_tip = 'POST EDIT'; }else{ $_title = '发布文章'; $_title_tip = 'POST NEW';}
        ?>
		<h3 class="page-header"><?php echo $_title ?> <small><?php echo $_title_tip;?></small></h3>
        <?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
		<form role="form" method="post">
			<div class="form-group">
				<input type="text" class="form-control" name="post_title" placeholder="<?php _e('在此输入标题','um');?>" value="<?php echo $post_title;?>" aria-required='true' required>
			</div>
			<div class="form-group">
				<?php wp_editor(  wpautop($post_content), 'post_content', array('media_buttons'=>true, 'quicktags'=>true, 'editor_class'=>'form-control', 'editor_css'=>'<style>.wp-editor-container{border:1px solid #ddd;}.switch-html, .switch-tmce{height:30px !important}</style>') ); ?>
			</div>
			<div class="form-group">
			<?php
				if($can_post_cat && is_array($can_post_cat)){
					$post_cat_output = '<p class="help-block">'.__('选择文章分类', 'um').'</p>';
					$post_cat_output .= '<select name="post_cat[]" class="form-control">';
					foreach ( $can_post_cat as  $key => $value ) {
						$category = get_category( $value );
						//~ if( (!empty($post_cat)) && in_array($category->term_id,$post_cat)) 
						$post_cat_output .= '<option value="'.$category->term_id.'">'. $category->name.'</option>';
					}
					$post_cat_output .= '</select>';
					echo $post_cat_output;
				}
                $tag_names = array();
                $tags = wp_get_post_tags($_GET['id']);
                foreach ($tags as $tag ) {
                    $tag_names[] = $tag->name;
                }
                $keywords = implode(',', $tag_names);
				$cc = array();
				if(isset($_GET['id'])) $cc['fromurl_value'] = get_post_meta( intval($_GET['id']), 'fromurl_value', true );
				if(isset($_GET['id'])) $cc['fromname_value'] = get_post_meta( intval($_GET['id']), 'fromname_value', true );
				$fromurl_value = $cc['fromurl_value'];
				$fromname_value = $cc['fromname_value'];
				?>
			</div>
			<div class="form-group">
				<p class="help-block"><?php _e('文章来源链接', 'um');?></p>
				<input type="text" class="form-control"  name="fromurl_value" placeholder="" value="<?php echo stripcslashes(htmlspecialchars_decode($fromurl_value)); ?>">
			</div>
			<div class="form-group">
				<p class="help-block"><?php _e('文章来源名', 'um');?></p>
				<input type="text" class="form-control"  name="fromname_value" placeholder="" value="<?php echo stripcslashes(htmlspecialchars_decode($fromname_value)); ?>">
			</div>
            <div class="form-group">
				<p class="help-block"><?php _e('输入标签, 多个标签以英文逗号分隔', 'um');?></p>
                <input type="text" class="form-control"  name="post_tags" placeholder="" value="<?php echo $keywords; ?>">
            </div>
			<div class="form-group text-right">
				<select name="post_status">
					<option value ="pending"><?php _e('提交审核','um');?></option>
					<option value ="draft"><?php _e('保存草稿','um');?></option>
				</select>
				<input type="hidden" name="act" value="update">
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
				<button type="submit" class="btn btn-success"><?php _e('确认操作','um');?></button>
			</div>	
		</form>
	</div>
    <?php }?>
</article>