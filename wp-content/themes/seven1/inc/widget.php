<?php
//用户面板
class Zrz_User_Box extends WP_Widget {
    public function __construct() {
        parent::__construct(
           'user_box',
           __('(7b2)用户面板 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '用户面板', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
           'show_follow' => '0',
           'show_fans'=>'0',
           'new_user'=>__('欢迎您新朋友！','ziranzhi2'),
           'old_user'=>__('欢迎您再回来！','ziranzhi2'),
           'mobile_hide'=>'0',
           'fixed'=>'0'
        );
        $new_user = isset($instance[ 'new_user' ]) ? $instance[ 'new_user' ] : $default['new_user'];
        $old_user = isset($instance[ 'old_user' ]) ? $instance[ 'old_user' ] : $default['old_user'];
       $show_follow = isset($instance[ 'show_follow' ]) ? $instance[ 'show_follow' ] : $default['show_follow'];
       $show_fans = isset($instance[ 'show_fans' ]) ? $instance[ 'show_fans' ] : $default['show_fans'];
       $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default['mobile_hide'];
       $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'new_user' ); ?>">新用户提示文字：</label>
        <textarea class="widefat" type="text" id="<?php echo $this->get_field_id( 'new_user' ); ?>" rows="4" name="<?php echo $this->get_field_name( 'new_user' ); ?>"><?php echo $new_user; ?></textarea>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'old_user' ); ?>">老用户提示文字：</label>
        <textarea class="widefat" type="text" id="<?php echo $this->get_field_id( 'old_user' ); ?>" rows="4" name="<?php echo $this->get_field_name( 'old_user' ); ?>"><?php echo $old_user; ?></textarea>
    </p>
   <p>
       <label>移动端显示吗？</label>
       <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
           <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
           <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
       </select>
   </p>
   <p>
       <label>是否浮动？</label>
       <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
           <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
           <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
       </select>
   </p>
   <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'new_user' ] = $new_instance[ 'new_user' ];
        $instance[ 'old_user' ] = $new_instance[ 'old_user' ];
        $instance[ 'show_follow' ] = strip_tags( $new_instance[ 'show_follow' ] );
        $instance[ 'show_fans' ] = strip_tags( $new_instance[ 'show_fans' ] );
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }
        $c_user_id = get_current_user_id();
        $user_id = $c_user_id;
        if(is_singular()){
            global $post;
            $user_id = $post->post_author;
        }
        $user_data = get_userdata($user_id);
        $comment = wp_get_current_commenter();

        $task = new ZRZ_TASK($user_id);
        $pre = $task->task_finish();

        //如果是文章内页或者已经登录，显示作者面板
        if(is_singular() || is_user_logged_in()){
            $fans = get_user_meta($user_id,'zrz_followed',true);
            $fans = !empty($fans) ? count($fans) : 0;
            $follow = get_user_meta($user_id,'zrz_follow',true);
            $follow = !empty($follow) ? count($follow) : 0;
            $cover = get_user_meta($user_id,'zrz_open',true);
            $cover = isset($cover['cover']) && !empty($cover['cover']) ? $cover['cover'] : '';
            if($cover){
                $width = (int)zrz_get_theme_settings('page_width');
                $width = ceil($width*0.25249-10);
                $cover_uri =  zrz_get_thumb(zrz_get_media_path().'/'.$cover['key'],$width,100);
            }
            unset($width);

    ?>
			<!-- 登录用户面板 -->
			<div class="widget_user box">
                <div class="widget-cover <?php echo $cover ? '' : 'widget-cover-over'; ?>">
                    <?php if($cover){ ?>
                        <div class="widget-cover-bg img-bg" style="background-image:url(<?php echo $cover_uri; ?>);">
                        </div>
                    <?php }else{ ?>
                        <div class="widget-cover-bg img-bg blur" style="background-image:url(<?php echo zrz_get_avatar($user_id,20,60); ?>);background-color:<?php echo zrz_get_avatar_background_by_id($user_id); ?>;margin:-10px">
                        </div>
                    <?php } ?>
                </div>
				<div class="pd10 lin">
                    <div calss="clearfix">
                        <div class="widget-author-avatar">
        					<?php echo get_avatar($user_id, '50'); ?>
                            <p class="widget-author fs12 t-c"><?php if(is_singular()){ echo '作者';}else{echo '本人';} ?></p>
                        </div>
    					<div class="widget-user-info pos-r">
    						<div class="widget-user-name mar5-b"><?php echo zrz_get_user_page_link($user_id).zrz_get_lv($user_id,'name'); ?></div>
    						<p class="gray fs12">第 <?php echo $user_id; ?> 号会员，<a href="<?php echo home_url('/hot'); ?>"><?php echo (int)get_user_meta($user_id,'zrz_hot' ,true); ?> 活跃度</a></p>
    					</div>
                    </div>
					<div class="widget-user-total mar10-b b-t pd10-t mar20-t">
						<a href="<?php echo zrz_get_user_page_url($user_id,'posts'); ?>" class="fd">
							<span><?php echo count_user_posts($user_id, 'post' ); ?></span>
							<p class="gray">文章</p>
						</a><a href="#" class="fd">
							<span><?php echo zrz_commentCount($user_id); ?></span>
							<p class="gray">评论</p>
						</a><a href="<?php echo zrz_get_user_page_url($user_id,'follow'); ?>" class="fd">
							<span><?php echo $follow; ?></span>
							<p class="gray">关注</p>
						</a><a href="<?php echo zrz_get_user_page_url($user_id,'fans'); ?>" class="fd">
    							<span><?php echo $fans; ?></span>
    							<p class="gray">粉丝</p>
    					</a>
					</div>
						<p class="widget_user-des pd10 bg-b fs12"><?php echo $user_data->description ? mb_strimwidth(strip_tags($user_data->description),0, 100 ,"...") : '没有个人简介'; ?></p>
				</div>
					<?php
                        if(((is_user_logged_in() && $c_user_id == $user_id) || current_user_can('delete_users')) && $pre){
                            echo ($c_user_id == $user_id ? '<div class="bg-blue-light pd10 widget-title gray t-c pos-r"><a class="link-block" href="'.home_url('/task').'"><span class="task-100" style="width:'.$pre.'"></span><b>已经完成了今天任务的'.$pre.'</b></a></div>' : '<div class="bg-blue-light pd10 gray pos-r">财富</div>').
                    		'<div class="pd10 clearfix">
                                <div class="fl"><a href="'.home_url('/gold?uid='.$user_id).'">'.zrz_get_rmb($user_id).'</a></div><div class="fr"><a href="'.home_url('/gold?uid='.$user_id).'">'.zrz_coin($user_id).'</a></div>
                            </div>';
                        }
                    ?>
			</div>
            <?php if(is_user_logged_in()){ ?>
                <div class="box pd10 mission pos-r mar10-b mar10-t l1" id="mission">
                    <a href="#" @click.stop.prevent="mission()"><i class="iconfont zrz-icon-font-liwu red"></i><span class="text mar5-l" v-html="Mtext">&nbsp;</span></a>
                </div>
            <?php }else{ ?>
                <div class="mar16-b"></div>
            <?php } ?>
		<?php }

        //如果没有登录，显示欢迎面板
        if(!is_user_logged_in()){
            if(!empty($comment['comment_author_email'])){ ?>
    			<!-- 回头访客录用户面板 -->
    			<div class="widget_guest clearfix box mar10-b">
    				<div class="pd10">
    					<p class="fs14 mar10-b clearfix gray">Hi! <?php echo $comment['comment_author']; ?></p>
    					<p class="pd10-t pd10-b"><?php echo $instance[ 'old_user' ]; ?></p>
    				</div>
                    <div class="bg-blue-light pd10 widget-title gray b-t b-b"><span class="gray fs12">您在本站有<?php echo zrz_comment_count($comment['comment_author_email'],true); ?>条评论</span></div>
                    <div class="pd10">
                        <a href="javascript:void(0)" onclick="openWin('<?php echo weixin_oauth_url(); ?>','weixin','500','500')" class="fs12 opwx pos-r">微信登录</a>
                        <a href="javascript:void(0)" onclick="openWin('<?php echo weibo_oauth_url(); ?>','weibo','500','500')" class="fs12 opwb pos-r">微博登录</a>
                        <a href="javascript:void(0)" onclick="openWin('<?php echo qq_oauth_url(); ?>','qq','500','500')" class="fs12 opqq pos-r">QQ登录</a>
                    </div>
    			</div>
    		<?php }else{ ?>
    			<!-- 新访客录用户面板 -->
    			<div class="widget_guest clearfix box mar10-b">
    				<div class="pd10">
    					<p class="fs14 mar10-b gray">嗨! 新朋友</p>
    					<p class="pd10-t pd10-b"><?php echo $instance[ 'new_user' ]; ?></p>
    				</div>
                    <div class="bg-blue-light pd10 widget-title gray b-t b-b">免注册登录</div>
                    <div class="pd10">
                        <?php if(zrz_get_social_settings('open_weixin')){ ?>
                            <a href="javascript:void(0)" onclick="openWin('<?php echo weixin_oauth_url(); ?>','weixin','500','500')" class="fs12 opwx pos-r">微信登录</a>
                        <?php } ?>
                        <?php if(zrz_get_social_settings('open_weibo')){ ?>
                            <a href="javascript:void(0)" onclick="openWin('<?php echo weibo_oauth_url(); ?>','weibo','500','500')" class="fs12 opwb pos-r">微博登录</a>
                        <?php } ?>
                        <?php if(zrz_get_social_settings('open_qq')){ ?>
                            <a href="javascript:void(0)" onclick="openWin('<?php echo qq_oauth_url(); ?>','qq','500','500')" class="fs12 opqq pos-r">QQ登录</a>
                        <?php } ?>
                    </div>
    			</div>
    		<?php }
        }

        if($fixed){
            echo '</div>';
        }
        echo $after_widget;
    }

}

