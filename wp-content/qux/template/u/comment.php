<?php
// 页码start
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

// Item html
$item_html = '<li class="tip">'.__('没有找到记录','um').'</li>';

?>
    <div class="page-wrapper">
        <div class="dashboard-main">
		<div class="page-header">
	        <h3 id="info">评论列表<small>  提示：认真填写的点评都有可能被推荐为精彩评论哦。</small></h3>
        </div>
			<!-- Page global message -->
			<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
            <div class="dashboard-wrapper select-comment">
<?php if(!$comments_count>0) { 
       if($current_user->ID==$curauth->ID){
                 echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>您还没发表过任何的评论。我们期待您的精彩点评。</p><a class="btn btn-info" href="/">去首页看看</a></div>';
          }else{
                 echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>TA还没发表过任何的评论。这里空空如也...</p><a class="btn btn-info" href="/">去首页看看</a></div>';}
       }else{ ?>			
<?php
	$comments_status = $oneself ? '' : 'approve';
	$all = get_comments( array('status' => '', 'user_id'=>$curauth->ID, 'count' => true) );
	$approve = get_comments( array('status' => '1', 'user_id'=>$curauth->ID, 'count' => true) );
	$pages = $oneself ? ceil($all/$number) : ceil($approve/$number);
	$comments = get_comments(array('status' => $comments_status,'order' => 'DESC','number' => $number,'offset' => $offset,'user_id' => $curauth->ID));
	if($comments){
		$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 条评论，其中 %2$s 条已获准， %3$s 条正等待审核。','um'),$all, $approve, $all-$approve) . '</li>';
		foreach( $comments as $comment ){
			$item_html .= ' <li>';
			if($comment->comment_approved!=1) $item_html .= '<small class="text-danger">'.__( '这条评论正在等待审核','um' ).'</small>';
            $item_html .= um_get_avatar( $curauth->ID , '36' , um_get_avatar_type($curauth->ID) );
			$item_html .= '<div class="comment message-content comment-message">'.convert_smilies($comment->comment_content).'</div>';
			$item_html .= '<a class="info" href="'.htmlspecialchars( get_comment_link( $comment->comment_ID) ).'">'.sprintf(__('%1$s  发表在  %2$s','um'),$comment->comment_date,get_the_title($comment->comment_post_ID)).'</a>';
			$item_html .= '</li>';
		}
		if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','um'),$paged, $pages, $number).'</li>';
	}
	echo '<ul class="user-msg">'.$item_html.'</ul>';
	echo um_pager($paged, $pages);
} ?>
			</div>
        </div>
    </div>