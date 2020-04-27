<?php
/*
* 研究所 相关方法
*/

//添加研究所的文章类型
function zrz_create_post_type() {
	$name = zrz_custom_name('labs_name');
	$labels = array(
 		'name' => sprintf( '%1$s',$name),
    	'singular_name' => sprintf( '%1$s',$name),
    	'add_new' => __('添加一个研究','ziranzhi2'),
    	'add_new_item' => __('添加一个研究','ziranzhi2'),
    	'edit_item' => __('编辑研究','ziranzhi2'),
    	'new_item' => __('新的研究','ziranzhi2'),
    	'all_items' => __('所有研究','ziranzhi2'),
    	'view_item' => __('查看研究','ziranzhi2'),
    	'search_items' => __('搜索研究','ziranzhi2'),
    	'not_found' =>  __('没有研究','ziranzhi2'),
    	'not_found_in_trash' =>__('回收站为空','ziranzhi2'),
    	'parent_item_colon' => '',
    	'menu_name' => sprintf( '%1$s',$name),
    );
	register_post_type( 'labs', array(
		'labels' => $labels,
		'has_archive' => true,
 		'public' => true,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail','comments'),
		'taxonomies' => array('labtype','post_tag'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'labs' ),
		)
	);
}
add_action( 'init', 'zrz_create_post_type' );

//添加研究所的研究项目
add_action( 'init', 'zrz_create_lab_taxonomies', 0 );
function zrz_create_lab_taxonomies() {

	$labels = array(
		'name'              => __( '研究类型', 'ziranzhi2' ),
		'singular_name'     => __( '研究类型', 'ziranzhi2' ),
		'search_items'      => __( '搜索研究类型', 'ziranzhi2' ),
		'all_items'         => __( '所有研究类型', 'ziranzhi2' ),
		'parent_item'       => __( '父级研究类型', 'ziranzhi2' ),
		'parent_item_colon' => __( '父级研究类型：', 'ziranzhi2' ),
		'edit_item'         => __( '编辑研究类型', 'ziranzhi2' ),
		'update_item'       => __( '更新研究类型', 'ziranzhi2' ),
		'add_new_item'      => __( '添加研究类型', 'ziranzhi2' ),
		'new_item_name'     => __( '研究类型名称', 'ziranzhi2' ),
		'menu_name'         => __( '研究类型', 'ziranzhi2' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'labs' ),
		'capabilities' => array(
			'delete_terms'=>false,
			'manage_terms'=>false,
			'edit_terms'=>false
		)
	);

	register_taxonomy( 'labtype', array( 'labtype' ), $args );
}

/**
 * 修改链接类型为post_id.html
 */
 add_filter('post_type_link', 'zrz_custom_lab_link', 1, 3);
 function zrz_custom_lab_link( $link, $post = 0 ){
     if ( $post->post_type == 'labs' ){
         return home_url( 'labs/' . $post->ID .'.html' );
     } else {
         return $link;
     }
 }
 add_action( 'init', 'zrz_custom_lab_rewrites_init' );
 function zrz_custom_lab_rewrites_init(){
     add_rewrite_rule(
         'labs/([0-9]+)?.html$',
         'index.php?post_type=labs&p=$matches[1]',
         'top' );
     add_rewrite_rule(
         'labs/([0-9]+)?.html/comment-page-([0-9]{1,})$',
         'index.php?post_type=labs&p=$matches[1]&cpage=$matches[2]',
         'top'
         );
 }

//获取研究项目类型
function zrz_get_labs_terms($type,$post_id = 0){
	if(!$post_id){
		global $post;
		if(isset($post->ID)){
			$post_id  = $post->ID;
		}else{
			$post_id = 0;
		}
	}

	$term_list = wp_get_post_terms($post_id, 'labtype', array('fields' => $type));
	if($term_list) return $term_list[0];
}

