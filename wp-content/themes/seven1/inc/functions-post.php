<?php
/**
 * 与文章相关的函数
 */

//获取文章浏览数据
add_action( 'wp_ajax_zrz_view', 'zrz_view' );
add_action( 'wp_ajax_nopriv_zrz_view', 'zrz_view' );
function zrz_view(){
	$pid = isset( $_POST['pid'] ) && is_numeric( $_POST['pid'] ) ? $_POST['pid'] : '';
	if(!$pid) exit;
	$view = get_post_meta($pid,'views',true);
    $view = $view ? $view : 0;
	$favorites = zrz_get_post_favorites($pid);
	print json_encode(array('status'=>200,'views' =>$view+1,'favorites'=>$favorites));
	unset($favorites);
    update_post_meta($pid,'views',$view +1);
    exit;
}

//内页上一篇文章，下一批文章
function zrz_get_pre_next_post(){
	$previous_post = get_previous_post();
	$next_post = get_next_post();
	if(is_singular('shop')){
		$previous_post = get_previous_post(false,'','shoptype');
		$next_post = get_next_post(false,'','shoptype');
	}

	$html = '';
	$next_id = isset($next_post->ID) ? $next_post->ID : false;
	$pre_id = isset($previous_post->ID) ? $previous_post->ID : false;

	$args = array( 'numberposts' => 1, 'orderby' => 'rand', 'post_status' => 'publish' );

	//如果没有上一篇或者下一篇，则显示随机文章
	if(!$pre_id){
		$rand_posts = get_posts( $args );
		$previous_post = $rand_posts[0];
		$pre_id = $previous_post->ID;
	}

	if(!$next_id){
		$rand_posts = get_posts( $args );
		$next_post = $rand_posts[0];
		$next_id = $next_post->ID;
	}

	$next_thumb = zrz_get_post_thumb($next_id);
	$pre_thumb = zrz_get_post_thumb($pre_id);

	if($pre_id){
		$html .= '
			<div class="post-pre pos-r fd">
				<div class="box">
					<div class="post-navigation-in pd10 pos-r l0 shadow">
					<div class="img-bg pos-a" style="background-image:url('.zrz_get_thumb($pre_thumb,420,118).')"></div>
						<span class="navigation-cat mar10-b">'.zrz_get_first_category($pre_id).'</span>
						<div class="navigation-title pos-r">
							<a href="'.get_permalink($pre_id).'">
								<i class="iconfont zrz-icon-font-jiantou pos-a"></i><span>'.esc_html($previous_post->post_title).'</span>
							</a>
						</div>
						<span class="navigation-time fs12 mar5-t gray">'.zrz_time_ago($pre_id).'</span>
					</div>
				</div>
			</div>';
	}
	if($next_id){
		$html .= '
			<div class="post-next pos-r fd">
				<div class="box">
					<div class="post-navigation-in pd10 pos-r l0 shadow">
					<div class="img-bg pos-a" style="background-image:url('.zrz_get_thumb($next_thumb,420,118).')"></div>
						<span class="navigation-cat mar10-b">'.zrz_get_first_category($next_id).'</span>
						<div class="navigation-title pos-r">
							<a href="'.get_permalink($next_id).'">
								<span>'.esc_html($next_post->post_title).'</span><i class="iconfont zrz-icon-font-jiantou-copy-copy pos-a"></i>
							</a>
						</div>
						<span class="navigation-time fs12 mar5-t gray">'.zrz_time_ago($next_id).'</span>
					</div>
				</div>
			</div>
		';
	}

	return $html;
}

/*
* 当前文章的状态
*/
function zrz_post_status(){
    global $post;
    if($post->post_status === 'pending'){
        $status = esc_html__('待审','ziranzhi2');
    }elseif($post->post_status === 'draft'){
        $status = esc_html__('草稿','ziranzhi2');
    }elseif($post->post_status === 'future'){
        $status = esc_html__('定时发布','ziranzhi2');
    }elseif($post->post_status === 'private'){
        $status = esc_html__('私密的文章','ziranzhi2');
    }elseif($post->post_status === 'trash'){
        $status = esc_html__('垃圾文章','ziranzhi2');
    }elseif($post->post_status === 'auto-draft'){
        $status = esc_html__('自动保存的草稿','ziranzhi2');
    }else{
		$status = '';
	}
    return $status;
}

//获取某个文章的分类
function zrz_get_first_category($post_id = 0,$display = false,$info = false){
	if(!$post_id){
		$categories = get_the_category();
	}else{
		$categories = get_the_category($post_id);
	}

	$html = '';

    if ( ! empty( $categories )) {
		if($display){
			foreach ($categories as $cat) {
				$html .= '<a class="list-category bg-blue-light color" href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
			}
		}elseif($info){
			$html = array(
				'id'=>$categories[0]->term_id,
				'name'=>$categories[0]->name,
			);
		}else{
			$html .= '<a class="list-category bg-blue-light color" href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
		}
    }

	return $html;
}

/*
* 文章 meta
*/
function zrz_post_meta($post_id = 0){

	if($post_id == 0){
		global $post;
		$post_id = $post->ID;
		$comment_count = $post->comment_count;
		$cat = zrz_get_first_category();
	}else{
		$comment_count = wp_count_comments( $post_id );
		$comment_count = $comment_count->approved;
		$cat = zrz_get_first_category($post_id);
	}

    $views = get_post_meta($post_id,'views',true);
    $loveNub = get_post_meta($post_id, 'zrz_favorites', true );
    $views = $views ? $views : 0 ;
    $loveNub = !empty($loveNub) ? count($loveNub) : 0;
	$face = zrz_get_post_reaction($post_id);
	$display = zrz_get_display_settings('single');
	$face = $face && (isset($display['reaction']) && $display['reaction'] == 1) ? $face.ZRZ_THEME_DOT : '';
	$sticky = is_sticky() ? '<span class="sticky">置顶</span><span class="dot"></span>' : '';
    $html = '<div class="post-meta meta mar10-t clearfix">
            <span class="list-category-l hide5">'.$face.$cat.ZRZ_THEME_DOT.$sticky.'</span><i class="iconfont zrz-icon-font-eye"></i>'.$views.'<span class="dot"></span><i class="iconfont zrz-icon-font-pinglun"></i>'.$comment_count.'<span class="dot"></span><i class="iconfont zrz-icon-font-collect"></i>'.$loveNub.'
            </div>';
    return $html;
}

//获取文章标签
function zrz_get_post_tags($display = false){
	$tags = wp_get_post_tags(get_the_id());
	$html = '<div class="zrz-post-tags l1 fs12">';
	$html .= zrz_get_first_category($display);
	$html .='<span class="single-tags mar10-l">';
	foreach ( $tags as $tag ) {
		$tag_link = get_tag_link( $tag->term_id );
		$html .= '<a href="'.esc_url($tag_link).'">';
		$html .= '# '.esc_attr($tag->name).'<span>'.esc_attr($tag->count).'</span></a>';
	}
	$html .= '</span></div>';
	return $html;
}

//获取文章描述
function zrz_get_post_des($post_id){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}

	$post_meta = zrz_seo_get_post_meta($post_id, 'zrz_seo_description');
	$post_excpert = get_post_field('post_excerpt',$post_id);
	$post_content = zrz_get_content_ex(get_post_field('post_content',$post_id),150);

	//如果存在SEO描述输出，否则输出文章摘要，否则输出文章内容截断
	$description = $post_meta ? $post_meta : ($post_excpert ? $post_excpert : $post_content);

	return trim(strip_tags($description));
}

/*
 *  分享按钮
*/
function zrz_get_share($type = false){
	if(zrz_is_weixin()) return;
	global $post;
    if(is_single() || is_page()){
        $post_title = esc_attr(get_the_title());
        $post_content = esc_attr(zrz_get_post_des($post->ID));
        $thumb = esc_url(get_the_post_thumbnail_url());
        $url = esc_url(get_the_permalink());
        $site_name = esc_attr(get_bloginfo( 'name' ));
    }else{
        $post_title = esc_attr(get_bloginfo('name'));
        $post_content = esc_attr(get_bloginfo( 'description', 'display' ));
        $thumb = esc_url(zrz_get_theme_settings('logo'));
        $url = esc_url(home_url('/'));
        $site_name = esc_attr(get_bloginfo( 'name' ));
    }

	if($type){
		return 'http://service.weibo.com/share/share.php?url='.$url.'&coun=1&pic='.zrz_get_post_thumb_img($post->ID).'&title='.$post_title;
	}else{
		$weibo = 'http://service.weibo.com/share/share.php?url='.$url.'&coun=1&pic='.$thumb.'&title='.$post_title;
	}

    $qqzone = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='.$url.'&desc=来<'.$site_name.'>看看这篇文章吧，有惊喜哦！&site='.$site_name.'&summary='.$post_content.'&pics='.$thumb.'&title='.$post_title;

    $qq = 'http://connect.qq.com/widget/shareqq/index.html?url='.$url.'&desc=来<'.$site_name.'>看看这篇文章吧，有惊喜哦！&title='.$post_title.'&summary='.$post_content.'&pics='.$thumb.'&site='.$site_name;
	
    return '<div class="share-box fs12">

        <div class="weixin mouh" id="share-weixin">
			'.($post->post_type != 'topic' ? '<i class="iconfont zrz-icon-font-weimingming-"></i>' : '微信').'
			<div class="wx-t-x pos-a hide box" id="weixin-box">
		        <img class="qrcode fl bor-3" src="'.ZRZ_THEME_URI.'/inc/qrcode/index.php?c='.$url.'" />
		    </div>
		</div>
        '.ZRZ_THEME_DOT.'
        <a href="javascript:void(0)" onclick="openWin(\''.$qqzone.'\',\'weixin\',500,500)" class="qzone">
			'.($post->post_type != 'topic' ? '<i class="iconfont zrz-icon-font-qqkongjian1"></i>' : 'QQ空间').'
		</a>
        '.ZRZ_THEME_DOT.'
        <a href="javascript:void(0)" onclick="openWin(\''.$qq.'\',\'qq\',1100,800)" class="qq">
			'.($post->post_type != 'topic' ? '<i class="iconfont zrz-icon-font-qq"></i>' : 'QQ').'
		</a>
        '.ZRZ_THEME_DOT.'
        <a href="javascript:void(0)" onclick="openWin(\''.$weibo.'\',\'weibo\',600,600)" class="weibo">
			'.($post->post_type != 'topic' ? '<i class="iconfont zrz-icon-font-weibo1"></i>' : '微博').'
		</a>

	</div>';
}

