<?php
function zrz_options_media_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

    $options = apply_filters('jike_media_upload_get',array(
            'media_place'=>$_POST['media_place'],
            'huiyuan'=>$_POST['huiyuan'],
            'single_max_width'=>$_POST['single_max_width'],
            'aliyun'=>array(
                'access_key'=>trim_value($_POST['aliyun_access_key']),
                'access_key_secret'=>trim_value($_POST['aliyun_access_key_secret']),
                'bucket'=>trim_value($_POST['aliyun_bucket']),
                'path'=>trim_value($_POST['aliyun_path']),
                'host'=>trim_value($_POST['aliyun_host']),
                'endpoint'=>trim_value($_POST['aliyun_endpoint']),
                'watermark'=>trim_value($_POST['watermark'])
            ),
            'qiniu'=>array(
                'access_key'=>$_POST['qiniu_access_key'],
                'access_key_secret'=>$_POST['qiniu_access_key_secret'],
                'bucket'=>$_POST['qiniu_bucket'],
                'path'=>$_POST['qiniu_path'],
                'host'=>$_POST['qiniu_host']
            ),
			'upyun'=>array(
                'bucket'=>$_POST['upyun_bucket'],
                'operator_name'=>$_POST['upyun_operator_name'],
                'operator_pwd'=>$_POST['upyun_operator_pwd'],
                'path'=>$_POST['upyun_path'],
				'host'=>$_POST['upyun_host']
            ),
            'auto_avatar'=>$_POST['auto_avatar'],
            'avatar_first'=>$_POST['avatar_first'],
            'webp'=>$_POST['webp'],
            'max_width'=>trim_value($_POST['max_width']),
            'quality'=>trim_value($_POST['quality']),
            'avatar_gif'=>$_POST['avatar_gif'],
            'avatar_host'=>$_POST['avatar_host'],
        ));

    if(get_option('zrz_media_setting') !== false){
        update_option( 'zrz_media_setting',$options );
    }else{
        add_option( 'zrz_media_setting', $options);
    }

    zrz_settings_error('updated');

endif;
	$option = new zrzOptionsOutput();
	?>

