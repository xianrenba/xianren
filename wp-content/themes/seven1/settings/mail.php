<?php
function zrz_options_mail_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

      $options = array(
          'FromName'=>trim_value($_POST['FromName']),
          'From'=>trim_value($_POST['From']),
          'Host'=>trim_value($_POST['Host']),
          'Port'=>trim_value($_POST['Port']),
          'Username'=>$_POST['Username'],
          'Password'=>$_POST['Password'],
          'SMTPAuth'=>$_POST['SMTPAuth'],
          'SMTPSecure'=>$_POST['SMTPSecure'],
          'open'=>$_POST['open']
          );

        update_option( 'zrz_mail_setting',$options );

    zrz_settings_error('updated');

  endif;

	$option = new zrzOptionsOutput();
	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('邮件发送设置','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		    zrz_admin_tabs('mail');
		?>

        <h3 class="title"><?php _e('邮件发送设置','ziranzhi2');?></h3>
        <p>有些服务器不支持 mail() 函数，所以使用 wrodpress 自带的 phpmailer 类来实现邮件的发送，优点多多！</p>
		<?php
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('是否启用','ziranzhi2'),
                'after' => '<p class="description">'.__('如果您使用了其他插件实现邮件发送，或者自己的服务器支持，可以关闭此项','ziranzhi2').'</p>',
                'key' => 'open',
                'value' => array(
                    'default' => array(zrz_get_mail_settings('open')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' )
                    )
                )
            ),
                array(
                    'type' => 'select',
                    'th' => __('SMTP认证','ziranzhi2'),
                    'key' => 'SMTPAuth',
                    'value' => array(
                        'default' => array(zrz_get_mail_settings('SMTPAuth')),
                        'option' => array(
                            true => __( '启用', 'ziranzhi2' ),
                            false => __( '关闭', 'ziranzhi2' )
                        )
                    )
                ),
    			array(
    				'type' => 'input',
    				'th' => __('发信人昵称','ziranzhi2'),
                    'after' => '<p class="description">'.__('邮件中显示的发件人姓名，比如：张三','ziranzhi2').'</p>',
    				'key' => 'FromName',
    				'value' => zrz_get_mail_settings('FromName')
    			),
                array(
    				'type' => 'input',
    				'th' => __('显示的发信邮箱','ziranzhi2'),
                    'after' => '<p class="description">'.__('邮件中显示的发件人邮箱地址，比如：xxx@xxx.com','ziranzhi2').'</p>',
    				'key' => 'From',
    				'value' => zrz_get_mail_settings('From')
    			),
                array(
    				'type' => 'input',
    				'th' => __('邮箱的SMTP服务器地址','ziranzhi2'),
                    'after' => '<p class="description">'.__('请查询自己邮箱提供商的 SMTP 地址，比如：smtp.163.com','ziranzhi2').'</p>',
    				'key' => 'Host',
    				'value' => zrz_get_mail_settings('Host')
    			),
                array(
    				'type' => 'input',
    				'th' => __('SMTP服务器端口','ziranzhi2'),
                    'after' => '<p class="description">'.__('请查询自己邮箱提供商的端口地址，默认：25','ziranzhi2').'</p>',
    				'key' => 'Port',
    				'value' => zrz_get_mail_settings('Port')
    			),
                array(
                    'type' => 'select',
                    'th' => __('SMTP加密方式','ziranzhi2'),
                    'key' => 'SMTPSecure',
                    'value' => array(
                        'default' => array(zrz_get_mail_settings('SMTPSecure')),
                        'option' => array(
                            'tls' => __( 'tls', 'ziranzhi2' ),
                            'ssl' => __( 'ssl', 'ziranzhi2' ),
                            'no'=> __( '空', 'ziranzhi2' )
                        )
                    )
                ),
                array(
    				'type' => 'input',
    				'th' => __('邮箱地址','ziranzhi2'),
                    'after' => '<p class="description">'.__('您的邮箱地址，请正确输入。','ziranzhi2').'</p>',
    				'key' => 'Username',
    				'value' => zrz_get_mail_settings('Username')
    			),
                array(
    				'type' => 'input-password',
    				'th' => __('邮箱密码','ziranzhi2'),
                    'after' => '<p class="description">'.__('您的邮箱密码或授权码（根据服务商的不同需求设置）。','ziranzhi2').'</p>',
    				'key' => 'Password',
    				'value' => zrz_get_mail_settings('Password')
    			),
    		) );

		?>
        <p class="description" style="color:red">阿里云 ECS 关闭了<code>25</code>端口，如果您使用的是阿里云 ECS 请选择 <code>SSL</code> 的加密方式，并且正确填入相应的端口。</p>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
	<?php
}