//发布一个研究
add_action( 'wp_ajax_zrz_save_labs', 'zrz_save_labs' );
function zrz_save_labs(){

	$post_author_id = get_current_user_id();
	$post_id = 0;

	check_ajax_referer($post_author_id, 'security' );
	$time = 1;

	if(isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id'])){
		$post_id = (int)$_REQUEST['post_id'];
		$post_author_id = get_post_field( 'post_author', $post_id);
		$time = zrz_check_edit_time('labs',$post_id);
		$time = $time > 0 ? $time : 0;
	}

	//检查是否登录，并且是否有发布的权限
	if(!zrz_current_user_can('labs')){
		print json_encode(array('status'=>401,'msg'=>__('你没有权限这么做','ziranzhi2'),'w'=>'s'));
		exit;
	}

	//如果是编辑文章检查用户权限
	if($post_author_id && ($post_author_id !== get_current_user_id() && !current_user_can('edit_users'))){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2'),'w'=>'s'));
		exit;
	}

	//检查是否超时
	if($time <= 0 && !current_user_can('delete_users')){
		print json_encode(array('status'=>401,'msg'=>__('已过了可编辑的时间','ziranzhi2'),'w'=>'s'));
		exit;
	}

	$labRequired = $_REQUEST['labRequired'];

	//检查是否有标题
	if(!isset($labRequired['title']) || empty($labRequired['title'])){
		print json_encode(array('status'=>401,'msg'=>__('请输入标题','ziranzhi2'),'w'=>'s'));
		exit;
	}

	$title = esc_attr($labRequired['title']);

	//检查是否有封面图片ID
	if(!isset($labRequired['attid']) || !is_numeric($labRequired['attid'])){
		print json_encode(array('status'=>401,'msg'=>__('请上传封面图片','ziranzhi2'),'w'=>'s'));
		exit;
	}

	//封面图片ID
	$thumbnail = $labRequired['attid'];

	//文章内容（研究项目的描述）
	$content = sanitize_text_field($labRequired['content']);
	$picked_arr = array('isaid','youguess','vote','relay');

	if(!isset($_REQUEST['picked']) && !in_array($_REQUEST['picked'],$picked_arr)){
		print json_encode(array('status'=>401,'msg'=>__('请选择文章类型','ziranzhi2'),'w'=>'s'));
		exit;
	}
	$media_path = zrz_get_media_path().'/';
	if($_REQUEST['picked'] === 'youguess'){

		//“你猜”，数据检查与消毒
		if(!isset($_REQUEST['guessItems']) || empty($_REQUEST['guessItems'])){
			print json_encode(array('status'=>401,'msg'=>__('缺少问题项','ziranzhi2'),'w'=>'s'));
			exit;
		}

		if(!isset($_REQUEST['guessResouts']) || empty($_REQUEST['guessResouts'])){
			print json_encode(array('status'=>401,'msg'=>__('缺少最终结果项','ziranzhi2'),'w'=>'s'));
			exit;
		}

		$guessItems = $_REQUEST['guessItems'];
		$itme = array();
		$j = 0;
		$error = array();

		foreach ($guessItems as $guessItem) {
			$j++;
			if(isset($guessItem['q']) && !empty($guessItem['q'])){

				$i = 0;
				$ke_arr = array();
				foreach ($guessItem['l'] as $key => $val) {
					if(!empty($val['t'])){
						$i++;
						array_push($ke_arr,$key);
					}
				}

				//如果每项问题的选项大于2个，并且已经设置了答案，则继续
				if($i >= 2){
					if(!empty($guessItem['a']) && in_array($guessItem['a'],$ke_arr)){
						$itme[] = array(
							'q'=>sanitize_text_field($guessItem['q']),
							'l'=>array(
								'a'=>array(
									'i'=>str_replace($media_path,'',sanitize_text_field($guessItem['l']['a']['i'])),
									't'=>sanitize_text_field($guessItem['l']['a']['t']),
								),
								'b'=>array(
									'i'=>str_replace($media_path,'',sanitize_text_field($guessItem['l']['b']['i'])),
									't'=>sanitize_text_field($guessItem['l']['b']['t']),
								),
								'c'=>array(
									'i'=>str_replace($media_path,'',sanitize_text_field($guessItem['l']['c']['i'])),
									't'=>sanitize_text_field($guessItem['l']['c']['t']),
								),
								'd'=>array(
									'i'=>str_replace($media_path,'',sanitize_text_field($guessItem['l']['d']['i'])),
									't'=>sanitize_text_field($guessItem['l']['d']['t']),
								)
							),
							'a'=>sanitize_text_field($guessItem['a'])
						);
					}else{
						$error[$j] = __('需要设置正确答案！','ziranzhi2');
					}
				}else{
					$error[$j] = __('问题的选项应该大于2个','ziranzhi2');
				}
			}else{
				$error[$j] = __('请提出一个问题','ziranzhi2');
			}
		}

		if(!empty($error)){
			print json_encode(array('status'=>401,'msg'=>$error,'w'=>'a'));
			exit;
		}

		if(empty($itme)){
			print json_encode(array('status'=>401,'msg'=>__('没有设置任何问题，请检查','ziranzhi2'),'w'=>'s'));
			exit;
		}

		//"你猜"，数据结果消毒
		$guessResouts = $_REQUEST['guessResouts'];
		$guessResoutsError = false;

		foreach ($guessResouts as $key => $val) {
			if(empty($val['t'])){
				$guessResoutsError = $key;
			}
		}

		if(!$guessResoutsError){
			$Ritem = array(
				'33'=>array(
	                'i'=>str_replace($media_path,'',sanitize_text_field($guessResouts['33']['i'])),
	                't'=>sanitize_text_field($guessResouts['33']['t'])
	            ),
	            '66'=>array(
	                'i'=>str_replace($media_path,'',sanitize_text_field($guessResouts['66']['i'])),
	                't'=>sanitize_text_field($guessResouts['66']['t'])
	            ),
	            '99'=>array(
	                'i'=>str_replace($media_path,'',sanitize_text_field($guessResouts['99']['i'])),
	                't'=>sanitize_text_field($guessResouts['99']['t'])
	            )
			);
		}else{
			$r = $guessResoutsError == '33' ? '0-33%' : ($guessResoutsError == '99' ? '66%-100%' : '34%-65%');
			print json_encode(array('status'=>401,'msg'=>sprintf(__('正确率在 %s 时没有说明文字。三个最终结果的文字项必填，请检查'),$r),'w'=>'s'));
			exit;
		}

	}elseif($_REQUEST['picked'] === 'vote'){
		//检查投票数据
		$vote = $_REQUEST['voteList'];
		$Rvote = array();
		if(count($vote) >= 2){
			foreach ($vote as $key => $value) {
				if(!empty($value['t'])){
					$Rvote[] = array(
						'i'=>str_replace($media_path,'',sanitize_text_field($value['i'])),
						't'=>sanitize_text_field($value['t']),
						'p'=>0,
						'c'=>0
					);
				}
			}
		}
		if(empty($Rvote)){
			print json_encode(array('status'=>401,'msg'=>__('有效的投票项目少于2个，请检查','ziranzhi2'),'w'=>'s'));
			exit;
		}
	}

	//前台发布的文章，做一个标记
	if (!isset($_SESSION)) {
		session_start();
	}
	$_SESSION['zrz_credit_add'] = 1;

	//编辑模式，确认发布时间
	$post_date = $post_date_gmt = null;
	if(get_post_meta($post_id,'zrz_credit_add',true)){
		$post_date = get_post_time( 'Y-m-d H:i:s', false , $post_id);
		$post_date_gmt = get_post_time( 'Y-m-d H:i:s', true , $post_id);
	}else{
		$post_date = current_time( 'Y-m-d H:i:s', false );
		$post_date_gmt = current_time( 'Y-m-d H:i:s', true);
	}

	//写入数据
	$status = zrz_get_writing_settings('labs_status');
	$status = current_user_can('edit_users') || $status == 1 ? 'publish' : 'pending';
	$po_arr = array(
			'ID'=> $post_id,
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => $status,
			'post_type'=>'labs',
			'post_date'      => $post_date,
			'post_date_gmt'  => $post_date_gmt,
			'ping_status'=>'close',
			'post_author' => $post_author_id ? $post_author_id : null,
			'comment_status'=>'open'
	);
	$post_id = wp_insert_post( $po_arr );
	set_post_thumbnail($post_id, $thumbnail);
	$resout = wp_set_object_terms( $post_id, $_REQUEST['picked'], 'labtype' );
	if($_REQUEST['picked'] === 'vote'){
		update_post_meta($post_id,'zrz_vote_list',$Rvote);
	}else if($_REQUEST['picked'] === 'youguess'){
		update_post_meta($post_id,'zrz_youguess_list',$itme);
		update_post_meta($post_id,'zrz_youguess_resout',$Ritem);
	}
	//增加一个钩子
	do_action('zrz_after_insert_labes',array($post_id,$_REQUEST));
	print json_encode(array('status'=>200,'msg'=>$post_id,'url'=>get_permalink($post_id)));
	exit;
}

