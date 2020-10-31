<?php

class Forum_AJAX {
    function __construct(){
        if (defined('DOING_AJAX') && DOING_AJAX && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){ //只接受ajax请求
            global $wpcomqadb;
            $this->qadb = $wpcomqadb;
            add_action('wp_ajax_forum_views', array($this, 'add_views'));
            add_action('wp_ajax_nopriv_forum_views', array($this, 'add_views'));

            add_action('wp_ajax_forum_answers_pagination', array($this, 'answers_pagination'));
            add_action('wp_ajax_nopriv_forum_answers_pagination', array($this, 'answers_pagination'));

            add_action('wp_ajax_forum_comments', array($this, 'get_comments'));
            add_action('wp_ajax_nopriv_forum_comments', array($this, 'get_comments'));

            add_action('wp_ajax_forum_add_comment', array($this, 'add_comment'));
            add_action('wp_ajax_nopriv_forum_add_comment', array($this, 'add_comment'));

            add_action('wp_ajax_forum_add_answer', array($this, 'add_answer'));
            add_action('wp_ajax_nopriv_forum_add_answer', array($this, 'add_answer'));

            add_action('wp_ajax_forum_add_question', array($this, 'add_question'));
            // add_action('wp_ajax_nopriv_forum_add_question', array($this, 'add_question'));

            add_action('wp_ajax_forum_img_upload', array($this, 'img_upload'));
            // add_action('wp_ajax_nopriv_forum_img_upload', array($this, 'img_upload'));

            add_action('wp_ajax_forum_delete_question', array($this, 'delete_question'));
            // add_action('wp_ajax_nopriv_forum_delete_question', array($this, 'delete_question'));

            add_action('wp_ajax_forum_approve_question', array($this, 'approve_question'));
            
            add_action('wp_ajax_forum_set_comment_top', array($this, 'set_comment_top'));

            add_action('wp_ajax_forum_set_top', array($this, 'set_top'));
            // add_action('wp_ajax_nopriv_forum_set_top', array($this, 'set_top'));

            add_action('wp_ajax_forum_delete_answer', array($this, 'delete_answer'));
            // add_action('wp_ajax_nopriv_forum_delete_answer', array($this, 'delete_answer'));

            add_action('wp_ajax_forum_delete_comment', array($this, 'delete_comment'));
            // add_action('wp_ajax_nopriv_forum_delete_comment', array($this, 'delete_comment'));
        }
    }

    function add_views(){
        $data = $_POST;
        if(isset($data['id']) && $data['id'] && is_numeric($data['id'])){
            echo $this->qadb->add_views($data['id']);
        }
        exit;
    }

    function answers_pagination(){
        global $wpcomqadb;
        $data = $_POST;
        $res = array();
        $res['result'] = 1;
        if(isset($data['page']) && isset($data['question']) && is_numeric($data['page']) && is_numeric($data['question'])){

            $answers_order = isset($forum_options['answers_order']) && $forum_options['answers_order']=='1' ? 'DESC' : 'ASC';
            $answers_per_page = _hui('forum_comment_number',20);
            $answers = $wpcomqadb->get_answers($data['question'], $answers_per_page, $data['page'], $answers_order);
            $res['result'] = 0;
            $res['answers'] = array();
            $res['delete'] = current_user_can( 'manage_options' ) ? 1 : 0;
            foreach ($answers as $answer) {
                $a = array();
                $a['ID'] = $answer->comment_ID;
                $a['content'] = wpautop($answer->comment_content);
                $a['time'] = forum_format_date(strtotime($answer->comment_date));
                $a['avatar'] = um_get_avatar( $answer->user_id, '60' ,um_get_avatar_type($answer->user_id) ,true);
                $a['comments'] = $answer->comment_karma;
                $user = get_user_by('ID', $answer->user_id);
                $author_name = $user->display_name ? $user->display_name : $user->user_nicename;
                if(class_exists('WPCOM_Member')){
                    $url = get_author_posts_url( $user->ID );
                    $author_name = '<a href="'.$url.'" target="_blank">'.$author_name.'</a>';
                    $a['avatar'] = '<a href="'.$url.'" target="_blank">'.$a['avatar'].'</a>';
                }
                $a['name'] = $author_name;
                $res['answers'][] = $a;
            }
        }
        echo json_encode($res);
        exit;
    }

