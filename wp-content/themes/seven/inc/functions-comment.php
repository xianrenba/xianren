<?php
/*
* 评论相关的函数
*/

// 评论回调
function zrz_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);
    $author = $self = $commentcount_html = $mod = $user = $parent_user = $agent = $author_parent = $mod_parent = $agent = '';

    //楼层
    $order = get_option('comment_order');
    global $commentcount;
    if(!$commentcount) {
        $page = (get_query_var('cpage') !='') ? get_query_var('cpage')-1 : get_page_of_comment( $comment->comment_ID, $args )-1;
        $cpp = get_option('comments_per_page');

        if ( $order === 'desc' ) { //倒序
            $total = get_comments('post_id='.$comment->comment_post_ID.'&parent=0&status=approve&count=true'); //获取主评论总数量
            if($page == 0){
                $commentcount = $total -1 ;
            }else{
                $commentcount = $total - ($page*$cpp) -1;
            }


        } else {
            $commentcount = $cpp * $page;
        }
    }

    $user_id = $comment->user_id;
    $commenter = $user_id === '0' ? (string)$comment->comment_author : zrz_get_user_page_link($user_id);

    //作者
    if($user_id === get_post_field( 'post_author', $comment->comment_post_ID )){
        $author = '<b class="comment-auth">A</b>';
    }

    //管理员
    if($user_id != '0' && is_super_admin($user_id)){
        $mod = '<b class="comment-mod">M</b>';
    }
    $user = $commenter.'<span class="fs12">'.$author.$mod.'</span>';

    //评论喜欢，反对
    $vote = get_comment_meta($comment->comment_ID,'zrz_comment_vote',true);
    $up = isset($vote['comment_up']) && !empty($vote['comment_up']) ? $vote['comment_up'] : array();
    $down = isset($vote['comment_down']) && !empty($vote['comment_down']) ? $vote['comment_down'] : array();

    $up_show = $down_show = true;

    $current_user = get_current_user_id();

    if(in_array($current_user,$up,true)){
        $up_show = false;
    }
    $up_dom = count($up) > 0 ? '<span class="comment-vote"><i class="iconfont zrz-icon-font-love"></i>'.count($up).'</span>' : '';

    if(in_array($current_user,$down,true)){
        $down_show = false;
    }
    $down_dom = count($down) > 0 ? '<span class="comment-vote"><i class="iconfont zrz-icon-font-buxihuan"></i>'.count($down).'</span>' : '';

    if(!$comment->comment_parent){
        if($order === 'desc'){
            --$commentcount;
        }else{
            ++$commentcount;
        }
        $commentcount_html = '<span class="comment-floor pos-a">'.$commentcount.'</span>';
    }else{
        $comment_parent = get_comment($comment->comment_parent);
        $user_id = $comment_parent->user_id;
        $commenter_parent = $user_id === '0' ? get_comment_author($comment->comment_parent) : zrz_get_user_page_link($user_id);
        //作者
        if($user_id === get_post_field( 'post_author', $comment->comment_post_ID )){
            $author_parent = '<b class="comment-auth">A</b>';
        }
        //管理员
        if($user_id != '0' && is_super_admin($user_id)){
            $mod_parent = '<b class="comment-mod">M</b>';
        }
        $parent_user = '<span class="comment_at gray">@</span>'.$commenter_parent.'<span class="fs12">'.$author_parent.$mod_parent.'</span>';
    }

    //是不是本人
    if(get_current_user_id() == $comment->user_id){
        $self = true;
    }

    $comment_agent = $comment->comment_agent;
    if(strpos($comment_agent, 'iPhone') !== FALSE){
        $agent = '<span class="mar5-l duk">via iPhone</span>';
    }elseif(strpos($comment_agent, 'Android') !== FALSE){
        $agent = '<span class="mar5-l duk">via Android</span>';
    }

    ?>
    <li>
    <article <?php comment_class(empty( $args['has_children'] ) ? '' :'parent') ?> id="comment-<?php echo $comment->comment_ID; ?>" itemscope itemtype="http://schema.org/Comment">
        <figure class="gravatar">
            <?php echo get_avatar( $comment, 43); ?>
            <?php echo zrz_get_lv($comment->user_id,'lv'); ?>
        </figure>
        <div class="comment-item b-t">
            <div class="comment-meta mar5-b pos-r" role="complementary">
                <span class="comment-author fs14">
                    <?php echo $user.$parent_user; ?>
                </span>
                <span class="dot"></span>
                <time class="comment-meta-item timeago fs12 gray" datetime="<?php comment_date('c') ?>" data-timeago="<?php comment_date('Y-n-j G:i:s') ?>" itemprop="datePublished">
                    <?php comment_date('Y-n-j G:i:s') ?>
                </time>
                <?php echo $down_dom && $up_dom ? $down_dom.'<span class="dot"></span>'.$up_dom : $down_dom.$up_dom; ?>
                <?php echo $agent.$commentcount_html; ?>
            </div>
            <div class="comment-content post-content" itemprop="text">
                <div class="comment-content-text"><?php comment_text() ?></div>
                <div class="comment-footer fs12 clearfix mar10-t">
                    <div class="fl">
                        <?php
                        if(!$self){
                             if($up_show){ ?>
                                <span class="voted love hide">已喜欢</span>
                                <button class="text cvote-up" data-Cid="<?php echo $comment->comment_ID; ?>"><b class=""></b><?php echo __('喜欢','ziranzhi2'); ?></button><span class="dot"></span>
                            <?php }else{
                                echo '<span class="voted love">已喜欢</span><span class="dot"></span>';
                            }
                            if($down_show){ ?>
                                <span class="voted hide">已反对</span>
                                <button class="text cvote-down" data-Cid="<?php echo $comment->comment_ID; ?>"><b class=""></b><?php echo __('反对','ziranzhi2'); ?></button>
                            <?php }else{
                                echo '<span class="voted">已反对</span>';
                            }
                        } ?>
                        <?php if ($comment->comment_approved == '0') : ?>
                            <p class="comment-meta-item fs12 red"><?php echo __('您的评论正在审核中','ziranzhi2');?></p>
                        <?php endif; ?>
                    </div>
                    <button class="text fr reply" data-id="<?php echo $comment->comment_ID; ?>" id="reply<?php echo $comment->comment_ID; ?>"><?php echo __('回复','ziranzhi2'); ?></button>
                </div>
                <div id="comment-form-<?php echo $comment->comment_ID; ?>"></div>
            </div>
        </div>
        <div id="comment-children-<?php echo $comment->comment_ID; ?>" class="children children-mark"></div>
    <?php }

