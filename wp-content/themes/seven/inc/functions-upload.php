<?php
/*
* 文件上传
*/

class zrz_media{

    public static $mime;//文件格式
    public static $type;//文件是什么类型？头像还是文章图片..
    public static $file;//文件
    public static $user_id;//当前用户ID
    public static $is_letter_avatar;//如果非空，则为用户名
    public static $subdir;//日期目录
    public static $cover_y;//用户封面图片裁剪的坐标
    public static $key;//文件名
    public static $keyi;//文件名，没有后缀

    public function __construct($file,$type = '',$user_id = 0,$is_letter_avatar = '',$cover_y = 0){

        self::$type = $type;
        self::$file = $file;

        //如果没有指定用户ID，则使用当前登陆的用户ID
        if(!$user_id){
            self::$user_id = get_current_user_id();
        }else{
            self::$user_id = $user_id;
        }

        //是否是头像上传
        if($is_letter_avatar){
            self::$is_letter_avatar = $is_letter_avatar;
        }

        //获取文件的扩展名
        $path = self::$file['name'];
        self::$mime = pathinfo($path, PATHINFO_EXTENSION);

        //用户封面图片裁剪的Y坐标
        if($cover_y)
        self::$cover_y = $cover_y;

        //路径
        $path = wp_upload_dir();
        //$upload_dir = zrz_upload_dir($path);
        self::$subdir = trim($path['subdir'],'/');

        //文件名
        self::$key = self::get_media_key();
    }

    //获得媒体文件名
    public static function get_media_key(){

        if(self::$key) return self::$key;

        //如果是字母头像，文件名直接是用户名的 md5
        if(self::$is_letter_avatar){
            return md5(strtolower(trim(self::$is_letter_avatar))).'_a_c.gif';
        }

        $key = self::$user_id.str_shuffle(uniqid());
        $ext = '.'.self::$mime;
        self::$keyi = $key;
        //如果是图片，为图片添加后缀，已示区分
        switch (self::$type) {
            case 'cover':
                return $key.'_cover'.$ext;
                break;
            case 'avatar':
                return $key.'_avatar'.$ext;
                break;
            case 'open':
                return $key.'_open_avatar'.$ext;
                break;
            case 'weixin':
                return $key.'_weixin'.$ext;
                break;
            case 'alipay':
                return $key.'_alipay'.$ext;
                break;
            case 'cat':
                return $key.'_cat'.$ext;
                break;
            case 'weixin':
                return $key.'_weixin'.$ext;
                break;
            case 'alipay':
                return $key.'_alipay'.$ext;
                break;
        default:
                return $key.$ext;
                break;
        }
    }