    function get_comments(){
        $data = $_POST;
        $res = array();
        if(isset($data['aid']) && $data['aid'] && is_numeric($data['aid'])){
            $comments = $this->qadb->get_comments($data['aid']);
            if($comments){
                $res_comments = array();
                foreach ($comments as $comment) {
                    $user = get_user_by('ID', $comment->user_id);
                    $author_name = $user->display_name ? $user->display_name : $user->user_nicename;
                    if($user){
                        $url = get_author_posts_url( $user->ID );
                        $author_name = '<a class="as-comment-url" href="'.$url.'" target="_blank">'.$author_name.'</a>';
                    }
                    $a = array();
                    $a['ID'] = $comment->comment_ID;
                    $a['content'] = wpautop($comment->comment_content);
                    $a['user'] = $author_name;
                    $a['date'] = $comment->comment_date;
                    $res_comments[] = $a;
                }
                $res['comments'] = $res_comments;
                $res['delete'] = current_user_can( 'manage_options' ) ? 1 : 0;
            }
            $res['result'] = 0;
        }else{
            $res['result'] = 1;
        }
        echo json_encode($res);
        exit;
    }

    
    function add_comment(){
        $data = $_POST;
        $res = array();
        $user =  wp_get_current_user();
        if($data['id'] && $user->ID/*  && $QAPress->is_active() */){
            $parent = get_comment($data['id']);
            $comment = array();
            $comment['comment_parent'] = $data['id'];
            $comment['comment_content'] = esc_html($data['comment']);
            $comment['user_id'] = $user->ID;
            $comment['comment_author_email'] = $user->user_email;
            $comment['comment_author'] = $user->display_name;
            $comment['comment_approved'] = 1;
            $comment['comment_post_ID'] = $parent->comment_post_ID;
            $comment['comment_type'] = 'forum_comment';

            if(trim(strip_tags($comment['comment_content'])) == ''){
                $res['result'] = 101;
            }else{
                $id = $this->qadb->insert_comment($comment);
                if($id){
                    $res['result'] = 0;
                }else{
                    $res['result'] = 1;
                }
            }
        }else{
            $res['result'] = 2;
        }
        
        echo json_encode($res);
        exit;
    }


    function add_answer(){
        $data = $_POST;
        $res = array();
        $user =  wp_get_current_user();
        if($data['id'] && $user->ID /* && $QAPress->is_active() */){ // 已登录
            $answer = array();
            $answer['comment_post_ID'] = $data['id'];
            $question = '';
            if($data['id'] && is_numeric($data['id'])) $question = $this->qadb->get_question($data['id']);;

            if($question){ // 问题存在
                $answer['comment_content'] = wp_kses_post($data['answer']);
                $answer['user_id'] = $user->ID;
                $answer['comment_author_email'] = $user->user_email;
                $answer['comment_author'] = $user->display_name;
                $answer['comment_approved'] = 1;
                $answer['comment_type'] = 'answer';

                if(trim($answer['comment_content']) == ''){
                    $res['result'] = 101;
                }else{
                    // 判断是否需要审核
                    if( !current_user_can( 'publish_posts' ) ){
                        $moderation = _hui('answer_moderation','0');
                        if( $moderation == '1' ){ // 第一次审核
                            $q_total = $this->qadb->get_questions_total_by_user($user->ID);
                            $a_total = $this->qadb->get_answers_total_by_user($user->ID);
                            $answer['comment_approved'] = $q_total || $a_total ? 1 : 0;
                        }else if( $moderation == '2' ){ // 全部需要审核
                            $answer['comment_approved'] = 0;
                        }
                    }
                
                    $id = $this->qadb->insert_answer($answer);
                    if($id){
                        $answer['ID'] = $id;
                        $res['result'] = 0;
                        $res['answer'] = $answer;
                        $res['answer']['date'] = forum_format_date(strtotime(current_time('mysql')));
                        $res['answer']['content'] = '<p>'.$answer['comment_content'].'</p>';
                        $res['user'] = array();
                        $user_name = $user->display_name ? $user->display_name : $user->user_nicename;
                        $res['user']['nickname'] = $user_name;
                        $res['user']['avatar'] = um_get_avatar($user->ID , '60' , um_get_avatar_type($user->ID) , true); 
						
                        //邮件、通知信息，发送问题作者和管理员
                        if( _hui('email_answer') ){
							$subject = '您发布的问题有了新的回复';
                            $color = '#1471CA';
                            $content = '<p>您的问题【'.$question->post_title.'】有了新回复：</p>';
                            $content .= '<div style="background: #f9f9f9;color:#666;padding: 10px 10px 10px 15px;border-left: 3px solid '.$color.';">'.$answer['comment_content'].'</div>';
                            $content .= '<p>详情请访问：<a href="'.get_permalink($question->ID).'">'.get_permalink($question->ID).'</a></p>';

                            $quser = get_user_by('ID', $question->post_author);
                            if($quser->ID) um_basic_mail( '', $quser->data->user_email, $subject, $content );
                            // 抄送管理员
                            $cc = _hui('email_answer_cc');
                            if( $cc ) {
                                $admin_email = get_bloginfo ('admin_email');
                                if($quser->email != $admin_email) um_basic_mail( '', $admin_email, $subject, $content );
                            }
                        }
                    }else{
                        $res['result'] = 1;
                    }
                }
            }else{ // 问题不存在
                $res['result'] = 4;
            }
        }else{ // 未登录
            $res['result'] = 2;
        }

        echo json_encode($res);
        exit;
    }