//积分排行
class Zrz_Hot_Credit extends WP_Widget {
    public function __construct() {
        parent::__construct(
           'hot_credit',
           __('(7b2)财富排行 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '侧边栏显示财富排行，通用！', 'zrz' )
           )

       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('财富排行','ziranzhi2'),
            'top_user_nub' => '5',
            'top_exclude_users'=>'',
            'mobile_hide'=>0,
            'fixed'=>0
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
        $top_user_nub = isset($instance[ 'top_user_nub' ]) ? $instance[ 'top_user_nub' ] : $default['top_user_nub'];
        $top_exclude_users = isset($instance[ 'top_exclude_users' ]) ? $instance[ 'top_exclude_users' ] : $default['top_exclude_users'];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default['mobile_hide'];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
   <p>
       <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
       <label for="<?php echo $this->get_field_id( 'top_user_nub' ); ?>">最多显示多少人：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'top_user_nub' ); ?>" name="<?php echo $this->get_field_name( 'top_user_nub' ); ?>" value="<?php echo esc_attr( $top_user_nub ); ?>">
    </p>
    <p>
       <label for="<?php echo $this->get_field_id( 'top_exclude_users' ); ?>">排行中排除哪些人：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'top_exclude_users' ); ?>" name="<?php echo $this->get_field_name( 'top_exclude_users' ); ?>" value="<?php echo $top_exclude_users; ?>">
       <div>填写要排除的用户ID，用英文的逗号 <span style="color:red;font-size:16px">,</span> 隔开</div>
   </p>
   <p>
       <label>移动端显示吗？</label>
       <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
           <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
           <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
       </select>
   </p>
   <p>
       <label>是否浮动？</label>
       <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
           <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
           <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
       </select>
   </p>
   <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'top_user_nub' ] = strip_tags( $new_instance[ 'top_user_nub' ] );
        $instance[ 'top_exclude_users' ] = strip_tags( $new_instance[ 'top_exclude_users' ] );
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }

        echo $before_title .$instance[ 'title' ].'<span class="fr"><a href="'.zrz_get_custom_page_link( 'top' ).'">ALL ❯</a></span>'.$after_title;
        $exclude = $instance[ 'top_exclude_users' ];
        $exclude = explode(',',$exclude);
        $user_html = '<ul class="box">';
        $args = array(
            'meta_key' => 'zrz_credit_total',
            'orderby' => 'meta_value_num',
            'order'	 => 'DESC',
            'number' => $instance[ 'top_user_nub' ],
            'exclude' =>$exclude

        );
        $users = get_users($args);
        if(!empty($users)){
            foreach ($users as $user) {

                $user_html .= '
                        <li class="pos-r fs12 clearfix">
                            <a class="hot_credit_ava t-c" href="'.zrz_get_user_page_url($user->ID).'">'.get_avatar($user->ID,50).'<br>'.zrz_get_lv($user->ID,'lv').'</a>
                            <div class="hot_credit_r">
                                '.zrz_get_user_page_link((int)$user->ID).'
                                <p class="gray mar5-t l1">'.count_user_posts($user->ID, 'post' ).' 文章 • '.zrz_comment_count($user->ID,false).' 评论</p>
                            </div>
                            <div class="pos-a hot_credit_nub">'.zrz_coin($user->ID,'nub').'</div>
                        </li>
                ';
            }
        }else{
            $user_html .='<div class="pd10 t-c gray">没有财富数据</div>';
        }
        $user_html .='</ul>';
        echo $user_html;
        unset($user_html);
        if($fixed){
            echo '</div>';
        }
        echo $after_widget;
    }

}

