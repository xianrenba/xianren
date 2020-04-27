<?php
/*
* 分类页面的设置项目
*/

//分类页面的背景图片
add_action( 'wp_ajax_zrz_upload_category_image', 'zrz_upload_category_image' );
function zrz_upload_category_image(){
    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : '';
    if(!$cat_id || !current_user_can('delete_users')){
        exit;
    }
    $upload = new zrz_media($_FILES['file'],'cat',$cat_id,'',0);
    $resout = $upload->media_upload();
    print $resout;
    exit;
}

add_action( 'wp_ajax_zrz_update_category_blur', 'zrz_update_category_blur' );
function zrz_update_category_blur(){
    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : '';
    if(!$cat_id || !current_user_can('delete_users')){
        print json_encode(array('status'=>401,'msg'=>'权限不足'));
        exit;
    }
    $img = get_term_meta($cat_id,'cat_img',true);
    $img = is_array($img) ? $img : array();
    $img['blur'] = (int)!$img['blur'];
    if(update_term_meta($cat_id,'cat_img',$img)){
        print json_encode(array('status'=>200,'msg'=>$img));
        exit;
    }
}

//存储分类背景图片
function zrz_get_category_meta($cat_id,$type,$size = array()){
    $img = get_term_meta($cat_id,'cat_img',true);
    if($img){
        if(isset($img['image']) && $type === 'image'){
            if(!empty($size)){
                return zrz_get_thumb(zrz_get_media_path().'/'.$img['image'],$size[0],$size[1],false,false);
            }else{
                return zrz_get_thumb(zrz_get_media_path().'/'.$img['image'],(int)zrz_get_theme_settings('page_width'),190,false,false);
            }
        }elseif(isset($img['blur']) && $type === 'blur'){
            return $img['blur'];
        }
    }
    return 0;
}