<div class="wrap">
	<h1><?php _e('柒比贰主题设置','zrz');?></h1>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
        <h2 class="title"><?php _e('媒体','zrz');?></h2>

		<?php
		zrz_admin_tabs('media');

        echo '<h2 class="title" style="margin-top:40px">储存设置</h2>';
		$option->table( array(
            array(
                'type' => 'select',
                'th' => __('请选择文件储存位置','ziranzhi2'),
                'after' => '<p>'.__('如果使用了云服务，请先配置好云，然后在下面填写相关参数','ziranzhi2').'</p>',
                'key' => 'media_place',
                'value' => array(
                    'default' => array(zrz_get_media_settings('media_place')),
                    'option' => array(
                        'localhost' => __( '本地', 'ziranzhi2' ),
                        //'qiniu' => __( '七牛云', 'ziranzhi2' ),
                        'aliyun' => __( '阿里云OSS', 'ziranzhi2' ),
                        //'upyun' => __( '又拍云', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<div id="localhost-set" class="hide"><h3>回源CDN设置</h3>';
        $huiyuan = zrz_get_media_settings('huiyuan');
        $option->table( array(
            array(
                'type' => 'input',
                'th' => __('请输入回源（cdn）的域名','ziranzhi2'),
                'after'=>__('<p>如果需要回源请设置，如果不需要，请留空！比如<code>https://status.7b2.com</code></p>','ziranzhi2'),
                'key' => 'huiyuan',
                'value' => $huiyuan
            )
        ));
        echo '</div><div id="qiniu-set" class="hide"><h3>七牛云设置</h3>';
        $qiniu = zrz_get_media_settings('qiniu');
        $option->table( array(
            array(
                'type' => 'input',
                'th' => __('AK','ziranzhi2'),
                'key' => 'qiniu_access_key',
                'value' => $qiniu['access_key']
            ),
            array(
                'type' => 'input-password',
                'th' => __('SK','ziranzhi2'),
                'key' => 'qiniu_access_key_secret',
                'value' => $qiniu['access_key_secret']
            ),
            array(
                'type' => 'input',
                'th' => __('上传的空间','ziranzhi2'),
                'after'=>__('<p>七牛云中建立的上传空间</p>','ziranzhi2'),
                'key' => 'qiniu_bucket',
                'value' => $qiniu['bucket']
            ),
            array(
                'type' => 'input',
                'th' => __('上传的目录','ziranzhi2'),
                'after'=>__('<p>留空，则上传到OSS根目录，默认上传到 <code>wp-content/uploads</code> 目录</p>','ziranzhi2'),
                'key' => 'qiniu_path',
                'value' => $qiniu['path']
            ),
            array(
                'type' => 'input',
                'th' => __('绑定的域名','ziranzhi2'),
                'after'=>__('<p>可以是七牛云的默认域名，建议自己绑定的域名。（支持https）</p>比如：<p><code>http://xxxx.bkt.clouddn.com</code></p><p><code>http://images.my-domain.com</code></p>','ziranzhi2'),
                'key' => 'qiniu_host',
                'value' => $qiniu['host']
            ),
        ));
        echo '</div><div id="aliyun-set" class="hide"><h3>阿里云 OSS 设置</h3>';
        $aliyun = zrz_get_media_settings('aliyun');
        $option->table( array(
            array(
                'type' => 'input',
                'th' => __('Access Key ID','ziranzhi2'),
                'key' => 'aliyun_access_key',
                'value' => $aliyun['access_key']
            ),
            array(
                'type' => 'input-password',
                'th' => __('Access Key Secret','ziranzhi2'),
                'key' => 'aliyun_access_key_secret',
                'value' => $aliyun['access_key_secret']
            ),
            array(
                'type' => 'input',
                'th' => __('上传的空间','ziranzhi2'),
                'after'=>__('<p>OSS中建立的上传空间</p>','ziranzhi2'),
                'key' => 'aliyun_bucket',
                'value' => $aliyun['bucket']
            ),
            array(
                'type' => 'input',
                'th' => __('上传的目录','ziranzhi2'),
                'after'=>__('<p>留空，则上传到根目录，默认上传到 <code>wp-content/uploads</code> 目录</p>','ziranzhi2'),
                'key' => 'aliyun_path',
                'value' => $aliyun['path']
            ),
            array(
                'type' => 'input',
                'th' => __('OSS绑定的域名','ziranzhi2'),
                'after'=>__('<p>可以是OSS的默认域名，也可以是自己绑定的域名。（支持https）</p>比如：<p><code>http://my-bucket.oss-cn-shenzhen.aliyuncs.com</code></p><p><code>http://status.your-domain.com</code></p>','ziranzhi2'),
                'key' => 'aliyun_host',
                'value' => $aliyun['host']
            ),
            array(
                'type' => 'input',
                'th' => __('OSS上传的端域','ziranzhi2'),
                'after' => __('<p>默认为<code>oss-cn-hangzhou.aliyuncs.com</code>,可选的节点有：</p><p><code>oss-cn-shenzhen.aliyuncs.com</code></p><p><code>oss-cn-shanghai.aliyuncs.com</code></p><p><code>oss-us-west-1.aliyuncs.com</code><p>如果您的服务器也是阿里云的可以使用内网的端域：</p><p><code>	
                oss-cn-hangzhou-<span style="color:red">internal</span>.aliyuncs.com</code></p>','ziranzhi2'),
                'key' => 'aliyun_endpoint',
                'value' => $aliyun['endpoint']
            ),
            array(
                'type' => 'input',
                'th' => __('OSS水印参数','ziranzhi2'),
                'after'=>__('<p>OSS的水印参数，文档请参考：<a target="_blank" href="https://help.aliyun.com/document_detail/44957.html?spm=a2c4g.11186623.6.1027.xpVkuf">OSS水印文档</a><br>例如：<code>/watermark,image_cGFuZGEucG5nP3gtb3NzLXByb2Nlc3M9aW1hZ2UvcmVzaXplLFBfMzA,t_90,g_se,x_10,y_10</code>，不设置水印请留空</p>','ziranzhi2'),
                'key' => 'watermark',
                'value' => isset($aliyun['watermark']) ? $aliyun['watermark'] : ''
            ),
        ));
        echo '</div><div id="upyun-set" class="hide">又拍云设置';
		$upyun = zrz_get_media_settings('upyun');
        $option->table( array(
            array(
                'type' => 'input',
                'th' => __('服务名','ziranzhi2'),
                'key' => 'upyun_bucket',
                'value' => $upyun['bucket']
            ),
            array(
                'type' => 'input',
                'th' => __('操作员用户名','ziranzhi2'),
				'after'=>__('<p>当前服务所对应的操作员账号</p>','ziranzhi2'),
                'key' => 'upyun_operator_name',
                'value' => $upyun['operator_name']
            ),
            array(
                'type' => 'input-password',
                'th' => __('操作员密码','ziranzhi2'),
                'after'=>__('<p>当前服务所对应的操作员密码</p>','ziranzhi2'),
                'key' => 'upyun_operator_pwd',
                'value' => $upyun['operator_pwd']
            ),
            array(
                'type' => 'input',
                'th' => __('上传的目录','ziranzhi2'),
                'after'=>__('<p>留空，则上传到根目录，默认上传到 <code>wp-content/uploads</code> 目录</p>','ziranzhi2'),
                'key' => 'upyun_path',
                'value' => $upyun['path']
            ),
			array(
                'type' => 'input',
                'th' => __('绑定的域名','ziranzhi2'),
                'after'=>__('<p>可以是又拍云的默认域名，建议自己绑定的域名。（支持https）</p>比如：<p><code>http://xxx.b0.upaiyun.com</code></p><p><code>http://www.my-domain.com</code></p>','ziranzhi2'),
                'key' => 'upyun_host',
                'value' => $upyun['host']
            ),
        ));
        echo '</div>';
        echo '<h2>图像优化</h2>';
        echo '图像优化只对前端的富文本编辑器生效。';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('是否允许图片自动启用webp格式？','ziranzhi2'),
                'after' => '<p>'.__('只有设置了云储存以后，此项才会生效','ziranzhi2').'</p>',
                'key' => 'webp',
                'value' => array(
                    'default' => array(zrz_get_media_settings('webp')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('上传图片最大尺寸','ziranzhi2'),
                'after' => '<p>'.__('为了提高上传速度，节省图片空间，上传的图片体积会经过压缩，的默认尺寸<code>900</code>，如果不压缩尺寸，请填0','ziranzhi2').'</p>',
                'key' => 'max_width',
                'value' => zrz_get_media_settings('max_width')
            ),
            array(
                'type' => 'input',
                'th' => __('图片的压缩质量','ziranzhi2'),
                'after' => '<p>'.__('上传图片的压缩质量从1-100，建议<code>94</code>，如果大于这个数值，则图片的体积可能会大幅增长。','ziranzhi2').'</p>',
                'key' => 'quality',
                'value' => zrz_get_media_settings('quality')
            ),
            array(
                'type' => 'input',
                'th' => __('文章内部图片裁剪尺寸','ziranzhi2'),
                'after' => '<p>'.__('文章内部图片如果需要裁剪，请填写裁剪的尺寸，如果不裁剪，请直接填0','ziranzhi2').'</p>',
                'key' => 'single_max_width',
                'value' => zrz_get_media_settings('single_max_width')
            ),
        ));
        echo '<h2>头像设置</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('是否开启字母头像？','ziranzhi2'),
                'after' => '<p>'.__('不开启的时候，显示默认的头像','ziranzhi2').'</p>',
                'key' => 'auto_avatar',
                'value' => array(
                    'default' => array(zrz_get_media_settings('auto_avatar')),
                    'option' => array(
                        1 => __( '开启', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('第一个字作为头像还是最后一个字作为头像','ziranzhi2'),
                'after' => '<p>'.__('支持中英文','ziranzhi2').'</p>',
                'key' => 'avatar_first',
                'value' => array(
                    'default' => array(zrz_get_media_settings('avatar_first')),
                    'option' => array(
                        1 => __( '第一个字', 'ziranzhi2' ),
                        0 => __( '最后一个字', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('是否允许gif动画头像','ziranzhi2'),
                'key' => 'avatar_gif',
                'value' => array(
                    'default' => array(zrz_get_media_settings('avatar_gif')),
                    'option' => array(
                        1 => __( '允许', 'ziranzhi2' ),
                        0 => __( '不允许', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('是否储存第三方登陆的用户头像到本地服务器？','ziranzhi2'),
                'after' => '<p>'.__('如果用户社交登陆的时候一直卡在“跳转中”，请切换直接使用第三方服务器。（中途切换可能造成已有的社交登陆用户头像不能显示，最好不要频繁变动）','ziranzhi2').'</p>',
                'key' => 'avatar_host',
                'value' => array(
                    'default' => array(zrz_get_media_settings('avatar_host')),
                    'option' => array(
                        1 => __( '将头像储存到本地服务器', 'ziranzhi2' ),
                        0 => __( '直接使用第三方服务器', 'ziranzhi2' ),
                    )
                )
            ),
        ))
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
    <style>
#aliyun-set,#upyun-set,#qiniu-set,#huiyuan-set{display:none}
</style>
<script>
    var jsms = jQuery('#media_place').find(':selected').val();
    if(jsms == 'localhost'){
        jQuery('#localhost-set').show();
    }else if(jsms == 'juhe'){
        jQuery('#juhe-set').show();
    }else if(jsms == 'aliyun'){
        jQuery('#aliyun-set').show();
    }else{
        jQuery('#huiyuan-set').show();
    }

    jQuery('#media_place').on('change', function() {
        var val = jQuery(this).find(':selected').val();
        var array = ['localhost','juhe','aliyun','huiyuan'];
        array.forEach(function(_val){
            if(val == _val){
                jQuery('#'+_val+'-set').show();
            }else{
                jQuery('#'+_val+'-set').hide();
            }
        });
    })
</script>
</div>
	<?php
}
