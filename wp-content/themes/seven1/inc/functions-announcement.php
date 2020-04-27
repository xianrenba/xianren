<?php
//添加公告所的文章类型
function zrz_create_post_type_announcement() {
	$gg = array(
 		'name' => __('公告','ziranzhi2'),
    	'singular_name' => __('公告','ziranzhi2'),
    	'add_new' => __('添加一个公告','ziranzhi2'),
    	'add_new_item' => __('添加一个公告','ziranzhi2'),
    	'edit_item' => __('编辑公告','ziranzhi2'),
    	'new_item' => __('新的公告','ziranzhi2'),
    	'all_items' => __('所有公告','ziranzhi2'),
    	'view_item' => __('查看公告','ziranzhi2'),
    	'search_items' => __('搜索公告','ziranzhi2'),
    	'not_found' =>  __('没有公告','ziranzhi2'),
    	'not_found_in_trash' =>__('回收站为空','ziranzhi2'),
    	'parent_item_colon' => '',
    	'menu_name' => __('公告','ziranzhi2'),
    );
	register_post_type( 'announcement', array(
		'labels' => $gg,
		'has_archive' => true,
 		'public' => true,
		'supports' => array( 'title', 'editor','thumbnail'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'announcement' ),
		)
	);
}
add_action( 'init', 'zrz_create_post_type_announcement' );

add_filter('post_type_link', 'custom_book_link_gg', 1, 3);
function custom_book_link_gg( $link, $post = 0 ){
    if ( $post->post_type == 'announcement' ){
        return home_url( 'announcement/' . $post->ID .'.html' );
    } else {
        return $link;
    }
}

add_action( 'init', 'custom_book_rewrites_gg_init' );
function custom_book_rewrites_gg_init(){
    add_rewrite_rule(
        'announcement/([0-9]+)?.html$',
        'index.php?post_type=announcement&p=$matches[1]',
        'top' );
    add_rewrite_rule(
        'announcement/([0-9]+)?.html/comment-page-([0-9]{1,})$',
        'index.php?post_type=announcement&p=$matches[1]&cpage=$matches[2]',
        'top');
}

add_action( 'wp_ajax_zrz_get_announcement', 'zrz_get_announcement' );
add_action( 'wp_ajax_nopriv_zrz_get_announcement', 'zrz_get_announcement' );
function zrz_get_announcement(){
	$args = array(
		'post_type' => 'announcement',
		'orderby'  => 'date',
		'post_status'=>'publish',
		'order'=>'DESC',
		'posts_per_page'=>zrz_get_display_settings('show_announcement_count')
	);
	$the_query = new WP_Query($args);
	$arr = array();
	$i = 0;
	$masArr = array();
	if ( $the_query->have_posts()) {

		while ($the_query->have_posts()){
			$the_query->the_post();
			$i++;
			$id = get_the_id();
			$title = get_the_title();
			$link = get_permalink();
			$arr[] = '<a data-id="'.$id.'" href="'.$link.'">'.$title.ZRZ_THEME_DOT.'<span class="time gray mobile-hide">'.get_post_time( 'Y-m-d H:i:s', false).'</span></a>';
			if($i == 1){
				$masArr = array(
					'id'=>$id,
					'title'=>$title,
					'des'=>zrz_get_content_ex('',30),
					'link'=>get_permalink(),
					'thumb'=>zrz_get_thumb(zrz_get_post_thumb(),200,200)
				);
			}
		};

		wp_reset_postdata();
		print json_encode(array('status'=>200,'msg'=>$arr,'msgArr'=>$masArr));
		unset($masArr);
		unset($arr);
		exit;
	}else{
		print json_encode(array('status'=>401,'msg'=>__('没有公告','ziranzhi2')));
		unset($masArr);
		unset($arr);
		exit;
	}
}

//关闭公告
function zrz_close_announcement(){
	$args = array(
	    'numberposts' => 1,
	    'offset' => 0,
		'orderby'  => 'date',
		'order'=>'DESC',
	    'post_type' => 'announcement',
	    'post_status' => 'publish',
	);
	$recent_posts = wp_get_recent_posts($args);
	$post_id = $recent_posts[0];
	$post_id = $post_id['ID'];

	$cookie_id = zrz_getcookie('zrz_announcement');

	if($cookie_id == $post_id){
		return false;
	}

	return true;
}

//公告html
if ( ! function_exists( 'header_announcement_7b2' ) ) {
	function header_announcement_7b2(){
		$count = zrz_get_display_settings('show_announcement_count');
		$text = zrz_get_display_settings('announcement_text');
		$menu = wp_nav_menu( array(
            'theme_location' => 'top-banner-menu',
            'container_id'=>'zrz-menu-top-banner',
            'menu_id' => 'nav-menu',
            'container_class'=> 'zrz-menu-in ',
            'menu_class'=>'zrz-top-banner-menu',
            'depth'=>0,
            'echo' => FALSE,
            'fallback_cb' => '__return_false' )
		);
		if(!$count) return;
		echo '<div class="header-top" ref="announcement">
				'.($count > 0 ?
				'<div class="header-top-in pos-r clearfix" ref="announcementInfo">
					<div class="gg fl" v-if="marqueeList.length > 0">
						<i class="iconfont zrz-icon-font-gonggao"></i>
						<div class="marquee_box" @mouseover="hoverStop(\'stop\')" @mouseout="hoverStop(\'con\')">
							<ul class="marquee_list" :class="{\'marquee_top\':animate}">
								<li v-for="(item, index) in marqueeList" v-html="item">
								</li>
							</ul>
						</div>
					</div>
					'.($menu ? '<div class="fr">'.$menu.'</div>' : '<div class="fr" @click="showSearchBox">'.$text.'</div>').'
				</div>' : '').'
			</div>';
		unset($menu);
	}
}
