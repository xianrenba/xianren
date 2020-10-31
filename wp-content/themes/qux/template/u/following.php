<?php
// 页码start
$paged = max( 1, get_query_var('page') );
$number = 9;
$offset = ($paged-1)*$number;

?>
<div class="page-wrapper">
   <div class="page-header">
		<h3 id="info"><?php _e('粉丝','um');?>(<?php echo um_following_count($curauth->ID); ?>) <small><?php if($current_user->ID==$curauth->ID){_e('我的关注','um');}else{_e('TA的关注','um');} ?></small></h3>
   </div>
   <div class="widget-body">
		<div class="item">
			<ul class="flowlist following-list clx">
			<?php if(um_following_count($curauth->ID)==0){
                if($current_user->ID==$curauth->ID){
                    echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>您还没有关注任何人，赶快去看看吧</p><a class="btn btn-info" href="/">去首页看看</a></div>';
                }else{
					echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>TA还没有关注任何人...</p><a class="btn btn-info" href="/">去首页看看</a></div>';}
				}else{
                    //echo um_follow_list($curauth->ID,20,'following');
                    $all = um_following_count($curauth->ID);
	                $pages = ceil($all / $number);
                    $results = um_following($curauth->ID,$number,$offset);
  					$field='user_id';
	                if($results)
		            foreach($results as $result){
			            $user = get_userdata($result->$field); ?>
                        <div class="follow-box follower-box col-md-4 col-sm-6">
                            <div class="box-inner transition">
                                <div class="cover" style="background-image: url(<?php echo _get_user_cover($user->ID,'small',UM_URI . '/img/personcard-cover.jpg'); ?>)">
                                <?php echo um_get_avatar( $result->$field , '40' , um_get_avatar_type($result->$field) ); ?>
		                            <div class="mask">
		                                <h2><?php echo $user->display_name; ?></h2>
                                        <p>简介 :<?php echo $user->description; ?></p>
                                    </div>
                                </div>
                                <div class="user-stats">
		                            <span class="following"><span class="unit">关注</span><?php echo um_following_count($user->ID); ?></span>
		                            <span class="followers"><span class="unit">粉丝</span><?php echo um_follower_count($user->ID); ?></span>
		                            <span class="posts"><span class="unit">文章</span><?php echo num_of_author_posts($user->ID); ?></span>
		                        </div>
		                        <div class="user-interact">
		                        <?php echo um_follow_button($user->ID);?>
		                            <a class="pm-btn" href="<?php echo add_query_arg('tab', 'message', get_author_posts_url( $user->ID )); ?>" title="发送站内消息"><i class="fa fa-envelope"></i>私信</a>
		                            <a class="dropdown-toggle more-link-btn" href="javascript: void 0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars" ></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo add_query_arg('tab', 'index', get_author_posts_url( $user->ID )); ?>">访问主页</a></li>
                                        <li><a href="<?php echo add_query_arg('tab', 'post', get_author_posts_url( $user->ID )); ?>">他的文章</a></li>
                                        <li><a href="<?php echo add_query_arg('tab', 'comment', get_author_posts_url( $user->ID )); ?>">他的评论</a></li>
                                    </ul>
		                        </div>
	                        </div>
                        </div>
                    <?php }
                    $pages = ceil($all/$number);
					echo um_pager($paged, $pages);   
                    } ?>
			</ul>
	    </div>
	</div>
</div>