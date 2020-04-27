<?php
function zrz_options_reading_page(){
  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

      $option = array(
          'ajax_post'=>$_POST['ajax_post'],
          'ajax_comment'=>$_POST['ajax_comment'],
          'show_topic_thumb'=>$_POST['show_topic_thumb'],
          'open_new'=>$_POST['open_new'],
          'highlight'=>$_POST['highlight'],
          'ajax_post_more'=>$_POST['ajax_post_more']
      );
      if($_POST['ajax_comment'] == 1){
          update_option('default_comments_page','oldest');
      }else{
          update_option('default_comments_page','newest');
      }
      update_option('zrz_reading_setting',$option);

    zrz_settings_error('updated');
    endif;
    $option = new zrzOptionsOutput();
	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','zrz');?></h1>
    <h2 class="title"><?php _e('阅读','zrz');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		zrz_admin_tabs('reading');
		?>

		<?php
        echo '<h2>文章</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' =>'文章加载使用分页还是加载更多按钮？',
                'key' => 'ajax_post',
                'value' => array(
                    'default' => array(zrz_get_reading_settings('ajax_post')),
                    'option' => array(
                        1 => __( '加载更多', 'zrz' ),
                        0 => __( '分页', 'zrz' )
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' =>'文章列表菜单在新窗口打开？',
                'after' => '<p>'.__('启用之后首页和存档页的文章会在新窗口打开','zrz').'</p>',
                'key' => 'open_new',
                'value' => array(
                    'default' => array(zrz_get_reading_settings('open_new')),
                    'option' => array(
                        1 => __( '新窗口打开', 'zrz' ),
                        0 => __( '原窗口打开', 'zrz' )
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' =>'是否启用评论列表AJAX加载？',
                'after' => '<p>'.__('如果贵站的文章评论很多，大部分超过10页，则建议关闭 ajax 加载','zrz').'</p>',
                'key' => 'ajax_comment',
                'value' => array(
                    'default' => array(zrz_get_reading_settings('ajax_comment')),
                    'option' => array(
                        1 => __( '启用', 'zrz' ),
                        0 => __( '关闭', 'zrz' )
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' =>'自动加载到第几页后停止自动加载',
                'after'=>'<p>如果您上面选择了加载更多按钮，用户在页面下拉到底部的时候，将会自动加载内容。（对文章和评论以及其他分页同时起效）<br>
                请填写您需要自动加载到第几页的时候停止自动加载？<br>
                如果关闭自动加载功能，请在这里填<code>0</code></br>
                如果您希望它一直自动加载下去，请填<code>auto</code></p>
                ',
                'key' => 'ajax_post_more',
                'value' => zrz_get_reading_settings('ajax_post_more')
            ),
            // array(
            //         'type' => 'select',
            //         'th' =>'是否启用文章内部图片评论功能？',
            //         'after' => '<p>'.__('后台编辑器不支持此选项','zrz').'</p>',
            //         'key' => 'zrz_post_img_comment',
            //         'value' => array(
            //             'default' => array(intval(get_option('zrz_post_img_comment',1))),
            //             'option' => array(
            //                 1 => __( '启用', 'zrz' ),
            //                 0 => __( '关闭', 'zrz' )
            //             )
            //         )
            //     ),
            array(
                'type' => 'select',
                'th' =>'是否开启代码高亮功能？',
                'key' => 'highlight',
                'value' => array(
                    'default' => array(zrz_get_reading_settings('highlight')),
                    'option' => array(
                        1 => __( '启用', 'zrz' ),
                        0 => __( '关闭', 'zrz' )
                    )
                )
            ),
        ) );
        echo '<h2>论坛</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' =>'论坛帖子列表是否显示缩略图',
                'key' => 'show_topic_thumb',
                'value' => array(
                    'default' => array(zrz_get_reading_settings('show_topic_thumb')),
                    'option' => array(
                        1 => __( '启用', 'zrz' ),
                        0 => __( '关闭', 'zrz' )
                    )
                )
            ),
        ) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'zrz' );?>"></p>
	</form>
</div>
	<?php
}
