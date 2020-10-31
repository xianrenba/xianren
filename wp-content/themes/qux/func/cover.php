<?php

require_once(dirname(__FILE__)."/../../../../wp-load.php");
add_action('wp_ajax_nopriv_create-bigger-image','get_bigger_img');
add_action('wp_ajax_create-bigger-image','get_bigger_img');

//输出缩略图地址
function post_thumbnail_src( $post = null ){
	if( $post === null ){
    	global $post;
	}

   	if( has_post_thumbnail( $post ) ){    //如果有特色缩略图，则输出缩略图地址
        $post_thumbnail_src = get_post_thumbnail_id($post->ID);
    } else {
        $post_thumbnail_src = '';
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        if(!empty($matches[1][0])){

        	global $wpdb;
        	$att = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s'", $matches[1][0] ) );

        	if( $att ){
        		$post_thumbnail_src = $att->ID; 
        	}else{
        		$post_thumbnail_src = $matches[1][0]; 
        	}
            
        }else{
			$post_thumbnail_src = get_template_directory_uri().'/img/rand/'.rand(1,15).'.jpg';
        }
    }
    return $post_thumbnail_src;
}

/**
 * 图像裁切
 */


function timthumb( $src, $size = null, $set = null ){

	$modular = _hui('thumbnail_handle');

	if( is_numeric( $src ) ){
		if( $modular == 'timthumb_mi' ){
			$src = image_downsize( $src, $size['w'].'-'.$size['h'] );
		}else{
			$src = image_downsize( $src, 'full' );
		}
		$src = $src[0];
	}

	if( $set == 'original' ){
		return $src;
	}

	if( $modular == 'timthumb_php' || empty($modular) || $set == 'tim' ){

		return get_stylesheet_directory_uri().'/func/timthumb.php?src='.$src.'&h='.$size["h"].'&w='.$size['w'].'&zc=1&a=c&q=100&s=1';

	}else{
		return $src;
	}	

} 

function substr_ext($str, $start = 0, $length, $charset = 'utf-8', $suffix = ''){
    if (function_exists('mb_substr')) {
        return mb_substr($str, $start, $length, $charset) . $suffix;
    }
    if (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset) . $suffix;
    }
    $re['utf-8'] = '/[-]|[?-?][?-?]|[?-?][?-?]{2}|[?-?][?-?]{3}/';
    $re['gb2312'] = '/[-]|[?-?][?-?]/';
    $re['gbk'] = '/[-]|[?-?][@-?]/';
    $re['big5'] = '/[-]|[?-?]([@-~]|?-?])/';
    preg_match_all($re[$charset], $str, $match);
    $slice = join('', array_slice($match[0], $start, $length));
    return $slice . $suffix;
}

function mi_str_encode($string){
    return $string;
	$len = strlen($string);
    $buf = '';
    $i = 0;
    while ($i < $len) {
        if (ord($string[$i]) <= 127) {
            $buf .= $string[$i];
        } elseif (ord($string[$i]) < 192) {
            $buf .= '&#xfffd;';
        } elseif (ord($string[$i]) < 224) {
            $buf .= sprintf('&#%d;', ord($string[$i + 0]) + ord($string[$i + 1]));
            $i = $i + 1;
            $i += 1;
        } elseif (ord($string[$i]) < 240) {
            ord($string[$i + 2]);
            $buf .= sprintf('&#%d;', ord($string[$i + 0]) + ord($string[$i + 1]) + ord($string[$i + 2]));
            $i = $i + 2;
            $i += 2;
        } else {
            ord($string[$i + 2]);
            ord($string[$i + 3]);
            $buf .= sprintf('&#%d;', ord($string[$i + 0]) + ord($string[$i + 1]) + ord($string[$i + 2]) + ord($string[$i + 3]));
            $i = $i + 3;
            $i += 3;
        }
        $i = $i + 1;
    }
    return $buf;
}

