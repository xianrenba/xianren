<?php
function zrz_options_ads_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

    $options = array(
        // 'home_list'=>array(
        //     'open'=>$_POST['home_list_open'],
        //     'str'=>$_POST['home_list_str']
        // ),
        // 'home_card'=>array(
        //     'open'=>$_POST['home_card_open'],
        //     'str'=>$_POST['home_card_str']
        // ),
        'single_footer'=>array(
            'open'=>$_POST['single_footer_open'],
            'str'=>$_POST['single_footer_str']
            )
        );

    update_option('zrz_ads_setting',$options);
    zrz_settings_error('updated');

  endif;

	$option = new zrzOptionsOutput();

	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','zrz');?></h1>
    <h2 class="title"><?php _e('广告设置','zrz');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		      zrz_admin_tabs('ads');
		?>

		<?php
        $home_list = zrz_get_ads_settings('home_list');
        $home_card = zrz_get_ads_settings('home_card');
        $single_footer = zrz_get_ads_settings('single_footer');
        $option->table( array(
            // array(
            //     'type' => 'select',
            //     'th' => __('开启首页文章列表中的广告位？','zrz'),
            //     'after' => '<p class="description">将在首页列表中显示</p>',
            //     'key' => 'home_list_open',
            //     'value' => array(
            //         'default' => array($home_list['open']),
            //         'option' => array(
            //             1 => __( '开启', 'zrz' ),
            //             0 => __( '关闭', 'zrz' ),
            //         )
            //     )
            // ),
            // array(
            //     'type' => 'textarea',
            //     'th' => __('首页文章列表中的广告代码','zrz'),
            //     'key' => 'home_list_str',
            //     'value' => zrz_get_html_code($home_list['str'])
            // ),
            // array(
            //     'type' => 'select',
            //     'th' => __('开启首页文章网格模式中的广告位？','zrz'),
            //     'after' => '<p class="description">将在首页列表中显示</p>',
            //     'key' => 'home_card_open',
            //     'value' => array(
            //         'default' => array($home_card['open']),
            //         'option' => array(
            //             1 => __( '开启', 'zrz' ),
            //             0 => __( '关闭', 'zrz' ),
            //         )
            //     )
            // ),
            // array(
            //     'type' => 'textarea',
            //     'th' => __('首页文章网格模式中的广告代码','zrz'),
            //     'key' => 'home_card_str',
            //     'value' => zrz_get_html_code($home_card['str'])
            // ),
            array(
                'type' => 'select',
                'th' => __('开启文章内页底部广告？','zrz'),
                'after' => '<p class="description">文章内页（single页面）中显示</p>',
                'key' => 'single_footer_open',
                'value' => array(
                    'default' => array($single_footer['open']),
                    'option' => array(
                        1 => __( '开启', 'zrz' ),
                        0 => __( '关闭', 'zrz' ),
                    )
                )
            ),
            array(
                'type' => 'textarea',
                'th' => __('文章内页底部广告','zrz'),
                'key' => 'single_footer_str',
                'value' => zrz_get_html_code($single_footer['str'])
            ),
    		) );

		?>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'zrz' );?>"></p>
	</form>
</div>
	<?php
}