function zrz_comment_callback_close() {
    echo '</article></li>';
}

//获得用户的评论数量
function zrz_comment_count($user,$type) {
    if($type){
        $count_comment = get_comments('author_email='.$user.'&count=true');
    }else{
        $count_comment = get_comments('user_id='.$user.'&count=true');
    }
    return $count_comment;
}


//表情
function smilies_reset() {
	global $wpsmiliestrans;
	$theme_url = ZRZ_THEME_URI.'/images/smilies/';
	if ( !get_option( 'use_smilies' ) )
	    return;
	    $wpsmiliestrans = array(
			'✗咧嘴笑✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f600.svg" />',
			'✗微笑✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f604.svg" />',
			'✗酷酷的✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f60e.svg" />',
			'✗不爽✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f612.svg" />',
			'✗汗✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f613.svg" />',
			'✗笑哭了✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f602.svg" />',
			'✗我爱你✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f60d.svg" />',
			'✗得意✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f60f.svg" />',
			'✗亲亲✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f619.svg" />',
			'✗不是吧!✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f61f.svg" />',
			'✗不舒服✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f616.svg" />',
			'✗吐舌头✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f61d.svg" />',
			'✗恐惧✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f628.svg" />',
			'✗惊吓✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f631.svg" />',
			'✗哭了✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f62d.svg" />',
			'✗骷髅头✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f480.svg" />',
			'✗粑粑✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f4a9.svg" />' ,
			'✗棒棒的✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f44d.svg" />',
			'✗肌肉✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f4aa.svg" />',
			'✗拳头✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f44a.svg" />',
            '✗害羞✗' => '<img class="zrz-smilies" src="'.$theme_url.'1f61a.svg" />',
			//以下是兼容旧数据，如果你没有旧的数据需要兼容，可以删除
			':mad:'=>'<img class="zrz-smilies" src="'.$theme_url.'1f62d.svg" />',
			':oops:'=>'<img class="zrz-smilies" src="'.$theme_url.'1f613.svg" />',
			':twisted:' => '<img class="zrz-smilies" src="'.$theme_url.'1f600.svg" />',
			':mrgreen:' => '<img class="zrz-smilies" src="'.$theme_url.'1f604.svg" />',
			':wink:' => '<img class="zrz-smilies" src="'.$theme_url.'1f60e.svg" />',
			':roll:' => '<img class="zrz-smilies" src="'.$theme_url.'1f612.svg" />',
			':cool:' => '<img class="zrz-smilies" src="'.$theme_url.'1f613.svg" />',
			':sad:' => '<img class="zrz-smilies" src="'.$theme_url.'1f602.svg" />',
			':evil:' => '<img class="zrz-smilies" src="'.$theme_url.'1f60d.svg" />',
			':smile:' => '<img class="zrz-smilies" src="'.$theme_url.'1f60f.svg" />',
			':eek:' => '<img class="zrz-smilies" src="'.$theme_url.'1f619.svg" />',
			':shock:' => '<img class="zrz-smilies" src="'.$theme_url.'1f61f.svg" />',
			':mrgreen:' => '<img class="zrz-smilies" src="'.$theme_url.'1f616.svg" />',
			':twisted:' => '<img class="zrz-smilies" src="'.$theme_url.'1f61d.svg" />',
			':lol:' => '<img class="zrz-smilies" src="'.$theme_url.'1f628.svg" />',
			':grin:' => '<img class="zrz-smilies" src="'.$theme_url.'1f631.svg" />',
			':neutral:' => '<img class="zrz-smilies" src="'.$theme_url.'1f62d.svg" />',
			':razz:' => '<img class="zrz-smilies" src="'.$theme_url.'1f480.svg" />',
			':cry:' => '<img class="zrz-smilies" src="'.$theme_url.'1f4a9.svg" />' ,
			':???:' => '<img class="zrz-smilies" src="'.$theme_url.'1f44d.svg" />',
			':?:' => '<img class="zrz-smilies" src="'.$theme_url.'1f4aa.svg" />',
			':idea:' => '<img class="zrz-smilies" src="'.$theme_url.'1f44a.svg" />',
			':arrow:' => '<img class="zrz-smilies" src="'.$theme_url.'1f619.svg" />',
			':!:' => '<img class="zrz-smilies" src="'.$theme_url.'1f604.svg" />',
	    );
}
smilies_reset();

