<?php
/**
 * Video Functions
 */
function dp_video($post_id, $autoplay = false) {
	$file = get_post_meta($post_id, 'dp_video_file', true);
	$files = !empty($file) ? explode("\n", $file) : array();
	$url = trim(get_post_meta($post_id, 'dp_video_url', true));
	$code = trim(get_post_meta($post_id, 'dp_video_code', true));
	
	if(!empty($code)) {
		$video = do_shortcode($code);
		$video = apply_filters('dp_video_filter', $video);
		$video = extend_video_html($video, $autoplay);		
		
		echo $video;
	} 
	elseif(!empty($url)) {
		$url = trim($url);
		$video = '';
		$youtube_player = '';
		
		// youku tudou
		if(preg_match('/youku|tudou/i', $url)) {  ?>
         <script type="text/javascript" src="//cytroncdn.videojj.com/latest/Iva.js"></script>
         <script type="text/javascript">
            var ivaInstance = new Iva(
                'videoBox', //父容器id
                {
                    appkey: 'B1s-irrMB', //必填，请在控制台查看应用标识
                    video: '<?php echo $url; ?>',
                    title: '<?php the_title(); ?>', //选填，建议填写方便后台数据统计
                    vnewsEnable: false,//是否开启新闻推送功能，默认为true
                    skinSelect: 1,//选填，播放器皮肤，可选0、1、2，默认为0，
                    autoplay: false,//选填，是否自动播放，默认为false
                    autoFormat: true,//选填，是否自动选择最高清晰度，默>认为false
                    bubble: false,//选填，是否开启云泡功能，默认为true
                    jumpStep: 10,//选填，左右方向键快退快进的时间
                    tagTrack: false,//选填，云链是否跟踪，默认为false
                    tagShow: false,//选填，云链是否显示，默认为false
                    tagDuration: 5,//选填，云链显示时间，默认为5秒
                    tagFontSize: 16,//选填，云链文字大小，默认为16像素
                    editorEnable: false, // 选填，当用户登录之后，是否允许加载编辑器，默认为true
                    vorEnable: false, // 选填，是否允许加载灵悟，默认为true
                    vorStartGuideEnable: false //选填， 是否启用灵悟新人引导，默认为true
                }
            );
         </script>;<?php
           return;
           // WordPress Embeds                                      
		} else {
			global $wp_embed;
			$orig_wp_embed = $wp_embed;
			
			$wp_embed->post_ID = $post_id;
			$video = $wp_embed->autoembed($url);
			
			if(trim($video) == $url) {
				$wp_embed->usecache = false;
				$video = $wp_embed->autoembed($url);
			}
			
			$wp_embed->usecache = $orig_wp_embed->usecache;
			$wp_embed->post_ID = $orig_wp_embed->post_ID;
		}
		
		$video = extend_video_html($video, $autoplay);

		echo $video;
	} 
	elseif(!empty($files)) {
		$poster	= get_post_meta($post_id, 'dp_video_poster', true);
		if(empty($poster) && has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large'))
			$poster = $thumb[0];
			
		$player = array('video_file' => 'video++');
		$player = !empty($player['video_file']) ? $player['video_file'] : 'mediaelement';
		
		$args = array(
			'files' => $files,
			'poster' => $poster,
			'autoplay' => $autoplay
		);
		dp_player($player, $args);
	}
  
}

function dp_player($player = '', $args = array()) {
	if(empty($player) || empty($args['files']))
		return;
	
	$defaults = array(
		'files' => array(),
		'poster' => '',
		'autoplay' => false
	);
	$args = wp_parse_args($args, $defaults);
	
	extract($args);
	
	/* WordPress Native Player: MediaElement */
	if($player == 'mediaelement') {
		$atts = array();
		foreach($files as $file) {
			$file = trim($file);
			
			if(strpos($file, 'youtube.com') !== false)
				$atts['youtube'] = $file;
			else {
				$type = wp_check_filetype($file, wp_get_mime_types());
				$atts[$type['ext']] = $file;
			}
		}
			
		echo wp_video_shortcode($atts); 
    }
      
	/* video++ */
	elseif($player == 'video++') {
	   foreach($files as $file) {
         
       $file = trim($file); ?>
         <script type="text/javascript" src="//cytroncdn.videojj.com/latest/Iva.js"></script>
         <script type="text/javascript">
            var ivaInstance = new Iva(
                'videoBox', //父容器id
                {
                    appkey: 'B1s-irrMB', //必填，请在控制台查看应用标识
                    video: '<?php echo $file; ?>',
                    title: '<?php the_title(); ?>', //选填，建议填写方便后台数据统计
                    //cover: '<?php echo $video_img; ?>', //选填，视频封面url
                    skinSelect: 1,
                    vnewsEnable: false, //是否开启新闻推送功能，默认为true
                    autoplay: true, //选填，是否自动播放，默认为false
                    autoFormat: true, //选填，是否自动选择最高清晰度，默>认为false
                    rightHand: false, //选填，是否开启右键菜单，默认为false
                    bubble: false, //选填，是否开启云泡功能，默认为true
                    tagTrack: false, //选填，云链是否跟踪，默认为false
                    tagShow: false, //选填，云链是否显示，默认为false
                    editorEnable: false, // 选填，当用户登录之后，是否允许加载编辑器，默认为true
                    vorEnable: false, // 选填，是否允许加载灵悟，默认为true
                    vorStartGuideEnable: false //选填， 是否启用灵悟新人引导，默认为true
                }
            );
         </script><?php 
        }
	}
		
}

function is_video($post = null){
	$post = get_post($post);
	if(!$post)
		return false;
	
	// Back compat, if the post has any video field, it also is a video. 
	$video_file = get_post_meta($post->ID, 'dp_video_file', true);
	$video_url = get_post_meta($post->ID, 'dp_video_url', true);
	$video_code = get_post_meta($post->ID, 'dp_video_code', true);
	// Post meta by Automatic Youtube Video Post plugin
	$tern_wp_youtube_video = get_post_meta($post->ID, '_tern_wp_youtube_video', true);
	if(!empty($video_code) || !empty($video_url) || !empty($video_file) || (!empty($tern_wp_youtube_video) && function_exists('tern_wp_youtube_video')))
		return $post->ID;
	
	return has_post_format('video', $post);
}

/**
 * Add extra parameters to video url to control video
 */
function extend_video_html($html, $autoplay = false, $wmode = 'opaque') {
	$replace = false;
	
	preg_match('/src=[\"|\']([^ ]*)[\"|\']/', $html, $matches);
	
	if(isset($matches[1])) {
		$url = $matches[1];
      
		if($replace) {
			$url = esc_attr($url);	
			$html = preg_replace('/src=[\"|\']([^ ]*)[\"|\']/', 'src="'.$url.'"', $html);
		}
	}
	
	return $html;
}

/**
 * Ajax inline video action for list large view
 */
add_action( 'wp_ajax_nopriv_ajax-video', 'dp_ajax_video' );
add_action( 'wp_ajax_ajax-video', 'dp_ajax_video');
function dp_ajax_video() {
	if(!isset($_REQUEST['action']) || !isset($_REQUEST['id']) || $_REQUEST['action'] != 'ajax-video')
		return false;

	$post_id = $_REQUEST['id'];

	dp_video($post_id, true);
	
	die();
}


/*
 * Add a classname to <div> element which wrapped
 * wp video shortcode, so we can use it later 
 */
add_filter( 'wp_video_shortcode', 'wp_video_shortcode_wrapper', 10, 5);
function wp_video_shortcode_wrapper($html, $atts, $video, $post_id, $library) {
	$class .= 'wp-video-shortcode-wrapper';
	if($library === 'mediaelement')
		$class .= ' meplayer';
	$html = str_replace('<div style="width: 640px;"', '<div class="'.$class.'"style="width: 100%;""', $html);
	
	return $html;
}