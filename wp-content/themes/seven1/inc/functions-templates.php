<?php
//主题使用的钩子
//header
add_action( 'header_7b2', 'header_announcement_7b2', 0 );//顶部公告

//menu
add_action( 'header_menu_7b2', 'header_menu_mobile_button_left_7b2', 0 );//移动菜单左侧按钮
add_action( 'header_menu_7b2', 'header_menu_logo_7b2', 10 );//logo
add_action( 'header_menu_7b2', 'header_menu_mobile_top_7b2', 30 );//移动菜单顶部logo和搜索框
add_action( 'header_menu_7b2', 'header_top_menu_7b2', 40 );//菜单

//首页
add_action( 'home_before_7b2', 'home_big_img_7b2', 0 );//首页背景图片
add_action( 'home_before_7b2', 'home_carousel_7b2', 10 );//首页幻灯
add_action( 'home_before_7b2', 'home_mission_7b2', 20 );//移动端首页签到
add_action( 'home_before_7b2', 'home_collection_7b2', 30 );//首页专题

add_action( 'home_loop_before_7b2', 'home_menu_7b2', 0 );//首页菜单

add_action( 'home_loop_middle_7b2', 'home_labs_7b2', 0 );//首页研究所

//content
add_action( 'content_after_7b2', 'footer_toolbar_7b2', 0 );//工具条

//footer
add_action( 'footer_content_7b2', 'footer_links_7b2', 0 );//工具条
add_action( 'footer_content_7b2', 'footer_copy_7b2', 10 );//底部版权

add_action( 'footer_before_7b2', 'footer_tools_7b2',  0);//登陆组件

add_action( 'footer_after_7b2','weixin_share_fn',  0);//登陆组件

//顶部菜单移动端
if ( ! function_exists( 'header_menu_mobile_button_left_7b2' ) ) {
    function header_menu_mobile_button_left_7b2(){
        echo '<div class="mobile-top-menu pos-a">
            <button class="text" @click.stop="showTopMenu()"><i class="zrz-icon-font-tubiaozhizuomoban-copy iconfont"></i></button>
        </div>';
    }
}

//logo
if ( ! function_exists( 'header_menu_logo_7b2' ) ) {
    function header_menu_logo_7b2(){
        if ( is_front_page() || is_home() ){
            echo '<h1 class="site-title pos-a"><a href="'.esc_url( home_url( '/' ) ).'" rel="home">'.zrz_get_logo().'</a></h1>';
        }else{
            echo '<p class="site-title pos-a"><a href="'.esc_url( home_url( '/' ) ).'" rel="home">'.zrz_get_logo().'</a></p>';
        }
    }
}

//移动端菜单最顶部
if ( ! function_exists( 'header_menu_mobile_top_7b2' ) ) {
    function header_menu_mobile_top_7b2(){
        $show_labs = zrz_get_display_settings('labs_show');
        $show_topic = class_exists( 'bbPress' );
        $show_shop = zrz_get_display_settings('shop_show');
        $show_bubble = zrz_get_display_settings('bubble_show');
        ?>
            <div class="menu-top l1" ref="topMenu">
                <div class="menu-sroll">
                    <div class="clearfix hide"><div class="fl mar5-t"><?php echo zrz_get_logo(); ?></div><div class="fr menu-close" @click="close"><i class="iconfont zrz-icon-font-icon-x"></i></div></div>
                    <div class="menu-search pos-r">
                        <select v-model="type">
                            <option value="post">
                                文章
                            </option>
                            <?php if($show_labs){ ?>
                                <option value="labs" v-if="showTable.labs">
                                    研究所
                                </option>
                            <?php }
                            if($show_topic){
                            ?>
                                <option value="topic" v-if="showTable.topic">
                                    话题
                                </option>
                            <?php }
                            if($show_shop){
                            ?>
                                <option value="shop" v-if="showTable.shop">
                                    商品
                                </option>
                            <?php }
                            if($show_bubble){
                            ?>
                                <option value="pps" v-if="showTable.bubble">
                                    冒泡
                                </option>
                            <?php } ?>
                        </select>
                        <form class="" :action="action+'?s='+key+'&post_type='+type" method="post">
                            <input type="text" name="" value="" v-model="key">
                            <button class="text" name="submit"><i class="zrz-icon-font-sousuo iconfont"></i></button>
                        </form>
                    </div>
                    <?php wp_nav_menu( array('theme_location' => 'header-menu','container_id'=>'zrz-menu-in', 'menu_id' => 'nav-menu','container_class'=> 'zrz-menu-in ', 'menu_class'=>'zrz-post-menu clearfix','depth'=>2 ) ); ?>
                </div>
            </div>
        <?php
    }
    unset($show_labs);
    unset($show_shop);
    unset($show_bubble);
}