//获取评论表情
add_action('wp_ajax_nopriv_zrz_smiley', 'zrz_smiley');
add_action('wp_ajax_zrz_smiley', 'zrz_smiley');
function zrz_smiley(){
    $html = '';
    global $wpsmiliestrans;
    $i = 0;
    foreach ($wpsmiliestrans as $key => $value) {
        if($i>=21) break;
    	$html .= '<span class="smily-button mouh click" key-data="'.$key.'" >'.$value.'</span>';
        $i++;
    }
	print json_encode(array('status'=>200,'html'=>$html));
	exit;
}

//评论提交
add_action( 'wp_ajax_zrz_save_comment', 'zrz_save_comment' );
add_action( 'wp_ajax_nopriv_zrz_save_comment', 'zrz_save_comment' );
function zrz_save_comment(){

    $content = isset($_POST['comment']) ? strip_tags($_POST['comment']) : '';
    if(strlen($content) < 4){
        print json_encode(array('status'=>401,'msg'=>__('评论字数过少！')));
        exit;
    }

    if(is_numeric($_POST['author'])){
        print json_encode(array('status'=>401,'msg'=>__('用户名不可为纯数字！')));
        exit;
    }

    $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );

    if ( is_wp_error( $comment )) {
        $data = $comment->get_error_messages();
        if ( ! empty( $data ) ) {
            $response = array(
                  "status" => 401,
                  "msg" =>$data[0],
                );
                print json_encode($response);
                exit;
        } else {
            exit;
        }
    }

    $user = wp_get_current_user();

    $post_id = $_POST['comment_post_ID'];

    if(is_user_logged_in()){
        $include_unapproved = $user->ID;
    }else{
        $guest = wp_get_current_commenter();
        $include_unapproved = $guest['comment_author_email'] ? $guest['comment_author_email'] : 'empty';
    }

    $comments = get_comments('post_id='.$post_id.'&comment__in='.$comment->comment_ID.'&status=approve&include_unapproved='.$include_unapproved);

    do_action('set_comment_cookies', $comment, $user);

    $term_list = wp_get_post_terms($post_id, 'labtype', array('fields' => 'slugs'));

    if(isset($_POST['type']) && $_POST['type'] == 'pps'){
        $type = 'zrz_pps_comment_callback';
    }elseif($term_list[0] === 'isaid'){
        $type = 'zrz_isaid_comment_callback';
    }elseif(isset($_POST['type']) && $_POST['type'] == 'bubble'){
        $type = 'zrz_bubble_comment_callback';
    }else{
        $type = 'zrz_comment_callback';
    }

    $msg = wp_list_comments( array(
        'callback' => $type,
        'echo'=>false
        ), $comments);

    $response = array(
          'status' => 200,
          'msg' =>$msg,
          'comment_id'=>$comment->comment_ID
        );
        print json_encode($response);
        exit;
}