//热门文章
class Zrz_Hot_Posts extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'hot_post',
           __('(7b2)近期热门文章 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '近期热门文章！', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('热门文章','ziranzhi2'),
            'hot_nub' => '5',
            'hot_days_after'=>'30',
            'mobile_hide'=>'0',
            'fixed'=>'0'
       );
       $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
       $hot_nub = isset($instance[ 'hot_nub' ]) ? $instance[ 'hot_nub' ] : $default[ 'hot_nub' ];
       $hot_days_after = isset($instance[ 'hot_days_after' ]) ? $instance[ 'hot_days_after' ] : $default[ 'hot_days_after' ];
       $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
       $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
   <p>
       <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
   </p>
   <p>
       <label for="<?php echo $this->get_field_id( 'hot_nub' ); ?>">一共显示多少篇：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_nub' ); ?>" name="<?php echo $this->get_field_name( 'hot_nub' ); ?>" value="<?php echo esc_attr( $hot_nub ); ?>">
   </p>
   <p>
       <label for="<?php echo $this->get_field_id( 'hot_days_after' ); ?>">几天之内：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_days_after' ); ?>" name="<?php echo $this->get_field_name( 'hot_days_after' ); ?>" value="<?php echo $hot_days_after; ?>">
   </p>
   <p>
       <label>移动端显示吗？</label>
       <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
           <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
           <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
       </select>
   </p>
   <p>
       <label>是否浮动？</label>
       <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
           <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
           <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
       </select>
   </p>
   <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'hot_nub' ] = strip_tags( $new_instance[ 'hot_nub' ] );
        $instance[ 'hot_days_after' ] = strip_tags( $new_instance[ 'hot_days_after' ] );
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }
        echo $before_title.$instance[ 'title' ].$after_title;
        //排除置顶文章
        $sticky = get_option( 'sticky_posts' ,'');
        $html = '<ul class="box">';
        $args = array(
            'date_query' => array(
                         array(
                   'after'     => date('Y-m-d',strtotime("-".$instance[ 'hot_days_after' ]." days")),//7天的时间
                   'inclusive' => true,
                   )),
            'meta_key' =>'views',
            'orderby' => 'meta_value_num',
            'order'	 => 'DESC',
            'post__not_in'=>$sticky,
            'posts_per_page'  => $instance[ 'hot_nub' ],
            'paged'	=> '1',
            'post_type'=>'post'
        );
        $the_query = new WP_Query($args);
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();
            $thumb = zrz_get_post_thumb();
            if($thumb){
                $thumb = '<img class="pos-a" src="'.zrz_get_thumb(zrz_get_post_thumb(),120,90).'">';
            }

            $html .= '<li class="pos-r">
                    <a href="'.get_permalink().'" rel="bookmark">
                    '.$thumb.'<h2 class="'.($thumb ? '' : 'mar0').'">'.get_the_title().'</h2></a></li>';
             endwhile;
            wp_reset_postdata();
         else :
            $html .= '<li class="t-c gray">没有热门文章</li>';
         endif;
         echo $html.'</ul>';
         unset($html);
         if($fixed){
             echo '</div>';
         }
         echo $after_widget;
    }
}