//顶部菜单
if ( ! function_exists( 'header_top_menu_7b2' ) ) {
    function header_top_menu_7b2(){
        $is_login = is_user_logged_in();
    	$shop_show = zrz_get_display_settings('shop_show');
        $tx_admin = zrz_get_credit_settings('zrz_tx_admin');
        ?>
        <div class="sign-button pos-r pos-a">
            <?php if($is_login){
                $this_user = wp_get_current_user();
            ?>
                <div class="sign-button-r mar10-r mobile-hide">
                    <button class="head-vip text" @click="showWriteBox"><i class="zrz-icon-font-dengpao iconfont"></i></button>
                    <?php if($shop_show){ ?>
                        <a class="button text pos-r" href="<?php echo home_url('/cart'); ?>" ref="addToCart"><i class="zrz-icon-font-haofangtuo400iconfont2gouwu iconfont"></i><span v-text="shopCount" class="pos-a" v-if="shopCount > 0"></span></a>
                    <?php } ?>
                </div>
                <span class="mouh text click" @click.stop="showUserMenu"><?php echo get_avatar($this_user->ID,'60'); ?><span v-cloak><span class="pos-a fs12 hd-tips" v-show="msgCount > 0"></span></span></span>
                <div :class="['user-login-menu','pos-a','pjt','transform-out',showMenu ? 'transform-in' : '']" v-cloak>
                    <a class="b-b pd10" href="<?php echo zrz_get_user_page_url($this_user->ID); ?>">
                        <?php echo get_avatar($this_user->ID,'60'); ?>
                        <div class="user-home-link">
                            <?php echo $this_user->display_name; ?>
                            <p class="fs12 gray">
                                <?php echo __('个人主页','7b2'); ?>
                            </p>
                        </div>
                    </a>
                    <div class="mobile-icon hide b-b">
                        <button class="head-vip text" @click="showWriteBox"><i class="zrz-icon-font-dengpao iconfont"></i> 发起</button><span class="dot"></span>
                        <?php if($shop_show){ ?>
                            <a class="button text pos-r" href="<?php echo home_url('/cart'); ?>" ref="addToCart"><i class="zrz-icon-font-haofangtuo400iconfont2gouwu iconfont"></i><span v-text="shopCount" class="pos-a" v-if="shopCount > 0"></span>购物车</a>
                        <?php } ?>
                    </div>
                    <a href="<?php echo zrz_get_custom_page_link('notifications'); ?>" class="hd-avatar">
                        <i class="zrz-icon-font-icon_message iconfont"></i><?php echo __('消息','7b2'); ?><span class="fs12" v-text="msgCount" v-show="msgCount > 0"></span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/directmessage')); ?>">
                        <i class="zrz-icon-font-wodesixin iconfont"></i><?php echo __('私信','7b2'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/gold')); ?>">
                        <i class="zrz-icon-font-cny iconfont"></i><?php echo __('财富','7b2'); ?>
                    </a>
                    <a href="<?php echo home_url('/vips'); ?>"><i class="zrz-icon-font-huiyuan iconfont"></i>付费会员</a>
                    <a class="b-b" href="<?php echo home_url('/user/'.$this_user->ID.'/setting'); ?>">
                        <i class="zrz-icon-font-setting1 iconfont"></i><?php echo __('编辑个人资料','7b2'); ?>
                    </a>
                    <?php if($tx_admin == $this_user->ID){ ?>
                        <a href="<?php echo esc_url(home_url('/withdraw')); ?>">
                            <i class="zrz-icon-font-tixian iconfont"></i><?php echo __('提现管理','7b2'); ?>
                        </a>
                    <?php } ?>
                    <?php if(current_user_can('delete_users')){ ?>
                        <a href="<?php echo esc_url(home_url('/wp-admin/index.php')); ?>">
                        <i class="zrz-icon-font-setting1 iconfont"></i><?php echo __('进入后台','7b2'); ?>
                        </a>
                    <?php } ?>
                    <a href="<?php echo esc_url(wp_logout_url(zrz_get_curl())); ?>">
                        <i class="zrz-icon-font-tuichu iconfont"></i><?php echo __('登出','7b2'); ?>
                    </a>
                </div>
                <?php }else{ ?>
                    <div class="mobile-button">
                        <button class="text" @click.stop="sign('in')"><i class="iconfont zrz-icon-font-40one"></i></button>
                    </div>
                    <div class="mobile-hide">
                        <button class="empty" @click="sign('in')" v-cloak>登录</button>
                        <button class="small mar10-l" @click="sign('up')" v-if="canReg == 1" v-cloak>快速注册</button>
                    </div>
            <?php } ?>
        </div>
        <?php
    }
}

//首页背景图片
if ( ! function_exists( 'home_big_img_7b2' ) ) {
    function home_big_img_7b2(){
        $home_bg = zrz_get_display_settings('home_bg');
        $open = isset($home_bg['open']) ? $home_bg['open'] : array(0);
        $open = $open[0];
        $html = '';
        if($open == 1 && isset($home_bg['img'])){
            $type = isset($home_bg['type']) ? $home_bg['type'] : array(0);
            $type = $type[0] == 1 ? false : true;
            $html = '
                <div class="home-bg pos-a">
                	<div class="pos-a img-bg '.($type ? 'blur' : '').'" style="background-image:url('.zrz_get_thumb($home_bg['img'],$type ? 100 : 1903,'full').')"></div>
                </div>
            ';
        }
        echo $html;
        unset($html);
    }
}

//首页幻灯
if ( ! function_exists( 'home_carousel_7b2' ) ) {
    function home_carousel_7b2(){
        
        $show = zrz_get_display_settings('swipe_show');
        $style = zrz_get_display_settings('swipe_style');
        $is_mobile = zrz_wp_is_mobile();
        $post_arr = get_option('zrz_swipe_posts',true);

        if($style == 0 || empty($post_arr) || !is_array($post_arr)) return;

        $html = '';
        
        if($style == 1) : 
            $width = zrz_get_theme_settings('page_width');
            if($is_mobile){
                $width = '500';
                $height = '360';
            }else{
                $width = (int)$width;
                $height = 422;
            }
            
            $html .= '<div id="carousel" class="home-big-swipe pos-r mar16-b" ref="bigCarousel" v-cloak>';
            foreach ($post_arr as $key=>$val) {

                $post_type = get_post_type($key);
                $title = get_the_title($key);
                $link = get_permalink($key);

                if(isset($val) && $val){
                    $thumb = $val;
                }else{
                    $thumb = zrz_get_post_thumb($key);
                }

                $cat = $post_type == 'post' ? zrz_get_first_category($key) : get_first_labs_7b2($key);
                $author = get_post_field('post_author',$key);

                $html .= '<div class="bigcarousel-cell pos-a">
                    <img class="home-thumb-big pos-a" src="'.zrz_get_thumb($thumb,$width,$height).'" />
                    <a class="link-block" href="'.$link.'" rel="bookmark"></a>
                    '.($show ? '<div class="bigcarousel-info pos-a">
                    <div class="wp-cat">'.$cat.'</div>
                    <h2>'.$title.'</h2>
                    <div class="wp-meta fs12 mar10-t">
                        '.get_avatar($author,40,40).zrz_get_user_page_link($author).ZRZ_THEME_DOT.zrz_time_ago($key).'
                    </div>
                </div>' : '').'</div>';
                
                

            }
            $html .= '</div>';

        else :

            $html .= '<div id="carousel" class="home-wrapper mar16-b '.($is_mobile ? 'home-carousel' : '').'"><div ref="carousel" class="'.($is_mobile ? 'main-carousel' : '').'">';
            $count_post = count($post_arr);
            if($count_post < 4) return;
            $i = 0;
            foreach ($post_arr as $key=>$val) {
                $i++;
                $post_type = get_post_type($key);
                $title = get_the_title($key);
                $link = get_permalink($key);

                if(isset($val) && $val){
                    $thumb = $val;
                }else{
                    $thumb = zrz_get_post_thumb($key);
                }
                
                $cat = $post_type == 'post' ? zrz_get_first_category($key) : get_first_labs_7b2($key);
                $author = get_post_field('post_author',$key);
                if(($i == 1 && $count_post <= 4) || $is_mobile || ($i>=1 && $i <= $count_post-3 && $count_post > 4)){
                    if($count_post > 4 && $i == 1 && !$is_mobile){
                        $html .= '<div class="fd pos-r wp-first" ref="pcCarousel">';
                    }
                    $html .= '<div class="wp-l '.($i == 1 && $count_post <= 4 ? 'fd pos-r' : '').' img-bg '.($is_mobile ? 'carousel-cell' : '').'" style="background-image:url('.zrz_get_thumb($thumb,570,365).')">
                            <a class="link-block" href="'.$link.'" rel="bookmark"></a>
                            '.($show ? '<div :class="[\'pos-a\',\'bg-bkg\',\'shadow\',titleClass]" v-cloak>
                            <div class="wp-cat">'.$cat.'</div>
                            <h2>'.$title.'</h2>
                            <div class="wp-meta fs12 mar10-t">
                                '.get_avatar($author,40,40).zrz_get_user_page_link($author).ZRZ_THEME_DOT.zrz_time_ago($key).'
                            </div>
                        </div>' : '').'
                        </div>';
                    if($count_post > 4 && $i == $count_post -3 && !$is_mobile){
                        $html .= '</div>';
                    }
                }elseif($i == $count_post -2){
                    $html .='<div class="fd wp-r">
                        <div class="wp-r-t pos-r img-bg" style="background-image:url('.zrz_get_thumb($thumb,560,180).')">
                            <a class="link-block" href="'.$link.'" rel="bookmark"></a>
                            '.($show ? '<div class="pos-a bg-bkg shadow">
                            <div class="wp-cat">'.$cat.'</div>
                            <h2>'.$title.'</h2>
                        </div>' : '').'
                        </div>';
                }elseif($i == $count_post -1){
                    $html .= '<div class="wp-r-b">
                        <div class="wp-r-b-l fd pos-r" >
                        <div class="img-bg pos-a" style="background-image:url('.zrz_get_thumb($thumb,282,176).')">
                            <a class="link-block" href="'.$link.'" rel="bookmark"></a>
                            '.($show ? '<div class="pos-a bg-bkg shadow">
                            <div class="wp-cat">'.$cat.'</div>
                            <h2>'.$title.'</h2>
                        </div>' : '').'
                        </div></div>';
                }else{
                    $html .= '<div class="wp-r-b-r fd pos-r">
                    <div class="img-bg pos-a" style="background-image:url('.zrz_get_thumb($thumb,282,176).')">
                        <a class="link-block" href="'.$link.'" rel="bookmark"></a>
                        '.($show ? '<div class="pos-a bg-bkg shadow">
                        <div class="wp-cat">'.$cat.'</div>
                        <h2>'.$title.'</h2>
                    </div>' : '').'
                        </div>
                    </div>';
                }
            }

            if($is_mobile){
                $html .= '</div></div>';
            }else{
                $html .= '</div></div></div></div>';
            }

        endif;

        echo $html;
        unset($html);
    }
}

//首页签到
if ( ! function_exists( 'home_mission_7b2' ) ) {
    function home_mission_7b2(){
        echo '<div class="box pd10 mission pos-r mar10-b mar10-t l1" id="home-mission" ref="homeMission" v-cloak>
        	<a href="#" @click.stop.prevent="mission()"><i class="iconfont zrz-icon-font-liwu red"></i><span class="text mar5-l" v-html="Mtext">&nbsp;</span></a>
        </div>';
    }
}

//首页专题
if ( ! function_exists( 'home_collection_7b2' ) ) {
    function home_collection_7b2(){

        $collections_show_mobile = zrz_get_display_settings('collections');
        $collections_show = isset($collections_show_mobile['collections_show_index']) ? $collections_show_mobile['collections_show_index'] : true;
        if(!$collections_show) return;
        $collections_show_mobile = isset($collections_show_mobile['text']) ? $collections_show_mobile['text'] : array();
    	$arr = array();
    	$ids = array();
    	$thumb = '';
    	if($collections_show_mobile){
    		$row = explode(PHP_EOL, $collections_show_mobile );
    		if($row && is_array($row) && !empty($row)){
    			foreach ($row as $val) {
    				$val = DeleteHtml($val);
    				$list = explode( "|", $val );
    				$arr[$list[0]] = $list[1];
    				$ids[] = $list[0];
    			}
    		}
    	}
    	$taxonomies = get_terms( array(
    		'taxonomy' => 'collection',
    		'hide_empty' => false,
    		'number'=>6,
    		'include'=>$ids,
    		'orderby'=>'include'
    	) );
    	$html = '';
    	if ( !empty($taxonomies) ) {
    		$html = '<div id="home-collections" class="home-collections pos-r mar16-b" @click="openList($event)">
    		<div class="pd10 clearfix fs12 l1">
    			<span class="fl">专题</span>
    			<a href="'.home_url('/collections').'" class="fr" v-if="locked">所有专题 ❯</a>
    			<span class="open-colle fr" v-else><i class="iconfont zrz-icon-font-zhankaishangxia"></i></span>
    		</div>
    		<ul class="l-8" @click.stop="" v-show="locked">';
    		foreach ($taxonomies as $key => $val) {
    			if(in_array($val->term_id,$ids)){
    				$thumb = zrz_get_thumb($arr[$val->term_id],177,85);
    			}else{
    				$thumb = zrz_get_category_meta($val->term_id,'image',array(177,85));
    			}
    			$html .= '<li class="collections-item fd t-c l0">
    			<div class="collections-item-in">
    				<div class="collections-item-img pos-r t-c" style="background-image:url('.$thumb.')">
    					<a href='.esc_url(get_term_link($val->term_id)).' class="pos-a">
    						<span class="pos-a shadow">'.$val->name.'</span>
    					</a>
    				</div>
    				<div class="pd10-b pd10-t bg-blue-light">
    					<p class="fs13 gray">已更新'.$val->count.'篇文章</p>
    				</div>
    			</div>
    			</li>';
    		}
    		$html .= '</ul></div>';
    	}
        echo $html;
        unset($html);
    }
}

//首页菜单
if(!function_exists('home_menu_7b2')){
    function home_menu_7b2(){
        $menu = wp_nav_menu( array(
            'theme_location' => 'home-post-menu',
            'container_id'=>'zrz-menu-post',
            'menu_id' => 'nav-menu',
            'container_class'=> 'zrz-menu-in ',
            'menu_class'=>'zrz-menu-post',
            'depth'=>0,
            'echo' => FALSE,
            'fallback_cb' => '__return_false' )
        );

        if ( ! empty ( $menu ) ){
            echo '<div id="home-menu-post" class="home-menu-post pos-r box mar16-b" ref="homeMenu">' . $menu . '</div>';
        }
    }
}

//研究所
if ( ! function_exists( 'home_labs_7b2' ) ) {
    function home_labs_7b2(){
        if(!zrz_get_display_settings('labs_show') || !zrz_get_display_settings('labs_show_index')) return;

        $list = '';
        $args = apply_filters( 'home_labs_7b2_args',array(
            'orderby' => 'date',
            'order'	 => 'DESC',
            'posts_per_page'  =>6,
            'post_type'=>'labs'
        ));
        $the_query = new WP_Query($args);
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();
            $post_author = zrz_get_user_page_link();
            $thumb = zrz_get_post_thumb();
            $join_count = get_post_meta(get_the_id(),'zrz_join_num',true);
            $join_count = $join_count ? $join_count : 0;
            $term = wp_get_post_terms(get_the_id(), 'labtype');
            $term = $term[0];
            $link = get_permalink();
            if($thumb){
                $thumb = zrz_get_thumb(zrz_get_post_thumb(),410,300);
            }

            $list .= '<li class="pos-r" v-cloak>
                    <div class="pos-a home-labs-thumb-p">
                        <div class="home-labs-thumb thumb-in" style="background-image:url('.$thumb.')"><a class="link-block" href="'.$link.'" rel="bookmark"></a></div>
                    </div>
                    <div class="home-labs-list-r">
                        <p class="home-labs-cat"><a href="'.get_term_link($term->term_id).'">'.$term->name.'</a></p>
                        <h2><a href="'.$link.'"  rel="bookmark">'.get_the_title().'</a></h2>
                        <p class="home-labs-des pos-r"><i class="iconfont zrz-icon-font-shangyinhao"></i>'.zrz_get_content_ex().'<i class="iconfont zrz-icon-font-xiayinhao"></i></p>
                        <p class="home-labs-author gray mar20-t label-meta mar20-b">'.get_avatar(get_the_author_meta( 'ID' ),'30').$post_author.'</p>
                        <p class="mar10-t gray label-meta">'.$join_count.'人参与<i class="dot">|</i>'.zrz_time_ago().'</p>
                    </div></li>';
             endwhile;
            wp_reset_postdata();
         else :
            return;
         endif;
        echo '<div class="box mar16-b home-labs" ref="homeLabs">
            <ul class="clearfix home-labs-list">
                '.$list.'
            </ul>
        </div>';
        unset($list);
    }
}

//底部工具条
if ( ! function_exists( 'footer_toolbar_7b2' ) ) {
    function footer_toolbar_7b2(){
        ?>
            <div id="go-top" class="go-top" :style="'z-index:'+index">

                <?php 
                    $mobile_menu = zrz_get_display_settings('mobile_menu'); 
                    $show = isset($mobile_menu['show']) ? $mobile_menu['show'] : false;
                    $text = isset($mobile_menu['show_text']) ? zrz_get_html_code($mobile_menu['show_text']) : false;

                    if($show == 0 && zrz_wp_is_mobile()){
                        $row = explode(PHP_EOL, $text );
                        $html = '';
                        if(!empty($row)){
                            foreach ($row as $val) {
                                $list = explode( "|", $val );
                                $html .= '<div class="pos-r tool-button"><a href="'.$list[0].'">'.zrz_get_html_code($list[2]).'<span>'.$list[1].'</span></a></div>';
                            }
                        }

                        if(is_user_logged_in()){
                            $html .= '<div class="pos-r tool-button mobile-faqi"><a href="javascript:void(0)" @click="showSearchBox(\'write\')"><i class="iconfont zrz-icon-font-dengpao"></i><span>发起</span></a></div>';
                            $html .= '<div class="pos-r tool-button"><a href="'.zrz_get_user_page_url(get_current_user_id()).'"><i class="zrz-icon-font-40one iconfont"></i><span>我的</span></a></div>';
                        }else{
                            $html .= '<div class="pos-r tool-button" @click="loginAc()"><i class="zrz-icon-font-baomingrenyuan- iconfont"></i><span>登陆</span></div>';
                        }
                        echo $html;
                        unset($html);
                    }else{
                ?>
                    <template v-cloak>
                        <?php
                            if(zrz_get_theme_settings('theme_style_select') && !zrz_is_custom_tax('bbpress') && !zrz_is_custom_tax('collection') && !is_single() && !is_page() && !zrz_is_page(false,'links') && !is_post_type_archive('shop')){ ?>
                                <div class="pos-r tool-button style-change"><button class="text" @click="changeStyle('pinterest')" v-if="themeStyle == 'list'"><i class="iconfont zrz-icon-font-dibucaidan_wanggexiangqing"></i><span>网格</span></button><button class="text" @click="changeStyle('list')" v-if="themeStyle == 'pinterest'"><i class="iconfont zrz-icon-font-liebiao"></i><span>列表</span></button></div>
                        <?php }?>
                        <?php
                            $go_top = zrz_get_display_settings('go_top');
                            $contect = $go_top['contect'];
                            $search = $go_top['search'];
                            if($go_top['open']){
                        ?>
                            <div class="toolbar-write pos-r tool-button">
                                <button class="text" @click="showSearchBox('write')"><i class="iconfont zrz-icon-font-dengpao"></i><span>发起</span></button>
                            </div>
                            <?php if($contect['open']){ ?>
                                <div class="pos-r tool-button" v-cloak>
                                    <button class="text " @click="msg"><i class="iconfont zrz-icon-font-message1"></i><span>反馈</span></button>
                                </div>

                            <?php } if($search['open']){ ?>
                                <div class="pos-r tool-button" v-cloak>
                                    <button class="text" @click="showSearchBox('search')"><i class="iconfont zrz-icon-font-sousuo"></i><span>搜索</span></button>
                                </div>
                            <?php } if(is_singular('post') || is_singular('labs')){ ?>
                                <div class="pos-r tool-button" v-cloak>
                                    <button class="text" @click="goComment()"><i class="iconfont zrz-icon-font-iconfontpinglun"></i><span>评论</span></button>
                                </div>
                            <?php } ?>
                            <div class="pos-r tool-button" v-cloak>
                                <button class="text" @click="goTop"><i class="iconfont zrz-icon-font-arrowup"></i><span>Top</span></button>
                            </div>
                        <?php } ?>
                    </template>
                <?php } ?>
                <msg-box :show="showBox" :tid="uid" :tname="uname" :mtype="mtype" title="问题反馈" @close-form="closeForm"></msg-box>
                <go-top :show="showSearch" :type="type" @close-form="closeForm"></go-top>
    		</div>
        <?php
    }
}

if ( ! function_exists( 'footer_links_7b2' ) ) {
    function footer_links_7b2(){

        $menu = wp_nav_menu( array(
            'theme_location' => 'footer-menu',
            'container_id'=>'zrz-menu-footer',
            'menu_id' => 'nav-menu',
            'container_class'=> 'zrz-menu-in ',
            'menu_class'=>'zrz-footer-menu mar20-b',
            'depth'=>0,
            'echo' => FALSE,
            'fallback_cb' => '__return_false' )
        );

        if ( ! empty ( $menu ) ){
            echo '<div class="footer-menu">' . $menu . '</div>';
        }

        if(zrz_get_theme_settings('link_cat') == '') return;
        
        if ( !wp_is_mobile() && is_home() && !get_query_var('zrz_page_type')) {
            $bookmarks = get_bookmarks(array(
                'category'=>zrz_get_theme_settings('link_cat'),
                'orderby'=>'link_rating',
                'order'=>'DESC'
            ));
            if ( !empty($bookmarks) ) {
                echo '<div class="footer-links mar20-b b-b b-t mobile-hide bg-blue-light">';
                    foreach ($bookmarks as $bookmark) {
                        echo '<a target="_blank" href="' . $bookmark->link_url . '">' . $bookmark->link_name . '</a>';
                    }
                echo '</div>';
            }
         }
    }
}

if ( ! function_exists( 'footer_copy_7b2' ) ) {
    function footer_copy_7b2(){
        $copy = zrz_get_theme_settings('site_copy');
        $queries = get_num_queries();
        $timer = timer_stop(0,4);
        $ipc = get_option('zh_cn_l10n_icp_num');

        $html = '<div class="footer-copy clearfix"><div class="footer-left fl">';
        if($copy){
            $html .= zrz_get_html_code($copy);
        }else{
            $html .= '<p class="mar10-b">
               Since 2015, Build with <span class="red">♥</span> by <a href="https://7b2.com">柒比贰</a>
            </p>';
        }
        $html .= '
        <p class="mar10-b mobile-hide">
           '.$queries.' queries '.$timer.' s
        </p>
        <p class="ipc"><a href="http://www.miitbeian.gov.cn/" target="_blank" rel="nofollow">'.$ipc.'</a></p>';
        $html .= '</div><div class="footer-right fr t-r mobile-hide">';
        $html .= zrz_get_logo('logo-footer').'<p class="mar10-t">'.get_bloginfo( 'description' ).'</p>';
        $html .= '</div></div>';
        echo $html;
        unset($html);
    }
}

if ( ! function_exists( 'footer_tools_7b2' ) ) {
    function footer_tools_7b2(){
        ?>
            <div id="sign-form">
            	<sign-form :show-form="showBox" :signup="signup" :signin="signin" :security="security" @close-form="closeForm"></sign-form>
            	<input type="hidden" ref="security" value="<?php echo wp_create_nonce('zrz-sign'); ?>">
            </div>
            <input type="hidden" id="security" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
        <?php
    }
}

if(!function_exists('weixin_share_fn')){
    function weixin_share_fn(){
        $share_data = zrz_get_wxshare_data();
        if(!$share_data) return;
        $wx_data = $share_data['msg'];
        $post_data = $share_data['post_data'];
    ?>
    <script>
         wx.config ({
                debug : false,    // true:调试时候弹窗
                appId : '<?php echo $wx_data['appId']; ?>',  // 微信appid
                timestamp : '<?php echo $wx_data['timestamp']; ?>', // 时间戳
                nonceStr : '<?php echo $wx_data['nonceStr']; ?>',  // 随机字符串
                signature : '<?php echo $wx_data['signature']; ?>', // 签名
                jsApiList : [
                    // 所有要调用的 API 都要加到这个列表中
                    'onMenuShareTimeline',       // 分享到朋友圈接口
                    'onMenuShareAppMessage',  //  分享到朋友接口
                    'onMenuShareQQ',         // 分享到QQ接口
                    'onMenuShareWeibo'      // 分享到微博接口
                ]
            });

            
            wx.ready (function () {
                // 微信分享的数据
                var shareData = {
                    "imgUrl" : '<?php echo $post_data['imgUrl']; ?>',    // 分享显示的缩略图地址
                    "link" : '<?php echo $post_data['link']; ?>',    // 分享地址
                    "desc" : '<?php echo $post_data['desc']; ?>',   // 分享描述
                    "title" :'<?php echo $post_data['title']; ?>',   // 分享标题
                    success : function () {  
                        alert("分享成功"); 
                    } 
                };
                wx.onMenuShareTimeline (shareData); 
                wx.onMenuShareAppMessage (shareData); 
                wx.onMenuShareQQ (shareData); 
                wx.onMenuShareWeibo (shareData);
            });

            wx.error(function(res){
                console.log(res);
            })
    </script>
    <?
    }
}