//评论加载
add_action('wp_ajax_nopriv_zrz_load_more_comments', 'zrz_load_more_comments');
add_action('wp_ajax_zrz_load_more_comments', 'zrz_load_more_comments');
function zrz_load_more_comments(){
    $postid = isset($_POST["post_id"]) && is_numeric($_POST["post_id"]) ? $_POST["post_id"] : 0;
    $paged = isset($_POST["paged"]) && is_numeric($_POST["paged"]) ? $_POST["paged"] : 0;

    if($postid && $paged){

        if(is_user_logged_in()){
            $user = wp_get_current_user();
            $include_unapproved = $user->ID;
        }else{
            $guest = wp_get_current_commenter();
            $include_unapproved = $guest['comment_author_email'] ? $guest['comment_author_email'] : 'empty';
        }

        $term_list = wp_get_post_terms($postid, 'labtype', array('fields' => 'slugs'));

        //如果是 “我说” 栏目中的评论
        if($term_list[0] === 'isaid'){
            $arr = array(
                'post_id'=>$postid,
                'status'=>'approve',
                'include_unapproved'=>$include_unapproved,
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                     'key' => 'zrz_isaid_vote_count',
                     'compare' => 'NOT EXISTS',
                     'value' => ''
                    ),
                    array(
                     'key' => 'zrz_isaid_vote_count',
                    )
                )
            );
            $comments = get_comments($arr);
            $callback = 'zrz_isaid_comment_callback';
            $par_page = 20;
            //参与人数加一
			zrz_update_post_join_num($postid);
        }else{
            $order = get_option('comment_orde','asc');
            $comments = get_comments('post_id='.$postid.'&order='.$order.'&status=approve&include_unapproved='.$include_unapproved);
            $callback = 'zrz_comment_callback';
            $par_page = get_option('comments_per_page',10);
        }

        $msg = wp_list_comments( array(
            'callback' => $callback,
            'page' => $paged,
            'per_page' =>$par_page,
            'echo'=>false
            ), $comments);
        if($msg){
            print json_encode(array('status'=>200,'msg'=>$msg));
            exit;
        }else{
            print json_encode(array('status'=>401,'msg'=>__('没有更多了','ziranzhi2')));
            exit;
        }
    }else{
        print json_encode(array('status'=>401,'msg'=>__('加载失败','ziranzhi')));
        exit;
    }
}

// 评论审核通过，添加积分
function zrz_comment_unapproved_to_approved($comment){
    do_action( "zrz_comment_unapproved_to_approved", $comment->comment_ID, $comment );
}
add_action('comment_unapproved_to_approved', 'zrz_comment_unapproved_to_approved');