//广告专用小工具
class Zrz_Index_Add extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'index_add',
           __('(7b2)广告小工具 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '首页广告位', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('广告小工具','ziranzhi2'),
            'add'=>'',
            'mobile_hide'=>'0',
            'fixed'=>'0'
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
        $add = isset($instance[ 'add' ]) ? $instance[ 'add' ] : $default[ 'add' ];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题（可为空）：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'add' ); ?>">请将广告的 html 代码填写到此处</label>
        <textarea class="widefat" type="text" id="<?php echo $this->get_field_id( 'add' ); ?>" rows="8" name="<?php echo $this->get_field_name( 'add' ); ?>"><?php echo $add; ?></textarea>
    </p>
    <p>
        <label>移动端显示吗？</label>
        <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
            <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
            <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
        </select>
    </p>
    <p>
        <label>是否浮动？</label>
        <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
            <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
            <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
        </select>
    </p>
    <?php
}
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'add' ] = $new_instance[ 'add' ];
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }
        if($instance[ 'title' ]){
            echo $before_title.$instance[ 'title' ].$after_title;
        }
        echo '<div class="index-add b-t pd10">'.$instance[ 'add' ].'</div>';
        if($fixed){
            echo '</div>';
        }
        echo $after_widget;
    }
}

//随机文章
class Zrz_Rand_Posts extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'rand_posts',
           __('(7b2)随机文章 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '随机文章！', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('随机文章','ziranzhi2'),
            'mobile_hide'=>0,
            'height'=>180,
            'fixed'=>0
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
        $height = isset($instance[ 'height' ]) ? $instance[ 'height' ] : $default['height'];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
        <label>缩略图高度：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr( $height ); ?>">
    </p>
    <p>
        <label>移动端显示吗？</label>
        <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
            <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
            <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
        </select>
    </p>
    <p>
        <label>是否浮动？</label>
        <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
            <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
            <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
        </select>
    </p>
    <?php
}
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'height' ] = $new_instance[ 'height' ];
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }

        echo $before_title.$instance[ 'title' ].$after_title;
        $html = '<div class="rand-post box">';
        $query = new WP_Query('posts_per_page=1&ignore_sticky_posts=1&orderby=rand');
        if ( $query->have_posts() ) {
           while ( $query->have_posts() ) {
               $query->the_post();
                $thumb = zrz_get_post_thumb() ? '<img src="'.zrz_get_thumb(zrz_get_post_thumb(),270,$instance[ 'height' ]).'" class=" rand-post-img" />' : '';
                $html .= '<a href="'.get_the_permalink().'">'.$thumb.'<h2 class="pd15">'.get_the_title().'</h2></a>';
           }
           wp_reset_postdata();
        }else{
           $html .= '<div class="pd15 gray">没有随机文章</div>';
        }

        echo $html;
        unset($html);
        if($fixed){
            echo '</div></div>';
        }
        echo $after_widget;
    }
}

