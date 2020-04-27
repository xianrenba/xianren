<?php
/*
* 与头像相关的函数
*/

//生成字母头像
class Get_Letter_Avatar{
	static public $letter;
	static public $size;

	public function __construct($letter,$size){
		self::$letter = $letter ? $letter :'空';
		self::$size = $size;
	}

	//生成字母头像
	public function get_letter_avatar(){

		//如果没有安装 GD 返回空
		if(!function_exists('imagecreate')) return '';

		//获取文字
		$letter = self::get_letter();

		//如果头像存在，直接返回头像
		$letter_md5 = md5(strtolower(trim($letter)));

		$avatar_base_dir = 'uploads'.DIRECTORY_SEPARATOR.'avatar'.DIRECTORY_SEPARATOR.$letter_md5.'.png';
		$avatar_base_uri = 'uploads/avatar/'.$letter_md5.'.png';

		if(file_exists(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$avatar_base_dir)){
	        $avatar = home_url('/').'wp-content/'.$avatar_base_uri;
	    }

		//检查字体是否存在
		if(!file_exists(ZRZ_THEME_DIR.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'SourceHanSansCN-Normal.ttf')){
			return '';
		}

		//创建一个 100*100 的空白图像
		$image = imagecreatetruecolor(300,300);

		//背景颜色
		$bgcolor = imagecolorallocate($image,255,255,255);

		//设置透明
		imagecolortransparent($image,$bgcolor);

		//填充到图像中
		imagefill($image,0,0,$bgcolor);

		//字体路径
		$font = dirname(__FILE__).DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.'SourceHanSansCN-Normal.ttf';

		//字体颜色
		$fontcolor = imagecolorallocate($image,250,250,250);

		//获取字体的真实宽高
		if(function_exists('imagettfbbox')){
			$box = imagettfbbox(180, 0, $font, $letter);
			$txtw = $box[2];
			$txth = $box[1]+$box[7];
		}else{
			$txtw = $txth = 10;
		}

		//坐标
		$x = 148-($txtw/2);
		$y = 150-($txth/2);
		$anger = 0;

		//生成字体
		ImageTTFText($image,180,$anger,$x,$y,$fontcolor,$font,$letter);

		ob_start();
		ImagePng($image);
		$image = ob_get_contents();
		ob_end_clean();

		$upload_file = @file_put_contents(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$avatar_base_dir, $image );
		if($upload_file) {
			$avatar = home_url('/').'wp-content/'.$avatar_base_uri;
		}

		return apply_filters('zrz_get_letter_avatar',$avatar);
	}

	//获取第一个字符，或者最后一个字符
	public function get_letter(){
		$start = zrz_get_media_settings('avatar_first');
		if($start === '1'){
			$name = mb_substr( self::$letter, 0 ,1,"utf-8");
		}else{
			$name = '/(?<!^)(?!$)/u';
			$name = preg_split($name, self::$letter );
			$name = end($name);
		}
		//如果不存在则返回文字“空”
		if(!$name && $name != 0){
			$name = '空';
		}

		return $name;
	}
}

//替换默认头像
function zrz_get_avatar_cus($avatar, $id_or_email, $size, $default, $alt ) {

	if($id_or_email === 'email@example.com') return $avatar;

	$default_avatar = zrz_get_media_settings('auto_avatar');

	if( is_object($id_or_email) ) {

		if($default_avatar){
			$user_id = $id_or_email->user_id === '0' ? md5(home_url()).'-'.$id_or_email->comment_author : $id_or_email->user_id;
		}else{
			$user_id = $id_or_email->user_id === '0' ? $id_or_email->comment_author_email : $id_or_email->user_id;
		}

	}elseif(is_email($id_or_email)){

		$user_id = email_exists($id_or_email);

	}else{
		$user_id = $id_or_email;
	}

	return '<img src="'.zrz_get_avatar($user_id,$size).'" class="avatar" width="'.$size.'" height="'.$size.'" style="background-color:'.(!is_numeric($user_id) ? zrz_get_avatar_background_by_id(rand(0,9)) : zrz_get_avatar_background_by_id($user_id)).'"/>';
};
add_filter( 'get_avatar', 'zrz_get_avatar_cus', 1, 5 );


//后台设置项中不显示自定义的头像
function zrz_default_avatar_select($avatar_list){

	global $avatar_defaults;

	$avatar = explode('<br />', $avatar_list );

	$content = '';

	$i = 0;
	foreach( $avatar_defaults as $default_key=>$default_value ){
		$content .= preg_replace( '/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', '<img src="'.get_avatar_url('email@example.com', array('default'=>$default_key)).' class="avatar" width="32" height="32">', $avatar[$i] ) . '<br />';
		$i++;
	}

	return $content;
}
add_filter('default_avatar_select', 'zrz_default_avatar_select');

//动态获取头像
add_action( 'wp_ajax_zrz_change_avatar', 'zrz_change_avatar' );
add_action( 'wp_ajax_nopriv_zrz_change_avatar', 'zrz_change_avatar' );
function zrz_change_avatar(){
	$name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : false;
	if($name){
		print json_encode(array('status'=>200,'src'=>esc_url(zrz_get_avatar(md5(home_url()).'-'.$name,40))));
	    exit;
	}
	print json_encode(array('status'=>401));
    exit;
}

//获取头像
function zrz_get_avatar($user_id,$size){

	if(strpos($user_id, md5(home_url()).'-') !== false){
		$user_name = str_replace(md5(home_url()).'-','',$user_id);
		$avatar = new Get_Letter_Avatar($user_name,$size);
		return zrz_get_thumb($avatar->get_letter_avatar(),$size,$size,0,false);
	}else{
		$user_data = new zrz_get_user_data($user_id,$size);
		return $user_data->get_avatar();
	}
}