function zrz_comment_add_credit($comment_id, $comment_object){

    //检查积分是否添加过
    $check = get_comment_meta($comment_object->comment_ID, 'zrz_rec_credit',true);
    if($check) return;

    //如果评论未得到批准
    if($comment_object->comment_approved != 1) return;

    //获取评论的作者
    $comment_author = $comment_object->user_id ? (int)$comment_object->user_id : (string)$comment_object->comment_author;

    //获取评论的文章ID
    $post_id = $comment_object->comment_post_ID;

    //获取评论所在的文章作者ID
    $post_author_id = (int)get_post_field( 'post_author', $post_id);

    //如果有父级评论
    $parent_user_id = 0;
    if($comment_object->comment_parent > 0){
        $parent_user_id = get_comment( $comment_object->comment_parent );
        $parent_user_id = $parent_user_id->user_id ? (int)$parent_user_id->user_id : 0;
    }

    //如果给自己的文章评论，返回
    if($post_author_id === $comment_author) return;

    //给评论者加的积分
    $credit = zrz_get_credit_settings('zrz_credit_comment');

    //给文章作者加的积分（随机奖励）
    $rand_credit = explode("-", zrz_get_credit_settings('zrz_credit_post_commented'));
	$rand_credit = rand($rand_credit[0], $rand_credit[1]);

    //给文章作者添加通知和积分
    if(!$parent_user_id || ($parent_user_id && $parent_user_id != $post_author_id)){
        $init = new Zrz_Credit_Message($post_author_id,3);
        $add_msg = $init->add_message($comment_author,$rand_credit,$post_id,$comment_id);
    }

    //如果评论者是注册用户，则增加积分
    if(is_int($comment_author)){
        $init = new Zrz_Credit_Message($comment_author,2);
        $add_msg = $init->add_message($post_author_id,$credit,$post_id,$comment_id);
    }

    //如果存在父级，并且父级不是游客，给父级评论者通知和积分
    if($parent_user_id){
        $init = new Zrz_Credit_Message($parent_user_id,1);
        $add_msg = $init->add_message($comment_author,$rand_credit,$post_id,$comment_id);
    }

    //已经增加了积分，写入标记
    update_comment_meta($comment_id, 'zrz_rec_credit',1);
    return true;
}
add_action('wp_insert_comment', 'zrz_comment_add_credit' , 99, 2 );
add_action('zrz_comment_unapproved_to_approved', 'zrz_comment_add_credit' , 99, 2 );