//个人成就
class Zrz_User_Achievement extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'user_achievement',
           __('(7b2)个人成就 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '推荐在个人主页中使用', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('个人成就','ziranzhi2'),
            'mobile_hide'=>0,
            'fixed'=>0
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
        <label>移动端显示吗？</label>
        <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
            <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
            <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
        </select>
    </p>
    <p>
        <label>是否浮动？</label>
        <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
            <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
            <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
        </select>
    </p>
    <?php
}
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';

        $current_user = get_current_user_id();
        $self = true;
        $user_id = is_user_logged_in() ? $current_user : 0;
        if(is_author()){
            $user_id = get_query_var('author');
            if($user_id != $current_user){
                $self = false;
            }
        }

        if(!$user_id) return;

        $user_data = get_userdata($user_id);

        $fans = get_user_meta($user_id,'zrz_followed',true);
        $fans = !empty($fans) ? count($fans) : 0;
        $follow = get_user_meta($user_id,'zrz_follow',true);
        $follow = !empty($follow) ? count($follow) : 0;

        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }

        echo $before_title.$instance[ 'title' ].$after_title;

        echo '<div class="box fs12 t-c">
        <p class="pd10">'.($self ? '您' : esc_attr($user_data->display_name)).'已来到这里 <b>'.zrz_time_days($user_data->user_registered).'</b> 天</p>
        <p class="mar10-b">发表了 <b>'.count_user_posts($user_id, 'post' ).'</b> 篇文章，<b>'.zrz_comment_count($user_id,false).'</b> 条评论</p>
        <div class="side-user-follow pd10 b-t">
            <a class="fd" href="'.zrz_get_user_page_url($user_id).'/follow'.'">
                <b>'.$follow.'</b>
                关注
            </a><a class="fd side-user-fans b-l" href="'.zrz_get_user_page_url($user_id).'/fans'.'">
                <b>'.$fans.'</b>
                粉丝
            </a>
        </div>
        '.($self ? '<div class="bg-blue-light pd10 widget-title gray t-l">财富</div>
        <div class="pd10 clearfix">
            <div class="fl">'.zrz_get_rmb($user_id).'</div><div class="fr">'.zrz_coin($user_id).'</div>
        </div>' : '').'
        </div>';

        if($fixed){
            echo '</div>';
        }
        echo $after_widget;
    }
}

