<?php
    /**
     * 主题设置项保存提示
    */
    function zrz_settings_error($type='updated',$message=''){
        $type = $type=='updated' ? 'updated' : 'error';
        if(empty($message)) $message = $type=='updated' ?  __('设置已保存。','zrz') : __('保存失败，请重试。，','zrz');
        add_settings_error(
            'zrz_settings_message',
            esc_attr( 'zrz_settings_updated' ),
            $message,
            $type
        );
        settings_errors( 'zrz_settings_message' );
    }

    /*
    * 主题设置项目
    */
    function zrz_get_theme_settings($type){
        //默认值
        $setting_arr = array(
            'theme_style'=>'list',//文章的默认展现形式，list 为列表模式，pinterest为网格模式
            'theme_style_mobile'=>'pinterest',
            'theme_style_select'=>1,//是否允许用户选择文章展现形式
            'page_width'=>'1140',//页面宽度
            'keywords'=>'',//关键词
            'description'=>'',//描述
            'post_exclude'=>array(),//首页排除分类
            'statistics'=>'',//统计代码
            'logo'=> ZRZ_THEME_URI.'/images/logo.svg',//logo
            'js_local'=>1,
            'logo_w'=>'',
            'meta'=>'',//header 部分 meta
            'link_cat'=>'',//页面底部显示的友情链接分类ID
            'clear_head'=>1,//是否清除无用的 meta
            'menu_style'=>1,//菜单显示模式
            'site_copy'=>'
                <p class="mar10-b">
                   Since 2015, Build with <span class="red">♥</span> by <a href="https://7b2.com">柒比贰</a>
                </p>
            ',
            'show_sidebar'=>1,
            'pinterest_count'=>3
        );

        $setting_arr = get_option('zrz_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    /*
    * 社交登陆设置
    */
    function zrz_get_social_settings($type){
        //默认值
        $setting_arr = array(
            'open_qq'=>0,//是否启用QQ登陆
            'open_weibo'=>0,//是否启用微博登陆
            'open_weixin_gz'=>0,
            'open_weixin'=>0,
            'open_qq_key'=>'',
            'open_qq_secret'=>'',
            'open_weibo_key'=>'',
            'open_weibo_secret'=>'',
            'open_weixin_key'=>'',
            'open_weixin_secret'=>'',
            'open_weixin_gz_key'=>'',
            'open_weixin_gz_secret'=>'',
            'complete_material'=>0,//是否需要强制用户完善资料
            'open_new_window'=> 1,//是否在新窗口打开
            'type'=>1,//1使用邮箱验证，2使用短信验证，3邮箱和短信均可验证，4使用人机验证注册
            'has_invitation'=>0,
            'invitation_must'=>1,
            'phone_setting'=>array(
                'accessKeyId'=>'',
                'accessKeySecret'=>'',
                'signName'=>'',
                'templateCode'=>''
            )
        );

        $setting_arr = get_option('zrz_social_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    /*
    * 获取主题列表风格设置
    */
    function zrz_get_theme_style(){
        if(zrz_wp_is_mobile()){
            $style = zrz_get_theme_settings('theme_style_mobile');
        }else{
            $style = zrz_get_theme_settings('theme_style');
        }
        if(!zrz_get_theme_settings('theme_style_select')){
            return $style;
        }else{
            return zrz_getcookie('theme_style') ? zrz_getcookie('theme_style') : $style;
        }
    }

    /*
    * 获取媒体设置项
    */
    function zrz_get_media_settings($type){
        //默认值
        $setting_arr = array(
            'media_place'=>'localhost',//媒体文件储存的位置
            'aliyun'=>array(
                'access_key'=>'',
                'access_key_secret'=>'',
                'bucket'=>'',
                'path'=>'wp-content/uploads',
                'host'=>'',
                'endpoint'=>'oss-cn-hangzhou.aliyuncs.com',
                'watermark'=>''
            ),
            'qiniu'=>array(
                'access_key'=>'',
                'access_key_secret'=>'',
                'bucket'=>'',
                'path'=>'wp-content/uploads',
                'host'=>'',
            ),
			'upyun'=>array(
                'bucket'=>'',
                'operator_name'=>'',
                'operator_pwd'=>'',
                'path'=>'wp-content/uploads',
				'host'=>'',
            ),
            'max_width'=>900,
            'media_check'=>1,
            'quality'=>94,
            'auto_avatar'=>1,
            'avatar_first'=>0,
            'avatar_gif'=>1,//是否头像允许 gif 动画？
            'avatar_host'=>1
        );

        $setting_arr = get_option('zrz_media_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    //获取积分设置项
    function zrz_get_credit_settings($type){
        $setting_arr = array(
    		'zrz_credit_signup'=>260,//注册奖励
    		'zrz_credit_comment'=>50,//发表评论奖励
    		'zrz_credit_post'=>200,//投稿奖励
    		'zrz_credit_post_commented'=>'5-20',//文章被评论或评论被回复或帖子被回复得分
    		'zrz_credit_comment_vote_up'=>50,//评论被点赞同评论者获得的积分
    		'zrz_credit_comment_vote_up_deduct'=>-40,//点赞同的人扣掉的积分
    		'zrz_credit_love'=>60,//文章点赞（收藏）奖励
            'zrz_credit_follow'=>60,//被关注
            'zrz_credit_followed'=>30,//关注他人
    		//'zrz_rec_credit'=>20,//每天可得积分的次数
            'zrz_credit_mission'=>'50-200',//签到奖励
            'zrz_credit_reply'=>70,//回复帖子
            'zrz_credit_topic'=>120,//创建一个帖子
            'zrz_credit_pps'=>100,//发表一个冒泡
            'zrz_credit_labs'=>200,//发表一个研究
			'zrz_credit_invitation'=>200,//成功邀请获得的积分
			'zrz_credit_be_invitation'=>200,//被邀请获得的积分
            'zrz_credit_rmb'=>260,//积分兑换
            'zrz_credit_name'=>'积分',//积分名称
            'zrz_credit_display'=>1,//显示类型
            'zrz_tx_min'=>50,//提成最小金额
            'zrz_cc'=>0.05,//网站抽成比例
            'zrz_tx_allowed'=>1,//是否允许提成
            'zrz_tx_admin'=>1//提成操作员ID
    	);

        $setting_arr = get_option('zrz_credit_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    //任务设置
    function zrz_get_task_setting($type){
        $setting_arr = array(
            'comment'=>array(
                'count'=>3,
                'open'=>1,
                'type'=>1
            ),//评论，主动任务
            'post'=>array(
                'count'=>1,
                'open'=>1,
                'type'=>1
            ),//发文，主动任务
            'post_commented'=>array(
                'count'=>3,
                'open'=>1,
                'type'=>0
            ),//文章被回复，回复被评论，被动任务
            'comment_vote_up'=>array(
                'count'=>2,
                'open'=>1,
                'type'=>0
            ),//评论被点赞，被动任务
            'comment_vote_up_deduct'=>array(
                'count'=>2,
                'open'=>1,
                'type'=>1
            ),//给其他人的评论点赞，主动任务
            'followed'=>array(
                'count'=>2,
                'open'=>1,
                'type'=>0
            ),//关注他人，主动任务
            'follow'=>array(
                'count'=>2,
                'open'=>1,
                'type'=>0
            ),//被关注，被动任务
            // 'mission'=>array(
            //     'count'=>1,
            //     'open'=>1,
            //     'type'=>1
            // ),//签到，主动任务
            'reply'=>array(
                'count'=>2,
                'open'=>1,
                'type'=>1
            ),//回复帖子，主动任务
            'topic'=>array(
                'count'=>2,
                'open'=>1,
                'type'=>1
            ),//发表帖子，主动任务
            'pps'=>array(
                'count'=>1,
                'open'=>1,
                'type'=>1
            ),//发表冒泡，主动任务
            'labs'=>array(
                'count'=>1,
                'open'=>1,
                'type'=>1
            ),//发表研究，主动任务
            'invitation'=>array(
                'count'=>5,
                'open'=>1,
                'type'=>1
            ),//邀请注册，主动任务
        );
        $setting_arr = get_option('zrz_task_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    //获取等级制度设置项
    function zrz_get_lv_settings($type = ''){
        $setting_arr = apply_filters( 'zrz_default_lv',array(
    		'lv0'=>array(
                'name'=>__('学前班','ziranzhi2'),
                'credit'=>0,
                'capabilities'=>array(
                    'message','comment','reply'
                )
            ),
            'lv1'=>array(
                'name'=>__('小学','ziranzhi2'),
                'credit'=>300,
                'capabilities'=>array(
                    'message','post','comment','reply','topic','bubble'
                )
            ),
            'lv2'=>array(
                'name'=>__('初中','ziranzhi2'),
                'credit'=>3000,
                'capabilities'=>array(
                    'message','post','comment','topic','reply','bubble'
                )
            ),
            'lv3'=>array(
                'name'=>__('高中','ziranzhi2'),
                'credit'=>30000,
                'capabilities'=>array(
                    'message','post','comment','topic','reply','bubble'
                )
            ),
            'lv4'=>array(
                'name'=>__('大学','ziranzhi2'),
                'credit'=>300000,
                'capabilities'=>array(
                    'message','labs','post','comment','topic','reply','bubble'
                )
            ),
            'lv5'=>array(
                'name'=>__('研究生','ziranzhi2'),
                'credit'=>3000000,
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                )
            ),
            'lv6'=>array(
                'name'=>__('博士','ziranzhi2'),
                'credit'=>30000000,
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                )
            ),
            'lv7'=>array(
                'name'=>__('博导','ziranzhi2'),
                'credit'=>300000000,
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                )
            ),
            'vip'=>array(
                'name'=>__('永久会员','ziranzhi2'),
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                ),
                'time'=>0,
                'price'=>600,
                'open'=>1,
                'allow_all'=>1
            ),
            'vip1'=>array(
                'name'=>__('周会员','ziranzhi2'),
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                ),
                'time'=>7,
                'price'=>7,
                'open'=>1,
                'allow_all'=>1
            ),
            'vip2'=>array(
                'name'=>__('月会员','ziranzhi2'),
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                ),
                'time'=>30,
                'price'=>28,
                'open'=>1,
                'allow_all'=>1
            ),
            'vip3'=>array(
                'name'=>__('年会员','ziranzhi2'),
                'capabilities'=>array(
                    'message','lottery','activity','labs','post','comment','topic','reply','bubble'
                ),
                'time'=>365,
                'price'=>300,
                'open'=>1,
                'allow_all'=>1
            ),
    	));

        $setting_arr = get_option('zrz_lv_setting',$setting_arr);
        if(!$type) return $setting_arr;
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    //投稿设置项
    function zrz_get_writing_settings($type){
        $setting_arr = array(
    		'cat'=>array(1),//允许投稿的分类
            'cat_more'=>1,//是否允许分类多选
    		'min_strlen'=>140,//投稿最少字数
    		'max_strlen'=>12000,//投稿最多字数
    		'status'=>0,//投稿后的状态
    		'edit_time'=>24,//多长时间内允许编辑
            'post_format'=>1,//是否允许用户选择文章形式
            'tag_count'=>5,//允许输入的最大标签数量
            'custom_tags'=>array(''),//管理员自定义的标签
            'related_chose'=>1,//是否允许用户选择相关文章
            'video_size'=>10,//允许上传的视频大小
            'labs_edit_time'=>24,//发布研究后多久允许继续编辑
            'labs_status'=>0,//研究提交以后处于什么状态
            'auto_draft'=>1
            // 'allow_video'=>1,//允许上传视频？
            // 'allow_fj'=>1,//允许上传附件？
            // 'fj_open'=>0,//附件新窗口打开？
    	);

        $setting_arr = get_option('zrz_writing_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    /*
    * 广告位设置
    */
    function zrz_get_ads_settings($type){
        //默认值
        $setting_arr = array(
            'home_list'=>array(
                'open'=>0,
                'str'=>''
            ),
            'home_card'=>array(
                'open'=>0,
                'str'=>''
            ),
            'single_footer'=>array(
                'open'=>0,
                'str'=>''
            ),
        );

        $setting_arr = get_option('zrz_ads_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    /*
    * 阅读设置
    */
    function zrz_get_reading_settings($type){
        //默认值
        $setting_arr = array(
            'ajax_post'=>0,
            'open_new'=>0,
            'ajax_comment'=>0,
            'show_topic_thumb'=>0,
            'highlight'=>0,
            'ajax_post_more'=>1
        );

        $setting_arr = get_option('zrz_reading_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    function zrz_open_new(){
        return zrz_get_reading_settings('open_new');
    }

    /*
    * 邮箱设置
    */
    function zrz_get_mail_settings($type){
        //默认值
        $setting_arr = array(
            'FromName'=>'',
            'From'=>'',
            'Host'=>'',
            'Port'=>'25',
            'Username'=>'',
            'Password'=>'',
            'open'=>0
        );

        $setting_arr = get_option('zrz_mail_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    /*
    * 显示设置
    */
    function zrz_get_display_settings($type){
        //默认值
        $setting_arr = array(
            'labs_show'=>1,
            'labs_show_index'=>1,
            'shop_show'=>1,
            'bubble_show'=>1,
            'activity_show'=>1,
            'display_top'=>1,
            'shop_style'=>1,
			'shop_show_d'=>1,
			'shop_show_g'=>1,
            'shop_show_c'=>1,
            'swipe_show'=>1,
            'sigup_welcome'=>'',
            'show_announcement_count'=>0,
            'delete_msg'=>array(
                'msg_open'=>0,
                'msg_time'=>90
            ),
            'custom_name'=>array(
                'labs_name'=>'研究所',
                'bubble_name'=>'冒泡',
                'shop_name'=>'商城',
                'bubble_default_topic_name'=>'广场'
            ),
            'single'=>array(
                'reaction'=>1,//表情投票
                'long_weibo'=>1,//长微博
                'navigation'=>1,//上一篇，下一篇
                'ds'=>1
            ),
			'go_top'=>array(
				'open'=>1,
                'contect'=>array(
					'id'=>1,
					'open'=>1,
				),
				'search'=>array(
					'open'=>1,
				)
            ),
            'home_bg'=>array(
                'open'=>array(0),
                'type'=>array(1),
                'img'=>''
            ),
            'collections'=>array(
                'text'=>'',
                'show_mobile'=>0,
                'collections_show_index'=>1
            ),
            'activity'=>array(
                'swiper_show'=>1,
                'swiper_arg'=>'',
            ),
            //评论顶部显示的一句话
            'hello'=>'生如夏花之绚烂，死如秋叶之静美
        我们听过无数的道理，却仍旧过不好这一生
        每一个不曾起舞的日子，都是对生命的辜负
        因为爱过，所以慈悲；因为懂得，所以宽容
        向来缘浅，奈何情深
        不乱于心，不困于情。不畏将来，不念过往
        早知如此绊人心，何如当初莫相识
        你还不来，我怎敢老去
        要么庸俗，要么孤独
        心之所向，素履以往，生如逆旅，一苇以航
        世界以痛吻我，要我报之以歌
        喜欢就会放肆，但爱就是克制
        人生如逆旅，我亦是行人
        我需要，最狂的风，和最静的海
        你本无意穿堂风，偏偏孤倨引山洪
        笑，全世界便与你同声笑，哭，你便独自哭
        人的一切痛苦，本质上都是对自己的无能的愤怒
        愿你出走半生，归来仍是少年
        在最深的绝望里，遇见最美丽的风景
        惟沉默是最高的轻蔑
        停留是刹那，转身即天涯
        据说那些你一笑就跟着你笑的人，不是傻逼就是爱你的人
        白昼之光，岂知夜色之深
        黑夜无论怎样悠长，白昼总会到来
        这世上所有的不公平都是因为当事人能力的不足
        有些面具戴得太久，就摘不下来了
        谢谢你的微笑 曾经慌乱过我的年华'
        );

        $setting_arr = get_option('zrz_display_setting',$setting_arr);
        return isset($setting_arr[$type]) ? $setting_arr[$type] : '';
    }

    function zrz_custom_name($type){
        $cus_name = zrz_get_display_settings('custom_name');
        switch ($type) {
            case 'labs_name':
                $name = $cus_name['labs_name'];
                break;
            case 'bubble_name':
                $name = $cus_name['bubble_name'];
                break;
            case 'shop_name':
                $name = $cus_name['shop_name'];
                break;
            case 'bubble_default_topic_name':
                $name = $cus_name['bubble_default_topic_name'];
                break;
            default:
                $name = '';
                break;
        }
        return $name;
    }

    function zrz_display_links(){

        $arr = apply_filters('zrz_display_links_filter',array(
            array(
                'show'=>zrz_current_user_can('post'),
                'link'=>esc_url(zrz_get_custom_page_link('write')),
                'text'=>__('写文章','ziranzhi2'),
                'icon'=>'<i class="iconfont zrz-icon-font-write1"></i>'
            ),
            array(
                'show'=>zrz_current_user_can('labs') && zrz_get_display_settings('labs_show') ? true : false,
                'link'=>esc_url(zrz_get_custom_page_link('add-labs')),
                'text'=>sprintf( '发起%1$s',zrz_custom_name('labs_name')),
                'icon'=>'<i class="iconfont zrz-icon-font-shiyan"></i>'
            ),
            array(
                'show'=>zrz_current_user_can('topic') && class_exists( 'bbPress' ) ? true : false,
                'link'=>esc_url(zrz_get_custom_page_link('new-topic')),
                'text'=>__('发起话题','ziranzhi2'),
                'icon'=>'<i class="iconfont zrz-icon-font-tiezi"></i>'
            ),
            array(
                'show'=>zrz_current_user_can('bubble') && zrz_get_display_settings('bubble_show') ? true : false,
                'link'=>esc_url(zrz_get_custom_page_link('bubble')),
                'text'=>sprintf( '发起%1$s',zrz_custom_name('bubble_name')),
                'icon'=>'<i class="iconfont zrz-icon-font-iocnqipaotu"></i>'
            )
        ));

        $html = '';

        foreach ($arr as $key => $val) {
            if($val['show']){
                $html .= '<div class="page-tools-item pos-r allow fd">
                <a href="'.$val['link'].'" class="write-box-in"><span class="write-ico">'.$val['icon'].'</span><span class="write-text">'.$val['text'].'</span></a>
            </div>';
            }
        }

        if($html == ''){
            $html = '<div class="pd20 b-t fs14">您没有权限发布内容，请购买会员或者提升权限。</div>';
        }

        return $html;
    }

    /*
    * 前端风格选择
    */
    add_action('wp_ajax_zrz_set_theme_style_cookie', 'zrz_set_theme_style_cookie');
    add_action('wp_ajax_nopriv_zrz_set_theme_style_cookie', 'zrz_set_theme_style_cookie');
    function zrz_set_theme_style_cookie(){
        if(!isset($_POST['type'])) exit;
        if($_POST['type'] === 'list'){
            zrz_setcookie('theme_style','list',86400);
        }elseif($_POST['type'] === 'pinterest'){
            zrz_setcookie('theme_style','pinterest',86400);
        }else{
            exit;
        }

        print json_encode(array('status'=>200,'msg'=>$_POST['type']));
        exit;
    }

    /*
    * 支付设置
    */
    function zrz_get_pay_settings($type,$key = ''){
        $setting_arr = array(
            'alipay'=>array(
                'open_alipay'=>0,
                'appid'=>'',//支付宝支付应用的appid
                'saPrivateKey'=>'',//商户私钥，填写对应签名算法类型的私钥，默认 RSA2 算法
                'alipayPublicKey'=>''//商户公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
            ),
            'weixin'=>array(
                '_open_weixin'=>0,
                'appid'=>'',//微信小程序应用的appid
                'gz_appid'=>'',//微信公众号的appid
                'mch_id'=>'',//微信商户号
                'mch_key'=>'',//微信支付密钥
                'key_path'=>''//证书存放路径
            ),
            'weixinpay'=>array(
                'open_weixin'=>0,
                'mchid'=>'',
                'key'=>'',
            ),
            'xunhu'=>array(
                'open'=>0,
                'plugins'=>'',
                'appid'=>'',
                'appsecret'=>''
            ),
            'youzan'=>array(
                'open'=>0,
                'client_id'=>'',
                'client_secret'=>'',
                'kdt_id'=>''
            ),
            'card'=>array(
                'open'=>1,
                'html'=>'请前往<a href="xxx.com" target="_blank">xxx.com</a>网站购买卡密，然后回到此处进行充值操作。'
            )
        );

        $setting_arr = get_option('zrz_pay_setting',$setting_arr);
        if($key){
            return isset($setting_arr[$type][$key]) ? $setting_arr[$type][$key] : '';
        }
        return isset($setting_arr[$type]) ? $setting_arr[$type] : array();
    }

    /*
    * 远程下载字体保存到本地
    */
    function zrz_get_theme_fonts($type = false){
        $fonts = array(
            'AgentOrange',
            'Cartoonia_3D',
            'False_3d',
            'From_Cartoon_Blocks',
            'MinginBling',
            'MomsDiner',
            'planet_benson_2',
            'PWHappyChristmas',
            'SourceHanSansCN-Normal'
        );

        if(!file_exists(ZRZ_THEME_DIR.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'SourceHanSansCN-Normal.ttf')){
            if($type) return false;
            $i = 0;
            foreach ($fonts as $val) {
                $i++;
                if($val == 'SourceHanSansCN-Normal'){
                  $success =  file_put_contents(ZRZ_THEME_DIR.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.$val.'.ttf', fopen('https://shijiechao.oss-cn-hangzhou.aliyuncs.com/fonts/'.$val.'.ttf', 'r'));
                    if($success){
                        echo '<p style="color:green;font-size:12px;">头像字体'.$val.'安装成功('.$i.'/9)</p>';
                    }else{
                        echo '<p style="color:red;font-size:12px;">头像字体'.$val.'安装失败</p>';
                    }
                }else{
                    $success = file_put_contents(ZRZ_THEME_DIR.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.$val.'.ttf', fopen('https://ziranzhi.oss-cn-hangzhou.aliyuncs.com/fonts/'.$val.'.ttf', 'r'));
                    if($success){
                        echo '<p style="color:green;font-size:12px;">验证码字体'.$val.'安装成功('.$i.'/9)</p>';
                    }else{
                        echo '<p style="color:red;font-size:12px;">验证码字体'.$val.'安装失败</p>';
                    }
                }
            }

            echo '<p style="color:green;font-size:12px;">全部字体安装成功，请享用。</p>';
            return;
        }else{
            return __('字体已经存在，不用再次安装','ziranzhi2');
        }
        return __('下载失败','ziranzhi2');;
    }