    //文件上传
    public static function media_upload(){

        //未登录用户返回错误
        if(!is_user_logged_in()) {
            return json_encode(array('status'=>401,'msg'=>__('请先登录','ziranzhi2')));
        }

        //检查数据完整性
        if(!self::$mime || !self::$type || !self::$file){
            return json_encode(array('status'=>401,'msg'=>__('参数不全！','ziranzhi2')));
        }

        $type = self::$type;
        $id = '';

        /*
        * 一下类型的图片，都不用在数据库中生成图像属性数据，直接上传
        * cover 用户封面图片
        * avatar 用户头像
        * comment 评论图片
        * small 文章图片->小图
        * cat 分类图片
        * guess 研究所->你猜
        * guessResouts 研究所->你猜->结果
        * vote 研究所->投票
        */

        if($type === 'cover' || $type === 'avatar' || $type === 'comment' || $type === 'small' || $type === 'cat' || $type === 'guess' || $type === 'guessResouts' || $type === 'vote' || $type === 'weixin' || $type === 'alipay'){

            //检查扩展名
            $ext_check = self::ext_check(array('jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF'));
            if($ext_check){
                return $ext_check;
            }

            //直接上传文件
            add_filter('upload_dir', 'zrz_upload_dir', 100, 1);
            $resout = wp_upload_bits(self::$key, null, file_get_contents(self::$file["tmp_name"]));
        }else{
            // 上传文件，并储存 Attachment 信息
            add_filter('upload_dir', 'zrz_upload_dir', 100, 1);
            require_once( ABSPATH . 'wp-admin/includes/file.php' );

            $upload_overrides = array( 'test_form' => false,'unique_filename_callback' => array(__CLASS__, 'get_media_key') );
            $resout = wp_handle_upload(self::$file, $upload_overrides);

            $attachment = array(
                'post_title' => self::$keyi,
                'post_content' => '',
                'post_type' => 'attachment',
                'post_parent' => 0,
                'post_mime_type' => self::$file['type'],
                'guid' => $resout['url']
            );

            $id = wp_insert_attachment( $attachment,$resout[ 'file' ], 0 );
            wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $resout[ 'file' ] ) );

        }

        //返回结果
        if($resout['error']){
            return json_encode(array('status'=>401,'msg'=>$resout['error']));
        }else{
            if($type === 'cover'){
                $url = self::update_user_data('cover');
            }elseif($type === 'avatar'){
                $url = self::update_user_data('avatar');
            }elseif($type === 'cat'){
                $url = self::update_user_data('cat');
            }elseif($type === 'weixin'){
                $url = self::update_user_data('weixin');
            }elseif($type === 'alipay'){
                $url = self::update_user_data('alipay');
            }else{
                $url = zrz_get_thumb($resout['url'],180,100);
            }
            if($url){
                return json_encode(array('status'=>200,'msg'=>__('上传成功','ziranzhi2'),'url'=>$url,'Turl'=>$resout['url'],'imgdata'=>$id));
            }else{
                return json_encode(array('status'=>401,'msg'=>__('数据写入失败','ziranzhi2'),'url'=>$url));
            }
        }
    }

    //扩展名验证
    public static function ext_check($arr){
        if(!in_array(self::$mime,$arr)){
            return json_encode(array(
				'status'=>401,
				'msg'=> printf(
						esc_html__( '不允许使用 %1$s 格式的文件', 'ziranzhi2' ),
						self::$mime
					)
			    )
			);
        }else{
            return false;
        }
    }

    //更新用户信息并返回
    public static function update_user_data($type){
        $user_code = get_user_meta(self::$user_id,'zrz_qcode',true);
        $user_code = is_array($user_code) ? $user_code : array();
        if($type === 'cover'){
            $resout = new zrz_update_user_data(
                'cover',
                array(
                    'key'=>self::$subdir.'/'.self::$key,
                    'top'=>self::$cover_y
                ),
                self::$user_id
            );
            $cover = $resout->update_user_meta();
            if($cover){
                $url = new zrz_get_user_data(self::$user_id);
                return $url->get_cover();
            }
        }elseif($type === 'avatar'){
            $resout = new zrz_update_user_data(
                'avatar',
                self::$subdir.'/'.self::$key,
                self::$user_id,
                'default'
            );
            $avatar = $resout->update_user_meta();
            if($avatar){
                $url = new zrz_get_user_data(self::$user_id,140);
                return $url->get_avatar('default');
            }
        }elseif($type === 'cat'){
            $meta = get_term_meta(self::$user_id,'cat_img',true);
            $meta = is_array($meta) ? $meta : array();
            $meta['image'] = self::$subdir.'/'.self::$key;
            if(update_term_meta(self::$user_id,'cat_img',$meta))
            return zrz_get_thumb(zrz_get_media_path().'/'.self::$subdir.'/'.self::$key,(int)zrz_get_theme_settings('page_width'),240,false,false);
        }elseif($type === 'weixin'){
            $user_code['weixin'] = self::$subdir.'/'.self::$key;
        }elseif($type === 'alipay'){
            $user_code['alipay'] = self::$subdir.'/'.self::$key;
        }
        if($type === 'weixin' || $type === 'alipay'){
            update_user_meta(self::$user_id,'zrz_qcode',$user_code);
            return zrz_get_thumb(zrz_get_media_path().'/'.self::$subdir.'/'.self::$key,160,'full',false,false);
        }
    }
}

//初始化上传SDK
add_action('init', 'zrz_upload_init', 1);
function zrz_upload_init(){
    $media_setting = zrz_get_media_settings('media_place');
    $yun_setting = zrz_get_media_settings($media_setting);
    if($media_setting === 'localhost') return;
    if($media_setting === 'aliyun'){
        define('OSS_ACCESS_ID', trim($yun_setting['access_key'],' '));
        define('OSS_ACCESS_KEY', trim($yun_setting['access_key_secret'],' '));
        define('OSS_ENDPOINT', trim($yun_setting['endpoint'],' '));
        require_once('SDK/OSSWrapper.php');
    }

    //add_filter('upload_dir', 'zrz_upload_dir',101,1);

}