//热门商品
class Zrz_Hot_Shop extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'hot_shop',
           __('(7b2)热门商品 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '可以显示热门的商品，抽奖和兑换项目', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('热门商品','ziranzhi2'),
            'type'=>'normal',
            'hot_nub'=>5,
            'hot_days_after'=>7,
            'mobile_hide'=>0,
            'fixed'=>0
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
        $type = isset($instance[ 'type' ]) ? $instance[ 'type' ] : $default['type'];
        $hot_nub = isset($instance[ 'hot_nub' ]) ? $instance[ 'hot_nub' ] : $default['hot_nub'];
        $hot_days_after = isset($instance[ 'hot_days_after' ]) ? $instance[ 'hot_days_after' ] : $default['hot_days_after'];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
        <label>要显示的商品类型</label>
        <select name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id('type'); ?>">
            <option value="normal" <?php echo ($type == 'normal' ? 'selected="selected"' : ''); ?>>购买</option>
            <option value="exchange" <?php echo ($type == 'exchange' ? 'selected="selected"' : ''); ?>>兑换</option>
            <option value="lottery" <?php echo ($type == 'lottery' ? 'selected="selected"' : ''); ?>>抽奖</option>
        </select>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'hot_nub' ); ?>">显示几篇：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_nub' ); ?>" name="<?php echo $this->get_field_name( 'hot_nub' ); ?>" value="<?php echo $hot_nub; ?>">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'hot_days_after' ); ?>">几天之内：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_days_after' ); ?>" name="<?php echo $this->get_field_name( 'hot_days_after' ); ?>" value="<?php echo $hot_days_after; ?>">
    </p>
    <p>
        <label>移动端显示吗？</label>
        <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
            <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
            <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
        </select>
    </p>
    <p>
        <label>是否浮动？</label>
        <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
            <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
            <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
        </select>
    </p>
    <?php
}
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'type' ] = strip_tags( $new_instance[ 'type' ] );
        $instance[ 'hot_nub' ] = strip_tags( $new_instance[ 'hot_nub' ] );
        $instance[ 'hot_days_after' ] = strip_tags( $new_instance[ 'hot_days_after' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';

        $current_user = get_current_user_id();

        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }

        echo $before_title.$instance[ 'title' ].$after_title;

        $html = '<ul class="box">';
        $args = array(
            'date_query' => array(
                array(
                   'after' => date('Y-m-d',strtotime("-".$instance[ 'hot_days_after' ]." days")),
                   'inclusive' => true,
                )
            ),
            'meta_key' =>'views',
            'meta_query' => array(
                'relation' => 'AND',
        		array(
        			'key' => 'zrz_shop_type',
        			'value' => $instance[ 'type' ],
        			'compare' => '=',
        		),
                array(
                    'key'=>'zrz_shop_count',
                    'value'=>'s:8:"sold_out";i:1;',
                    'compare' => 'Like',
                )
        	),
            'orderby' => 'meta_value_num',
            'order'	 => 'DESC',
            'posts_per_page'  => $instance[ 'hot_nub' ],
            'paged'	=> '1',
            'post_type'=>'shop'
        );
        $query = new WP_Query($args);
        if ( $query->have_posts() ) {
           while ( $query->have_posts() ) {
               $query->the_post();
               $post_id = get_the_id();
               $type = get_post_meta($post_id,'zrz_shop_type',true);
               switch ($type) {
                   case 'normal':
                       $price = zrz_get_shop_price_dom();
                       $html_in = '<div class="pos-r mar10-t gray"><span>¥ '.$price['price'].'</span>'.$price['msg'].'</div>';
                       break;
                   case 'exchange':
                       $credit = get_post_meta($post_id,'zrz_shop_need_credit',true);
                       $html_in = '<div class="mar10-t"><span>'.zrz_coin(0,0,$credit).'</span></div>';
                       break;
                   case 'lottery':
                       $credit = (int)zrz_get_shop_lottery($post_id,'credit');
                       $html_in = '<div class="mar10-t"><span>'.zrz_coin(0,0,$credit).'</span></div>';
                       break;
                   default:
                       $html_in = '';
                       break;
               }
               $thumb = zrz_get_post_thumb() ? '<img src="'.zrz_get_thumb(zrz_get_post_thumb(),66,66).'" />' : '';
               $html .= '<li class="clearfix"><a class="hot-shop-img fl" href="'.get_the_permalink().'">'.$thumb.'</a>
                   <div class="hot-shop-content">
                   <a href="'.get_the_permalink().'"><h2>'.get_the_title().'</h2></a>
                   '.$html_in.'
                   </div>
               </li>
               ';
           }
           wp_reset_postdata();
        }else{
           $html .= '<div class="pd10 t-c gray">没有商品</div>';
        }

        echo $html.'</ul>';
        unset($html);
        if($fixed){
            echo '</div>';
        }
        echo $after_widget;
    }
}

//热门商品
class Zrz_Shop_Dynamic extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'shop_dynamic',
           __('(7b2)购买动态 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '可以实时显示用户购买，兑换和抽奖的动态', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('购买动态','ziranzhi2'),
            'hot_nub'=>10,
            'mobile_hide'=>0,
            'fixed'=>0
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default['title'];
        $hot_nub = isset($instance[ 'hot_nub' ]) ? $instance[ 'hot_nub' ] : $default['hot_nub'];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'hot_nub' ); ?>">显示动态的数量：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_nub' ); ?>" name="<?php echo $this->get_field_name( 'hot_nub' ); ?>" value="<?php echo $hot_nub; ?>">
    </p>
    <p>
        <label>移动端显示吗？</label>
        <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
            <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
            <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
        </select>
    </p>
    <p>
        <label>是否浮动？</label>
        <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
            <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
            <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
        </select>
    </p>
    <?php
}
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'hot_nub' ] = strip_tags( $new_instance[ 'hot_nub' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';

        $current_user = get_current_user_id();

        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }

        echo $before_title.$instance[ 'title' ].$after_title;
        $nub = $instance[ 'hot_nub' ];
        $html = '<ul class="box">';
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_order';
        $where = $wpdb->prepare( "order_type = %s OR order_type = %s OR order_type = %s", 'c','d','g');
        $where = "SELECT * FROM $table_name WHERE $where ORDER BY order_date DESC LIMIT $nub";
        $data = $wpdb->get_results($where);
        if($data){
            foreach ($data as $val) {
                $order_id = explode('-',$val->order_id);
                if($order_id && count($order_id) > 2){
                    $post_id = $order_id[1];
                }else{
                    $post_id = $val->post_id;
                }

                $user_id = $val->user_id;
                $order_type = $val->order_type;
                $text = $order_type == 'c' ? '抽到' : ($order_type == 'd' ? '兑换' : '购买');
                $thumb = zrz_get_post_thumb($post_id) ? '<img src="'.zrz_get_thumb(zrz_get_post_thumb($post_id),50,50).'" />' : '';
                $html .= '<li class="cleafix">
                            <div class="shop-dynamic-img fl">'.$thumb.'</div>
                            <div class="shop-dynamic-content mar5-t">
                                <p class="fs12">'.zrz_str_encryption(get_the_author_meta('display_name',$user_id)).' <span class="gray">刚刚 </span>'.$text.' <span class="gray">了</span></p>
                                <p class="shop-dynamic-title"><a class="fs14" href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a></p>
                            </div>
                          </li>';
            }
            $html .= '</ul>';
        }else{
            $html .= '<div class="pd10 t-c gray">没有商品</div>';
        }

        echo $html;
        unset($html);
        if($fixed){
            echo '</div>';
        }
        echo $after_widget;
    }
}

