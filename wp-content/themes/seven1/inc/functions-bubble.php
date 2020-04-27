<?php
//新增冒泡文章样式
function zrz_custom_post_pps() {
    $name = zrz_custom_name('bubble_name');
    $labels = array(
        'name'               => sprintf( '%1$s',$name),
        'singular_name'      => sprintf( '%1$s',$name),
        'add_new'            => sprintf( '新建一个%1$s',$name),
        'add_new_item'       => sprintf( '新建一个%1$s',$name),
        'edit_item'          => sprintf( '编辑%1$s',$name),
        'new_item'           => sprintf( '新%1$s',$name),
        'all_items'          => sprintf( '所有%1$s',$name),
        'view_item'          => sprintf( '查看%1$s',$name),
        'search_items'       => sprintf( '搜索%1$s',$name),
        'not_found'          => sprintf( '没有找到有关的%1$s',$name),
        'not_found_in_trash' => sprintf( '回收站里没有%1$s',$name),
        'parent_item_colon'  => '',
        'menu_name'          => sprintf( '%1$s',$name),
    );
    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive'   => true,
        'rewrite' => array( 'slug' => 'bubble' ),
    );
    register_post_type( 'pps', $args );
}
add_action( 'init', 'zrz_custom_post_pps' );

//添加一个分类法pps
function zrz_pps_taxonomy(){
    $name = zrz_custom_name('bubble_name');
    $labels = array(
            'name' => sprintf( '%1$s话题',$name),
            'singular_name' => sprintf( '%1$s话题',$name),
            'search_items' => __( '搜索' ,'ziranzhi2' ),
            'popular_items' => sprintf( '热门的%1$s话题',$name),
            'all_items' => sprintf( '所有%1$s话题',$name),
            'edit_item' => sprintf( '编辑%1$s话题',$name),
            'update_item' => sprintf( '更新%1$s话题',$name),
            'add_new_item' => sprintf( '新建%1$s话题',$name),
            'new_item_name' => sprintf( '新的%1$s话题',$name),
    );
    $args = array(
            'labels' => $labels,
            'hierarchical' => true,//分层级
			'singular_label' => 'mp',
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'           => array( 'slug' => 'bubbles' ),
    );
    register_taxonomy('mp',array('pps'), $args);
}
add_action('init', 'zrz_pps_taxonomy');

add_filter('post_type_link', 'custom_book_link', 1, 3);
function custom_book_link( $link, $post = 0 ){
    if ( $post->post_type == 'pps' ){
        return home_url( 'bubble/' . $post->ID .'.html' );
    } else {
        return $link;
    }
}

add_action( 'init', 'custom_book_rewrites_init' );
function custom_book_rewrites_init(){
    add_rewrite_rule(
        'bubble/([0-9]+)?.html$',
        'index.php?post_type=pps&p=$matches[1]',
        'top' );
    add_rewrite_rule(
        'bubble/([0-9]+)?.html/comment-page-([0-9]{1,})$',
        'index.php?post_type=pps&p=$matches[1]&cpage=$matches[2]',
        'top');
}