function draw_txt_to($card, $pos, $str, $iswrite, $font_file){
    $_str_h = $pos['top'];
    $fontsize = $pos['fontsize'];
    $width = $pos['width'];
    $margin_lift = $pos['left'];
    $hang_size = $pos['hang_size'];
    $temp_string = '';
    $tp = 0;
    $font_color = imagecolorallocate($card, $pos['color'][0], $pos['color'][1], $pos['color'][2]);
    $i = 0;
    while ($i < mb_strlen($str)) {
        $box = imagettfbbox($fontsize, 0, $font_file, mi_str_encode($temp_string));
        $_string_length = $box[2] - $box[0];
        $temptext = mb_substr($str, $i, 1);
        $temp = imagettfbbox($fontsize, 0, $font_file, mi_str_encode($temptext));
        if ($_string_length + $temp[2] - $temp[0] < $width) {
            $temp_string .= mb_substr($str, $i, 1);
            if ($i == mb_strlen($str) - 1) {
                $_str_h = $_str_h + $hang_size;
                $_str_h += $hang_size;
                $tp = $tp + 1;
                if ($iswrite) {
                    imagettftext($card, $fontsize, 0, $margin_lift, $_str_h, $font_color, $font_file, mi_str_encode($temp_string));
                }
            }
        } else {
            $texts = mb_substr($str, $i, 1);
            $isfuhao = preg_match('/[\\pP]/u', $texts) ? true : false;
            if ($isfuhao) {
                $temp_string .= $texts;
                $f = mb_substr($str, $i + 1, 1);
                $fh = preg_match('/[\\pP]/u', $f) ? true : false;
                if ($fh) {
                    $temp_string .= $f;
                    $i = $i + 1;
                }
            } else {
                $i = $i + -1;
            }
            $tmp_str_len = mb_strlen($temp_string);
            $s = mb_substr($temp_string, $tmp_str_len - 1, 1);
            if (is_firstfuhao($s)) {
                $temp_string = rtrim($temp_string, $s);
                $i = $i + -1;
            }
            $_str_h = $_str_h + $hang_size;
            $_str_h += $hang_size;
            $tp = $tp + 1;
            if ($iswrite) {
                imagettftext($card, $fontsize, 0, $margin_lift, $_str_h, $font_color, $font_file, mi_str_encode($temp_string));
            }
            $temp_string = '';
        }
        $i = $i + 1;
    }
    return $tp * $hang_size;
}

function is_firstfuhao($str){
    $fuhaos = array('0' => '"', '1' => '“', '2' => '\'', '3' => '<', '4' => '《');
    return in_array($str, $fuhaos);
}

//生成封面
function create_bigger_image($post_id,$date,$title,$content,$head_img,$qrcode_img=null){
	$im = imagecreatetruecolor(750,1190);
	$white = imagecolorallocate($im,255,255,255);
	$gray = imagecolorallocate($im,200,200,200);
	$foot_text_color = imagecolorallocate($im,153,153,153);
	$black = imagecolorallocate($im,0,0,0);
	$title_text_color = imagecolorallocate($im,51,51,51);
	$english_font = get_template_directory().'/fonts/Montserrat-Regular.ttf';
	$chinese_font = get_template_directory().'/fonts/MFShangYa_Regular.otf';
	$chinese_font_2 = get_template_directory().'/fonts/hanyixizhongyuan.ttf';
	imagefill($im,0,0,$white);
	$head_img = imagecreatefromstring(file_get_contents(timthumb($head_img,array('w'=>750,'h'=>'580'),'tim')));
	imagecopy($im,$head_img,0,0,0,0,750,580);
	$day = $date['day'];
	$day_width = imagettfbbox(85,0,$english_font,$day);
    $day_width = abs($day_width[2]-$day_width[0]);
	$year = $date['year'];
	$year_width = imagettfbbox(24,0,$english_font,$year);
	$year_width = abs($year_width[2]-$year_width[0]);
	$day_left = ($year_width-$day_width)/2;
	imagettftext($im,80,0,50+$day_left,480,$white,$english_font,$day);
	imageline($im,50,490,50+$year_width,490,$white);
	imagettftext($im,24,0,50,525,$white,$english_font,$year);
	$title = mi_str_encode($title);
  	$title_conf = array('color'=>array('0'=>0,'1'=>0,'2'=>0),'fontsize'=>28,'width'=>630,'left'=>60,'top'=>620,'hang_size'=>30);
 	draw_txt_to($im,$title_conf ,$title,true,$chinese_font);  
	//$title_width = imagettfbbox(28,0,$chinese_font,$title);
	//$title_width = abs($title_width[2] - $title_width[0]);
	//$title_left = 350 - $title_width / 2;
	//imagettftext($im,28,0,$title_left,730,$black,$chinese_font,$title);
	$conf = array('color'=>array('0'=>99,'1'=>99,'2'=>99),'fontsize'=>21,'width'=>630,'left'=>60,'top'=>760,'hang_size'=>23);
	draw_txt_to($im,$conf,$content,true,$chinese_font_2);
	//$style = array();
	//imagesetstyle($im,$style);
	imageline($im,0,1000,750,1000,$gray);
	$foot_text = _hui('bigger_desc');
	$foot_text = $foot_text ? $foot_text : get_bloginfo('description');
	$foot_text = mi_str_encode($foot_text);
	$logo_att = _hui('bigger_logo');
	if($logo_att){
		$att = wp_get_attachment_image_src($logo_att,'full');
		$logo_img = $att[0];
	}else{
		$site_logo = _hui('logo_src');
		if($site_logo){
			//$att = wp_get_attachment_image_src($site_logo,'full');
			//$logo_img = $att[0];
            $logo_img = _hui('logo_src');
		}else{
			$logo_img = '';
		}
	}
	$logo_img = imagecreatefromstring(file_get_contents(timthumb($logo_img,array('w'=>220,'h'=>56),'tim')));
	if($qrcode_img){
		imagecopy($im,$logo_img,56,1050,0,0,220,56);
		imagettftext($im,16,0,50,1140,$foot_text_color,$chinese_font_2,$foot_text);
		$qrcode_str = file_get_contents($qrcode_img);
		$qrcode_size = getimagesizefromstring($qrcode_str);
		$qrcode_img = imagecreatefromstring($qrcode_str);
		imagecopyresized($im,$qrcode_img,550,1020,0,0,150,150,$qrcode_size[0],$qrcode_size[1]);
	}else{
		imagecopy($im,$logo_img,284,1200,0,0,181,40);
		$foot_text_width = imagettfbbox(14,0,$chinese_font,$foot_text);
		$foot_text_width = abs($foot_text_width[2]-$foot_text_width[0]);
		$foot_text_left = 750-$foot_text_width/2;
		imagettftext($im,14,0,$foot_text_left,1275,$foot_text_color,$chinese_font_2,$foot_text);
	}
	/*$upload_dir = wp_upload_dir();
	$filename='/bigger-'.uniqid().'.png';
	$file=$upload_dir['path'].$filename;
	imagepng($im,$file);
	require_once ABSPATH.'wp-admin/includes/image.php';
	require_once ABSPATH.'wp-admin/includes/file.php';
	require_once ABSPATH.'wp-admin/includes/media.php';
	$src = media_sideload_image($upload_dir['url'].$filename,$post_id,NULL,'src');
	unlink($file);
	error_reporting(0);
	imagedestroy($im);
	if(is_wp_error($src)){
		return false;
	}
	return $src;*/
	Header("Content-type: image/GIF");
	ImageGIF($im);
    ImageDestroy($im); 
}

