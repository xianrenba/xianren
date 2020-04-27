<?php
    //浏览器是否支持 webp 格式的图片？
    function zrz_is_support_webp(){
        if(!zrz_get_media_settings('webp')) return false;
        $webp = zrz_getcookie('zrz_webp');
        if(!$webp) {
            $server = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
            $webp = strstr($server,'image/webp') ? 1 : 0;
            zrz_setcookie('zrz_webp',$webp,3600*24*30*12);
        }

        return $webp;
    }

    /*
    * 生成缩略图
    * $thumb 图片的 url （必须）（string）
    * $width 图片要裁剪的宽度（int）
    * $height 图片要裁剪的高度，如果为零则高度自适应（int）
    * $crop_y 图片要裁剪的Y坐标 （int）
    * $crop_only 直接裁剪还是缩放后裁剪 （bool）
    */
    function zrz_get_thumb($thumb,$width='160',$height='100',$crop_y = 0,$crop_only = false,$gif = true){

        //quality 为要生成图片的质量 0-100，默认96
        if($height == 'full'){
            $params = array( 'width' => $width, 'quality' => 96);
        }else{
            $params = array( 'width' => $width, 'height' => $height ,'crop' => true,'crop_only'=>$crop_only,'crop_y'=>$crop_y ? $crop_y : 0 ,'quality' => 96);
        }


        //不存在图片，则返回空
        if(!$thumb) return '';

        $place = zrz_get_media_settings('media_place');
        $webp = zrz_is_support_webp();

        //如果是头像，直接返回
        if(strpos($thumb,'/uploads/avatar') !== false){
            return $thumb;
        }

        //如果是新浪GIF图片
        if(strpos($thumb,'sinaimg.cn') !== false && zrz_getExt($thumb) == 'gif'){
            if($gif){
                return $thumb;
            }
            $thumb = str_replace(array('large','mw690','thumbnail'),'thumb180',$thumb);
            return $thumb;
        }

        //如果是本地图片
        $thumb_local = strpos($thumb,home_url());

        if(($place === 'localhost' && $thumb_local !== false) || $thumb_local !== false){
            if(zrz_getExt($thumb) == 'gif' && $gif){
                return $thumb;
            }
            return bfi_thumb($thumb,$params);
        }elseif($place === 'aliyun'){
            $webp = $webp == 1 ? '/format,webp' : '';
            $gif = $gif ? '' : '/format,jpg';
            if($crop_y){
                return $thumb.'?x-oss-process=image/resize,w_'.$width.'/crop,x_0,y_'.$crop_y.',w_'.$width.',h_'.$height.$webp;
            }elseif($height === 'full'){
                return $thumb.'?x-oss-process=image/resize,w_'.$width.$webp.$gif;
            }else{
                return $thumb.'?x-oss-process=image/resize,m_fill,limit_0,h_'.$height.',w_'.$width.$webp.$gif;
            }

        }elseif($place === 'qiniu'){
            $webp = $webp ? '/format/webp' : '';
            $gif = $gif ? '' : '/format/jpg';
            if($crop_y){
                return $thumb.'?imageMogr2/thumbnail/'.$width.'x/crop/!'.$width.'x'.$height.'a0a'.$crop_y.$webp.$gif;
            }elseif($height == 'full'){
                return $thumb.'?imageMogr2/auto-orient/thumbnail/'.$width.'x1000<'.$webp;
            }else{
                return $thumb.'?imageMogr2/auto-orient/thumbnail/!'.$width.'x'.$height.'r/gravity/Center/crop/'.$width.'x'.$height.'/quality/85/ignore-error/1'.$webp;
            }
        }
        return $thumb;
    }

    /*
    * 获取评论中第 N 张图片
    * $content 通常为文章内容,也可以是其他任意字符串(string)
    * $i 返回第几章图片 (int)
    */
    function zrz_get_first_img($content,$i = 0) {
        preg_match_all('~<img[^>]*src\s?=\s?([\'"])((?:(?!\1).)*)[^>]*>~i', $content, $match,PREG_PATTERN_ORDER);

        if(is_numeric($i)){
            return isset($match[2][$i]) ? esc_url($match[2][$i]) : '';
        }elseif($i == 'all'){
            return $match[2];
        }else{
            return isset($match[2][0]) ? esc_url($match[2][0]) : '';
        }
    }

    /*
    * 获取文章缩略图
    */
    function zrz_get_post_thumb($post_id = 0,$type = false){

        if(!$post_id) {
            global $post;
            $post_id = $post->ID;
            $content = $post->post_content;
        }else{
            $content = get_post_field('post_content', $post_id);
        }
        $post_thumbnail_url = get_the_post_thumbnail_url($post_id);

        //如果存在特色图，则返回特色图
        if($post_thumbnail_url){
            return esc_url($post_thumbnail_url);
        }

        //如果没有特色图则返回文章第一张图片
        if(!$type){
            return zrz_get_first_img($content,0);
        }else{
            return '';
        }


    }

    //图片路径
    function zrz_get_media_path(){
        $upload_dir =  zrz_upload_dir(wp_get_upload_dir());
        return trim($upload_dir['baseurl'],'/');
    }
