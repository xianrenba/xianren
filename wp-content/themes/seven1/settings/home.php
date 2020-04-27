<?php
function zrz_options_home_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

    $options = array(
            'keywords'=>$_POST['home_keywords'],
            'description'=>$_POST['home_description'],
            'page_width'=>trim_value($_POST['page_width']),
            'post_exclude'=>isset($_POST['home_post_exclude']) && !empty($_POST['home_post_exclude']) ? $_POST['home_post_exclude'] : array(),
            'meta'=>$_POST['meta'],
            'statistics'=>$_POST['site_statistics'],
            'logo'=>trim_value($_POST['logo']),
            'js_local'=>$_POST['js_local'],
            //'logo_w'=>$_POST['logo_w'],
            'clear_head'=>$_POST['clear_head'],
            'link_cat'=>trim_value($_POST['link_cat']),
            'theme_style_select'=>$_POST['theme_style_select'],
            'theme_style'=>$_POST['theme_style'],
            'theme_style_mobile'=>$_POST['theme_style_mobile'],
            'site_copy'=>$_POST['site_copy'],
            'show_sidebar'=>$_POST['show_sidebar'],
            'pinterest_count'=>trim_value($_POST['pinterest_count']),
            'separator'=>trim_value($_POST['separator'])
        );

	    update_option( 'zrz_setting', $options);

    if(isset($_POST['zh_cn_l10n_icp_num'])){
        update_option( 'zh_cn_l10n_icp_num',$_POST['zh_cn_l10n_icp_num']);
    }
    zrz_settings_error('updated');

  endif;

    $ipc = get_option('zh_cn_l10n_icp_num','');
  	$categories = get_categories( array('hide_empty' => 0) );
  	foreach ( $categories as $category ) {
  		$categories_array[$category->term_id] = $category->name;
  	}
	$option = new zrzOptionsOutput();

	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('基本设置','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		    zrz_admin_tabs('home');
            $js_local = zrz_get_theme_settings('js_local') !== null ? zrz_get_theme_settings('js_local') : 1;
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('PC端默认文章展现形式','ziranzhi2'),
                    'after' => '<p>'.__('请选择用户初次访问时PC端文章的展现形式','ziranzhi2').'</p>',
                    'key' => 'theme_style',
                    'value' => array(
                        'default' => array(zrz_get_theme_settings('theme_style')),
                        'option' => array(
                            'pinterest' => __( '网格', 'ziranzhi2' ),
                            'list' => __( '列表', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'th' => __('移动端默认文章展现形式','ziranzhi2'),
                    'after' => '<p>'.__('请选择用户初次访问时移动端文章的展现形式','ziranzhi2').'</p>',
                    'key' => 'theme_style_mobile',
                    'value' => array(
                        'default' => array(zrz_get_theme_settings('theme_style_mobile')),
                        'option' => array(
                            'pinterest' => __( '网格', 'ziranzhi2' ),
                            'list' => __( '列表', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'th' => __('是否显示首页侧边栏','ziranzhi2'),
                    'after' => '<p>'.__('默认显示','ziranzhi2').'</p>',
                    'key' => 'show_sidebar',
                    'value' => array(
                        'default' => array(zrz_get_theme_settings('show_sidebar')),
                        'option' => array(
                            1 => __( '显示', 'ziranzhi2' ),
                            0 => __( '隐藏', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
    				'type' => 'input',
    				'th' => __('网格模式下每横排显示的个数','ziranzhi2'),
                    'after' => '<p>默认3个，此项不影响移动端的样式。</p>',
    				'key' => 'pinterest_count',
    				'value' => zrz_get_theme_settings('pinterest_count')
    			),
                array(
                    'type' => 'select',
                    'th' => __('是否允许游客选择文章展现形式？','ziranzhi2'),
                    'after' => '<p>'.__('若禁止选择则只会显示上面设置的文章展现形式。','ziranzhi2').'</p>',
                    'key' => 'theme_style_select',
                    'value' => array(
                        'default' => array(zrz_get_theme_settings('theme_style_select')),
                        'option' => array(
                            0 => __( '禁止选择', 'ziranzhi2' ),
                            1 => __( '允许选择', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
    				'type' => 'input',
    				'th' => __('页面宽度','ziranzhi2'),
                    'after' => '<p>默认宽度<code>1140</code></p>',
    				'key' => 'page_width',
    				'value' => zrz_get_theme_settings('page_width')
    			),
                array(
                    'type' => 'select',
                    'th' => __('移除 head 区域多余的标签？','ziranzhi2'),
                    'after' => '<p>'.__('移除 版本号，feed，离线编辑接口等等','ziranzhi2').'</p>',
                    'key' => 'clear_head',
                    'value' => array(
                        'default' => array(zrz_get_theme_settings('clear_head')),
                        'option' => array(
                            0 => __( '保留', 'ziranzhi2' ),
                            1 => __( '移除', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
    				'type' => 'input',
    				'th' => __('LOGO','ziranzhi2'),
                    'after' => '<p>请输入 logo 图片的 url，如果不想使用图片 logo 此项请留空，<br>默认：<code>'.ZRZ_THEME_URI.'/images/logo.svg</code>（LOGO比例 140/36 。推荐使用SVG格式的LOGO。）</p>',
    				'key' => 'logo',
    				'value' => zrz_get_theme_settings('logo')
    			),
                array(
                    'type' => 'select',
                    'th' => __('JS库使用本地储存的还是远程公共库？','ziranzhi2'),
                    'after' => '<p>'.__('推荐使用远程公共库，当远程公共库失效或者抽风，再切换本地库。（默认选择远程公共库）','ziranzhi2').'</p>',
                    'key' => 'js_local',
                    'value' => array(
                        'default' => array($js_local),
                        'option' => array(
                            0 => __( '本地库', 'ziranzhi2' ),
                            1 => __( '公共库', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
    				'type' => 'input',
    				'th' => __('网站标题连接符','ziranzhi2'),
    				'after' => '<p>'.__('浏览器地址栏标题的连接符号，默认 <code>-</code>','ziranzhi2').'</p>',
    				'key' => 'separator',
    				'value' => zrz_get_theme_settings('separator')
    			),
    			array(
    				'type' => 'input',
    				'th' => __('首页关键词','ziranzhi2'),
    				'after' => '<p>'.__('网站首页的网页关键词，建议使用英文的逗号隔开','ziranzhi2').'</p>',
    				'key' => 'home_keywords',
    				'value' => zrz_get_theme_settings('keywords')
    			),
    			array(
    				'type' => 'textarea',
    				'th' => __('首页描述','ziranzhi2'),
    				'after' => '<p>'.__('网站首页的网页描述，推荐200字以内','ziranzhi2').'</p>',
    				'key' => 'home_description',
    				'value' => zrz_get_theme_settings('description')
    			),
                array(
                    'type' => 'textarea',
                    'th' => __('头部标签','ziranzhi2'),
                    'after' => '<p>'.__('通常情况下是 meta 标签、link 标签等等，当然，你使用 style 标签也是没问的。<br>通常情况下，这里是用来放置百度站长平台验证代码的','ziranzhi2').'</p>',
                    'key' => 'meta',
                    'value' => zrz_get_html_code(zrz_get_theme_settings('meta'))
                ),
    			array(
    				'type' => 'checkbox',
    				'th' => __('首页文章列表排除的分类','ziranzhi2'),
    				'after' => '<p>'.__('选择排除的分类，留空则不排除任何分类','ziranzhi2').'</p>',
    				'key' => 'home_post_exclude',
    				'value' => array(
    					'default' => zrz_get_theme_settings('post_exclude'),
    					'option' => $categories_array
    				)
    			),
                array(
                    'type' => 'input',
                    'th' => __('页面底部显示的友情链接分类ID','ziranzhi2'),
                    'key' => 'link_cat',
                    'value' => zrz_get_theme_settings('link_cat')
                ),
                array(
                    'type' => 'textarea',
                    'th' => __('统计代码','ziranzhi2'),
                    'after' => '<p>'.__('统计代码将会显示在网站的页脚位置','ziranzhi2').'</p>',
                    'key' => 'site_statistics',
                    'value' => zrz_get_html_code(zrz_get_theme_settings('statistics'))
                ),
                array(
                    'type' => 'textarea',
                    'th' => __('版权设置','ziranzhi2'),
                    'after' => '<p>'.__('版权设置将会显示在网站的页脚位置','ziranzhi2').'</p>',
                    'key' => 'site_copy',
                    'value' => zrz_get_html_code(zrz_get_theme_settings('site_copy'))
                ),
                array(
                    'type' => 'input',
                    'th' => __('备案号','ziranzhi2'),
                    'key' => 'zh_cn_l10n_icp_num',
                    'value' => $ipc
                ),
    		) );

		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
	<?php
}
