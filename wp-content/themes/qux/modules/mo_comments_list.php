<?php
/**
 * [mo_comments_list description]
 * @param  [type] $comment [description]
 * @param  [type] $args    [description]
 * @param  [type] $depth   [description]
 * @return [type]          [description]
 */
function mo_comments_list($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;

    global $commentcount, $wpdb, $post;
    if(!$commentcount) { //初始化楼层计数器

        $page     = get_query_var('cpage');//获取当前评论列表页码
        $cpp      = get_option('comments_per_page');//获取每页评论显示数量
        $pcs      = get_option('page_comments');//分页开关
        
        $comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID AND comment_type = '' AND comment_approved = '1' AND !comment_parent");
        $cnt      = count($comments);//获取主评论总数量

        if ( get_option('comment_order') === 'desc' ) { //倒序
            if (!$pcs || ceil($cnt / $cpp) == 1 || ($page > 1 && $page  == ceil($cnt / $cpp))) {
                $commentcount = $cnt + 1;//如果评论只有1页或者是最后一页，初始值为主评论总数
            } else {
                $commentcount = $cpp * $page + 1;
            }
        }else{ //顺序
            if( !$pcs ){
                $commentcount = 0;
            }else{
                $page = $page-1;
                $commentcount = $cpp * $page;
            }
        }
    }


    echo '<li '; comment_class(); echo ' id="comment-'.get_comment_ID().'">';
  
    /* echo '<span class="comt-f">';
    if(!$parent_id = $comment->comment_parent ) {
        if(get_option('comment_order') === 'desc'){
			switch ($commentcount){
				case 2 :echo "沙发";--$commentcount;break;
				case 3 :echo "板凳";--$commentcount;break;
				case 4 :echo "地板";--$commentcount;break;
				default:printf('%1$s楼', --$commentcount);
			}
	   }else{
		    switch ($commentcount){
			   case 0 :echo "沙发";++$commentcount;break;
			   case 1 :echo "板凳";++$commentcount;break;
			   case 2 :echo "地板";++$commentcount;break;
			   default:printf('%1$s楼', ++$commentcount);
			}  
	   }
    }
    echo '</span>'; */
    if(!$parent_id = $comment->comment_parent ) {
        echo '<span class="comt-f">#'. (get_option('comment_order') === 'desc'?--$commentcount:++$commentcount) .'</span>';
    }
  
    echo '<div class="comt-avatar">';
        //echo _get_the_avatar($user_id=$comment->user_id, $user_email=$comment->comment_author_email);
		echo um_get_avatar( (!empty($comment->user_id) ? $comment->user_id : $comment->comment_author_email) , '40' , um_get_avatar_type($comment->user_id) ); 
    echo '</div>';

    echo '<div class="comt-main" id="div-comment-'.get_comment_ID().'">';

        comment_text();

        if ($comment->comment_approved == '0'){
            echo '<span class="comt-approved">待审核</span>';
        }
        
        echo '<div class="comt-meta"><span class="comt-author">'.get_comment_author_link().'</span>';
        //显示VIP用户
        if(in_array(getUserMemberType($comment->user_id), array('1','2','3','4'))) echo um_member_icon($comment->user_id);
		//添加用户等级
		get_author_class($comment->comment_author_email, $comment->user_id);
		
        echo _get_time_ago($comment->comment_date); 
        echo CID_get_comment_flag();
        echo CID_get_comment_browser();
        if ($comment->comment_approved !== '0'){
            $replyText = get_comment_reply_link( array_merge( $args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
            // echo str_replace(' href', ' href="javascript:;" data-href', $replyText ); 
            if( strstr($replyText, 'reply-login') ){
                 echo preg_replace('# class="[\s\S]*?" href="[\s\S]*?"#', ' class="user-reg" href="javascript:;"', $replyText );
            }else{
                echo preg_replace('# href=[\s\S]*? onclick=#', ' href="javascript:;" onclick=', $replyText );
            }
        }
        echo '</div>';
        
    echo '</div>';
}
