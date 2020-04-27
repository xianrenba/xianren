<?php
function zrz_options_social_page(){

    if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

        $options = array(
            'open_qq'=>$_POST['open_qq'],
            'open_weibo'=>$_POST['open_weibo'],//是否启用微博登陆
            'open_weixin'=>$_POST['open_weixin'],
            'open_weixin_gz'=>$_POST['open_weixin_gz'],
            'open_qq_key'=>trim_value($_POST['open_qq_key']),
            'open_qq_secret'=>trim_value($_POST['open_qq_secret']),
            'open_weibo_key'=>trim_value($_POST['open_weibo_key']),
            'open_weibo_secret'=>trim_value($_POST['open_weibo_secret']),
            'open_weixin_key'=>trim_value($_POST['open_weixin_key']),
            'open_weixin_secret'=>trim_value($_POST['open_weixin_secret']),
            'open_weixin_gz_key'=>trim_value($_POST['open_weixin_gz_key']),
            'open_weixin_gz_secret'=>trim_value($_POST['open_weixin_gz_secret']),
            'open_check_code'=>$_POST['open_check_code'],
            'complete_material'=>$_POST['complete_material'],//是否需要强制用户完善资料
            'open_new_window'=> $_POST['open_new_window'],//是否在新窗口打开
            'type'=>$_POST['type'],//1使用邮箱验证，2使用短信验证，3邮箱和短信均可验证
            'has_invitation'=>$_POST['has_invitation'],
            'invitation_text'=>$_POST['invitation_text'],
            'invitation_must'=>$_POST['invitation_must'],
            'sms_select'=>$_POST['sms_select'],
            'phone_setting'=>array(
                'accessKeyId'=>trim_value($_POST['accessKeyId']),
                'accessKeySecret'=>trim_value($_POST['accessKeySecret']),
                'signName'=>trim_value($_POST['signName']),
                'templateCode'=>trim_value($_POST['templateCode']),
            ),
            'yunpian'=>array(
                'apikey'=>trim_value($_POST['apikey']),
                'text'=>trim_value($_POST['text'])
            ),
            'juhe'=>array(
                'tpl_id'=>trim_value($_POST['tpl_id']),
                'key'=>trim_value($_POST['key'])
            )
        );


        update_option( 'zrz_social_setting',$options );

        zrz_settings_error('updated');

    endif;

	$option = new zrzOptionsOutput();

	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('登录与注册','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		      zrz_admin_tabs('social');
		?>

        <h2 class="title"><?php _e('社交登录','ziranzhi2');?></h2>
		<?php
        $option->table( array(
    			array(
    				'type' => 'select',
    				'th' => __('启用QQ登录','ziranzhi2'),
    				'key' => 'open_qq',
    				'value' => array(
    					'default' => array(zrz_get_social_settings('open_qq')),
    					'option' => array(
    						1 => __( '启用', 'ziranzhi2' ),
    						0 => __( '关闭', 'ziranzhi2' )
    					)
    				)
    			),
    			array(
    				'type' => 'input',
    				'th' => __('QQ ID','ziranzhi2'),
    				'key' => 'open_qq_key',
    				'value' => zrz_get_social_settings('open_qq_key')
    			),
    			array(
    				'type' => 'input-password',
    				'th' => __('QQ KEY','ziranzhi2'),
    				'key' => 'open_qq_secret',
    				'value' => zrz_get_social_settings('open_qq_secret')
    			),
    			array(
    				'type' => 'select',
    				'th' => __('启用微博登录','ziranzhi2'),
    				'key' => 'open_weibo',
    				'value' => array(
    					'default' => array(zrz_get_social_settings('open_weibo')),
    					'option' => array(
    						1 => __( '启用', 'ziranzhi2' ),
    						0 => __( '关闭', 'ziranzhi2' )
    					)
    				)
    			),
    			array(
    				'type' => 'input',
    				'th' => __('WEIBO KEY','ziranzhi2'),
    				'key' => 'open_weibo_key',
    				'value' => zrz_get_social_settings('open_weibo_key')
    			),
    			array(
    				'type' => 'input-password',
    				'th' => __('WEIBO SECRET','ziranzhi2'),
    				'key' => 'open_weibo_secret',
    				'value' => zrz_get_social_settings('open_weibo_secret')
                ),
                array(
    				'type' => 'select',
    				'th' => __('启用微信pc端扫码登录','ziranzhi2'),
    				'key' => 'open_weixin',
    				'value' => array(
    					'default' => array(zrz_get_social_settings('open_weixin')),
    					'option' => array(
    						1 => __( '启用', 'ziranzhi2' ),
    						0 => __( '关闭', 'ziranzhi2' )
    					)
    				)
    			),
    			array(
    				'type' => 'input',
                    'th' => __('WEIXIN KEY','ziranzhi2'),
                    'key' => 'open_weixin_key',
                    'after' => '<p class="description">'.__('微信开放平台网站应用的key','ziranzhi2').'</p>',
    				'value' => zrz_get_social_settings('open_weixin_key')
    			),
    			array(
    				'type' => 'input-password',
                    'th' => __('WEIXIN SECRET','ziranzhi2'),
                    'key' => 'open_weixin_secret',
                    'after' => '<p class="description">'.__('微信开放平台网站应用的secret','ziranzhi2').'</p>',
    				'value' => zrz_get_social_settings('open_weixin_secret')
                ),
                array(
    				'type' => 'select',
    				'th' => __('启用微信公众号内授权登录','ziranzhi2'),
    				'key' => 'open_weixin_gz',
    				'value' => array(
    					'default' => array(zrz_get_social_settings('open_weixin_gz')),
    					'option' => array(
    						1 => __( '启用', 'ziranzhi2' ),
    						0 => __( '关闭', 'ziranzhi2' )
    					)
    				)
    			),
    			array(
    				'type' => 'input',
                    'th' => __('WEIXIN KEY','ziranzhi2'),
                    'key' => 'open_weixin_gz_key',
                    'after' => '<p class="description">'.__('微信公众平台->基本配置->开发者ID(AppID)','ziranzhi2').'</p>',
    				'value' => zrz_get_social_settings('open_weixin_gz_key')
    			),
    			array(
    				'type' => 'input-password',
                    'th' => __('WEIXIN SECRET','ziranzhi2'),
                    'key' => 'open_weixin_gz_secret',
                    'after' => '<p class="description">'.__('微信公众平台->基本配置->开发者密码','ziranzhi2').'</p>',
    				'value' => zrz_get_social_settings('open_weixin_gz_secret')
    			),
                array(
                    'type' => 'select',
                    'th' => __('强制用户完善资料？','ziranzhi2'),
                    'after' => '<p class="description">'.__('第一次社交登陆的时候是否强制需要用户完善个人资料？','ziranzhi2').'</p>',
                    'key' => 'complete_material',
                    'value' => array(
                        'default' => array(zrz_get_social_settings('complete_material')),
                        'option' => array(
                            true => __( '启用', 'ziranzhi2' ),
                            false => __( '关闭', 'ziranzhi2' )
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'th' => __('社交登陆新窗口打开？','ziranzhi2'),
                    'key' => 'open_new_window',
                    'after' => '<p class="description">'.__('点击社交登陆按钮之后，跳转到QQ或者微博的登陆界面，是新窗口打开，还是在当前的窗口跳转？建议新窗口打开','ziranzhi2').'</p>',
                    'value' => array(
                        'default' => array(zrz_get_social_settings('open_new_window')),
                        'option' => array(
                            1 => __( '新窗口打开', 'ziranzhi2' ),
                            0 => __( '当前窗口直接跳转', 'ziranzhi2' )
                        )
                    )
                ),
    		) );

		?>
        <h3>社交登录申请</h3>
        <p class="description" style="color:red">QQ登录申请地址：<a href="https://connect.qq.com/" target="_blank">https://connect.qq.com/</a></p>
        <p class="description" style="color:red">微博登录申请地址：<a href="http://open.weibo.com/development" target="_blank">http://open.weibo.com/development</a></p>
        <p class="description" style="color:red">微信登录的申请地址<a target="_blank" href="https://open.weixin.qq.com/">https://open.weixin.qq.com/</a></p>

        <h3>回调地址的填写方式</h3>
        <p class="description" style="color:red">QQ和微博的回调地址请填写：<?php echo home_url('/open'); ?></p>
        <p class="description"><span style="color:red">微信的回调地址请填写：<?php echo str_replace(array('http://','https://'),'',home_url()); ?></span>（请注意没有http:// 或 https://）</p>

        <h2 class="title"><?php _e('注册验证方式','ziranzhi2');?></h2>
        <?php
            $option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('注册和找回密码时使用何种验证方式','ziranzhi2'),
                    'after' => '<p class="description">'.__('使用邮箱验证请先完成主题设置中的 <邮件发送设置>，并确保邮件可以发送出去。<br>使用短信验证注册，请确保开通了阿里云的短信服务，并且已经在下面设置完成。<br>两种验证同时启用，请确保以上两个设置均已完成。','ziranzhi2').'</p>',
                    'key' => 'type',
                    'value' => array(
                        'default' => array(zrz_get_social_settings('type')),
                        'option' => array(
                            1 => __( '邮箱验证', 'ziranzhi2' ),
                            2 => __( '短信验证', 'ziranzhi2' ),
                            3 => __( '同时启用邮箱验证和短信验证', 'ziranzhi2' ),
                            4 => __( '主题自动生成的验证码验证', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
    				'type' => 'select',
    				'th' => __('是否启用邀请码功能','ziranzhi2'),
    				'key' => 'has_invitation',
    				'value' => array(
                        'default' => array(zrz_get_social_settings('has_invitation')),
                        'option' => array(
                            1 => __( '启用', 'ziranzhi2' ),
                            0 => __( '禁用', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
    				'type' => 'select',
    				'th' => __('邀请码必填？','ziranzhi2'),
    				'key' => 'invitation_must',
                    'after' => '<p class="description">'.__('如果邀请码必填，则必须邀请码才可以注册，如果选填，邀请码可为空。','ziranzhi2').'</p>',
    				'value' => array(
                        'default' => array(zrz_get_social_settings('invitation_must')),
                        'option' => array(
                            1 => __( '必填', 'ziranzhi2' ),
                            0 => __( '选填', 'ziranzhi2' ),
                        )
                    )
                ),
                array(
                    'type' => 'input',
                    'th' => __('邀请获取连接','ziranzhi2'),
                    'after' => '<p>例如：</p><p><code>http://xxxx.com/xxx|获取</code></p>',
                    'key' => 'invitation_text',
                    'value' =>zrz_get_social_settings('invitation_text')
                ),
                array(
    				'type' => 'select',
    				'th' => __('开启登陆验证码','ziranzhi2'),
    				'key' => 'open_check_code',
                    'after' => '<p class="description">'.__('如果开启，则登陆的时候必须输入验证码。','ziranzhi2').'</p>',
    				'value' => array(
                        'default' => array(zrz_get_social_settings('open_check_code')),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
            ));
            ?>
            <h2 class="title"><?php _e('短信验证设置项','ziranzhi2');?></h2>

            <?php
                $select = zrz_get_social_settings('sms_select');
                $option->table( array(
                    array(
                        'type' => 'select',
                        'th' => __('请选择短信验证服务商','ziranzhi2'),
                        'after' => '<p>'.__('选择以后，请设置下面的相关参数','ziranzhi2').'</p>',
                        'key' => 'sms_select',
                        'value' => array(
                            'default' => array($select),
                            'option' => array(
                                'aliyun' => __( '阿里云', 'ziranzhi2' ),
                                'yunpian' => __( '云片', 'ziranzhi2' ),
                                'juhe' => __( '聚合', 'ziranzhi2' )
                            )
                        )
                    )
                ));
            ?>

            <div id="aliyun-set">
                <?php
                    $aliyun = zrz_get_social_settings('phone_setting');
                    $option->table( array(
                        array(
                            'type' => 'input',
                            'th' => __('accessKeyId','ziranzhi2'),
                            'key' => 'accessKeyId',
                            'after' => '<p class="description">'.__('阿里云的<code>accessKeyId</code>','ziranzhi2').'</p>',
                            'value' => $aliyun['accessKeyId']
                        ),
                        array(
                            'type' => 'input-password',
                            'th' => __('accessKeySecret','ziranzhi2'),
                            'key' => 'accessKeySecret',
                            'after' => '<p class="description">'.__('阿里云的<code>accessKeySecret</code>','ziranzhi2').'</p>',
                            'value' => $aliyun['accessKeySecret']
                        ),
                        array(
                            'type' => 'input',
                            'th' => __('签名名称','ziranzhi2'),
                            'key' => 'signName',
                            'after' => '<p class="description">'.__('请填写短信服务->签名管理->签名名称（必须是已通过的签名）','ziranzhi2').'</p>',
                            'value' => $aliyun['signName']
                        ),
                        array(
                            'type' => 'input',
                            'th' => __('模版Code','ziranzhi2'),
                            'key' => 'templateCode',
                            'after' => '<p class="description">'.__('请填写短信服务->模版管理->模版Code（必须是已通过的模版Code）','ziranzhi2').'</p>',
                            'value' => $aliyun['templateCode']
                        ),
                    ))
                ?>
                <p class="description" style="color:red">如果开启了手机注册请填写。短信服务申请地址：<a href="https://www.aliyun.com/product/sms?spm=5176.8142029.388261.388.e93976f4ow0JNw" target="_blank">短信服务-阿里云</a></p>
            </div>
            <div id="yunpian-set">
                <?php
                    $yunpian = zrz_get_social_settings('yunpian');
                    $option->table(array(
                        array(
                            'type' => 'input-password',
                            'th' => __('apikey','ziranzhi2'),
                            'after' => '<p>'.__('云片管理控制台->账户设置->子账户管理','ziranzhi2').'</p>',
                            'key' => 'apikey',
                            'value' => $yunpian['apikey']
                        ),
                        array(
                            'type' => 'input',
                            'th' => __('模板内容','ziranzhi2'),
                            'after' => '<p>'.sprintf(
                                __('云片管理控制台->国内短信->签名模板报备->已审核的【模板内容】%s比如：%s','ziranzhi2'),
                                '<br>',
                                '<code>'.__('【'.get_bloginfo('name').'】您的验证码是#code#。如非本人操作，请忽略本短信','ziranzhi2').'</code>').'</p>',
                            'key' => 'text',
                            'value' => $yunpian['text']
                        )
                    ));
                ?>
                <p>一小时内同一号码、同一类型的验证码不能发送超过3次！申请地址：<a target="_blank" href="https://www.yunpian.com/">https://www.yunpian.com/</a></p>
            </div>

            <div id="juhe-set">
                <?php
                    $juhe = zrz_get_social_settings('juhe');
                    $option->table(array(
                        array(
                            'type' => 'input',
                            'th' => __('模板ID','ziranzhi2'),
                            'after' => '<p>'.__('我的聚合->短信API服务->短信模板->模板ID','ziranzhi2').'</p>',
                            'key' => 'tpl_id',
                            'value' => $juhe['tpl_id']
                        ),
                        array(
                            'type' => 'input-password',
                            'th' => __('Appkey','ziranzhi2'),
                            'after' => '<p>'.__('数据中心->我的数据->AppKey','ziranzhi2').'</p>',
                            'key' => 'key',
                            'value' => $juhe['key']
                        )
                    ));
                ?>
                <p>申请地址：<a target="_blank" href="https://www.juhe.cn/docs/api/id/54">https://www.juhe.cn/docs/api/id/54</a></p>
            </div>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
<style>
#aliyun-set,#yunpian-set,#juhe-set{display:none}
</style>
<script>
    var jsms = jQuery('#sms_select').find(':selected').val();
    if(jsms == 'yunpian'){
        jQuery('#yunpian-set').show();
    }else if(jsms == 'juhe'){
        jQuery('#juhe-set').show();
    }else{
        jQuery('#aliyun-set').show();
    }

    jQuery('#sms_select').on('change', function() {
        var val = jQuery(this).find(':selected').val();
        var array = ['yunpian','juhe','aliyun'];
        array.forEach(function(_val){
            if(val == _val){
                jQuery('#'+_val+'-set').show();
            }else{
                jQuery('#'+_val+'-set').hide();
            }
        });
    })
</script>
	<?php
}
