<?php
function zrz_options_lv_page(){

    $array = apply_filters( 'zrz_default_lv_arg',array('lv0','lv1','lv2','lv3','lv4','lv5','lv6','lv7','vip','vip1','vip2','vip3'));

    if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

    $options = array();

    foreach ($array as $key) {
        if(strpos($key,'vip') !== false){
            $options[$key] = array(
                'name'=>$_POST[$key.'_name'],
                'capabilities'=>isset($_POST[$key.'_capabilities']) && !empty($_POST[$key.'_capabilities']) ? $_POST[$key.'_capabilities'] : array(),
                'time'=>trim_value($_POST[$key.'_time']),
                'price'=>trim_value($_POST[$key.'_price']),
                'open'=>$_POST[$key.'_open'],
                'allow_all'=>$_POST[$key.'_allow'],
            );
        }else{
            $options[$key] = array(
                'name'=>$_POST[$key.'_name'],
                'credit'=>trim_value(isset($_POST[$key.'_credit']) ? $_POST[$key.'_credit'] : 0),
                'capabilities'=>isset($_POST[$key.'_capabilities']) && !empty($_POST[$key.'_capabilities']) ? $_POST[$key.'_capabilities'] : array()
            );
        }

    }

	update_option( 'zrz_lv_setting', $options);

    zrz_settings_error('updated');

    endif;

    $option = new zrzOptionsOutput();

	?>
<div class="wrap">

	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('等级制度','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php zrz_admin_tabs('lv');?>
		<?php
        $capabilities = array('activity'=>'活动','message'=>'私信','labs'=>sprintf( '%1$s',zrz_custom_name('labs_name')),'post'=>'发文','comment'=>'评论','topic'=>'发帖','reply'=>'回帖','bubble'=>sprintf( '%1$s',zrz_custom_name('bubble_name')));
        $html = '';

        foreach ($array as $key) {
            $lv = zrz_get_lv_settings($key);
            echo '<h2>'.$key.' 等级设置</h2>';

            if(isset($lv['credit'])){
                $arr = array(
                    array(
                        'type' => 'input',
                        'th' => '名称',
                        'key' => $key.'_name',
                        'value' => $lv['name']
                    ),
                    array(
                        'type' => 'input',
                        'th' => '积分',
                        'key' => $key.'_credit',
                        'value' => $lv['credit']
                    ),
                    array(
                        'type' => 'checkbox',
                        'th' => __('权限','ziranzhi2'),
                        'key' => $key.'_capabilities',
                        'value' => array(
                            'default' => $lv['capabilities'],
                            'option' => $capabilities
                        )
                    ),
                );
            }else{
                $arr = array(
                    array(
                        'type' => 'select',
                        'th' => __('是否启用','ziranzhi2'),
                        'after' => '<p>'.__('是否启用此会员等级','ziranzhi2').'</p>',
                        'key' =>$key.'_open',
                        'value' => array(
                            'default' => array((isset($lv['open']) ? $lv['open'] : 0)),
                            'option' => array(
                                1 => __( '启用', 'ziranzhi2' ),
                                0 => __( '关闭', 'ziranzhi2' ),
                            )
                        )
                    ),
                    array(
                        'type' => 'input',
                        'th' => '名称',
                        'key' => $key.'_name',
                        'value' => isset($lv['name']) ? $lv['name'] : '',
                    ),
                    array(
                        'type' => 'checkbox',
                        'th' => __('权限','ziranzhi2'),
                        'key' => $key.'_capabilities',
                        'value' => array(
                            'default' => isset($lv['capabilities']) ? $lv['capabilities'] : array(),
                            'option' => $capabilities
                        )
                    ),
                    array(
                        'type' => 'input',
                        'th' => __('时效','ziranzhi2'),
                        'after' => '<p>'.__('会员过期时间，单位是天，请输入天数（纯数字），若要永久有效，请填写0','ziranzhi2').'</p>',
                        'key' => $key.'_time',
                        'value' => isset($lv['time']) ? $lv['time'] : 0,
                    ),
                    array(
                        'type' => 'input',
                        'th' => __('购买价格','ziranzhi2'),
                        'after' => '<p>'.__('请直接填写数字，如果在购买付费会员的页面不想显示请留空','ziranzhi2').'</p>',
                        'key' => $key.'_price',
                        'value' => isset($lv['price']) ? $lv['price'] : '',
                    ),
                    array(
                        'type' => 'select',
                        'th' => __('允许查看所有内容？','ziranzhi2'),
                        'after' => '<p>'.__('是否允许当前等级的用户不受限制，查看任何文章内容？如果选择禁止，则只能查看专门为此用户组设置的内容','ziranzhi2').'</p>',
                        'key' =>$key.'_allow',
                        'value' => array(
                            'default' => array((isset($lv['allow_all']) ? $lv['allow_all'] : 0)),
                            'option' => array(
                                1 => __( '允许', 'ziranzhi2' ),
                                0 => __( '禁止', 'ziranzhi2' ),
                            )
                        )
                    ),
                );
            }
            $option->table($arr);
        }

		?>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
	<?php
}
