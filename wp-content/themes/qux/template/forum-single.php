<?php get_header(); ?>
<div class="main">
	<section class="container">
<div class="content-wrap">
<div class="q-content content">
<?php 
    global $wp_query, $wpcomqadb, $current_user;
    //$wpcomqadb = new Forum_SQL();
    $post_id = get_the_ID();//isset($wp_query->query['post']) ? $wp_query->query['post'] : $wp_query->query['froum'];
    if(!$post_id) return;


    $answers_per_page = _hui('forum_comment_number',20);

    $question = get_post($post_id);

    if( ! ( $question && isset($question->ID) ) ){
        exit();
    }

    $user = get_user_by('ID', $question->post_author);
    $author_name = $user->display_name ? $user->display_name : $user->user_nicename;
    $url = get_author_posts_url( $user->ID );
    $author_name = '<a href="'.$url.'" target="_blank">'.$author_name.'</a>';
    $answers_order = 'ASC';
    $answers_top = $wpcomqadb->get_answers_top($question->ID);
    $cat = get_the_terms($question->ID, 'forum_cat');
    $cat = $cat[0];

    $html = '<div class="q-content q-single" data-id="'.$question->ID.'">
            <div class="q-header topic-header">
                '.($question->menu_order==1?'<span class="put-top">置顶</span>':'').'
                <h1 class="q-title">'.get_the_title($question->ID).'</h1>
                <div class="q-info">';
    if( current_user_can( 'manage_options' ) ){
        $edit_url = forum_edit_url($question->ID);
        $html .= '<div class="pull-right qa-manage">';
        if($question->post_status=='pending') $html .= '<a class="j-approve" href="javascript:;">审核通过</a>';
        $html .= '<a href="'.$edit_url.'">编辑</a>
            <a class="j-set-top" href="javascript:;">'.($question->menu_order==1?'取消置顶':'置顶').'</a>
            <a class="j-del" href="javascript:;">删除</a>
        </div>';
    }
    $html .= '<span class="q-author">'.$author_name.'</span>
                    <span class="q-author">发布于 '.forum_format_date(get_post_time('U', false, $question->ID)).'</span>
                    <span class="q-cat">分类：<a href="'.forum_category_url($cat->slug).'">'.$cat->name.'</a></span>
                </div>
            </div>
            <div class="q-entry entry-content">'.wpautop(do_shortcode($question->post_content)).'</div>
            <div class="q-answer" id="answer">
                <h3 class="as-title">'.$question->comment_count.'个回复</h3> <ul class="as-list">';
    if($answers_top){
    	$notin = '';
        foreach ($answers_top as $answer) {
            $user = get_user_by('ID', $answer->user_id);
            $author_name = $answer->comment_author;
            $avatar = um_get_avatar($answer->user_id , '60' , um_get_avatar_type($answer->user_id));
            $notin .= $answer->comment_ID;
            if($user){
                $author_name = isset($user->display_name) ? $user->display_name : $user->user_nicename;
				$avatar = um_get_avatar($answer->user_id , '60' , um_get_avatar_type($answer->user_id));
				$url = get_author_posts_url( $user->ID );
				$author_name = '<a href="'.$url.'" target="_blank">'.$author_name.'</a>';
            }

            $html .= '<li id="as-'.$answer->comment_ID.'" class="as-item" data-aid="'.$answer->comment_ID.'">
                        <div class="as-avatar">'.$avatar.'</div>
                        <div class="as-main">
                            <div class="as-user">'.$author_name.'</div>
                            <div class="as-content entry-content">'.wpautop($answer->comment_content).'</div>
                            <div class="as-action">
                                <span class="as-quality"><i class="fa fa-get-pocket"></i>本回答被提问者采纳</span>
                                <span class="as-time">'.forum_format_date(strtotime($answer->comment_date)).'</span>
                                <span class="as-reply-count"><a class="j-reply-list" href="javascript:;">'.$answer->comment_karma.'条评论</a></span>
                                <span class="as-reply"><a class="j-reply" href="javascript:;">我来评论</a></span>';
            if( current_user_can( 'manage_options' ) ) $html .='<span class="as-del"><a class="j-answer-del" href="javascript:;">删除</a></span>';
            if( current_user_can( 'manage_options' ) || $current_user->ID == $answer->user_id) $html .='<span class="as-top"><a class="j-answer-utop" href="javascript:;">取消最佳</a></span>';
            $html .= '</div>
                        </div>
                    </li>';
        }
    }
    
    $answers = $wpcomqadb->get_answers($question->ID, $answers_per_page, 1, $answers_order, $notin);
    if($answers){
        foreach ($answers as $answer) {
            $user = get_user_by('ID', $answer->user_id);
            $author_name = $answer->comment_author;
            $avatar = um_get_avatar($answer->user_id , '60' , um_get_avatar_type($answer->user_id));
            if($user){
                $author_name = isset($user->display_name) ? $user->display_name : $user->user_nicename;
				$avatar = um_get_avatar($answer->user_id , '60' , um_get_avatar_type($answer->user_id));
				$url = get_author_posts_url( $user->ID );
				$author_name = '<a href="'.$url.'" target="_blank">'.$author_name.'</a>';
            }

            $html .= '<li id="as-'.$answer->comment_ID.'" class="as-item" data-aid="'.$answer->comment_ID.'">
                        <div class="as-avatar">'.$avatar.'</div>
                        <div class="as-main">
                            <div class="as-user">'.$author_name.'</div>
                            <div class="as-content entry-content">'.wpautop($answer->comment_content).'</div>
                            <div class="as-action">
                                <span class="as-time">'.forum_format_date(strtotime($answer->comment_date)).'</span>
                                <span class="as-reply-count"><a class="j-reply-list" href="javascript:;">'.$answer->comment_karma.'条评论</a></span>
                                <span class="as-reply"><a class="j-reply" href="javascript:;">我来评论</a></span>';
            if( current_user_can( 'manage_options' ) ) $html .='<span class="as-del"><a class="j-answer-del" href="javascript:;">删除</a></span>';
            if( (current_user_can( 'manage_options' ) || $current_user->ID == $answer->user_id) && !$notin) $html .='<span class="as-top"><a class="j-answer-top" href="javascript:;">最佳</a></span>';
            $html .= '</div>
                        </div>
                    </li>';
        }
    }
    if(!$answers && !$answers_top){
        $html .= '<li class="as-item-none" style="text-align: center;color: #999;padding-top: 10px;">暂无回复</li>';
    }

    $html .= '</ul>';

    if($question->comment_count>$answers_per_page){
        $html .= '<div class="q-load-wrap"><a class="q-load-more" href="javascript:;">加载更多评论</a></div>';
    }

    $current_user =  wp_get_current_user();
    if($current_user->ID){
        ob_start();
        wp_editor( '', 'editor-answer', forum_editor_settings(array('textarea_name'=>'answer')) );
        $editor_contents = ob_get_clean();
        $answer_html = '<form id="as-form" class="as-form" action="" method="post" enctype="multipart/form-data">
                    <h3 class="as-form-title">我来回复</h3>
                    '.$editor_contents.'
                    <input type="hidden" name="id" value="'.$question->ID.'">
                    <div class="as-submit clearfix">
                        <div class="pull-right"><input class="btn-submit" type="submit" value="提 交"></div>
                    </div>
                </form>';
    }else{
        $answer_html = '<div class="as-login-notice">请 <a href="javascript:;" class="user-reg" data-sign="0">登录</a> 或 <a href="javascript:;" class="user-reg" data-sign="1">注册</a> 后回复</div>';
    }
    
    $html .= $answer_html.'</div></div>';
    if ( _hui('forum_login') && !is_user_logged_in() ) { 
    	
    	echo '<div class="q-content q-panel"><div class="no-login">该贴需要登录后才能查看。</div></div>';
    
    }else{
    	
    	echo $html;
    }
?>
</div>
</div>
<!-- sidebar  -->	
<?php get_sidebar(); ?>	

    </section>
</div>
<?php get_footer(); ?>