//热门话题
class Zrz_Hot_topic extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'hot_topic',
           __('(7b2)热门帖子 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '热门帖子！', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('热门帖子','ziranzhi2'),
            'hot_nub' => '5',
            'hot_days_after'=>'30',
            'mobile_hide'=>'0',
            'fixed'=>'0'
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default[ 'title' ];
        $hot_nub = isset($instance[ 'hot_nub' ]) ? $instance[ 'hot_nub' ] : $default[ 'hot_nub' ];
        $hot_days_after = isset($instance[ 'hot_days_after' ]) ? $instance[ 'hot_days_after' ] : $default[ 'hot_days_after' ];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
   <p>
       <label for="<?php echo $this->get_field_id( 'hot_nub' ); ?>">一共显示多少个帖子：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_nub' ); ?>" name="<?php echo $this->get_field_name( 'hot_nub' ); ?>" value="<?php echo esc_attr( $hot_nub ); ?>">
   </p>
    <p>
       <label for="<?php echo $this->get_field_id( 'hot_days_after' ); ?>">几天之内：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hot_days_after' ); ?>" name="<?php echo $this->get_field_name( 'hot_days_after' ); ?>" value="<?php echo $hot_days_after; ?>">
   </p>
   <p>
       <label>移动端显示吗？</label>
       <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
           <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
           <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
       </select>
   </p>
   <p>
       <label>是否浮动？</label>
       <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
           <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
           <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
       </select>
   </p>
   <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'hot_nub' ] = strip_tags( $new_instance[ 'hot_nub' ] );
        $instance[ 'hot_days_after' ] = strip_tags( $new_instance[ 'hot_days_after' ] );
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }
        echo $before_title.$instance[ 'title' ].$after_title;

        $html = '<ul class="box">';
        $args = array(
            'date_query' => array(
                         array(
                   'after'     => date('Y-m-d',strtotime("-".$instance[ 'hot_days_after' ]." days")),
                   'inclusive' => true,
                   )),
            'meta_key' =>'_bbp_reply_count',
            'orderby' => 'meta_value_num',
            'order'	 => 'DESC',
            'posts_per_page'  => $instance[ 'hot_nub' ],
            'paged'	=> '1',
            'post_type'=>'topic'
        );

        $not_in = zrz_bbp_has_topics_query();
        if(!empty($not_in)){
            $args['post_parent__not_in'] = $not_in['post_parent__not_in'];
        }


        $the_query = new WP_Query($args);
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();

            $html .= '<li>
                    '.get_avatar(get_the_author_meta('ID') ,24).'
                    <h2 class="widget-post-h2"><a href="'.get_permalink().'">'.get_the_title().'</a></h2></li>';
             endwhile;
            wp_reset_postdata();
         else :
            $html .= '<div class="pd10 t-c gray">没有帖子</div>';
         endif;
         echo $html.'</ul>';
         unset($html);
         if($fixed){
             echo '</div>';
         }
         echo $after_widget;
    }
}

