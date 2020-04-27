<?php
//微信分享
add_action( 'wp_ajax_zrz_get_wxshare_data', 'zrz_get_wxshare_data' );
add_action( 'wp_ajax_nopriv_zrz_get_wxshare_data', 'zrz_get_wxshare_data' );
function zrz_get_wxshare_data(){
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if(!$id){
        print json_encode(array('status'=>401,'msg'=>'参数错误'));
        exit;
    }

    require_once ZRZ_THEME_DIR .'/inc/SDK/Wxjs/jssdk.php';
    $jssdk = new JSSDK(zrz_get_social_settings('open_weixin_gz_key'), zrz_get_social_settings('open_weixin_gz_secret'));
    $signPackage = $jssdk->GetSignPackage();

    if($signPackage){
        $title = get_the_title($id);
        $des = zrz_get_post_des($id);
        $link = get_permalink($id);
        $img = zrz_get_post_thumb($id);

        print json_encode(array('status'=>200,'msg'=>$signPackage,'post_data'=>array(
            'imgUrl'=>$img,
            'link'=>$link,
            'desc'=>$des,
            'title'=>$title
        )));
	    exit;
    }else{
        print json_encode(array('status'=>401,'msg'=>$signPackage));
	    exit;
    }

}