//更新投票数据
add_action('wp_ajax_nopriv_zrz_get_vote_resout', 'zrz_get_vote_resout');
add_action( 'wp_ajax_zrz_get_vote_resout', 'zrz_get_vote_resout' );
function zrz_get_vote_resout(){
	$post_id = isset($_REQUEST['pid']) ? (int)$_REQUEST['pid'] : 0;
	if($post_id){
		if(isset($_REQUEST['list']) && is_array($_REQUEST['list']) && !empty($_REQUEST['list'])){
			$vote_count = (int)get_post_meta($post_id,'zrz_vote_count',true);
			$vote_list = get_post_meta($post_id,'zrz_vote_list',true);

			foreach ($_REQUEST['list'] as $val) {
				$vote_list[$val]['p'] ++;
			}
			$vote_count ++;
			//参与人数加一
			zrz_update_post_join_num($post_id);
			update_post_meta($post_id,'zrz_vote_list',$vote_list);
			update_post_meta($post_id,'zrz_vote_count',$vote_count);
			print json_encode(array('status'=>200,'msg'=>$vote_list));
			exit;
		}
	}
	print json_encode(array('status'=>401,'msg'=>$post_id));
	exit;
}

/*我说，评论回调*/
function zrz_isaid_comment_callback($comment, $args, $depth) {

    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);
    $author = $self = $commentcount_html = $mod = $user = $parent_user = $agent = $author_parent = $mod_parent = $agent = '';

    //楼层
    global $commentcount;

	if(!$commentcount){
		$commentcount = 0;
	}

	if($commentcount%7==0){
		$commentcount = 0;
	}

	$commentcount ++ ;

	$color_arr = array('35bdc7','fca61e','e65a4f','a1c15c','76cba2','8f82bc','f29c9f');

    $user_id = $comment->user_id;
    $commenter = $user_id === '0' ? get_comment_author($comment->comment_ID) : zrz_get_user_page_link((int)$user_id);

	$comment_vote = get_comment_meta($comment->comment_ID,'zrz_isaid_vote_count',true);
	$comment_vote = $comment_vote ? $comment_vote : 0;

	$current_user = get_current_user_id();

	$comment_content = get_comment_text();
	$comment_text = sanitize_text_field(get_comment_text());
    ?>
    <li class="isaid-list fd">
    <article id="comment-<?php echo $comment->comment_ID; ?>" class="isaid-comment-content mar5 pos-r" style="<?php echo 'background-color:#'.$color_arr[$commentcount-1]; ?>">

	        <figure class="said-avatar fs12 pos-a white">
	            <?php echo get_avatar( $comment, 30).'<div class="fd">'.$commenter.ZRZ_THEME_DOT.zrz_get_lv($comment->user_id,'lv').'</div>'; ?>
	        </figure>
			<?php
				$size = '';
				if(mb_strlen($comment_content,'utf8') < 8){
					$size = 'big';
				}
			?>
			<div class="isaid-info white pos-a w100 bottom0 left0 top0">
	                <div class="<?php echo $size; ?> lm w100 t-c pd10"><?php echo zrz_get_content_ex($comment_text,100); ?></div>
                    <?php if ($comment->comment_approved == '0') { ?>
                        <p class="comment-meta-item fs12 red"><?php _e('您的评论正在审核中','ziranzhi2');?></p>
                    <?php } ?>
					<div class="isaid-zan t-c pos-a w100">
						<button class="empty" data-cid="<?php echo $comment->comment_ID; ?>"><span id="isaid<?php echo $comment->comment_ID; ?>"><?php echo $comment_vote; ?></span>人赞赏过 <i id="isaidI<?php echo $comment->comment_ID; ?>" class="iconfont <?php if(zrz_has_vate($comment->comment_ID,$current_user) && is_user_logged_in()){ echo 'zrz-icon-font-zan';}else{echo 'zrz-icon-font-fabulous';} ?>"></i></button>
					</div>
			</div>
    <?php }

	add_action('wp_ajax_nopriv_zrz_youguess_join', 'zrz_youguess_join');
	add_action( 'wp_ajax_zrz_youguess_join', 'zrz_youguess_join' );
	function zrz_youguess_join(){
		$post_id = isset($_POST['pid']) && is_numeric($_POST['pid']) ? (int)$_POST['pid'] : '';
		if($post_id){
			zrz_update_post_join_num($post_id);
		}
		exit;
	}

	add_action('wp_ajax_nopriv_zrz_isaid_comment_up', 'zrz_isaid_comment_up');
	add_action( 'wp_ajax_zrz_isaid_comment_up', 'zrz_isaid_comment_up' );
	function zrz_isaid_comment_up(){
		$comment_id = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 0;
		if(!$comment_id){
			print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
			exit;
		}else{
			$conut_vote = (int)get_comment_meta($comment_id, 'zrz_isaid_vote_count',true);
			$post_join = get_comment( $comment_id );
			$post_id = $post_join->comment_post_ID;

			//参与人数加一
			zrz_update_post_join_num($post_id);

			if(is_user_logged_in()){
				$user_id = get_current_user_id();
				$comment_isaid_vote = (array)get_comment_meta($comment_id, 'zrz_isaid_comment_vote',true);
				if(in_array($user_id,$comment_isaid_vote)){
					$comment_isaid_vote = zrz_array_unset($comment_isaid_vote,$user_id);
					$conut_vote = $conut_vote - 1;
					$ac = false;
				}else{
					array_push($comment_isaid_vote,$user_id);
					$conut_vote = $conut_vote + 1;
					$ac = true;

				}
				update_comment_meta($comment_id, 'zrz_isaid_comment_vote',$comment_isaid_vote);
				update_comment_meta($comment_id, 'zrz_isaid_vote_count',$conut_vote);
			}else{
				$ac = isset($_REQUEST['ac']) && $_REQUEST['ac'] ? $_REQUEST['ac'] : false;
				if(!$ac){
					print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
					exit;
				}else{
					if($ac == 'add'){
						$conut_vote = $conut_vote + 1;
						$ac = true;
					}elseif($ac == 'del'){
						$conut_vote = $conut_vote - 1;
						$ac = false;
					}
					update_comment_meta($comment_id, 'zrz_isaid_vote_count',$conut_vote);
				}
			}

			if($ac === false){
				print json_encode(array('status'=>200,'msg'=>false));
				exit;
			}else{
				print json_encode(array('status'=>200,'msg'=>true));
				exit;
			}
		}
	}

	function zrz_has_vate($comment_id,$user_id){
		$comment_isaid_vote = (array)get_comment_meta($comment_id, 'zrz_isaid_comment_vote',true);
		if(in_array($user_id,$comment_isaid_vote)){
			return true;
		}else{
			return false;
		}
	}

	function zrz_update_post_join_num($post_id){
		if($post_id){
			$post_join = (int)get_post_meta($post_id,'zrz_join_num',true);
			update_post_meta($post_id,'zrz_join_num',$post_join+1);
		}
	}

	//接力
	add_action( 'wp_ajax_zrz_relay_update', 'zrz_relay_update' );
	function zrz_relay_update(){
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$des = isset($_POST['des']) ? $_POST['des'] : '';

		if(!$title || !$des){
			print json_encode(array('status'=>401));
			exit;
		}

		$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
		$resout = false;

		//提交
		$arg = array(
			'ID'=> $id,
			'post_type'=>'labsc',
			'post_title' => $title,
			'post_excerpt'=>$des,
			'post_parent'=>$post_id,
			'post_status'=>'pending'
		);

		$c_id = wp_update_post( $arg );

		if($c_id){
			zrz_update_post_join_num($post_id);
			print json_encode(array('status'=>200));
			exit;
		}else{
			print json_encode(array('status'=>401));
			exit;
		}
	}

	//接力审核
	add_action( 'wp_ajax_zrz_relay_check', 'zrz_relay_check' );
	function zrz_relay_check(){
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$cc = false;
		if(current_user_can('delete_users')){
			$cc = wp_update_post(array(
		        'ID'    =>  $id,
		        'post_status'   =>  'publish'
	        ));
		}
		if($cc){
			print json_encode(array('status'=>200,'msg'=>$cc));
			exit;
		}else{
			print json_encode(array('status'=>401,'msg'=>$cc));
			exit;
		}
	}

	//接力删除
	add_action( 'wp_ajax_zrz_relay_del', 'zrz_relay_del' );
	function zrz_relay_del(){
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$cc = false;
		$current_user_id = get_current_user_id();
		$post_author = get_post_field('post_author',$id);
		$type = get_post_type($id);

		if(current_user_can('delete_users') || ($post_author == $current_user_id && $type == 'labsc' && get_post_status($id) == 'pending')){
			// global $wpdb;
		    // $table_name = $wpdb->prefix . 'post';
			// $wpdb->query( ' DELETE FROM '.$table_name.' WHERE id='.$id);
			$cc = wp_delete_post($id);
		}
		if($cc){
			print json_encode(array('status'=>200));
			exit;
		}else{
			print json_encode(array('status'=>401));
			exit;
		}
	}

	add_action('wp_ajax_nopriv_zrz_get_relay_list', 'zrz_get_relay_list');
	add_action( 'wp_ajax_zrz_get_relay_list', 'zrz_get_relay_list' );
	function zrz_get_relay_list(){
		$paged = isset($_POST['paged']) ? $_POST['paged'] : 0;
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		if(!$id){
			print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
			exit;
		}
		$nub = get_option('posts_per_page', 10);
		$offset = ($paged-1)*$nub;

		$args = array(
			'post_parent' => $id,
			'post_type'   => 'labsc',
			'posts_per_page' => $nub,
			'post_status'=>'any',
			'offset'=>$offset,
			'orderby'=>'date',
			'order'=>'ASC'
		);
		$the_query = new WP_Query( $args );
		// The Loop
		if ( $the_query->have_posts() ) {
			$html = '';
			ob_start();
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
					get_template_part( 'formats/content','relay');
				wp_reset_postdata();
			}
			$list = ob_get_clean();
			print json_encode(array('status'=>200,'msg'=>$list));
			unset($list);
			exit;
		}else{
			print json_encode(array('status'=>401,'msg'=>__('没有更多','ziranzhi2')));
			exit;
		}
	}

	//研究所图标
	function get_labs_icon_7b2(){
		$type = zrz_get_labs_terms('slugs');
		switch ($type) {
			case 'youguess':
				$icon = '<span class="labs-youguess"><i class=" zrz-icon-font-touzi iconfont"></i></span>';
				break;
			case 'relay':
				$icon = '<span class="relay-youguess"><i class="zrz-icon-font-taolun iconfont"></i></span>';
				break;
			case 'vote':
				$icon = '<span class="vote-youguess"><i class="zrz-icon-font-toupiao1 iconfont"></i></span>';
				break;
			default:
				$icon = '<span class="isay-youguess"><i class="zrz-icon-font-kantushuohua iconfont"></i></span>';
				break;
		}
		return apply_filters('get_labs_icon_7b2_filter', $icon);
	}

	//获取研究类型
	function get_first_labs_7b2($post_id = 0){
		if(!$post_id){
			global $post;
			if(!isset($post->ID)) return;
			$post_id = $post->ID;
		}

		if(get_post_type($post_id) == 'activity'){
			return '<a class="list-category activity-cat" href="'.home_url('/activity').'">活动</a>';
		}

		$labs = wp_get_post_terms($post_id, 'labtype', array("fields" => "all"));
		$labs = isset($labs[0]) ? $labs[0] : '';
		if(!$labs || !isset($labs->term_id) || !isset($labs->name)) return;
		$uri = get_term_link($labs->term_id);
		return '<a class="list-category labs-cat" href="'.$uri.'">'.$labs->name.'</a>';
	}