//最新评论
class Zrz_Newest_Comments_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
           'newest_comment',
           __('(7b2)最新评论 - 柒比贰', 'zrz' ),
           array (
               'description' => __( '最新评论！', 'zrz' )
           )
       );
    }

    public function form( $instance ) {
        $default = array(
            'title'=>__('最新评论','ziranzhi2'),
            'newest_comments_nub' => '5',
            'hide_author'=>1,
            'mobile_hide'=>'0',
            'fixed'=>'0'
        );
        $title = isset($instance[ 'title' ]) ? $instance[ 'title' ] : $default[ 'title' ];
        $hide_author = isset($instance[ 'hide_author' ]) ? $instance[ 'hide_author' ] : $default[ 'hide_author' ];
        $newest_comments_nub = isset($instance[ 'newest_comments_nub' ]) ? $instance[ 'newest_comments_nub' ] : $default[ 'newest_comments_nub' ];
        $mobile_hide = isset($instance[ 'mobile_hide' ]) ? $instance[ 'mobile_hide' ] : $default[ 'mobile_hide' ];
        $fixed = isset($instance[ 'fixed' ]) ? $instance[ 'fixed' ] : $default[ 'fixed' ];
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </p>
   <p>
       <label for="<?php echo $this->get_field_id( 'newest_comments_nub' ); ?>">一次显示几条评论：</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'newest_comments_nub' ); ?>" name="<?php echo $this->get_field_name( 'newest_comments_nub' ); ?>" value="<?php echo esc_attr( $newest_comments_nub ); ?>">
   </p>
   <p>
       <label for="<?php echo $this->get_field_id( 'hide_author' ); ?>">要隐藏的用户ID</label>
       <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'hide_author' ); ?>" name="<?php echo $this->get_field_name( 'hide_author' ); ?>" value="<?php echo esc_attr( $hide_author ); ?>">
   </p>
   <p>
       <label>移动端显示吗？</label>
       <select name="<?php echo $this->get_field_name( 'mobile_hide' ); ?>" id="<?php echo $this->get_field_id('mobile_hide'); ?>">
           <option value="1" <?php echo ($mobile_hide == 1 ? 'selected="selected"' : ''); ?>>显示</option>
           <option value="0" <?php echo ($mobile_hide == 0 ? 'selected="selected"' : ''); ?>>不显示</option>
       </select>
   </p>
   <p>
       <label>是否浮动？</label>
       <select name="<?php echo $this->get_field_name( 'fixed' ); ?>" id="<?php echo $this->get_field_id('fixed'); ?>">
           <option value="1" <?php echo ($fixed == 1 ? 'selected="selected"' : ''); ?>>浮动</option>
           <option value="0" <?php echo ($fixed == 0 ? 'selected="selected"' : ''); ?>>不浮动</option>
       </select>
   </p>
   <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'title' ] = $new_instance[ 'title' ];
        $instance[ 'hide_author' ] = $new_instance[ 'hide_author' ];
        $instance[ 'newest_comments_nub' ] = strip_tags( $new_instance[ 'newest_comments_nub' ] );
        $instance[ 'mobile_hide' ] = strip_tags( $new_instance[ 'mobile_hide' ] );
        $instance[ 'fixed' ] = strip_tags( $new_instance[ 'fixed' ] );
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        if(isset($instance[ 'mobile_hide' ]) && !$instance[ 'mobile_hide' ] && zrz_wp_is_mobile()) return;
        $fixed = isset($instance[ 'fixed' ]) && $instance[ 'fixed' ] == 1 ? 'side-fixed' : '';
        echo $before_widget;
        if($fixed){
            echo '<div class="side-fixed bg-w" data-sticky-class="is-side-sticky" data-margin-top="70">';
        }
        echo $before_title.$instance[ 'title' ].'<div class="fr" ref="commentSide" v-cloak><button :class="[{\'green\' : paged > 1 && !locked},\'mar10-r\',\'text\']" @click.stop.prevent="pager(\'prev\')">❮ PREV</button> <button :class="[{\'green\' : paged < pages && !locked},\'text\']" @click.stop.prevent="pager(\'next\')">NEXT ❯</button></div>'.$after_title;
        echo '
        <div id="newest-comments" data-hide="'.$instance[ 'hide_author' ].'" data-number="'.$instance[ 'newest_comments_nub' ].'">
                <ul v-if="comments.length > 0" v-cloak class="box">
                    <li class="" v-for="comment in comments">
                        <div class="clearfix">
                            <span class="" v-html="comment.avatar"></span>
                            <span class="newest_comment_author" v-html="comment.author"></span>
                            <span v-html="comment.date" class="fr"></span>
                        </div>
                        <div class="newest_comment_content pd10 mar10-t pos-r mar5-b pjt fs13" v-html="comment.content"></div>
                        <span class="gray fs12" v-html="comment.post_link"></span>
                    </li>
                </ul>
                <div class="pd10 b-t t-c gray pos-r" style="min-height:100px" v-else-if="!noneComments">
                    <b class="loading"></b>
                </div>
                <div class="pd10 b-t t-c gray pos-r" v-else v-cloak>
                    没有评论
                </div>
            </div>';
if($fixed){
    echo '</div>';
}
        echo $after_widget;
    }
}

function zrz_register_widget() {
    register_widget('Zrz_User_Box');
    register_widget( 'Zrz_Hot_Credit' );
    register_widget( 'Zrz_Hot_Posts' );
    register_widget('Zrz_Index_Add');
    register_widget('Zrz_Rand_Posts');
    register_widget('Zrz_Hot_Shop');
    register_widget('Zrz_Shop_Dynamic');
    register_widget('Zrz_User_Achievement');
    register_widget('Zrz_Hot_topic');
    register_widget('Zrz_Newest_Comments_Widget');
}
add_action( 'widgets_init', 'zrz_register_widget' );