//发布冒泡
add_action('wp_ajax_zrz_add_bubble','zrz_add_bubble');
add_action('wp_ajax_nopriv_zrz_add_bubble', 'zrz_add_bubble');
function zrz_add_bubble(){
    $name = zrz_custom_name('bubble_name');
	if(!is_user_logged_in()){
		print json_encode(array('status'=>401,'msg'=>'请登录'));
		exit;
	}

    $current_user = wp_get_current_user();

    $user = $current_user->ID;

    check_ajax_referer('pps'.$user, 'security' );

    //检查用户发布冒泡的权限
    if(!zrz_current_user_can('bubble')){
		print json_encode(array('status'=>401,'msg'=>sprintf( '您所在的用户组没有发布%1$s的权限',$name)));
		exit;
	}

	$imgs = isset( $_REQUEST['imgs'] ) && !empty($_REQUEST['imgs']) ? $_REQUEST['imgs'] : '';
	$videos = isset( $_REQUEST['videos'] ) && !empty($_REQUEST['videos']) ? $_REQUEST['videos'] : array();
	$text = isset( $_REQUEST['text'] ) && !empty($_REQUEST['text']) ? strip_tags($_REQUEST['text']) : '';
	$topic = isset($_REQUEST['topic']) ? esc_attr($_REQUEST['topic']) : '';
	$html = $text;

    if(!$topic){
        print json_encode(array('status'=>401,'msg'=>'请输入话题'));
		exit;
    }
    $videos = array_filter($videos);
	if($videos){
		foreach ($videos as $video) {
			$html .= '<div class="content-video-box content-video content-box" data-video-url="'.esc_url($video['src']).'" data-video-thumb="'.esc_url($video['img']).'" data-video-title="'.esc_attr($video['title']).'"><span class="pos-a img-bg" style="background-image:url('.esc_url($video['img']).')"></span></div>';
		}
	}
	if($imgs){
		foreach ($imgs as $img) {
			$html .= '<p><img src="'.esc_url($img).'" /></p>';
		}
	}

    $user_custom_data = get_user_meta($user,'zrz_user_custom_data',true);
    $url = preg_replace( '/^https?:\/\//', '', home_url() );
    $url = str_replace('.','_',$url);
    $arg = array(
        'comment_author' => $current_user->display_name,
        'comment_author_email' => isset($current_user->user_email) && !empty($current_user->user_email) ? $current_user->user_email : $url.$user.'@163.com',
        'comment_author_url' => '',
        'comment_content' => $text,
        'referrer'=>home_url('/bubble')
    );
    $check = zrz_check_spam($arg);

    if($check){
        print json_encode(array('status'=>401,'msg'=>'请不要发送垃圾信息！'));
		exit;
    }

    $term = get_term_by('name', $topic, 'mp');

    if(!$term){
        $resout = wp_insert_term(
           $topic,
          'mp',
          array(
            'slug' => $topic,
          )
        );

        if(is_wp_error( $resout )){
            $id = $resout->error_data;
            $id = $id['term_exists'];
        }else{
            $id = $resout['term_id'];
        }
        $topic = $id;
    }else{
        $topic = $term->term_id;
    }

    if(strlen($html) >= 2){
        if (!isset($_SESSION)) {
    	    session_start();
    	}
        $_SESSION['zrz_credit_add'] = 1;
        
        if(current_user_can('edit_users')){
            $status = 'publish';
        }else{
            $status = 'pending';
        }

        if(zrz_get_display_settings('bubble_check') == 0){
            $status = 'publish';
        }

        $po_arr = array(
                'post_title' => (strlen($text) > 1) ? zrz_get_content_ex($text,100) : '无标题' ,
                'post_content' => $html,
                'post_status' => $status,
                'post_author' => $user,
                'post_type'=>'pps',
                'tax_input' => array(
                    'mp' => array($topic)
                ),
                'comment_status'=>'open'
        );
        unset($html);
        $post_id = wp_insert_post( $po_arr );
        unset($po_arr);
        if($post_id){
            $query = new WP_Query('post_type=pps&p='.$post_id);
            if ( $query->have_posts() ) {
                ob_start();
               while ( $query->have_posts() ) {
                    $query->the_post();
                    get_template_part( 'formats/content','bubble');
               }
               $content = ob_get_clean();
               wp_reset_postdata();
            }

            print json_encode(array('status'=>200,'msg'=>$content));
            unset($content);
            exit;
        }
    }else{
        print json_encode(array('status'=>401,'msg'=>'字数太少'));
        exit;
    }
}

//提取冒泡的图片
function zrz_get_pp_content_img($post_id = 0){
    if(!$post_id){
        global $post;
        $content = $post->post_content;
        $post_id = $post->ID;
    }else{
        $content = get_post_field('post_content',$post_id);
    }
	preg_match_all('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',strip_shortcodes($content), $pics );
	$pics_count = count($pics[1]);
    $images = $pics[1];
	$html = $class = $height = $width = '';
    $home_url = home_url();

    $arr = array();
    //排除表情
    for ($i=0; $i < $pics_count; $i++) {
        if(strpos($images[$i],'smilies') === FALSE){
            $arr[] = $images[$i];
        }
    }
    $count = count($arr);

	if($count == 2){
        $width = '220';
        $height = '220';
    }elseif($count > 2){
        $width = '120';
        $height = '120';
    }

    if($count == 1){
		$html .= '<div class="pps-img-one mar10-t mar10-b" '.(zrz_getExt($arr[0]) == 'gif' ? 'data-img-gif="'.$arr[0].'" data-gif-text="播放"' : '').'><img src="'.zrz_get_thumb($arr[0],400,'full',0,false,false).'"></div>';
	}elseif($count >= 2){
        $html .= '<ul id="img-ul-small-'.$post_id.'" class="pp-list-img mar10-t mar10-b">';
        for ($i=0; $i < $count  ; $i++) {
            $html .= '<li id="img-small-'.$i.'-in-'.$post_id.'" class="pps-img-small fd mouh pos-r" data-postid="'.$post_id.'" data-index="'.$i.'"><img src="'.zrz_get_thumb($arr[$i],$width,$height,0,false,false).'"></li>';
        }
        $html .= '</ul><ul class="pps-img-big mar10-t">';
		for ($i=0; $i < $count  ; $i++) {
            $html .= '<li id="img-big-'.$i.'-in-'.$post_id.'" class="pps-img-big hide"><img src="'.zrz_get_thumb($arr[$i],400,'full',0,false,true).'"></li>';
        }
        $html .= '</ul>';
	}

	return $html;
}

//提取视频网址
function zrz_get_bubble_video($post_id = 0){
    if(!$post_id){
        global $post;
        $content = $post->post_content;
    }else{
        $content = get_post_field('post_content',$post_id);
    }

    $regex="/<div class=\"content-video-box content-video content-box\".*?><\/div>/ism";
    $html = '';
    if(preg_match_all($regex, $content, $matches)){
        $video = $matches[0];
        foreach ($video as $val) {
            $html .= $val;
        }
       return $html;
    }
}

add_action('wp_ajax_zrz_pending_bubble','zrz_pending_bubble');
function zrz_pending_bubble(){
    $post_id = isset($_POST['pid']) ? (int)$_POST['pid'] : 0;
    if(!current_user_can('edit_users')) {
        print json_encode(array('status'=>401,'msg'=>__('权限不足')));
        exit;
    }

    wp_publish_post( $post_id );
    print json_encode(array('status'=>200,'msg'=>__('审核成功')));
    exit;
    
}