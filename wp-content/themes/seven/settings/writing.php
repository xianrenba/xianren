<?php
function zrz_options_writing_page(){

    if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

        $custom_tags = isset($_POST['custom_tags']) ? explode(",",trim_value($_POST['custom_tags'])) : array();

        $options = array(
                'cat'=>isset($_POST['cat']) && !empty($_POST['cat']) ? $_POST['cat'] : array(1),//允许投稿的分类
                'cat_more'=>$_POST['cat_more'],//允许投稿的分类
                'min_strlen'=>trim_value($_POST['min_strlen']),//投稿最少字数
                'max_strlen'=>trim_value($_POST['max_strlen']),//投稿最多字数
                'post_format'=>$_POST['post_format'],//是否允许用户选择文章形式
                'tag_count'=>trim_value($_POST['tag_count']),//允许输入的标签最大数
                'custom_tags'=>$custom_tags,//管理员自定义的标签
                'video_size'=>trim_value($_POST['video_size']),//允许上传的视频体积
                'status'=>$_POST['status'],//投稿后的状态
                'edit_time'=>trim_value($_POST['edit_time']),//多长时间内允许编辑
                'related_chose'=>$_POST['related_chose'],//是否允许选择相关文章
                'labs_status'=>$_POST['labs_status'],//投稿后的状态
                'labs_edit_time'=>trim_value($_POST['labs_edit_time']),//多长时间内允许编辑
                'auto_draft'=>$_POST['auto_draft'],//多长时间内允许编辑
                // 'allow_fj'=>$_POST['allow_fj'],
                // 'allow_video'=>$_POST['allow_video'],
                // 'fj_open'=>$_POST['fj_open']
            );

        update_option( 'zrz_writing_setting',$options );

        zrz_settings_error('updated');

    endif;

	$categories = get_categories( array('hide_empty' => 0) );

	foreach ( $categories as $category ) {
		$categories_array[$category->term_id] = $category->name;
	}

	$option = new zrzOptionsOutput();

	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('投稿','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">

		<?php
        zrz_admin_tabs('writing');
        echo '<h2>写文章</h2>';
		$option->table( array(
			array(
				'type' => 'checkbox',
				'th' => __('允许投稿的分类','ziranzhi2'),
				'after' => '<p>'.__('多选','ziranzhi2').'</p>',
				'key' => 'cat',
				'value' => array(
					'default' => zrz_get_writing_settings('cat'),
					'option' => $categories_array
				)
			),
            array(
				'type' => 'select',
				'th' => __('分类可以多选？','ziranzhi2'),
                'key' => 'cat_more',
                'value' => array(
                    'default' => array(zrz_get_writing_settings('cat_more')),
                    'option' => array(
                        1 => __( '多选', 'ziranzhi2' ),
                        0 => __( '单选', 'ziranzhi2' )
                    )
                )
			),
			array(
				'type' => 'input',
				'th' => __('投稿的最少字数','ziranzhi2'),
				'after' => '<p>'.__('限制最少字数，一篇文章少于这个字数不允许投稿。一条微博是140，所以默认是140字。','ziranzhi2').'</p>',
				'key' => 'min_strlen',
				'value' => zrz_get_writing_settings('min_strlen')
			),
            array(
                'type' => 'input',
                'th' => __('允许上传的视频体积','ziranzhi2'),
                'after' => '<p>'.__('默认10M，此设置的数值应当小于或者等于服务器所限制的上传体积。','ziranzhi2').'</p>',
                'key' => 'video_size',
                'value' => zrz_get_writing_settings('video_size')
            ),
			array(
				'type' => 'input',
				'th' => __('投稿的最多字数','ziranzhi2'),
				'after' => '<p>'.__('限制最多字数，一篇文章超过这个字数不允许投稿。刊物文章一般是2200-12000，所以默认12000字。','ziranzhi2').'</p>',
				'key' => 'max_strlen',
				'value' => zrz_get_writing_settings('max_strlen')
			),
            array(
                'type' => 'select',
                'th' => __('是否允许选择相关文章？','ziranzhi2'),
                'key' => 'related_chose',
                'value' => array(
                    'default' => array(zrz_get_writing_settings('related_chose')),
                    'option' => array(
                        1 => __( '允许', 'ziranzhi2' ),
                        0 => __( '禁止', 'ziranzhi2' )
                    )
                )
            ),
            array(
				'type' => 'select',
				'th' => __('是否允许选择文章形式？','ziranzhi2'),
                'key' => 'post_format',
                'value' => array(
                    'default' => array(zrz_get_writing_settings('post_format')),
                    'option' => array(
                        1 => __( '允许', 'ziranzhi2' ),
                        0 => __( '禁止', 'ziranzhi2' )
                    )
                )
			),
            array(
                'type' => 'input',
                'th' => __('最多可以输入几个标签','ziranzhi2'),
                'after' => '<p>'.__('默认5个','ziranzhi2').'</p>',
                'key' => 'tag_count',
                'value' => zrz_get_writing_settings('tag_count')
            ),
            array(
                'type' => 'input',
                'th' => __('推荐的标签','ziranzhi2'),
                'after' => '<p>'.__('管理员可以设置若干个推荐标签，在投稿页面将会显示出来供用户选择，<span class="red">标签之间，请使用英文逗号隔开</span>','ziranzhi2').'</p>',
                'key' => 'custom_tags',
                'value' => implode(",",zrz_get_writing_settings('custom_tags'))
            ),
            array(
                'type' => 'select',
                'th' => __('用户投稿后处于什么状态？','ziranzhi2'),
                'key' => 'status',
                'value' => array(
                    'default' => array(zrz_get_writing_settings('status')),
                    'option' => array(
                        1 => __( '直接发布', 'ziranzhi2' ),
                        0 => __( '待审核状态', 'ziranzhi2' )
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('多少小时之内允许编辑','ziranzhi2'),
                'after' => '<p class="description">管理员可以随时编辑，此项只限制普通用户</p>',
                'key' => 'edit_time',
                'value' => zrz_get_writing_settings('edit_time')
            ),
            array(
                'type' => 'select',
                'th' => __('是否启用自动本地草稿？','ziranzhi2'),
                'key' => 'auto_draft',
                'value' => array(
                    'default' => array(zrz_get_writing_settings('auto_draft')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' )
                    )
                )
            ),
            // array(
            //     'type' => 'select',
            //     'th' => __('是否允许用户上传视频？','ziranzhi2'),
            //     'key' => 'allow_video',
            //     'value' => array(
            //         'default' => array(zrz_get_writing_settings('allow_video')),
            //         'option' => array(
            //             1 => __( '允许', 'ziranzhi2' ),
            //             0 => __( '禁止', 'ziranzhi2' )
            //         )
            //     )
            // ),
            // array(
            //     'type' => 'select',
            //     'th' => __('是否允许用户上传附件？','ziranzhi2'),
            //     'key' => 'allow_fj',
            //     'value' => array(
            //         'default' => array(zrz_get_writing_settings('allow_fj')),
            //         'option' => array(
            //             1 => __( '允许', 'ziranzhi2' ),
            //             0 => __( '禁止', 'ziranzhi2' )
            //         )
            //     )
            // ),
            // array(
            //     'type' => 'select',
            //     'th' => __('附件下载新窗口打开？','ziranzhi2'),
            //     'key' => 'fj_open',
            //     'value' => array(
            //         'default' => array(zrz_get_writing_settings('fj_open')),
            //         'option' => array(
            //             1 => __( '直接下载', 'ziranzhi2' ),
            //             0 => __( '新窗口打开下载', 'ziranzhi2' )
            //         )
            //     )
            // ),
		) );
        echo '<h2>发起研究</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('用户发布研究以后处于什么状态？','ziranzhi2'),
                'key' => 'labs_status',
                'value' => array(
                    'default' => array(zrz_get_writing_settings('labs_status')),
                    'option' => array(
                        1 => __( '直接发布', 'ziranzhi2' ),
                        0 => __( '待审核状态', 'ziranzhi2' )
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('多少小时之内允许编辑','ziranzhi2'),
                'after' => '<p class="description">管理员可以随时编辑，此项只限制普通用户</p>',
                'key' => 'labs_edit_time',
                'value' => zrz_get_writing_settings('labs_edit_time')
            ),

        ))
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
	<?php
}