    function delete_answer(){
        $res = array();
        if( current_user_can( 'manage_options' )/*  && $QAPress->is_active() */){ // 管理员才能执行此操作
            if(isset($_POST['id']) && is_numeric($_POST['id'])){
                $id = $_POST['id'];
                // 删除回复的评论
                $this->qadb->delete_comments( $id );
                // 删除回复
                $this->qadb->delete_answer( $id );
                $res['result'] = 0;
            }else{
                $res['result'] = 1;
            }
        }else{
            $res['result'] = 2;
        }
        echo json_encode($res);
        exit;
    }

    function delete_comment(){
        $res = array();
        if( current_user_can( 'manage_options' ) /* && $QAPress->is_active()  */){ // 管理员才能执行此操作
            if(isset($_POST['id']) && is_numeric($_POST['id'])){
                $id = $_POST['id'];
                // 删除评论
                $this->qadb->delete_comment( $id );
                $res['result'] = 0;
            }else{
                $res['result'] = 1;
            }
        }else{
            $res['result'] = 2;
        }
        echo json_encode($res);
        exit;
    }

    function add_question(){
        $res = array();
        $nonce = isset($_POST['add_question_nonce']) ? $_POST['add_question_nonce'] : '';

        // Check nonce
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'forum_add_question' ) ){
            $res['result'] = 1;
            echo json_encode($res);
            exit;
        }

        $user =  wp_get_current_user();
        if($user->ID){
            $post = array();
            $qid = isset($_POST['id']) ? $_POST['id'] : 0;

            $question = $qid ? $this->qadb->get_question($qid) : null;

            if($question && ( $question->post_author==$user->ID || $user->has_cap( 'edit_others_posts' ) ) ) { // 问题存在，并比对用户权限
                $post['ID'] = $question->ID;
                $post['post_author'] = $question->post_author;
            }else{
                $post['post_author'] = $user->ID;
                $post['post_status'] = 'publish';
                $post['post_type'] = 'forum';
                // 判断是否需要审核
                if( !current_user_can( 'publish_posts' ) ){
                    $moderation = _hui('question_moderation','0');
                    if( $moderation == '1' ){ // 第一次审核
                        $user_total = $this->qadb->get_questions_total_by_user($user->ID);
                        $post['post_status'] = $user_total ? 'publish' : 'pending';
                    }else if( $moderation == '2' ){ // 全部需要审核
                        $post['post_status'] = 'pending';
                    }
                }
            }

            $post['post_title'] = strip_tags($_POST['title']);
            $post['post_content'] = wp_kses_post($_POST['content']);

            if(trim($post['post_title']) == '' || trim($post['post_content']) == '' || trim($_POST['category']) == ''){
                $res['result'] = 101;
            }else if(mb_strlen(trim($post['post_content']))<10){ // 内容不能少于10个字符
                $res['result'] = 102;
            }else{
                $id = $this->qadb->insert_question($post);
                if(!$qid) update_post_meta($id, 'views', 1);
                wp_set_object_terms( $id, array( (int)$_POST['category'] ), 'forum_cat' );

                if($id){
                    $res['location'] = get_permalink($id);
                    if(!$qid){ // 新帖，非修改
                        //邮件、通知信息，发送管理员
                        if( _hui('email_new') ){
							$subject = '您的网站有新问题发布';
                            $color = '#1471CA';
                            $content = '<p>您的网站有新问题发布：</p>';
                            $content .= '<div style="background: #f9f9f9;color:#666;padding: 10px 10px 10px 15px;border-left: 3px solid '.$color.';">'.$post['post_title'].'</div>';
                            $content .= '<p>详情请访问：<a href="'.get_permalink($id).'">'.get_permalink($id).'</a></p>';
                            um_basic_mail( '', get_bloginfo ('admin_email'), $subject, $content );
                        }
                        $post = get_post($id);
                        if( $post->ID && $post->post_status=='pending' ){ //审核中
                            $list_page_id = _hui('list_page');
                            $res['location'] = get_permalink($list_page_id); // 跳转回列表
                            $res['msg'] = '发布成功，请等待网站管理员审核！'; // 提示语
                        }
                    }
                    $res['result'] = 0;
                    $res['id'] = $id;
                }else{
                    $res['result'] = 1;
                }
            }
            
        }else{
            $res['result'] = 2;
        }

        echo json_encode($res);
        exit;
    }

    function delete_question(){
        $res = array();
        if( current_user_can( 'manage_options' ) /* && $QAPress->is_active() */ ){ // 管理员才能执行此操作
            if(isset($_POST['id']) && is_numeric($_POST['id'])){
                $id = $_POST['id'];
                // 删除回复
                $this->qadb->delete_answers( $id );
                $this->qadb->delete_question( $id );
                $res['result'] = 0;
            }else{
                $res['result'] = 1;
            }
        }else{
            $res['result'] = 2;
        }
        echo json_encode($res);
        exit;
    }

    function approve_question(){
        global $wpdb;
        $res = array();
        if( current_user_can( 'manage_options' ) /* && $QAPress->is_active()  */){ // 管理员才能执行此操作
            if(isset($_POST['id']) && is_numeric($_POST['id']) && $post = get_post($_POST['id'])){
                $wpdb->update($wpdb->posts, array( 'post_status' => 'publish' ), array('ID' => $post->ID));
                wp_transition_post_status( 'publish', 'pending', $post );
                $res['result'] = 0;
            }else{
                $res['result'] = 1;
            }
        }else{
            $res['result'] = 2;
        }
        echo json_encode($res);
        exit;
    }

    function set_top(){
        $res = array();
        if( current_user_can( 'manage_options' ) ){ // 管理员才能执行此操作
            if(isset($_POST['id']) && is_numeric($_POST['id'])){
                $id = $_POST['id'];
                $this->qadb->set_top($id);
                $res['result'] = 0;
            }else{
                $res['result'] = 1;
            }
        }else{
            $res['result'] = 2;
        }
        echo json_encode($res);
        exit;
    }
    
    function set_comment_top(){
    	global $current_user;
    	$res = array();
    	$id = isset($_POST['id']) ? $_POST['id'] : '';
    	$qid = isset($_POST['qid']) ? $_POST['qid'] : '';
    	$func = isset($_POST['func']) ? $_POST['func'] : '';
    	$author = get_comment_author($id);
    	if( current_user_can( 'manage_options' ) || $author->ID == $current_user->ID){ 
    		if($func && $id && $qid){
    			delete_comment_meta($id, 'comment_top');
    			delete_post_meta($qid, 'qux_type');
    			$res['result'] = 0;
    		}elseif($id && $qid){
    			update_comment_meta( $id, 'comment_top', '1');
    			update_post_meta($qid, 'qux_type', '2');
    			$res['result'] = 0;
    		}else{
    			$res['result'] = 1;
    		}
    	}else{
    		$res['result'] = 2;
    	}
    	echo json_encode($res);
    	exit;
    }

    function img_upload(){
        $res = array();
        
        $user =  wp_get_current_user();
        if($user->ID){
            $upfile = $_FILES['upfile'];
            $upload_overrides = array('test_form' => false);
            $file_return = wp_handle_upload($upfile, $upload_overrides);

            if ($file_return && !isset($file_return['error'])) {
                // 保存到媒体库
                $attachment = array(
                    'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file_return['file'] ) ),
                    'post_mime_type' => $file_return['type'],
                );
                $attach_id = wp_insert_attachment($attachment, $file_return['file']);
                $attach_data = self::generate_attachment_metadata($attach_id, $file_return['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $res['result'] = 0;
                $file_return['alt'] = preg_replace( '/\.[^.]+$/', '', basename( $file_return['file'] ) );
                $res['image'] = $file_return;
            } else {
                $res['result'] = 1;
            }
        } else {
            $res['result'] = 2;
        }
        echo json_encode($res);
        exit;
    }

    function generate_attachment_metadata($attachment_id, $file) {
        $attachment = get_post ( $attachment_id );
        $metadata = array ();
        if (!function_exists('file_is_displayable_image')) include( ABSPATH . 'wp-admin/includes/image.php' );

        if (preg_match ( '!^image/!', get_post_mime_type ( $attachment ) ) && file_is_displayable_image ( $file )) {
            $imagesize = getimagesize ( $file );
            $metadata ['width'] = $imagesize [0];
            $metadata ['height'] = $imagesize [1];
            list ( $uwidth, $uheight ) = wp_constrain_dimensions ( $metadata ['width'], $metadata ['height'], 128, 96 );
            $metadata ['hwstring_small'] = "height='$uheight' width='$uwidth'";

            // Make the file path relative to the upload dir
            $metadata ['file'] = _wp_relative_upload_path ( $file );
            // work with some watermark plugin
            $metadata = apply_filters ( 'wp_generate_attachment_metadata', $metadata, $attachment_id );
        }
        return $metadata;
    }
}

new Forum_AJAX();