//自定义上传路径
function zrz_upload_dir($param){
    $media_setting = zrz_get_media_settings('media_place');
    if($media_setting === 'localhost') return $param;
    $yun_setting = zrz_get_media_settings($media_setting);
    switch ($media_setting) {
        case 'aliyun':
            $stream = 'oss';
            break;
        case 'qiniu':
            $stream = 'qiniu';
            break;
		case 'upyun':
            $stream = 'upyun';
            break;
        default:
            $stream = '';
            break;
    }
    if($yun_setting['host'] && $stream){
        if(isset($yun_setting['path']) && !empty($yun_setting['path'])){
            $param['basedir'] = $stream.'://'.trim($yun_setting['bucket'], '/').'/'.trim($yun_setting['path'],'/');
            $param['path'] = $param['basedir'] .'/'. trim($param['subdir'],'/');
            $param['baseurl'] = trim($yun_setting['host'], '/').'/'.trim($yun_setting['path'], '/');
            $param['url'] = $param['baseurl'] .'/'. trim($param['subdir'],'/');
        }else{
            $param['basedir'] = $stream.'://'.trim($yun_setting['bucket'], '/');
            $param['path'] = $param['basedir'] .'/'. trim($param['subdir'],'/');
            $param['baseurl'] = trim($yun_setting['host'], '/');
            $param['url'] = $param['baseurl'] .'/'. trim($param['subdir'],'/');
        }
    }
    return $param;
}

//删除附件
add_action('delete_attachment', 'zrz_upload_delete_thumbnail');
function zrz_upload_delete_thumbnail($id, $data=array()) {
    $dir = zrz_upload_dir(wp_upload_dir());
    $attachment = wp_get_attachment_url($id);
    $file = str_replace($dir['baseurl'],$dir['basedir'],$attachment);
    if(@file_exists($file)) @unlink($file);
}

//去掉默认的缩略图。
add_filter('pre_option_thumbnail_size_w',	'__return_zero' );
add_filter('pre_option_thumbnail_size_h',	'__return_zero' );
add_filter('pre_option_medium_size_w',		'__return_zero' );
add_filter('pre_option_medium_size_h',		'__return_zero' );
add_filter('pre_option_large_size_w',		'__return_zero' );
add_filter('pre_option_large_size_h',		'__return_zero' );

//禁止生成缩略图的数组
function zrz_intermediate_image_sizes_advanced($size){
	if(isset($sizes['full'])){
		return array('full'=>$sizes['full']);
	}else{
		return array();
	}
}
add_filter('intermediate_image_sizes_advanced','zrz_intermediate_image_sizes_advanced' );

// 保留原图
function zrz_image_size_names_choose($size){
   if(isset($sizes['full'])){
       return array('full'=>$sizes['full']);
   }else{
       return array();
   }
}
add_filter('image_size_names_choose','zrz_image_size_names_choose');
add_filter('wp_calculate_image_srcset_meta', '__return_empty_array');

//媒体库修改尺寸
function zrz_prepare_attachment_for_js($response, $attachment, $meta){
    $media_setting = zrz_get_media_settings('media_place');
    if($media_setting === 'localhost') return $response;
	if(isset($response['sizes'])){
		$orientation	= $response['sizes']['full']['orientation'];

        $size = array(
            'thumbnail'=>150,
            'medium'=>300,
            'medium_large'=>768,
            'large'=>1024
        );
        $image_src = wp_get_attachment_image_src($attachment->ID);
        foreach ($size as $key => $val) {
            $response['sizes'][$key]	= array(
				'url'			=> zrz_get_thumb($image_src[0],$val,$val),
				'width'			=> $val,
				'height'		=> $val,
				'orientation'	=> $orientation
			);
        }
	}
	return $response;
}
add_filter('wp_prepare_attachment_for_js', 'zrz_prepare_attachment_for_js', 10, 3);

add_filter('wp_get_attachment_url', 'zrz_get_attachment_url', 9999, 2);
function zrz_get_attachment_url($url, $id){
    $media_setting = zrz_get_media_settings('media_place');
    if($media_setting === 'localhost') return $url;
    $url = get_the_guid($id);
    $c_url = wp_get_upload_dir();
    $param = zrz_upload_dir(wp_get_upload_dir());

    if(strpos($url,$param['baseurl']) !== false){
        return $url;
    }else{
        return str_replace($c_url['baseurl'],$param['baseurl'],$url);
    }
}

//后台启动上传
add_action('admin_init', 'zrz_admin_init', 1);
function zrz_admin_init() {
    $media_setting = zrz_get_media_settings('media_place');
    $yun_setting = zrz_get_media_settings($media_setting);

    if($media_setting === 'localhost') return;
    if(has_filter('upload_dir', 'zrz_upload_dir')) return;
    global $pagenow;

    $action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
    if(in_array($action, array('upload-plugin', 'upload-theme'))) return;
    add_filter('upload_dir', 'zrz_upload_dir', 100, 1);
}
