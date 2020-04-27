<?php
function zrz_options_display_page(){

  if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :
    $options = array(
        'labs_show'=>$_POST['labs_show'],
        'shop_show'=>$_POST['shop_show'],
        'labs_show_index'=>$_POST['labs_show_index'],
        'bubble_show'=>$_POST['bubble_show'],
        'bubble_check'=>$_POST['bubble_check'],
        'activity_show'=>$_POST['activity_show'],
		'shop_style'=>$_POST['shop_style'],
        'shop_show_c'=>$_POST['shop_show_c'],
		'shop_show_g'=>$_POST['shop_show_g'],
        'shop_show_d'=>$_POST['shop_show_d'],
        'swipe_show'=>$_POST['swipe_show'],
        'swipe_style'=>$_POST['swipe_style'],
        'swipe_time'=>$_POST['swipe_time'],
        'show_html5_gg'=>$_POST['show_html5_gg'],
        'sigup_welcome'=>$_POST['sigup_welcome'],
        'delete_msg'=>array(
            'msg_open'=>$_POST['msg_open'],
            'msg_time'=>trim_value($_POST['msg_time'])
        ),
        'custom_name'=>array(
            'labs_name'=>$_POST['labs_name'],
            'bubble_name'=>$_POST['bubble_name'],
            'shop_name'=>$_POST['shop_name'],
            'bubble_default_topic_name'=>$_POST['bubble_default_topic_name']
        ),
        'single'=>array(
            'reaction'=>$_POST['reaction'],//表情投票
            'long_weibo'=>$_POST['long_weibo'],//封面
            'navigation'=>$_POST['navigation'],//上一篇，下一篇
            'ds'=>$_POST['ds']//上一篇，下一篇
        ),
        'hello'=>$_POST['hello'],
        'show_announcement_count'=>trim_value($_POST['show_announcement_count']),
        'announcement_text'=>trim_value($_POST['announcement_text']),
		'go_top'=>array(
			'open'=>$_POST['tool_open'],
			'contect'=>array(
				'id'=>trim_value($_POST['contect_id']),
				'open'=>$_POST['contect_open'],
			),
			'search'=>array(
				'open'=>$_POST['search_open'],
			)
        ),
        'home_bg'=>array(
            'open'=>array($_POST['home_bg_open']),
            'type'=>array($_POST['home_bg_type']),
            'img'=>trim_value($_POST['home_bg_img'])
        ),
        'collections'=>array(
            'text'=>trim_value($_POST['collections_text']),
            'show_mobile'=>$_POST['collections_show_mobile'],
            'collections_show_index'=>$_POST['collections_show_index'],
            'order'=>$_POST['collections_order'],
            'orderby'=>$_POST['collections_orderby'],
        ),
        'activity'=>array(
            'swiper_show'=>$_POST['activity_swiper_show'],
            'swiper_arg'=>trim_value($_POST['activity_swiper_arg']),
        ),
        'mobile_menu'=>array(
            'show'=>$_POST['mobile_menu_footer_show'],
            'show_text'=>$_POST['mobile_menu_footer_text']
        )
    );

    update_option( 'zrz_display_setting',$options );

    $sw = isset($_POST['swipe']) ? $_POST['swipe'] : '';
    $list = array();
    if($sw){
        $row = explode(PHP_EOL, $sw );
        $row = array_filter($row);
        if($row && is_array($row) && !empty($row)){
            foreach ($row as $val) {
                $val = DeleteHtml($val);
                $val = explode( "|", $val );
                if($val){
                    $list[$val[0]] = $val[1];
                }
            }
        }
    }

    //保存幻灯信息
    update_option('zrz_swipe_posts',$list);

    zrz_settings_error('updated');

  endif;

	$option = new zrzOptionsOutput();

	?>
<div class="wrap">

	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('显示设置','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php zrz_admin_tabs('display');?>
		<?php
        $custom_name = zrz_get_display_settings('custom_name');
        $delete_msg_time = zrz_get_display_settings('delete_msg');
		$go_top = zrz_get_display_settings('go_top');
        $home_bg = zrz_get_display_settings('home_bg');
		$contect = $go_top['contect'];
        $search = $go_top['search'];

        $collections = zrz_get_display_settings('collections');

        echo '<h2>移动端底部菜单设置</h2>';
        $mobile_menu = zrz_get_display_settings('mobile_menu');
        $mobile_menu_show = isset($mobile_menu['show']) ? $mobile_menu['show'] : '';
        $mobile_menu_data = isset($mobile_menu['show_text']) ? $mobile_menu['show_text'] : '';

        $option->table( array(
            array(
                'type' => 'select',
                'th' => __( '移动端底部显示什么菜单','ziranzhi2'),
                'key' => 'mobile_menu_footer_show',
                'value' => array(
                    'default' => array((int)$mobile_menu_show),
                    'option' => array(
                        1 => __( '默认菜单', 'ziranzhi2' ),
                        0 => __( '自定义菜单', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'textarea',
                'th' => __('移动端底部自定义菜单设置','ziranzhi2'),
                'after' => '<p>格式为 <code>菜单连接<span style="color:red">|</span>菜单文字<span style="color:red">|</span>菜单图标</code> ，每组占一行，最多支持6个，排序与此设置相同。</p>
                <p>例如：</p><p><code>'.home_url('/labs').'|研究所|&lt;i class=&quot;iconfont zrz-icon-font-xiangguan&quot;&gt;&lt;/i&gt;</code><br>
                <code>'.home_url('/bbs').'|论坛|&lt;i class=&quot;iconfont zrz-icon-font-tiezi&quot;&gt;&lt;/i&gt;</code><br>
                <code>'.home_url('/activity').'|活动|&lt;i class=&quot;iconfont zrz-icon-font-huodong&quot;&gt;&lt;/i&gt;</code><br>
                <code>'.home_url('/bubble').'|冒泡|&lt;i class=&quot;iconfont zrz-icon-font-iocnqipaotu&quot;&gt;&lt;/i&gt;</code><br>
                <code>'.home_url('/shop').'|店铺|&lt;i class=&quot;iconfont zrz-icon-font-2&quot;&gt;&lt;/i&gt;</code><br>
                </p>',
                'key' => 'mobile_menu_footer_text',
                'value' => zrz_get_html_code($mobile_menu_data)
            ),
        ));


        echo '<h2>活动模块设置</h2>';
        $activity = zrz_get_display_settings('activity');
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __( '是否启用活动模块','ziranzhi2'),
                'key' => 'activity_show',
                'value' => array(
                    'default' => array((int)zrz_get_display_settings('activity_show')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __( '是否使用活动模块的幻灯','ziranzhi2'),
                'key' => 'activity_swiper_show',
                'value' => array(
                    'default' => array(isset($activity['swiper_show']) ? $activity['swiper_show'] : 1),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'textarea',
                'th' => __('活动模块幻灯设置','ziranzhi2'),
                'after' => '<p>格式为 <code>活动的文章ID<span style="color:red">|</span>封面图片地址</code> ，活动的文章ID和所对应的封面图片用<span style="color:red">|</span>隔开，如果不设置封面，请在图片网址的地方写0，例如<code>1223|0</code>，每组占一行，最多支持6个，排序与此设置相同。图片请在 <a target="_blank" href="'.home_url('/wp-admin/media-new.php').'">媒体中心</a> 上传。</p><p>例如：<p><code>123|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>456|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>3434|0</code><br>
                <code>2344|0</code><br>
                <code>2334|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>5556|https://xxx.com/wp-content/uploads/xxx.jpg</code></p>',
                'key' => 'activity_swiper_arg',
                'value' => isset($activity['swiper_arg']) ? $activity['swiper_arg'] : ''
            ),
        ));

        echo '<h2>研究所设置</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => sprintf( '是否启用%1$s?',$custom_name['labs_name']),
                'after' => '<p>'.sprintf( '顶部菜单的%1$s项，请在菜单设置中手动更新',$custom_name['labs_name']).'</p>',
                'key' => 'labs_show',
                'value' => array(
                    'default' => array(zrz_get_display_settings('labs_show')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => sprintf( '自定义%1$s的名称',$custom_name['labs_name']),
                'after' => '<p>'.sprintf( '网站中显示的%1$s名称',$custom_name['labs_name']).'</p>',
                'key' => 'labs_name',
                'value' => $custom_name['labs_name']
            ),
            array(
                'type' => 'select',
                'th' => sprintf( '首页文章列表中是否显示%1$s?',$custom_name['labs_name']),
                'after' => '<p>'.sprintf( '如果开启了%1$s项，并且不想让它在首页显示，请选择关闭',$custom_name['labs_name']).'</p>',
                'key' => 'labs_show_index',
                'value' => array(
                    'default' => array(zrz_get_display_settings('labs_show_index')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<h2>商城设置</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => sprintf( '是否启用%1$s?',$custom_name['shop_name']),
                'after' => '<p>'.sprintf( '顶部菜单的%1$s项，请在菜单设置中手动更新',$custom_name['shop_name']).'</p>',
                'key' => 'shop_show',
                'value' => array(
                    'default' => array(zrz_get_display_settings('shop_show')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => sprintf( '自定义%1$s的名称',$custom_name['shop_name']),
                'after' => '<p>'.sprintf( '网站中显示的%1$s名称',$custom_name['shop_name']).'</p>',
                'key' => 'shop_name',
                'value' => $custom_name['shop_name']
            ),
            array(
                'type' => 'select',
                'th' => __('商城的显示模式为？','ziranzhi2'),
                'key' => 'shop_style',
                'value' => array(
                    'default' => array(zrz_get_display_settings('shop_style')),
                    'option' => array(
                        1 => __( '网格', 'ziranzhi2' ),
                        0 => __( '列表', 'ziranzhi2' ),
                    )
                )
            ),
			array(
                'type' => 'select',
                'th' => __('启用商品购买功能吗？','ziranzhi2'),
                'key' => 'shop_show_g',
                'value' => array(
                    'default' => array(zrz_get_display_settings('shop_show_g')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
			array(
                'type' => 'select',
                'th' => __('启用抽奖功能吗？','ziranzhi2'),
                'key' => 'shop_show_c',
                'value' => array(
                    'default' => array(zrz_get_display_settings('shop_show_c')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
			array(
                'type' => 'select',
                'th' => __('启用积分兑换功能吗？','ziranzhi2'),
                'key' => 'shop_show_d',
                'value' => array(
                    'default' => array(zrz_get_display_settings('shop_show_d')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<h2>冒泡设置</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => sprintf( '是否启用%1$s?',$custom_name['bubble_name']),
                'after' => '<p>'.sprintf( '顶部菜单的%1$s项，请在菜单设置中手动更新',$custom_name['bubble_name']).'</p>',
                'key' => 'bubble_show',
                'value' => array(
                    'default' => array(zrz_get_display_settings('bubble_show')),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => sprintf( '普通用户发布%1$s是否需要审核?',$custom_name['bubble_name']),
                'key' => 'bubble_check',
                'value' => array(
                    'default' => array(zrz_get_display_settings('bubble_check')),
                    'option' => array(
                        1 => __( '审核', 'ziranzhi2' ),
                        0 => __( '不审核', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => sprintf( '自定义%1$s的名称',$custom_name['bubble_name']),
                'after' => '<p>'.sprintf( '网站中显示的%1$s名称',$custom_name['bubble_name']).'</p>',
                'key' => 'bubble_name',
                'value' => $custom_name['bubble_name']
            ),
            array(
                'type' => 'input',
                'th' => __('冒泡默认的话题','ziranzhi2'),
                'after' => '<p>'.__('用户未创建，未选择话题的前提下默认显示的话题名称。默认设置为<code>广场</code>','ziranzhi2').'</p>',
                'key' => 'bubble_default_topic_name',
                'value' => $custom_name['bubble_default_topic_name']
            ),
        ));
        echo '<h2>首页顶部背景图片</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('启用首页顶部背景图片？','ziranzhi2'),
                'key' => 'home_bg_open',
                'value' => array(
                    'default' => isset($home_bg['open']) ? $home_bg['open'] : array(0),
                    'option' => array(
                        1 => __( '显示', 'ziranzhi2' ),
                        0 => __( '隐藏', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('显示模糊效果还是清晰效果？','ziranzhi2'),
                'key' => 'home_bg_type',
                'value' => array(
                    'default' =>isset($home_bg['type']) ? $home_bg['type'] : array(0),
                    'option' => array(
                        1 => __( '清晰', 'ziranzhi2' ),
                        0 => __( '模糊', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('背景图片地址','ziranzhi2'),
                'after' => '<p>'.__('请先将背景图片上传到服务器中（<a target="_blank" href="'.home_url('/wp-admin/media-new.php').'">媒体中心</a>），再将图片地址填到此处。','ziranzhi2').'</p>',
                'key' => 'home_bg_img',
                'value' => isset($home_bg['img']) ? $home_bg['img'] : ''
            ),
        ));
        echo '<h2>首页幻灯设置</h2>';
        $sw_arr = get_option('zrz_swipe_posts');
        $sw_str = '';
        if(is_array($sw_arr) && !empty($sw_arr)){
            $count = count($sw_arr);
            $i = 0;
            foreach ($sw_arr as $key=>$val) {
                $i++;
                if($i >= $count){
                    $sw_str .= $key.'|'.($val ? $val : 0);
                }else{
                    $sw_str .= $key.'|'.($val ? $val : 0).PHP_EOL;
                }
            }
        }
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('首页幻灯样式','ziranzhi2'),
                'key' => 'swipe_style',
                'value' => array(
                    'default' =>array(zrz_get_display_settings('swipe_style')),
                    'option' => array(
                        0 => __( '不显示幻灯', 'ziranzhi2' ),
                        1 => __( '通栏大幻灯', 'ziranzhi2' ),
                        2=>__( '小幻灯', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('幻灯切换速度','ziranzhi2'),
                'after' => '<p>默认4000毫秒，也就是4秒，1秒等于1000毫秒</p>',
                'key' => 'swipe_time',
                'value' => zrz_get_display_settings('swipe_time') ?: 4000 
            ),
            array(
                'type' => 'textarea',
                'th' => __('请输入要当作幻灯的文章ID和缩略图','ziranzhi2'),
                'after' => '<p>格式为 <code>文章ID<span style="color:red">|</span>封面图片地址</code> ，文章ID和所对应的封面图片用<span style="color:red">|</span>隔开，如果不设置封面，请在图片网址的地方写0，例如<code>1223|0</code>，每组占一行，排序与此设置相同。图片可以在 <a target="_blank" href="'.home_url('/wp-admin/media-new.php').'">媒体中心</a> 上传。</p><p>例如：<p><code>123|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>456|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>3434|0</code><br>
                <code>2344|0</code><br>
                <code>2334|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>5556|https://xxx.com/wp-content/uploads/xxx.jpg</code></p>',
                'key' => 'swipe',
                'value' => $sw_str
            ),
            array(
                'type' => 'select',
                'th' => __('幻灯是否显示标题？','ziranzhi2'),
                'key' => 'swipe_show',
                'value' => array(
                    'default' =>array(zrz_get_display_settings('swipe_show')),
                    'option' => array(
                        1 => __( '显示', 'ziranzhi2' ),
                        0 => __( '隐藏', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<h2>专题设置</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('首页是否显示热门专题列表','ziranzhi2'),
                'after' => '<p>'.__('如果关闭，首页将不再显示专题。','ziranzhi2').'</p>',
                'key' => 'collections_show_index',
                'value' => array(
                    'default' => isset($collections['collections_show_index']) ? array($collections['collections_show_index']) : array(0),
                    'option' => array(
                        1 => __( '显示', 'ziranzhi2' ),
                        0 => __( '关闭', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'textarea',
                'th' => __('请输入首页要显示的专题（留空则系统自动生成）','ziranzhi2'),
                'after' => '<p>格式为 <code>专题ID<span style="color:red">|</span>封面图片地址</code> ，专题ID和所对应的封面图片用<span style="color:red">|</span>隔开，每组占一行，最多支持6个，排序与此设置相同。图片请在 <a target="_blank" href="'.home_url('/wp-admin/media-new.php').'">媒体中心</a> 上传。</p><p>例如：<p><code>123|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>456|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>3434|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>2344|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>2334|https://xxx.com/wp-content/uploads/xxx.jpg</code><br>
                <code>5556|https://xxx.com/wp-content/uploads/xxx.jpg</code></p>',
                'key' => 'collections_text',
                'value' => isset($collections['text']) ? $collections['text'] : ''
            ),
            array(
                'type' => 'select',
                'th' => __('移动端首页是否始终显示专题？','ziranzhi2'),
                'after' => '<p>'.__('选择是，首页移动端则始终展示专题，选择否，首页移动端将默认隐藏专题，点击才可展开。','ziranzhi2').'</p>',
                'key' => 'collections_show_mobile',
                'value' => array(
                    'default' => isset($collections['show_mobile']) ? array($collections['show_mobile']) : array(0),
                    'option' => array(
                        1 => __( '是', 'ziranzhi2' ),
                        0 => __( '否', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('专题页面排序类型','ziranzhi2'),
                'after' => '<p>'.__('请选择专题页面的排序类型（降序或者升序）。','ziranzhi2').'</p>',
                'key' => 'collections_order',
                'value' => array(
                    'default' => isset($collections['order']) ? array($collections['order']) : array('DESC'),
                    'option' => array(
                        'DESC' => __( '降序', 'ziranzhi2' ),
                        'ASC' => __( '升序', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('专题页面排序方法','ziranzhi2'),
                'after' => '<p>'.__('请选择专题页面的排序方法（数量，时间等）。','ziranzhi2').'</p>',
                'key' => 'collections_orderby',
                'value' => array(
                    'default' => isset($collections['orderby']) ? array($collections['orderby']) : array('count'),
                    'option' => array(
                        'id' => __( '专题发布时间', 'ziranzhi2' ),
                        'count' => __( '专题包含文章数量', 'ziranzhi2' ),
                        'term_id' => __( '专题的ID', 'ziranzhi2' ),
                        'slug' => __( '专题的别名', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<h2>顶部工具条</h2>';
        $option->table( array(
            array(
                'type' => 'input',
                'th' => __('顶部公告显示几条？','ziranzhi2'),
                'after' => '<p>'.__('默认显示1条最新公告，如果默认不显示公告，请填0','ziranzhi2').'</p>',
                'key' => 'show_announcement_count',
                'value' => zrz_get_display_settings('show_announcement_count')
            ),
            array(
                'type' => 'select',
                'th' => __('新的公告是否通过浏览器的html5通知给用户？','ziranzhi2'),
                'key' => 'show_html5_gg',
                'value' => array(
                    'default' => array(zrz_get_display_settings('show_html5_gg') ?: 0),
                    'option' => array(
                        0 => __( '关闭html5通知', 'ziranzhi2' ),
                        1 => __( '开启html5通知', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('顶部公告栏右侧文字','ziranzhi2'),
                'after' => '<p>'.__('如果顶部公告跳右边没有设置菜单，可以在这里输入你想要显示的文字','ziranzhi2').'</p>',
                'key' => 'announcement_text',
                'value' => zrz_get_display_settings('announcement_text')
            )
        ));
        echo '<h2>消息设置</h2>';
        $option->table( array(
            array(
                'type' => 'textarea',
                'th' => __('欢迎信息','ziranzhi2'),
                'after' => '<p>'.__('用户初次注册成功后消息列表中显示的欢迎信息。','ziranzhi2').'</p>',
                'key' => 'sigup_welcome',
                'value' => zrz_get_display_settings('sigup_welcome')
            ),
            array(
                'type' => 'select',
                'th' => __('是否自动删除过期消息','ziranzhi2'),
                'after' => '<p>'.__('包括消息提示和积分明细。私信不会被删除。未读的消息不会被删除。如果消息比较重要，请定期备份数据库中 zrz_message 表，删除后无法恢复。','ziranzhi2').'</p>',
                'key' => 'msg_open',
                'value' => array(
                    'default' => array($delete_msg_time['msg_open']),
                    'option' => array(
                        1 => __( '删除', 'ziranzhi2' ),
                        0 => __( '保留', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'input',
                'th' => __('消息过期时间','ziranzhi2'),
                'after' => '<p>'.__('默认尺寸<code>90</code>天，如果保留消息，此项不会生效。','ziranzhi2').'</p>',
                'key' => 'msg_time',
                'value' => $delete_msg_time['msg_time']
            ),
        ));
		echo '<h2>页面右下角工具条</h2>';
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('是否启用页面右下角工具条','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'tool_open',
                'value' => array(
                    'default' => array($go_top['open']),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
			array(
                'type' => 'select',
                'th' => __('是否启用联系功能','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'contect_open',
                'value' => array(
                    'default' => array($contect['open']),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
			array(
                'type' => 'input',
                'th' => sprintf( '默认的联系人',$custom_name['shop_name']),
                'after' => '<p>'.__( '一般为管理员ID，默认为1，可以设置其他用户。','ziranzhi2').'</p>',
                'key' => 'contect_id',
                'value' => $contect['id']
            ),
			array(
                'type' => 'select',
                'th' => __('是否启用搜索功能','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'search_open',
                'value' => array(
                    'default' => array($search['open']),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<h2>文章内页设置</h2>';
        $single = zrz_get_display_settings('single');
        $option->table( array(
            array(
                'type' => 'select',
                'th' => __('是否显示文章表情投票功能？','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'reaction',
                'value' => array(
                    'default' => array(isset($single['reaction']) ? $single['reaction'] : 1),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('是否启用海报功能？','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'long_weibo',
                'value' => array(
                    'default' => array(isset($single['long_weibo']) ? $single['long_weibo'] : 1),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('是否启用上一篇，下一篇的导航','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'navigation',
                'value' => array(
                    'default' => array(isset($single['navigation']) ? $single['navigation'] : 1),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
            array(
                'type' => 'select',
                'th' => __('是否显示打赏按钮','ziranzhi2'),
                'after' => '<p>'.__('默认开启','ziranzhi2').'</p>',
                'key' => 'ds',
                'value' => array(
                    'default' => array(isset($single['ds']) ? $single['ds'] : 1),
                    'option' => array(
                        1 => __( '启用', 'ziranzhi2' ),
                        0 => __( '禁用', 'ziranzhi2' ),
                    )
                )
            ),
        ));
        echo '<h2>随机名言名句</h2>';
        echo '<style>#hello{height:400px}</style>';
        $option->table( array(
            array(
                'type' => 'textarea',
                'th' => __('随机名言名句','ziranzhi2'),
                'after' => '<p>'.__('纯文本，每句话占一行','ziranzhi2').'</p>',
                'key' => 'hello',
                'value' => zrz_get_display_settings('hello')
            )
        ))
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
	<?php
}