//获取文章长微博图
function zrz_get_post_thumb_img($post_id){
	$post_thumb = get_post_meta($post_id,'zrz_post_thumb',true);
	if($post_thumb){
		return zrz_get_media_path().$post_thumb;
	}else{
		return '';
	}
}

//文章投票表情
function zrz_get_face_array(){
    $array = array(
        'like'=>array(
			'name'=>esc_html__('喜欢','ziranzhi2'),
			'class'=>'face-like',
			'nub'=>0,
			'display'=>'',
			'per'=>0
		),
        'dislike'=>array(
			'name'=>esc_html__('心碎','ziranzhi2'),
			'class'=>'face-dislike',
			'nub'=>0,
			'display'=>'',
			'per'=>0
		),
        'lol'=>array(
			'name'=>esc_html__('笑尿了','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'cry'=>array(
			'name'=>esc_html__('大声哭','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'cute'=>array(
			'name'=>esc_html__('萌化了','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'fail'=>array(
			'name'=>esc_html__('好扎心','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'win'=>array(
			'name'=>esc_html__('你懂的','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'love'=>array(
			'name'=>esc_html__('小姐姐','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'omg'=>array(
			'name'=>esc_html__('受精了','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		),
        'wtf'=>array(
			'name'=>esc_html__('问号脸','ziranzhi2'),
			'nub'=>0,
			'class'=>'',
			'display'=>'',
			'per'=>0
		)
    );
    return $array;
}

//获取文章投票
add_action( 'wp_ajax_zrz_get_post_face', 'zrz_get_post_face' );
add_action( 'wp_ajax_nopriv_zrz_get_post_face', 'zrz_get_post_face' );
function zrz_get_post_face(){
	$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;

	$faces = zrz_get_face_array();
	$face_arr = array();

	//获取投票类型
	if(is_user_logged_in()){
		$user_id = get_current_user_id();
		$face_arr = get_user_meta($user_id,'zrz_reaction',true);
	}else{
		if(isset($_COOKIE['zrz_face']))
		$face_arr = unserialize(stripslashes($_COOKIE['zrz_face']));
	}

	$i = 0;
	if(isset($face_arr[$post_id])){
		foreach ($face_arr[$post_id] as $key=>$value) {
			$i++;
			$faces[$value]['display'] = 'active';
		}
	}
	//获取投票数量
	$count_face = get_post_meta($post_id,'zrz_reaction_count',true);
	$count_face = is_array($count_face) ? $count_face : false;

	$total = 0;
	$per = 0;
	if($count_face){
		foreach ($count_face as $key=>$val) {
			$faces[$key]['nub'] = $val;
			$total += (int)$val;
		}
	}

	//投票所占百分比
	print json_encode(array('status'=>200,'msg'=>$faces,'count'=>$i,'total'=>$total));
	unset($faces);
	exit;
}

//文章评价
add_action( 'wp_ajax_zrz_add_post_face', 'zrz_add_post_face' );
add_action( 'wp_ajax_nopriv_zrz_add_post_face', 'zrz_add_post_face' );
function zrz_add_post_face(){
	$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
	$face = isset($_POST['face']) ? sanitize_text_field($_POST['face']) : '';

	$faces = array('like','dislike','lol','cry','cute','fail','win','love','omg','wtf');

	if((!$post_id && !$face) || !in_array($face,$faces,true)){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	//评价数量
	$count_face = get_post_meta($post_id,'zrz_reaction_count',true);
	$count_face = is_array($count_face) ? $count_face : array();
	$count_face_i = isset($count_face[$face]) ? (int)$count_face[$face] : 0;

	$add = 0;

	if( is_user_logged_in() ) {
		//用户记录评价数据
		$user_id = get_current_user_id();
		$user_faces = get_user_meta($user_id,'zrz_reaction',true);
		$user_faces = is_array($user_faces) ? $user_faces : array();

		//文章记录评价的用户数据
		$post_faces = get_post_meta($post_id,'zrz_reaction',true);
		$post_faces = is_array($post_faces) ? $post_faces : array();

		if(isset($user_faces[$post_id])){

			$user_arr = $user_faces[$post_id];
			$face_arr = $post_faces[$face];

			//如果 user_meta 中包含当前元素，则删除当前元素
			$ac = false;
			if(in_array($face,$user_arr,true)){
				//键值互换
				$user_arr_u = array_flip($user_arr);
				$face_arr_u = array_flip($face_arr);

				unset($user_arr_u[$face]);
				unset($face_arr_u[$user_id]);

				//键值互换，修正数组
				$user_arr = array_flip($user_arr_u);
				$face_arr = array_flip($face_arr_u);

				$count_face_i  = $count_face_i - 1;
				$ac = true;
				$add = -1;
			}elseif(count($user_arr) < 3){

				array_push($user_arr,$face);
				array_push($face_arr,$user_id);

				$count_face_i  = $count_face_i + 1;
				$ac = true;
				$add = 1;
			}

			if($ac === true){

				$user_faces[$post_id] = $user_arr;
				$post_faces[$face] = $face_arr;

				update_user_meta($user_id,'zrz_reaction',$user_faces);
				update_post_meta($post_id,'zrz_reaction',$post_faces);
				$count_face[$face] = $count_face_i >= 0 ? $count_face_i : 0;
				update_post_meta($post_id,'zrz_reaction_count',$count_face);
				print json_encode(array('status'=>200,'msg'=>__('更新成功','ziranzhi2'),'ac'=>$add));
				exit;
			}else{
				print json_encode(array('status'=>401,'msg'=>__('更新失败','ziranzhi2'),'ac'=>$add));
				exit;
			}
		}else{
			$user_faces[$post_id] = array($face);
			$post_faces[$face] = array($user_id);
			update_user_meta($user_id,'zrz_reaction',$user_faces);
			update_post_meta($post_id,'zrz_reaction',$face_arr);
			$count_face[$face] = $count_face_i + 1;
			update_post_meta($post_id,'zrz_reaction_count',$count_face);
			print json_encode(array('status'=>200,'msg'=>__('更新成功','ziranzhi2'),'ac'=>1));
			exit;
		}
	}else{
		$ac = false;
		if(isset($_COOKIE['zrz_face'])) {
			$face_arr = unserialize(stripslashes($_COOKIE['zrz_face']));
			$face_arr = is_array($face_arr) ? $face_arr : array();

			if(isset($face_arr[$post_id])){

				if(in_array($face, $face_arr[$post_id],true)){

					$face_arr_u = array_flip($face_arr[$post_id]);

					unset($face_arr_u[$face]);

					$face_arr[$post_id] = array_flip($face_arr_u);

					$count_face_i = $count_face_i - 1;
					$ac = true;
					$add = -1;
				}elseif(count($face_arr[$post_id]) < 3){
					array_push($face_arr[$post_id],$face);
					$count_face_i = $count_face_i + 1;
					$ac = true;
					$add = 1;
				}
			}else{
				$face_arr[$post_id] = array($face);
				$count_face_i = 1;
				$ac = true;
				$add = 1;
			}

			if($ac){
				$count_face[$face] = $count_face_i >= 0 ? $count_face_i : 0;
				update_post_meta($post_id,'zrz_reaction_count',$count_face);
				zrz_setcookie('zrz_face',serialize($face_arr));
				print json_encode(array('status'=>200,'msg'=>__('更新成功','ziranzhi2'),'ac'=>$add));
				exit;
			}else{
				print json_encode(array('status'=>401,'msg'=>__('老铁，超过了3次，过一天再来吧！','ziranzhi2'),'ac'=>0));
				exit;
			}
		}else{
			$count_face[$face] = $count_face[$face] + 1;
			update_post_meta($post_id,'zrz_reaction_count',$count_face);
			zrz_setcookie('zrz_face',serialize(array($post_id=>array($face))));
			print json_encode(array('status'=>200,'msg'=>__('更新成功','ziranzhi2'),'ac'=>1));
			exit;
		}
	}
	print json_encode(array('status'=>401,'msg'=>__('更新失败','ziranzhi2')));
	exit;
}

//获取文章投票最终结果
function zrz_get_post_reaction($post_id = 0){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}
	$counts = get_post_meta(get_the_id(),'zrz_reaction_count',true);
	if(!$counts) return '';
	$face_vote = array_search(max($counts),$counts);
	if($counts[$face_vote]>=5){
		switch ($face_vote) {
			case 'like':
				$class = 'face-like';
				break;
			case 'dislike':
				$class = 'face-dislike';
				break;
			default:
				$class = '';
				break;
		}
		return '<img src="'.ZRZ_THEME_URI.'/images/face/'.$face_vote.'.svg" class="list-face '.$class.'" />';
	}
	return '';
}

//加载更多文章
add_action( 'wp_ajax_zrz_load_more_posts', 'zrz_load_more_posts' );
add_action( 'wp_ajax_nopriv_zrz_load_more_posts', 'zrz_load_more_posts' );
function zrz_load_more_posts(){

	$paged = isset($_POST['paged']) && is_numeric($_POST['paged']) ? $_POST['paged'] : false;
	$type_or_cat = isset($_POST['type']) ? $_POST['type'] : false;

	if(!$paged || !$type_or_cat){
		print json_encode(array('status'=>401,'msg'=>'非法操作'));
		exit;
	}

	$cat = null;
	$post_exclude = null;
	$tag_id = null;
	$post_type = 'post';
	$offset = '';
	if($type_or_cat === 'index'){
		$post_exclude = zrz_get_theme_settings('post_exclude');
		$cat = $type_or_cat;
	}elseif(strpos($type_or_cat,'catL') !== false){
		$cat = number($type_or_cat);
	}elseif(strpos($type_or_cat,'shop') !== false){
		$tag_id = number($type_or_cat);
		$post_type = 'shop';
	}elseif(strpos($type_or_cat,'collection') !== false){
		$tag_id = number($type_or_cat);
		$post_type = 'collection';
	}elseif(strpos($type_or_cat,'sptype') !== false){
		$post_type = 'shop-type';
		$type = explode('-',$type_or_cat);
		$type = $type[1];
	}elseif(strpos($type_or_cat,'bbp-home') !== false){
		$type = explode('-',$type_or_cat);
		$forum_id = $type[2];
		$forum_id = $forum_id == 0 ? null : $forum_id;
		$pre_post = get_option( '_bbp_topics_per_page', 15 );
		$offset = ($paged-1)*$pre_post;
		$post_type = 'bbp-home';
	}elseif(strpos($type_or_cat,'bbp-reply') !== false){
		$type = explode('-',$type_or_cat);
		$topic_id = $type[2];
		$topic_id = $topic_id == 0 ? null : $topic_id;
		$pre_post = get_option( '_bbp_replies_per_page', 15 );
		$offset = ($paged-1)*$pre_post;
		$post_type = 'bbp-reply';
	}elseif(strpos($type_or_cat,'user-posts') !== false){
		$type = explode('-',$type_or_cat);
		$user_id = $type[2];
	}

	if(!$offset){
		$number = get_option('posts_per_page', 10);
		$offset = ($paged-1)*$number;
	}

	$list = '';
	if($post_type == 'shop'){
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>'publish',
			'post_type'=>'shop',
			'tax_query'=>array(array(
				'taxonomy' => 'shoptype',
				'field'    => 'term_id',
				'terms'    => $tag_id
			)),
			'offset'=>$offset,
		);
	}elseif($post_type == 'shop-type'){
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>'publish',
			'post_type'=>'shop',
			'meta_key'=>'zrz_shop_type',
			'meta_value'=>$type,
			'offset'=>$offset,
		);
	}elseif($post_type == 'collection'){
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>'publish',
			'post_type'=>'post',
			'tax_query'=>array(array(
				'taxonomy' => 'collection',
				'field'    => 'term_id',
				'terms'    => $tag_id
			)),
			'offset'=>$offset,
		);
	}elseif($post_type == 'bbp-home'){
		$arg = array(
			'posts_per_page'=>$pre_post,
			'post_status'=>'publish',
			'post_type'=>'topic',
			'post_parent'=>$forum_id,
			'offset'=>$offset
		);
	}elseif($post_type == 'bbp-reply'){
		$arg = array(
			'posts_per_page'=>$pre_post,
			'post_status'=>'publish',
			'post_type'=>'reply',
			'post_parent'=>$topic_id,
			'offset'=>$offset
		);
	}elseif($type_or_cat == 'bubble-home'){
		$arg = array(
			'posts_per_page'=>$pre_post,
			'post_status'=>'publish',
			'post_type'=>'pps',
			'offset'=>$offset
		);
	}elseif(strpos($type_or_cat,'bubble-arc') !== false){
		$id = explode('-',$type_or_cat);
		$id = $id[2];
		$arg = array(
			'posts_per_page'=>$pre_post,
			'post_status'=>'publish',
			'post_type'=>'pps',
			'tax_query'=>array(array(
				'taxonomy' => 'mp',
				'field'    => 'term_id',
				'terms'    => $id
			)),
			'offset'=>$offset
		);
	}elseif(strpos($type_or_cat,'user-posts') !== false){
		$user_id = explode('-',$type_or_cat);
		$user_id = $user_id[2];
		$current_user = get_current_user_id();
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>$user_id == $current_user ? 'any' : 'publish',
			'post_type'=>'post',
			'author'=>$user_id,
			'offset'=>$offset,
		);
	}elseif(strpos($type_or_cat,'user-labs-') !== false){
		$user_id = explode('-',$type_or_cat);
		$user_id = $user_id[2];
		$current_user = get_current_user_id();
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>$user_id == $current_user ? 'any' : 'publish',
			'post_type'=>'labs',
			'author'=>$user_id,
			'offset'=>$offset,
		);
	}elseif(strpos($type_or_cat,'tag') !== false){
			$arg = array(
				'posts_per_page'=>$number,
				'post_status'=>'publish',
				'post_type'=>'post',
				'tag_id'=>number($type_or_cat),
				'offset'=>$offset,
			);
	}else{
		$arg = array(
			'posts_per_page'=>$number,
			'post_status'=>'publish',
			'post_type'=>$post_type,
			'cat'=>$cat,
			'offset'=>$offset,
			'category__not_in'=>$post_exclude
		);
	}

	if(class_exists( 'bbPress' ) && $post_type == 'bbp-home'){
		ob_start();
		if ( bbp_has_topics($arg) ){
			bbp_get_template_part('loop','topics');
		}
		$list = ob_get_clean();
	}elseif(class_exists( 'bbPress' ) && $post_type == 'bbp-reply'){
		ob_start();
		if ( bbp_has_replies($arg)){
			bbp_get_template_part( 'loop','replies' );
		}
		$list = ob_get_clean();
	}else{
		$the_query = new WP_Query($arg);
		ob_start();
		while ( $the_query->have_posts() ){
			$the_query->the_post();
				if($post_type == 'shop' || $post_type == 'shop-type'){
					get_template_part( 'formats/content','shop');
				}elseif(strpos($type_or_cat,'bubble-home') !== false || strpos($type_or_cat,'bubble-arc') !== false){
					get_template_part( 'formats/content','bubble');
				}elseif(strpos($type_or_cat,'user-posts-') !== false){
					if($user_id == $current_user || current_user_can('delete_users')){
						get_template_part( 'formats/content','user-edit');
					}else{
						get_template_part( 'formats/content','user');
					}
				}elseif(strpos($type_or_cat,'user-labs-') !== false){
					get_template_part( 'formats/content','user-labs');
				}else{
					get_template_part( 'formats/content',get_post_format());
				}
		}
		$list = ob_get_clean();
		wp_reset_postdata();
	}


	print json_encode(array('status'=>200,'msg'=>$list));
	unset($arg);
	unset($list);
	exit;
}

//文章收藏
add_action( 'wp_ajax_zrz_post_favorites', 'zrz_post_favorites' );
add_action( 'wp_ajax_nopriv_zrz_post_favorites', 'zrz_post_favorites' );
function zrz_post_favorites(){
	$post_id = isset($_POST['pid']) ? (int)$_POST['pid'] : 0;
	$type = isset($_POST['type']) ? $_POST['type'] : 0;

	if(!$post_id || !is_user_logged_in()){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	$user_id = get_current_user_id();

	//获取文章的收藏数组
	$post_favorites = get_post_meta($post_id, 'zrz_favorites', true );
	$post_favorites = is_array($post_favorites) ? $post_favorites : array();

	//获取用户的收藏数组
	$user_favorites = get_user_meta($user_id, 'zrz_user_favorites', true );
	$user_favorites = is_array($user_favorites) ? $user_favorites : array();
	$user_favorites_type = isset($user_favorites[$type]) ? $user_favorites[$type] : array();

	$ac = 0;

	if(!in_array($user_id, $post_favorites,true)){//检查当前用户是否已经点赞，如果没有点赞则执行加入
		array_push($post_favorites, $user_id);
		array_push($user_favorites_type, $post_id);
		$ac = 1;
	}else{
		foreach($post_favorites as $k => $v) {
			if($v == $user_id){
				unset($post_favorites[$k]);
			}
		}
		foreach($user_favorites_type as $k => $v) {
			if($v == $post_id){
				unset($user_favorites_type[$k]);
			}
		}
		$ac = 0;
	}

	//更新文章收藏数据
	update_post_meta($post_id, 'zrz_favorites', $post_favorites);

	//更新当前用户收藏的文章数据
	$user_favorites[$type] = $user_favorites_type;
	update_user_meta($user_id, 'zrz_user_favorites', $user_favorites);

	print json_encode(array('status'=>200,'msg'=>__('收藏成功','ziranzhi2'),'loved'=>$ac));
	exit;
}

//获取收藏数据
function zrz_get_post_favorites($post_id){

	if(!$post_id){
		return false;
	}

	$user_id = get_current_user_id();

	$arr = get_post_meta($post_id, 'zrz_favorites', true );
	$arr = is_array($arr) && !empty($arr) ? $arr : array();

	$msg = array(
		'loved'=>in_array($user_id, $arr,true) ? 1 : 0,
		'count'=>count($arr)
	);

	return $msg;
}

//相关文章
function zrz_get_realted_posts($post_id){

    //用户自定义的相关文章
    $custom_related = get_post_meta($post_id,'zrz_related',true);
	//var_dump($custom_related);
    $custom_related = is_array($custom_related) ? $custom_related : array();

    //获取当前的文章类型
    $post_type = get_post_type($post_id);

    //通过插件自动获取相关文章
    $yarpp_posts = defined('YARPP_VERSION') ? yarpp_get_related(array('limit' => 5,'post_type'=>$post_type,'post__not_in' => array($post_id)), $post_id) : array();

	foreach ($yarpp_posts as $yarpp_post) {
		array_push($custom_related,$yarpp_post->ID);
	}

    //去重去空
    $post_ids = array_filter(array_unique($custom_related));

    if(empty($post_ids) && count($post_ids) < 4) return '';
    $i = 0;

    $html = '<div class="post-related-footer">';
    foreach ($post_ids as $id) {
        if($i >= 4) break;
        $thumb = zrz_get_post_thumb($id);
        $thumb = zrz_get_thumb($thumb,320,185);
        $html .= '<div class="fd post-related pos-r">
			<div class="box">
                <a href="'.get_permalink($id).'">
                    <div class="related-img" style="background-image:url('.$thumb.')"></div>
                    <h2><span>'.get_the_title($id).'</span></h2>
                </a>
			</div>
        </div>';
		$i++;
    }
    $html .= '</div>';

    return $html;
}

//视频上传
add_action( 'wp_ajax_zrz_video_upload', 'zrz_video_upload' );
add_action( 'wp_ajax_nopriv_zrz_video_upload', 'zrz_video_upload' );
function zrz_video_upload(){
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$url = isset($_POST['url']) ? $_POST['url'] : '';
	$file = isset($_FILES['file']) ? $_FILES['file'] : '';
	$single = isset($_POST['single']) ? $_POST['single'] : '';

	$user_id = get_current_user_id();

	if(!$type){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	if($type == 'url'){
		$vodeo_dom = apply_filters('the_content', $url);
		if($single){
			print json_encode(array('status'=>200,'msg'=>$vodeo_dom));
			exit;
		}
		if($vodeo_dom){
			$img = zrz_base64_file_upload($url);

			if($img){
				print json_encode(array('status'=>200,'msg'=>$vodeo_dom,'img'=>$img,'src'=>$url));
				exit;
			}else{
				print json_encode(array('status'=>200,'msg'=>$vodeo_dom,'img'=>array('url'=>array('url'=>''),'title'=>$url),'src'=>$url));
				exit;
			}
		}else{
			print json_encode(array('status'=>401,'msg'=>__('不支持此地址','ziranzhi2')));
			exit;
		}
	}

	if($type == 'file'){
		if(!is_user_logged_in()){
			print json_encode(array('status'=>401,'msg'=>__('请登录','ziranzhi2')));
			exit;
		}
		check_ajax_referer($user_id, 'security' );
		$upload = new zrz_media($file,'video',$user_id,'',0);
		$resout = $upload->media_upload();
		print $resout;
		unset($file);
		exit;
	}
}

//通过ID获取文章或商品
add_action( 'wp_ajax_zrz_get_post_by_id', 'zrz_get_post_by_id' );
add_action( 'wp_ajax_nopriv_zrz_get_post_by_id', 'zrz_get_post_by_id' );
function zrz_get_post_by_id(){
	$post_id = isset($_POST['pid']) ? $_POST['pid'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	if(!is_numeric($post_id)){
		$post_id = url_to_postid( $post_id );
	}

	if((get_post_type($post_id) == 'post' || get_post_type($post_id) == 'shop') && get_post_status($post_id) == 'publish'){
		print json_encode(array('status'=>200,'msg'=>$post_id));
		exit;
	}else{
		print json_encode(array('status'=>401,'msg'=>__('ID无效','ziranzhi2')));
		exit;
	}
}

add_action( 'wp_ajax_zrz_get_related_post', 'zrz_get_related_post' );
function zrz_get_related_post(){
	$post_id = isset($_POST['pid']) ? $_POST['pid'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	if(!is_numeric($post_id)){
		$post_id = url_to_postid( $post_id );
	}

	if(get_post_type($post_id) == 'post' && get_post_status($post_id) == 'publish' && $type == 'post'){
		$post_comment = wp_count_comments( $post_id );
		print json_encode(array('status'=>200,'msg'=>array(
			'id'=>$post_id,
			'title'=>get_the_title($post_id),
			'img'=>zrz_get_thumb(zrz_get_post_thumb($post_id),200,160),
			'href'=>get_permalink($post_id),
		)));
		exit;
	}
	print json_encode(array('status'=>401,'msg'=>__('ID无效','ziranzhi2')));
	exit;
}

//距离现在的时间
function zrz_allow_edit_time($older_date){
    return floor(abs((int)current_time('timestamp') - (int)$older_date) / 3600);
}

//文章状态从其他到已发布时给作者添加积分和发送通知
function zrz_publish_post( $new_status, $old_status, $post ){
	if (!isset($_SESSION)) {
	    @session_start();
	}
    if($new_status != 'publish'){
        remove_id_from_swipe($post->ID);
    }
	//过滤掉机器和其他地方发表的文章
    $zrz_credit_add = isset($_SESSION['zrz_credit_add']) ? $_SESSION['zrz_credit_add'] : '';
	if(!$zrz_credit_add) return;

	unset($_SESSION['zrz_credit_add']);

	if( $new_status != 'publish' || ($post->post_type != 'post' && $post->post_type != 'topic' && $post->post_type != 'reply' && $post->post_type != 'pps' && $post->post_type != 'labs')) return;

	//如果增加过积分
    if(get_post_meta($post->ID,'zrz_credit_add',true)) return;

    //文章作者
    $user_id = $post->post_author;
    $post_type = $post->post_type;

    switch ($post_type) {
        case 'post':
            //发表文章时要添加的积分
            $credit = zrz_get_credit_settings('zrz_credit_post');
            //文章分类
            $id = $post->ID;
            $type = 5;
            break;
        case 'topic':
            //发表帖子时要添加的积分
            $credit = zrz_get_credit_settings('zrz_credit_topic');
            $id = $post->ID;
            $type = 17;
            break;
        case 'reply':
            //发表帖子回复时要添加的积分
            $credit = zrz_get_credit_settings('zrz_credit_reply');
            $id = wp_get_post_parent_id($post->ID);
            $type = 18;

            $parent_user = get_post_field( 'post_author', $id);

            //回帖者给自己回复不添加积分，给话题作者积分和通知
            $rand_credit = zrz_get_credit_settings('zrz_credit_post_commented');
            $rand_credit = explode("-", $rand_credit);
            $rand_credit = rand($rand_credit[0], $rand_credit[1]);
            if($user_id != $parent_user){
				$init = new Zrz_Credit_Message($parent_user,19);
			    $add_msg = $init->add_message($user_id,$rand_credit,$id,$post->ID);
            }

            //提到了某人，给某人加积分和通知
            $string = $post->post_content;
            preg_match_all('#'.home_url('/user/').'([\d]+)#i', $string, $matches);
            $users = $matches[1];
            $users = array_unique($users);
            if(!empty($users)){
                foreach ($users as $user) {
                    if($user != $user_id){
						$init = new Zrz_Credit_Message($user,20);
					    $add_msg = $init->add_message($user_id,$rand_credit,$id,$post->ID);
                    }
                }
            }
            break;
        case 'labs':
            //发表研究时要添加的积分
            $credit = zrz_get_credit_settings('zrz_credit_labs');
            //文章id
            $id = $post->ID;
            $type = 36;
            break;

		case 'pps':
			//发表冒泡时要添加的积分
			$credit = zrz_get_credit_settings('zrz_credit_pps');
			//文章id
			$id = $post->ID;
			$type = 24;
			break;

        default:
            break;
    }

    $init = new Zrz_Credit_Message($user_id,$type);
    $add_msg = $init->add_message($user_id,$credit,$id,$post->ID);

    if($add_msg){
        update_post_meta($post->ID,'zrz_credit_add',1);
    }
}
add_action(  'transition_post_status', 'zrz_publish_post', 10, 3 );


//后台启动上传
add_action('admin_init', 'zrz_admin_post_init', 2);
function zrz_admin_post_init() {
    global $pagenow;
    if(in_array($pagenow, array('post-new.php','edit.php','post.php'))){
		//后台发布的文章，做一个标记
		if (!isset($_SESSION)) {
		    session_start();
		}
		$_SESSION['zrz_credit_add'] = 1;
	}
}


//检查编辑时间是否超时，返回 true 则代表超时，返回 false 则代表未超时
function zrz_check_edit_time($type,$post_id){
	$allow_time = 0;
	if($type == 'post'){
		$allow_time = zrz_get_writing_settings('edit_time');
	}elseif($type == 'labs'){
		$allow_time = zrz_get_writing_settings('labs_edit_time');
	}

	$sin_time = zrz_allow_edit_time(get_post_time('U', false,$post_id));

	return (int)$allow_time - (int)$sin_time;
}

//发布文章
add_action( 'wp_ajax_zrz_insert_post', 'zrz_insert_post' );
function zrz_insert_post(){

	$user_id = get_current_user_id();

	$post_id = isset($_POST['pid']) && is_numeric($_POST['pid']) ? $_POST['pid'] : null;

	//检查是不是机器操作
	check_ajax_referer($user_id, 'security' );

	if(!is_user_logged_in()){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	//检查当前用户的发文权限
	if(!zrz_current_user_can('post') && user_can_edit($post_id)){
		print json_encode(array('status'=>401,'msg'=>__('你没有发文权限','ziranzhi2')));
		exit;
	}

	//检查是否存在分类
	$cats = isset($_POST['cats']) && is_array($_POST['cats']) && !empty($_POST['cats']) ? (array)$_POST['cats'] : '';
	if(!$cats){
		print json_encode(array('status'=>401,'msg'=>__('请选择分类','ziranzhi2')));
		exit;
	}

	$car_arr = array();
	foreach ($cats as $key => $value) {
		$car_arr[] = $key;
	}

	$cats = $car_arr;

	//检查是否存在分类
	$title = isset($_POST['title']) ? wp_strip_all_tags($_POST['title']) : '';
	if(strlen($title) == 0){
		print json_encode(array('status'=>401,'msg'=>__('请输入标题','ziranzhi2')));
		exit;
	}

	//检查文章内容
	if(!isset($_POST['content']) || !$_POST['content']){
		print json_encode(array('status'=>401,'msg'=>__('请输入文章内容','ziranzhi2')));
		exit;
	}

	//检查字数
	$str_length = mb_strlen(wp_strip_all_tags($_POST['content']), 'UTF-8');
	if(zrz_get_writing_settings('min_strlen') > $str_length || zrz_get_writing_settings('max_strlen') < $str_length){
		print json_encode(array('status'=>401,'msg'=>__('请检查文章的字数','ziranzhi2')));
		exit;
	}

	$post_date = $post_date_gmt = null;

	//编辑模式
	if($post_id){

		//检查文章作者
		if((get_post_field( 'post_author', $post_id ) != $user_id || get_post_type($post_id) != 'post') && !current_user_can('edit_users')){
			print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
			exit;
		}

		$user_id = get_post_field( 'post_author', $post_id );

		//检查是否编辑超时
		if(zrz_check_edit_time('post',$post_id) <= 0 && !current_user_can('delete_users')){
			print json_encode(array('status'=>401,'msg'=>__('已过了可编辑的时间','ziranzhi2')));
			exit;
		}

		//时间，如果是文章修改，则不改变时间，否则时间为当前
		if(get_post_status($post_id) == 'publish'){
			$post_date = get_post_time( 'Y-m-d H:i:s', false , $post_id);
			$post_date_gmt = get_post_time( 'Y-m-d H:i:s', true , $post_id);
		}else{
			$post_date = current_time( 'Y-m-d H:i:s', false );
			$post_date_gmt = current_time( 'Y-m-d H:i:s', true);
		}

	}


	//前台发布的文章，做一个标记
	if (!isset($_SESSION)) {
	    session_start();
	}
	$_SESSION['zrz_credit_add'] = 1;

	$draft = isset($_POST['draft']) && is_numeric($_POST['draft']) ? $_POST['draft'] : null;

	//是否草稿
	$type =  zrz_insert_post_role($draft);

	//设置摘要
	$excerpt = isset($_POST['excerpt']) ? wp_strip_all_tags($_POST['excerpt']) : '';

	//提交
	$arg = array(
			'ID'=> $post_id,
			'post_title' => $title,
			'post_content' => $_POST['content'],
			'post_status' => $type,
			'post_author' => $user_id,
			'post_date'      => $post_date,
			'post_date_gmt'  => $post_date_gmt,
			'post_category' => $cats,
			'comment_status'=>'open',
			'post_excerpt'=>$excerpt
	);

	$post_id = wp_insert_post( $arg );

	$files = isset($_POST['filesArg']) ? $_POST['filesArg'] : array();
	if(!empty($files)){
		foreach ($files as $file) {
			wp_update_post(
				array(
					'ID' => $file, 
					'post_parent' => $post_id
				)
			);
		}
	}

	//设置缩略图
	if(isset($_POST['thumb']) && is_numeric($_POST['thumb']) && $post_id){
		set_post_thumbnail($post_id, $_POST['thumb']);
		delete_post_meta($post_id,'zrz_thumb_video');
	}

	//设置文章形式
	$post_format = isset($_POST['format']) ? $_POST['format'] : 'none';
	set_post_format( $post_id , $post_format);

	//设置标签
	if(isset($_POST['tags']) && is_array($_POST['tags']) && $post_id){
		wp_set_post_tags($post_id, $_POST['tags'], false);
	}

	//设置视频
	if(isset($_POST['thumbVideo']) && !empty($_POST['thumbVideo'])){
		update_post_meta($post_id,'zrz_thumb_video',$_POST['thumbVideo']);
		delete_post_thumbnail($post_id);
	}

	//设置相关文章
	$related = isset($_POST['related']) && is_array($_POST['related']) ? $_POST['related'] : '';
	if($related){
		$related_arr = array();
		foreach ($related as $val) {
			$related_arr[] = $val['id'];
		}
		update_post_meta($post_id,'zrz_related',$related_arr);
	}else{
		delete_post_meta($post_id,'zrz_related');
	}

	//设置阅读权限
	$capabilities = isset($_POST['capabilities']) ? $_POST['capabilities'] : '';
	if($capabilities == 'credit'){
		update_post_meta($post_id,'capabilities',array('key'=>'credit','val'=>isset($_POST['credit']) ? (int)$_POST['credit'] : 0));
	}elseif($capabilities == 'lv'){
		update_post_meta($post_id,'capabilities',array('key'=>'lv','val'=>isset($_POST['lv']) ? (array)$_POST['lv'] : array()));
	}elseif($capabilities == 'rmb'){
		update_post_meta($post_id,'capabilities',array('key'=>'rmb','val'=>isset($_POST['rmb']) ? $_POST['rmb'] : 0));
	}elseif($capabilities == 'login'){
		update_post_meta($post_id,'capabilities',array('key'=>'login','val'=>''));
	}else{
		update_post_meta($post_id,'capabilities',array('key'=>'default','val'=>''));
	}

	if($post_id){
		if($type == 'publish'){
			wp_publish_post( $post_id );
		}
		print json_encode(array('status'=>200,'msg'=>__('发布成功','ziranzhi2'),'url'=>get_permalink($post_id)));
		exit;
	}else{
		print json_encode(array('status'=>401,'msg'=>__('发布失败','ziranzhi2')));
		exit;
	}

}

//商品插入文章短代码
function zrz_insert_post_fn( $atts, $content = null){
	if(!empty($atts)){
		foreach ($atts as $key => $val) {
			$post_id = $val;
			if(is_numeric($post_id)){
				$post_type  = get_post_type($post_id);
				$post_status = get_post_status($post_id);
				$des = zrz_get_post_des($post_id);
				$thumb = zrz_get_thumb(zrz_get_post_thumb($post_id),150,150);
				$view = get_post_meta($post_id,'views',true);

				if(($post_type == 'post' || $post_type == 'page') && $post_status == 'publish'){
					$post_comment = wp_count_comments( $post_id );
					return '<div class="content-post-box content-box" >
							<span class="pos-a img-bg blur-dk" style="background-image:url('.$thumb.')"></span>
							<div class="thumb-in" style="background-image: url('.$thumb.');"><a class="link-block" href="'.get_permalink($post_id).'"></a></div>
							<div class="content-post-box-des">
								<h2><a target="_blank" href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a></h2>
								'.($des ? '<p class="link-des">'.$des.'</p>' : '').'
								<div class="fs12 gray">
									<span class="postbox-comment"><i class="iconfont zrz-icon-font-pinglun"></i>'.$post_comment->approved.'</span><span class="postbox-views mar10-l"><i class="iconfont zrz-icon-font-eye"></i>'.$view.'</span>
								</div>
							</div>
						</div>';
				}elseif($post_type == 'shop' && $post_status == 'publish'){
					$s_type = get_post_meta($post_id, 'zrz_shop_type', true);

					if($s_type == 'normal'){
						$price = zrz_get_shop_price_dom($post_id);
						$price = $price['price'];
					}elseif($s_type == 'exchange'){
						$price = get_post_meta($post_id,'zrz_shop_need_credit',true);
					}else{
						$price = zrz_get_shop_lottery($post_id,'credit');
					}
					return '<div class="shop-box content-post-box content-box">
						<span class="pos-a img-bg blur-dk" style="background-image:url('.$thumb.')"></span>
							<div class="thumb-in" style="background-image: url('.$thumb.');"><a class="link-block" href="'.get_permalink($post_id).'"></a></div>
							<div class="content-post-box-des">
								<h2><a target="_blank" href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a><span data-shop-type="normal">['.($s_type == 'normal' ? '出售' : ($s_type == 'exchange' ? '兑换' : '抽奖')).']</span></h2>
								'.($des ? '<p class="link-des">'.$des.'</p>' : '').'
								<div class="content-post-box-price">
									<span data-shop-price="0.01">'.$price.'</span></div> <div class="fs12 gray"><span class="postbox-remaining">剩余：<b>'.zrz_shop_count_remaining($post_id).'</b></span><span class="postbox-views mar10-l">热度：<b>'.$view.'<b></b></b></span>
								</div>
							</div>
						</div>
					';
				}
			}
		}
	}
	return '';
}
add_shortcode( 'zrz_insert_post', 'zrz_insert_post_fn' );

add_shortcode( 'zrz_inv', 'zrz_inv_fn' );
function zrz_inv_fn($atts, $content = null ){
	$start = isset($atts['start']) ? (int)$atts['start'] : 1;
	$end = isset($atts['end']) ? (int)$atts['end'] : 20;
	$name = zrz_get_credit_settings('zrz_credit_name');
	global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_invitation';
	$codes = $wpdb->get_results( "SELECT * FROM $table_name WHERE id>=$start AND id<=$end" ,ARRAY_A );

	$html = '';
	if(count($codes) > 0){
		$html = '<table class="wp-list-table widefat fixed striped shop_page_order_option">
		<thead>
			<tr><td>编号</td><td>邀请码</td><td>奖励的'.$name.'</td><td>使用状态</td><td>使用者</td></tr>
		</thead>
		<tbody>';
		$i = 0;
		foreach ($codes as $code) {
			$i++;
			if($code['invitation_user']){
				$user = zrz_get_user_page_link($code['invitation_user']);
			}else{
				$user = '无';
			}
			$html .= '<tr>
			<td>'.$i.'</td>
			<td>'.$code['invitation_nub'].'</td>
			<td>'.$code['invitation_credit'].'</td>
			<td>'.($code['invitation_status'] ? '<span style="color:green">已使用</span>' : '<span style="color:red">未使用</span>').'</td>
			<td>'.$user.'</td>
			</tr>';
		}
		$html .= '</tbody>
		</table>';
	}
	return $html;
}

//下载按钮短代码
add_shortcode( 'zrz_file', 'zrz_file_down' );
function zrz_file_down($atts, $content = null ){
	if(empty($atts)) return;
	$r = apply_filters('zrz_file_short_arg',wp_parse_args($atts,array(
        'link'=>'',
        'name'=>'',
        'pass'=>'无',
        'code'=>'无',
        'local'=>''
    )));

	return '<div class="single-file bg-blue-light pd20 mar20-b">
		<div class="single-file-title clearfix mar10-b">
			<i class="iconfont zrz-icon-font-fujian"></i>
			<h2>'.$r['name'].'</h2>
			<p>提取码：'.$r['pass'].'，解压码：'.$r['code'].'</p>
		</div>
		<div class="single-file-content pos-r">
			<a class="mar10-r button" target="_blank" href="'.$r['link'].'" '.($r['local'] ? 'download="'.$r['name'].basename( $r['link'] ).'"' : '').'><i class="zrz-icon-font-xiazai iconfont"></i>下载</a>
			<button class="down-qcode"><i class="zrz-icon-font-erweima1 iconfont"></i>二维码</button>
			<div class="down-qcode-img hide pos-a box"><img src="'.ZRZ_THEME_URI.'/inc/qrcode/index.php?c='.$r['link'].'" /></div>
		</div>
	</div>';
}

//文章阅读短代码
if(!function_exists('zrz_content_hide')){
	function zrz_content_hide( $atts, $content = null ) {
		$user_id = get_current_user_id();

		global $post;
		$post_id = $post->ID;

		//获取文章阅读权限
		$cap = get_post_meta($post_id,'capabilities',true);

		//获取当前的用户组
		$c_rol = zrz_get_lv($user_id,'');

		//检查该用户组是否不限制阅读
		$c_lv = zrz_get_lv_settings($c_rol);
		$c_lv = isset($c_lv['allow_all']) && $c_lv['allow_all'] == 1 ? true : false;

		//如果没有限制，直接返回内容
		if(!$cap || !isset($cap['key']) || !is_array($cap) || $cap['key'] == 'default') return do_shortcode($content);

		//获取积分名称
		$name = zrz_get_credit_settings('zrz_credit_name');

		//如果是文章作者或者管理员直接查看
		if($user_id == get_post_field('post_author',$post_id) || current_user_can('edit_users') || $c_lv)
		return '<div class="content-hide-tips pos-r pd20b">
			<span class="fs12 gray pos-a content-hide-text">隐藏内容：'
			.($cap['key'] == 'credit' ? $name.'兑换' : ($cap['key'] == 'lv' ? '权限阅读' : ($cap['key'] == 'rmb' ? '付费阅读' : ($cap['key'] == 'login' ? '登录可见' : '')))).
			'</span><i class="iconfont zrz-icon-font-suokai pos-a"></i>'.do_shortcode($content).'
		</div>';

		//如果是登录用户可见
		if($cap['key'] == 'login'){
			if(is_user_logged_in()){
				return '<div class="content-hide-tips pos-r pd20b"><span class="fs12 gray pos-a content-hide-text">已登录</span><i class="iconfont zrz-icon-font-suokai pos-a"></i>'.do_shortcode($content).'</div>';
			}else{
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="mar20-b">当前内容已被隐藏，您需要登录才能查看</div>
					<button class="empty" @click="login(\'in\')">登录</button><button v-if="canReg == 1" class="mar10-l" @click="login(\'up\')" v-cloak>立刻注册</button>
				</div>';
			}
		}

		//如果是限制权限阅读
		if($cap['key'] == 'lv'){
			$cap_arr = isset($cap['val']) ? $cap['val'] : array();
			$user_lv = zrz_get_lv($user_id,'');
			$html = '<div class="content-hide-tips pos-r">';
			$cap_str = '';
			foreach ($cap_arr as $value) {
				$cap_str .= '<span class="user-lv '.$value.' mar10-l"><i class="zrz-icon-font-'.(strpos($value,'vip') !== false ? 'vip' : $value).' iconfont"></i></span>';
			}

			//如果用户未登录
			if(!is_user_logged_in()){
				$html .= '
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="no-credit t-c">
					<div class="mar20-b">限制以下用户组阅读此隐藏内容 </div>'.$cap_str.'
					<p class="gray">请先登录</p>
					<p class="fs12"><button class="empty" @click="login(\'in\')">登录</button><button v-if="canReg == 1" class="mar10-l" @click="login(\'up\')" v-cloak>立刻注册</button></p>
					</div>
					<span class="fs12 gray pos-a content-hide-text">您的用户组：'.zrz_get_lv($user_id,'name').'</span>';
			}elseif(!in_array($user_lv,$cap_arr,true)){
				$html .= '
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="no-credit t-c">
					<div class="mar20-b">限制以下用户组阅读此隐藏内容 </div>'.$cap_str.'
					<p class="fs12">权限不足，您可以参与网站互动提升等级，或者 <a class="empty" href="'.home_url('/gold').'">'.$name.'充值提升等级</a></p>
					</div>
					<span class="fs12 gray pos-a content-hide-text">您的用户组：'.zrz_get_lv($user_id,'name').'</span>';
			}else{
				$html .= '<span class="fs12 gray pos-a content-hide-text">会员专属内容</span><i class="iconfont zrz-icon-font-suokai pos-a"></i>'.do_shortcode($content);
			}
			$html .= '</div>';
			return $html;
		}


		//检查用户是否支付过（包含积分支付和人民币支付）
		$user_arr = get_post_meta($post_id,'zrz_buy_user',true);
		$user_arr = is_array($user_arr) ? $user_arr : array();
		$count = count($user_arr);

		//已经支付，则直接查看
		if(in_array($user_id,$user_arr,true)){
			return '<div class="content-hide-tips pos-r pd20b"><span class="fs12 gray pos-a content-hide-text">您已支付</span><i class="iconfont zrz-icon-font-suokai pos-a"></i>'.do_shortcode($content).'</div>';
		}

		/*
		* 积分支付可见
		*/
		//如果是积分支付
		if($cap['key'] == 'credit'){
			$credit = $cap['val'];

			//检查积分剩余
			$user_credit = (int)zrz_coin($user_id,'nub');

			//检查积分情况
			if(!is_user_logged_in()){
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="login-false mar20-b">当前隐藏内容需要支付'.zrz_coin(0,0,$credit).$name.'</div>
					<p class="t-c">'.($count > 0 ? '已有<span class="red">'.$count.'</span>人支付' : '').'</p>
					<div class="pc-button"><button class="empty" @click="login(\'in\')">登录</button><button v-if="canReg == 1" class="mar10-l" @click="login(\'up\')" v-cloak>立刻注册</button></div>
					<div class="miniapp-button"><a class="mini-signup" href="#signup">授权登录</a></div>
				</div>';
				//检查用户积分是否足够，积分不足，提示充值
			}elseif($user_credit < $credit) {
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="no-credit">
					<div class="mar10-b t-c">阅读当前隐藏内容需要支付'.zrz_coin(0,0,$credit).$name.'
					<p class="t-c">'.($count > 0 ? '已有<span class="red">'.$count.'</span>人支付' : '').'</p>
					<p class="gray">您的'.$name.'不足</p>
					<p class="fs12">您可以参与网站互动获取'.$name.'，或者 <a class="empty" href="'.home_url('/gold').'">'.$name.'充值</a></p>
					</div>
					<span class="fs12 gray pos-a content-hide-text">您的'.$name.'：'.zrz_coin(0,0,$user_credit).'</span>
				</div>';
				//未支付，提示充值
			}else{
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
				<div class="mar20-t">
				<p class="t-c">阅读当前隐藏内容需要支付'.zrz_coin(0,0,$credit).$name.'</p>
				<p class="t-c">'.($count > 0 ? '已有<span class="red">'.$count.'</span>人支付' : '').'</p>
				<button class="mar10-t" @click="coinPay"><span v-text="coinPayMsg">立刻支付</span></button>
				</div>
				<span class="fs12 gray pos-a content-hide-text">您的'.$name.'：'.zrz_coin(0,0,$user_credit).'</span>
				</div>';
			}
		}

		/*
		* 人民币支付可见
		*/
		//如果是金钱支付
		if($cap['key'] == 'rmb'){

			$need_rmb = $cap['val'];

			//检查用户余额
			$user_rmb = get_user_meta($user_id,'zrz_rmb',true);
			$user_rmb = $user_rmb ? $user_rmb : 0;

			if(!is_user_logged_in()){
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="login-false mar20-b">当前隐藏内容需要支付 <span class="content-hide-rmb">¥'.$need_rmb.'</span>。'.($count > 0 ? '已有<span class="red">'.$count.'</span>人支付' : '').'</div>
					<button @click="login(\'in\')">登录</button><button v-if="canReg == 1" class="mar10-l" @click="login(\'up\')" v-cloak>立刻注册</button>
				</div>';
			}elseif($user_rmb < $need_rmb){
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="login-false mar20-b">当前隐藏内容需要支付</div>
					<div class="content-hide-rmb">¥'.$need_rmb.'</div>
					<p class="t-c">'.($count > 0 ? '已有<span class="red">'.$count.'</span>人支付' : '').'</p>
					<button @click="payRmb(\''.$need_rmb.'\')">立刻支付</button>
					<span class="fs12 gray pos-a content-hide-text">您的余额：¥'.$user_rmb.'</span>
				</div>';
			}else{
				return '<div class="content-hide-tips pos-r t-c">
				<i class="iconfont zrz-icon-font-suo-copy pos-a"></i>
					<div class="login-false mar20-b">当前隐藏内容需要支付</div>
					<div class="content-hide-rmb mar20-b">¥'.$need_rmb.'</div>
					<p class="t-c">'.($count > 0 ? '已有<span class="red">'.$count.'</span>人支付' : '').'</p>
					<button @click="payRmb(\''.$need_rmb.'\')">立刻支付</button>
					<span class="fs12 gray pos-a content-hide-text">您的余额：¥'.$user_rmb.'</span>
				</div>';
			}

		}

		//如果无限制，直接输出
		return '<div class="post-content-hide">' . do_shortcode($content) . '</div>';
	}
}
add_shortcode( 'content_hide', 'zrz_content_hide' );

//积分购买文章
add_action( 'wp_ajax_zrz_post_pay_coin', 'zrz_post_pay_coin' );
function zrz_post_pay_coin(){
	if(!is_user_logged_in()){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	$post_id = isset($_POST['pid']) ? (int)$_POST['pid'] : 0 ;
	if(!$post_id){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	$user_id = get_current_user_id();

	//获取用户剩余积分
	$credit = get_user_meta($user_id,'zrz_credit_total',true);

	//获取文章所需积分
	$cap = get_post_meta($post_id,'capabilities',true);

	if(isset($cap['key']) && isset($cap['val']) && $cap['key'] == 'credit'){
		$need_credit = $cap['val'];

		//检查用户积分是否足够
		if($credit < $need_credit){
			print json_encode(array('status'=>401,'msg'=>__('积分不足','ziranzhi2')));
			exit;
		}

		//记录已经购买的用户
		zrz_update_shop_buy_user($user_id,$post_id);

		$post_author = get_post_field('post_author',$post_id);

		//给购买者添加消息,减掉积分
		$init = new Zrz_Credit_Message($user_id,33);
		$add_msg = $init->add_message($post_author, -$need_credit,$post_id,0);

		//给文章作者添加消息，增加积分
		$_init = new Zrz_Credit_Message($post_author,34);
		$_add_msg = $_init->add_message($user_id, $need_credit,$post_id,0);
		do_action('zrz_shop_buy_action', 'post_coin',$user_id,$post_id);
		print json_encode(array('status'=>200,'msg'=>$add_msg));
		exit;
	}
}

//删除文章
add_action('wp_ajax_zrz_del_post','zrz_del_post');
function zrz_del_post(){

	//检查用户是否登陆
	if(!is_user_logged_in()){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	$post_id = isset($_POST['pid']) && is_numeric($_POST['pid']) ? $_POST['pid'] : 0;
	$type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';
	if(!$type){
		$type = 'post';
	}

	$user_id = get_current_user_id();

	//检查用户权限
	if(get_post_field( 'post_author', $post_id ) != $user_id && !current_user_can('delete_users')){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
	}

	wp_trash_post($post_id);
	print json_encode(array('status'=>200,'msg'=>__('删除成功','ziranzhi2')));
	exit;
}

//设置幻灯
add_action('wp_ajax_zrz_set_post_swipe','zrz_set_post_swipe');
function zrz_set_post_swipe(){
	$post_id = isset($_POST['pid']) ? $_POST['pid'] : 0;
	if(!current_user_can('delete_users')) exit;

	//获取幻灯的文章
	$hd = get_option('zrz_swipe_posts');
	$hd = is_array($hd) ? $hd : array();
	$ac = remove_id_from_swipe($post_id);
	if($ac){
		print json_encode(array('status'=>200,'msg'=>'del'));
		exit;
	}else{
		$hd[$post_id] = '0';
		update_option('zrz_swipe_posts',$hd);
		print json_encode(array('status'=>200,'msg'=>'add'));
		exit;
	}
}

//当前文章是不是在幻灯中
function zrz_is_id_in_swipe($post_id){
	$hd = get_option('zrz_swipe_posts');
	if(isset($hd[$post_id])) return true;
	return false;
}

//从幻灯中移除
function remove_id_from_swipe($post_id){
	if(zrz_is_id_in_swipe($post_id)){
		$hd = get_option('zrz_swipe_posts');
		unset($hd[$post_id]);
		update_option('zrz_swipe_posts',$hd);
		return true;
	}
	return false;
}

function zrz_insert_post_role($draft) {
	//是否草稿
	if($draft){
		$type = 'draft';
	}elseif(current_user_can('edit_users')){
		$type = 'publish';
	}elseif(zrz_get_writing_settings('status') == 0){
		$type = 'pending';
	}else{
		$type = 'publish';
	}
	return apply_filters('zrz_insert_post_role_filter', $type);
}

//获取文章形式
function zrz_get_post_cap($post_id = 0){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}

	$cap = get_post_meta($post_id,'capabilities',true);
	$cap_icon = '';
	if(isset($cap['key']) && isset($cap['val'])){
			switch ($cap['key']) {
				case 'rmb':
					$cap_icon = '<i class="iconfont zrz-icon-font-fufeilianjie"></i>';
					break;
				case 'credit':
					$cap_icon = '<i class="iconfont zrz-icon-font-credit"></i>';
					break;
				case 'lv':
					$cap_icon = '<i class="iconfont zrz-icon-font-quanxian"></i>';
					break;
				case 'login':
					$cap_icon = '<i class="iconfont zrz-icon-font-denglu"></i>';
					break;
				default:
					$cap_icon = '';
                break;
			}
	}
	$cap_icon = '<span class="pos-a post-cap fs12 shadow">'.$cap_icon.'</span>';

	return apply_filters('zrz_insert_post_role_filter', $cap_icon);

}

//后台发文，增加选项
add_action('add_meta_boxes','zrz_post_metas_box_init');
function zrz_post_metas_box_init(){
	add_meta_box('post-role-metas',__('权限设置','ziranzhi2'),'zrz_post_role_box','post','side','high');
	add_meta_box('forum-role-metas',__('阅读权限设置','ziranzhi2'),'zrz_forum_role_box','forum','side','high');
}

function zrz_forum_role_box($post){
	$post_id = isset($post->ID) ? $post->ID : 0;
	if(get_post_type($post_id) != 'forum') return;
	
	$cap = get_option('zrz_bbp_capabilities',true);
	$cap = is_array($cap) ? $cap : array();
	$cap = isset($cap[$post_id]) ? $cap[$post_id] : array();

	$lv = zrz_get_lv_settings();
	$lv_dom = '<p>请选选择允许参与的权限组</p><div id="zrz-lv">';
	foreach ($lv as $key => $val) {
		$checked = '';
		if(isset($val['open']) && $val['open'] == 0) continue;
		if(in_array($key,$cap)){
			$checked = 'checked';
		}
		$lv_dom .= '<p style="margin-bottom:10px"><label class="dis"><input type="checkbox" value="'.$key.'" name="zrz_bbp_capabilities[]" '.$checked.'>'.$val['name'].'</label></p>';
	 }
	 $lv_dom .= '</div>';
	 echo $lv_dom;
	 unset($lv_dom);
}

add_action('save_post','zrz_forum_metas_box_save');
function zrz_forum_metas_box_save($post_id){
	$post_type = get_post_type($post_id);
	if($post_type != 'forum' || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) return $post_id;
	$setting = get_option('zrz_bbp_capabilities',true);
	$setting = is_array($setting) ? $setting : array();

	if(isset($_POST['zrz_bbp_capabilities']) && !empty($_POST['zrz_bbp_capabilities'])){
		$setting[$post_id] = $_POST['zrz_bbp_capabilities'];
	}else{
		unset($setting[$post_id]);
	}
	update_option('zrz_bbp_capabilities',$setting);
}

function zrz_post_role_box($post){
	$post_id = isset($post->ID) ? $post->ID : 0;
	if($post_id){
		$capabilities = '';
		$capabilities_value = array();

		$cap = get_post_meta($post_id,'capabilities',true);
		if(isset($cap['key']) && isset($cap['val'])){
			$capabilities = $cap['key'];
			$capabilities_value = $cap['val'];
		}
	}
	$lv = zrz_get_lv_settings();
	$lv_dom = '';
	foreach ($lv as $key => $val) {
		$checked = '';
		if(isset($val['open']) && $val['open'] == 0) continue;
		if(is_array($capabilities_value) && in_array($key,$capabilities_value)){
			$checked = 'checked';
		}
		$lv_dom .= '<p style="margin-bottom:10px"><label class="dis"><input type="checkbox" value="'.$key.'" name="lv[]" '.$checked.'>'.$val['name'].'</label></p>';
	 }
	 $name = zrz_get_credit_settings('zrz_credit_name');
	$html = '
		<p>阅读权限：</p>
		<div style="margin-bottom:20px">
			<label style="display:block"><input type="radio" value="default" name="capabilities" '.($capabilities == 'default' ? 'checked' : '').' class="role-checked">无限制</label>
			<label style="display:block"><input type="radio" value="login" name="capabilities" '.($capabilities == 'login' ? 'checked' : '').' class="role-checked">登录可见</label>
			<label style="display:block"><input type="radio" value="credit" name="capabilities" '.($capabilities == 'credit' ? 'checked' : '').' class="role-checked">'.$name.'阅读</label>
			<label style="display:block"><input type="radio" value="rmb" name="capabilities" '.($capabilities == 'rmb' ? 'checked' : '').' class="role-checked">付费阅读</label>
			<label style="display:block"><input type="radio" value="lv" name="capabilities" '.($capabilities == 'lv' ? 'checked' : '').' class="role-checked">允许阅读的用户组</label>
		</div>
		<div ="">
			<div id="zrz-credit" style="margin-top:20px;display:none;">
				<input type="text" placeholder="请输入'.$name.'" value="'.($capabilities == 'credit' ? $capabilities_value : '').'" name="credit">
			</div>
			<div id="zrz-rmb" style="margin-top:20px;display:none;">
			<input type="text" placeholder="请输入金额（单位元）" value="'.($capabilities == 'rmb' ? $capabilities_value : '').'" name="rmb">
			</div>
			<div id="zrz-lv" style="display:none;margin-top:20px">
				'.$lv_dom.'
			</div>
		</div><div style="margin-top:20px">设置好权限以后，请将要隐藏的内容使用以下短代码包裹起来：<p><code>[content_hide]</code><br><br><code>[/content_hide]</code></p></div>';
		echo $html;
		echo '<style>.dis{display:block}</style><script>
		var radio = document.querySelectorAll(".role-checked");
		for (var i = 0; i < radio.length; i++) {
			var val = radio[i].value;
			if(radio[i].checked == true){
				valueCheck(val);
			}
			radio[i].onclick = function(event){
				valueCheck(event.target.value);
			}
		}
		function valueCheck(val){
			if(val == "credit"){
				document.querySelector("#zrz-credit").style.display = "block";
				document.querySelector("#zrz-rmb").style.display = "none";
				document.querySelector("#zrz-lv").style.display = "none";
			}else if(val == "rmb"){
				document.querySelector("#zrz-credit").style.display = "none";
				document.querySelector("#zrz-rmb").style.display = "block";
				document.querySelector("#zrz-lv").style.display = "none";
			}else if(val == "lv"){
				document.querySelector("#zrz-credit").style.display = "none";
				document.querySelector("#zrz-rmb").style.display = "none";
				document.querySelector("#zrz-lv").style.display = "block";
			}else{
				document.querySelector("#zrz-credit").style.display = "none";
				document.querySelector("#zrz-rmb").style.display = "none";
				document.querySelector("#zrz-lv").style.display = "none";
			}
		}
		</script>';
		unset($html);
}

add_action('save_post','zrz_post_metas_box_save');
function zrz_post_metas_box_save($post_id){
	$post_type = get_post_type($post_id);
	if($post_type != 'post' || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) return $post_id;
	if(!isset($_POST['capabilities'])) return $post_id;
	$capabilities = isset($_POST['capabilities']) ? $_POST['capabilities'] : '';
	if($capabilities == 'credit'){
		update_post_meta($post_id,'capabilities',array('key'=>'credit','val'=>isset($_POST['credit']) ? (int)$_POST['credit'] : 0));
	}elseif($capabilities == 'lv'){
		update_post_meta($post_id,'capabilities',array('key'=>'lv','val'=>isset($_POST['lv']) ? (array)$_POST['lv'] : array()));
	}elseif($capabilities == 'rmb'){
		update_post_meta($post_id,'capabilities',array('key'=>'rmb','val'=>isset($_POST['rmb']) ? $_POST['rmb'] : 0));
	}elseif($capabilities == 'login'){
		update_post_meta($post_id,'capabilities',array('key'=>'login','val'=>''));
	}else{
		update_post_meta($post_id,'capabilities',array('key'=>'default','val'=>''));
	}
}

add_action( 'wp_ajax_zrz_img_url_to_base64', 'zrz_img_url_to_base64' );
add_action( 'wp_ajax_nopriv_zrz_img_url_to_base64', 'zrz_img_url_to_base64' );
function zrz_img_url_to_base64(){
	$url = isset($_POST['url']) ? $_POST['url'] : '';
	check_ajax_referer('long-img', 'security' );
	$url = zrz_get_thumb($url,430,430);

	if($url){
		$file_contents = wp_remote_post($url, array(
			'method' => 'GET',
			'timeout' => 300,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array( 'Accept-Encoding' => '' ),
			'sslverify' => false,
			)
		);
	
		$img_base64 = base64_encode($file_contents['body']);
		if($img_base64){
			print json_encode(array('status'=>200,'msg' =>'data:image/jpeg;base64,'.$img_base64));
			unset($img_base64);
			exit;
		}
	}
	print json_encode(array('status'=>401,'msg' =>'封面图片获取失败'));
	exit;
}

function user_can_edit($post_id = 0){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}
	$status = get_post_status($post_id);
	$post_type = get_post_type($post_id);
	$time = zrz_check_edit_time($post_type,$post_id);
	$time = $time > 0 ? $time : 0;
	$current_user_id = get_current_user_id();
	$post_author = get_post_field('post_author',$post_id);
	$admin = current_user_can('delete_users');

	//如果是管理员，则没有限制
	return $admin || ($current_user_id == $post_author && $status != 'publish' && $time > 0) ? true : false;
}

//文章编辑按钮
function zrz_post_edit_button($post_id = 0){
	if(!$post_id){
		global $post;
		$post_id = $post->ID;
	}
	$can = user_can_edit($post_id);
	$status = zrz_post_status($post_id);
	$status = $status ? $status : '已发布';
	$html = '';
	$post_type = get_post_type($post_id);
	$time = zrz_check_edit_time($post_type,$post_id);
	$time = $time > 0 ? $time : 0;
	$post_name = $post_type == 'post' ? '文章' : '研究';
	$admin = current_user_can('delete_users');

	if($can){
		$html .= '<div class="single-edit pd10 clearfix fs12"><div class="fl">当前'.$post_name.'处于 <span class="red">'.$status.' </span>状态，
		'.($admin ? '您是管理员，可以随时编辑。' : ($time > 0 ? '您在接下来的 <span class="red">'.$time.'</span> 小时之内可以继续编辑。' : '')).'</div>
		<div class="fr"><a class="text mar10-r button" href="'.($post_type == 'post' ? home_url('/write?pid='.$post_id) : home_url('/add-labs?pid='.$post_id)).'">编辑</a><button class="text mar10-r delete-post" @click="deletePost('.$post_id.')">删除</button>';
		if(current_user_can('delete_users')){
			$html .= '<button class="text set-hd" @click="setSwipe('.$post_id.',$event)">'.(zrz_is_id_in_swipe($post_id) ? '取消幻灯' : '设为幻灯').'</button>';
		}
		$html .= '</div></div>';
	}
	return apply_filters('zrz_post_edit_button_filter', $html);
}

//获取给文章打赏的人
function zrz_get_post_shang($post_id){
	$data = get_post_meta($post_id,'zrz_shang',true);
	$data = is_array($data) ? $data : array();
	$html = '<ul>';
	if(!empty($data)){
		$data = array_reverse($data);
		foreach ($data as $val) {
			$html .= '<li class="pos-r"><a class="link-block" href="'.zrz_get_user_page_url($val['user']).'">'.get_avatar($val['user'],50).'</a><div class="hide pjt"><p>'.get_the_author_meta('display_name',$val['user']).'</p><b>¥'.$val['rmb'].'</b></div></li>';
		}
		$count = count($data);
	}else{
		$html .= '<div class="gray">还没有人赞赏，快来当第一个赞赏的人吧！</div>';
		$count = 0;
	}

	$html .= '</ul>';
	return apply_filters('zrz_get_post_shang_fliter',array('count'=>$count,'html'=>$html));
}

//获取分类的分页数
add_action( 'wp_ajax_zrz_get_cat_posts_pages', 'zrz_get_cat_posts_pages' );
add_action( 'wp_ajax_nopriv_zrz_get_cat_posts_pages', 'zrz_get_cat_posts_pages' );
function zrz_get_cat_posts_pages(){
	$cat_id = isset($_POST['cat']) ? (int)$_POST['cat'] : 0;
	if(!$cat_id) exit;
	$nub = get_option('posts_per_page',18);
	$count = zrz_number_postpercat($cat_id);
	$pages = ceil( $count / $nub);
	print json_encode(array('status'=>200,'msg' =>$pages));
	exit;
}

function zrz_number_postpercat($idcat) {
	global $wpdb;
	$query = "SELECT count FROM $wpdb->term_taxonomy WHERE term_id = $idcat";
 	$num = $wpdb->get_col($query);
	return $num[0];
}