<?php
//申请链接
add_action('wp_ajax_zrz_insert_link','zrz_insert_link');
add_action('wp_ajax_nopriv_zrz_insert_link', 'zrz_insert_link');
function zrz_insert_link(){
    $link_url = isset($_POST['link_url']) ? trim(sanitize_text_field(htmlspecialchars($_POST['link_url'], ENT_QUOTES))) : '';
    $link_name = isset($_POST['link_name']) ? trim(sanitize_text_field(htmlspecialchars($_POST['link_name'], ENT_QUOTES))) : '';
    $link_image = isset($_POST['link_image']) ? esc_url($_POST['link_image']) : '';
    $link_category = isset($_POST['link_category']) ? trim(sanitize_text_field(htmlspecialchars($_POST['link_category'], ENT_QUOTES))) : '';
    $link_description = isset($_POST['link_description']) ? trim(sanitize_text_field(htmlspecialchars($_POST['link_description'], ENT_QUOTES))) : '';
    $current_user = get_current_user_id();
    $nub = (int)get_user_meta($current_user,'zrz_links_nub',true);

    if(!is_user_logged_in()){
        print json_encode(array('status'=>401,'msg'=>'请先登录'));
        exit;
    }

    if($nub > 3 ){
        print json_encode(array('status'=>401,'msg'=>'不可重复申请'));
        exit;
    }

    if($link_url && $link_name && $link_category && $link_description){
        $linkdata = array(
            'link_url'=>$link_url,
            'link_name'=>$link_name,
            'link_image'=>$link_image,
            'link_category'=>$link_category,
            'link_owner'=>$current_user,
            'link_description'=>$link_description,
            'link_visible'=> 'N'
        );
        $link_id = wp_insert_link( $linkdata );
        if($link_id){
            $init = new Zrz_Credit_Message(1,23);
            $add_msg = $init->add_message($current_user,0,0,$link_name.'<br>'.$link_url);
            if($add_msg){
                update_user_meta($current_user,'zrz_links_nub',$nub + 1);
            }
            print json_encode(array('status'=>200,'msg'=>'申请成功'));
            exit;
        }else{
            print json_encode(array('status'=>401,'msg'=>$link_id));
            exit;
        }
    }
    print json_encode(array('status'=>201,'msg'=>'申请失败，请完善资料'));
    exit;
}

//导航链接投票
add_action('wp_ajax_zrz_link_add_rating','zrz_link_add_rating');
add_action('wp_ajax_nopriv_zrz_link_add_rating', 'zrz_link_add_rating');
function zrz_link_add_rating(){
    $link_id = isset($_POST['link_id']) ? (int)$_POST['link_id'] : 0;
    $rating_count = (int)get_bookmark_field('link_rating',$link_id);

    if(!$link_id) exit;
    $ac = 0;
    if(isset($_COOKIE['zrz_link_rating'])){
        $rating_arr = unserialize(stripslashes($_COOKIE['zrz_link_rating']));
        $rating_arr = is_array($rating_arr) ? $rating_arr : array();
        if($rating_arr[$link_id] === 1){
            $rating_arr[$link_id] = 0;
            $rating_count = $rating_count - 1;
        }else{
            $rating_arr[$link_id] = 1;
            $rating_count = $rating_count + 1;
            $ac = 1;
        }
        zrz_setcookie('zrz_link_rating',serialize($rating_arr));
    }else{
        $rating = array();
        $rating[$link_id] = 1;
        zrz_setcookie('zrz_link_rating',serialize($rating));
        $rating_count = $rating_count + 1;
        $ac = 1;
    }

    //写入
    global $wpdb;
    $table_name = $wpdb->prefix . 'links';
    $sql = "UPDATE $table_name SET link_rating = ".$rating_count." WHERE link_id = $link_id";
    if($wpdb->query( $sql )){
        print json_encode(array('status'=>200,'msg'=>'投票成功','ac'=>$ac));
        exit;
    }else{
        print json_encode(array('status'=>401,'msg'=>'投票失败'));
        exit;
    }
}

//为导航链接页面增加站长ID项
function link_add_meta_box() {

		add_meta_box(
			'link_owner',
			__( '站长ID', 'ziranzhi2' ),
			'link_meta_box_callback',
			'link',
            'normal',
             'high'
		);
}
add_action( 'add_meta_boxes', 'link_add_meta_box' );

//增加站长ID选项回调
function link_meta_box_callback( $link ) {

    if(isset($link->link_id)){
        $value = get_bookmark($link->link_id);
        $value = $value->link_owner;
    }else{
        $value = '';
    }

	echo '<input type="text" id="link_owner" name="link_owner" value="' . esc_attr( $value ) . '" size="25" />';
    echo '<p>请输入此站长在本站的用户ID，如果站长没有在本站注册，请留空此项。</p>';
}

//保存
function link_save_meta_box_data($link_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'links';
    if($link_id){
        $sql = "UPDATE $table_name SET link_owner = ".(int)$_POST['link_owner']." WHERE link_id = $link_id";
        $wpdb->query( $sql );
    }
}
add_action( 'edit_link', 'link_save_meta_box_data' );