function get_bigger_img(){
    $post_id = sanitize_text_field($_GET['id']);
	//$post_id = sanitize_text_field($_POST['id']);
	//if(wp_verify_nonce($_POST['nonce'],'bigger-image-'.$post_id)){
		get_the_time('d',$post_id);
		get_the_time('Y/m',$post_id);
		$date = array('day'=>get_the_time('d',$post_id),'year'=>get_the_time('Y/m',$post_id));
		$title = get_the_title($post_id);
		$share_title = get_the_title($post_id);
		$title = substr_ext($title,0,28,'utf-8','');
		
		$post = get_post($post_id);
		$content = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
		$content = substr_ext(strip_tags(strip_shortcodes($content)),0,95,'utf-8','...');
		$content = str_replace(PHP_EOL,'',strip_tags(apply_filters('the_excerpt',$content)));
	
		//$head_img = post_thumbnail_src($post ? $post : get_post($post_id));
		//$att = wp_get_attachment_image_src($head_img,'full');
		$head_img = post_thumbnail_src($post);//$att[0];

		$qrcode_img = add_query_arg('data', get_the_permalink($post_id), _url_for('qr'));
		
		//if(get_post_meta($post_id,'bigger_cover',true)){
		    //$msg=array('s'=>200,'src'=>get_post_meta($post_id,'bigger_cover',true));
		//}else{
	
		//$result = create_bigger_image($post_id,$date,$title,$content,$head_img,$qrcode_img);
		//if($result){
		//	if(get_post_meta($post_id,'bigger_cover',true)){
		//		update_post_meta($post_id,'bigger_cover',$result);
		//	}else{
		//		add_post_meta($post_id,'bigger_cover',$result);
		//	}
			/*if(get_post_meta($post_id,'bigger_cover_share',true)){
				update_post_meta($post_id,'bigger_cover_share',$share_link);
			}else{
				add_post_meta($post_id,'bigger_cover_share',$share_link);
			}*/
		//	$msg=array('s'=>200,'src'=>$result);
		//}else{
		//	$msg=array('s'=>404,'m'=>'ERROR:404,封面生成失败，请稍后再试！');
		//}
		//}
    //}else{
	//	$msg=array('s'=>404,'m'=>'ERROR:404,安全认证失败，请联系管理员解决此问题！');
	//}
	//echo json_encode($msg);
	//exit();
	create_bigger_image($post_id,$date,$title,$content,$head_img,$qrcode_img);
}
get_bigger_img();
//($post_extend);