//喜欢，反对
add_action('wp_ajax_nopriv_zrz_commnet_up_down', 'zrz_commnet_up_down');
add_action('wp_ajax_zrz_commnet_up_down', 'zrz_commnet_up_down');
function zrz_commnet_up_down(){
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : '';

    //获取当前评论数据
	$comment = get_comment($comment_id);

	//评论作者
	$comment_author = $comment->user_id ? $comment->user_id : $comment->comment_author;

    //评论所在的文章
	$post_id = $comment->comment_post_ID;

    //当前用户
    $user_id = get_current_user_id();

    if(!is_user_logged_in() || !$type || !$comment_id || $user_id == $comment_author){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $comment_meta = get_comment_meta($comment_id,'zrz_comment_vote',true);
	$comment_meta = is_array($comment_meta) ? $comment_meta : array();
	$comment_up = isset($comment_meta['comment_up']) ?  $comment_meta['comment_up'] : array();
    $comment_down = isset($comment_meta['comment_down']) ?  $comment_meta['comment_down'] : array();

    if($type == 'up'){

        //检查是否已经投票
        if(in_array($user_id,$comment_up,true)){
            print json_encode(array('status'=>401,'msg'=>__('已经投过票','ziranzhi2')));
            exit;
        }

        //获取积分数据
        $credit_j = zrz_get_credit_settings('zrz_credit_comment_vote_up_deduct');//给点赞同的人减掉的积分
        $credit_z = zrz_get_credit_settings('zrz_credit_comment_vote_up');//获得赞的人增加的积分

        $user_credit = get_user_meta($user_id,'zrz_credit_total',true);

        if($user_credit + $credit_j < 0){
            print json_encode(array('status'=>401,'msg'=>__('积分不足','ziranzhi2')));
            exit;
        }

        //添加投票数据
        $comment_up[] = $user_id;
        $comment_meta['comment_up'] = $comment_up;

        update_comment_meta($comment_id,'zrz_comment_vote',$comment_meta);

        //给投票的人通知并减掉积分
        $init = new Zrz_Credit_Message($user_id,9);
        $add_msg = $init->add_message($comment_author, $credit_j,$post_id,$comment_id);

        //给评论作者增加积分并
        $init = new Zrz_Credit_Message($comment_author,10);
        $add_msg = $init->add_message($user_id, $credit_z,$post_id,$comment_id);

        print json_encode(array('status'=>200,'msg'=>__('投票成功','ziranzhi2')));
        exit;

    }elseif($type == 'down'){

        //检查是否已经投票
        if(in_array($user_id,$comment_down,true)){
            print json_encode(array('status'=>401,'msg'=>__('已经投过票','ziranzhi2')));
            exit;
        }

        //添加投票数据
        $comment_down[] = $user_id;
        $comment_meta['comment_down'] = $comment_down;

        update_comment_meta($comment_id,'zrz_comment_vote',$comment_meta);

        //给评论作者通知
        $init = new Zrz_Credit_Message($comment_author,8);
        $add_msg = $init->add_message($user_id, 0,$post_id,$comment_id);

        print json_encode(array('status'=>200,'msg'=>__('投票成功','ziranzhi2')));
        exit;
    }

    print json_encode(array('status'=>401,'msg'=>__('操作失败','ziranzhi2')));
    exit;

}

//支持评论图片
function zrz_allow_html_attributes_in_commentform() {
  global $allowedtags;
  $allowedtags['div'] = array('class'=>array('comment-img-box'));
  $allowedtags['img'] = array('src'=>array());
}
// Add WordPress hook to use the function
add_action('init', 'zrz_allow_html_attributes_in_commentform',11);

//获取游客评论信息
function zrz_get_commenter(){
    $comment_user = array(
        'user_name'=>'',
        'user_email'=>'',
        'comment_count'=>0,
        'avatar'=>''
    );
    if(!is_user_logged_in()){
        $curr_comment_user = wp_get_current_commenter();
        if(!empty($curr_comment_user['comment_author_email'])){
            $comment_user = array(
                'user_name' => esc_html($curr_comment_user['comment_author']),
                'user_email'=> esc_html($curr_comment_user['comment_author_email']),
                'comment_count'=>zrz_comment_count($curr_comment_user['comment_author_email'],true),
                'avatar'=> esc_url(zrz_get_avatar(md5(home_url()).'-'.$curr_comment_user['comment_author'],40))
            );
        }else{
            $comment_user = array(
                'avatar'=> esc_url(zrz_get_avatar(md5(home_url()).'-空',40)),
                'user_name' =>'',
                'comment_count'=>0,
                'user_email'=>'',
            );
        }
    }
    return $comment_user;
}

//冒泡评论回调
function zrz_bubble_comment_callback($comment, $args, $depth) {
    $comment_author = $comment->user_id ? get_avatar($comment->user_id,25).' '.zrz_get_user_page_link($comment->user_id) : get_avatar($comment->comment_author,30).' <span class="parent-author">'.$comment->comment_author.'</span>';
    $parent_comment = '';
    if($comment->comment_parent){
        $parent_comment = get_comment($comment->comment_parent);
        $parent_comment = $parent_comment->user_id ? zrz_get_user_page_link($parent_comment->user_id) : $parent_comment->comment_author;
        $parent_comment = ' @ <span class="parent-author">'.$parent_comment.'</span>';
    }
    ?>
    <li>
    <article <?php comment_class(empty( $args['has_children'] ) ? '' :'parent') ?> id="comment-<?php echo $comment->comment_ID; ?>" itemscope itemtype="http://schema.org/Comment">
        <div class="comment-author fs12 gray">
            <?php echo $comment_author.$parent_comment; ?><span class="dot"></span>
            <time class="comment-meta-item timeago fs12 gray" datetime="<?php comment_date('c') ?>" data-timeago="<?php comment_date('Y-n-j G:i:s') ?>" itemprop="datePublished">
                <?php comment_date('Y-n-j G:i:s') ?>
            </time>
        </div>
        <div class="bubble-comment-content-text"><?php comment_text() ?></div>
        <div class="bubble-comment-footer t-r"><button class="text bubble-reply" data-id="<?php echo $comment->comment_ID; ?>" data-author-name="<?php echo $comment->comment_author; ?>">回复</button></div>
<?php
}
function zrz_bubble_comment_callback_close() {
    echo '</article></li>';
}

//获取冒泡评论
add_action('wp_ajax_nopriv_zrz_bubble_comment', 'zrz_bubble_comment');
add_action('wp_ajax_zrz_bubble_comment', 'zrz_bubble_comment');
function zrz_bubble_comment(){
    $post_id = isset($_POST['pid']) ? $_POST['pid'] : '';
    $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;

    //检查文章类型
    if(get_post_type($post_id) != 'pps'){
        print json_encode(array('status'=>401,'msg'=>__('参数错误','ziranzhi2')));
        exit;
    }
    
    $user_id = get_current_user_id();

    if(is_user_logged_in()){
        $include_unapproved = $user_id;
    }else{
        $guest = wp_get_current_commenter();
        $include_unapproved = $guest['comment_author_email'] ? $guest['comment_author_email'] : 'empty';
    }

    $args = array(
        'post_id'=>$post_id,
        'status'=>'approve',
        'include_unapproved'=>$include_unapproved,
        'order'=>'DESC',
        'orderby'=>'comment_date',
    );

    $comments = get_comments($args);
    $pre_comment = get_option('comments_per_page');
    $pages = 0;
    if($paged == 1){
        $count = count($comments);
        $pages = ceil( $count / $pre_comment);
    }

    $msg = wp_list_comments( array(
        'callback' => 'zrz_bubble_comment_callback',
        'end-callback' => 'zrz_bubble_comment_callback_close',
        'max_depth'=>-1,
        'echo'=>false,
         'page' =>$paged,
        'per_page' =>$pre_comment,
        ), $comments);

    if($comments){
        print json_encode(array('status'=>200,'msg'=>$msg,'pages'=>$pages));
        exit;
    }else{
        print json_encode(array('status'=>401,'msg'=>__('没有评论','ziranzhi2')));
        exit;
    }

}

add_action( 'wp_ajax_zrz_newest_comments_load', 'zrz_newest_comments_load' );
add_action( 'wp_ajax_nopriv_zrz_newest_comments_load', 'zrz_newest_comments_load' );
function zrz_newest_comments_load() {
	$paged = isset($_POST['paged']) && is_numeric($_POST['paged']) ? $_POST['paged'] : 0;
    $number = isset($_POST['number']) && is_numeric($_POST['number']) ? $_POST['number'] : 0;
    $hide_author = isset($_POST['hide_author']) && is_numeric($_POST['hide_author']) ? $_POST['hide_author'] : 0;

    if(!$paged || !$number){
        print json_encode(array('status'=>401,'msg'=>__('没有评论','ziranzhi2')));
        exit;
    }

	$offset = $number*($paged-1);
    $pages = 0;

	$args = array(
		'number'=>$number,
		'status'=>'approve',
		'author__not_in' =>$hide_author,
		'offset'=>$offset,
		'type'=>'comment'
	);
	$comments = get_comments($args);

    if($paged == 1){
        $comments_count = get_comments('status=approve&type=comment&author__not_in='.$hide_author.'&count=true');
        $pages = ceil($comments_count/$number);
    }

	$html = array();
	if(!empty($comments)){
		foreach ($comments as $comment) {

			$post_type = get_post_type($comment->comment_post_ID);
            $post_name = $post_type == 'post' ? __('[文章]','ziranzhi2') : ($post_type == 'pps' ? sprintf( '[%1$s]',zrz_custom_name('bubble_name')) : ($post_type == 'labs' ? sprintf( '[%1$s]',zrz_custom_name('labs_name')) : ($post_type == 'shop' ? __('[商品]','ziranzhi2') : ($post_type == 'page' ? __('[页面]','ziranzhi2') : ($post_type == 'activity' ? __('[活动]','ziranzhi2') : '')))));
            $avatar = get_avatar($comment,50);

			$html[] = array(
				'avatar' => $avatar,
				'author' => $comment->user_id == '0' ? $comment->comment_author : zrz_get_user_page_link($comment->user_id),
				'post_link'=> '来自：<a href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'">'.get_the_title($comment->comment_post_ID).'</a> '.$post_name,
				'content' => zrz_get_comment_content($comment->comment_ID),
				'post_type'=> $post_type,
                'date'=>'<time class="comment-meta-item timeago fs12 gray" data-timeago="'.get_comment_date('Y-n-j G:i:s',$comment->comment_ID).'" itemprop="datePublished"></time>',
			);
		}

        print json_encode(array('status'=>200,'msg'=>$html,'pages'=>$pages));
        exit;
	}else{
        print json_encode(array('status'=>401,'msg'=>__('没有评论','ziranzhi2')));
        exit;
    }
}

//替换我文章，评论中的图片地址
function zrz_filter_comment_images( $content, $comment = null ) {
// return $content;
    $media_setting = zrz_get_media_settings('media_place');
    if($media_setting === 'localhost') return $content;

    $content_img_max_width = ceil(zrz_get_theme_settings('page_width')*0.74751) -40;

    $c_url = wp_get_upload_dir();
    $param = zrz_upload_dir(wp_get_upload_dir());

    $c_baseurl = str_replace('/', '\/', $c_url['baseurl']);
    $_baseurl = str_replace('/', '\/', $param['baseurl']);

    $cdn_exts   = 'png|jpg|jpeg|gif';

    if(strpos($content,$param['baseurl']) !== false){
        $regex	= '/src="' . $_baseurl . '([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . ')())([\"\\\'\s\?]{1})/';
    }else{
        $regex	= '/src="' . $c_baseurl . '([^\s\?\\\'\"\;\>\<]{1,}.(' . $cdn_exts . ')())([\"\\\'\s\?]{1})/';
    }

    $_webp = zrz_is_support_webp();

    $webp = '';
    $type = '';

    //如果开启了文中图片最大宽度的设置
    $aliyun = zrz_get_media_settings('aliyun');
    $watermark = isset($aliyun['watermark']) ? $aliyun['watermark'] : '';

    if($media_setting == 'upyun'){
        $type = '!/fw/'.$content_img_max_width;
        $webp = $_webp ? '/format/webp' : '';
    }elseif($media_setting == 'qiniu'){
        $type = $content_img_max_width ? '?imageMogr2/thumbnail/'.$content_img_max_width.'x1000<' : '';
        $webp = $_webp ? '/format/webp' : '';
    }elseif($media_setting == 'aliyun'){
        $type = $content_img_max_width ? '?x-oss-process=image/resize,w_'.$content_img_max_width : '';
        $webp = $_webp ? '/format,webp' : '';
    }

    $_content =  preg_replace($regex, 'src="'.$param['baseurl'].'$1$3'.$type.$webp.$watermark.'"', $content);
    if($_content){
        return $_content;
    }

    return $content;

}
add_filter( 'comment_text', 'zrz_filter_comment_images', 10, 2 );
add_filter( 'the_content', 'zrz_filter_comment_images' );
add_filter('bbp_get_topic_content', 'zrz_filter_comment_images');
add_filter('bbp_get_reply_content', 'zrz_filter_comment_images');

//通过评论ID获取评论内容
function zrz_get_comment_content($comment_id){
    $comment_status = wp_get_comment_status($comment_id);
    if($comment_status == 'approved'){
        $comment_text = get_comment_text($comment_id);
        $comment_text_out = convert_smilies(mb_strimwidth(strip_tags($comment_text),0, 100 ,"..."));
        $img = zrz_get_first_img($comment_text);
        if($img){
            return wpautop($comment_text_out).'<img class="msg-comment-img" src="'.$img.'">';
        }
        return $comment_text_out;
    }elseif($comment_status == 'unapproved'){
        return '<span class="gray">此评论正在审核中</span>';
    }else{
        return '<del>评论不存在</del>';
    }
}
