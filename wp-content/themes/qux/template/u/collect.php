<?php
// pages
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;
$post_likes = $user_info->um_post_likes?$user_info->um_post_likes:0;
$post_likes_array = explode(',',$post_likes);
$post_likes_count = $collects!=0?count($post_likes_array):0;
?>

    <div class="page-wrapper">
        <div class="dashboard-main">
		    <div class="page-header">
	            <h3 id="info">文章收藏<small>  提示：遇到不错的文章就收藏一下。</small></h3>
            </div>
            <div class="dashboard-header">
              <?php if($current_user->ID==$curauth->ID){ ?>
                <p class="tip">您已收藏<span>&nbsp;<?php echo $collects_count; ?>&nbsp;</span>篇文章</p>
                <?php }else{ ?>
				<p class="tip">TA已收藏<span>&nbsp;<?php echo $collects_count ; ?>&nbsp;</span>篇文章</p>
                <?php } ?>
				<!-- Page global message -->
				<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
			</div>
            <div class="dashboard-wrapper select-collect">
            <?php if(!$collects_count>0){ 
                if($current_user->ID==$curauth->ID){
                    echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>您还没有收藏文章哦，遇到好的文章可以收藏在这里。</p><a class="btn btn-info" href="/">去首页看看</a></div>';
                }else{
		            echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>TA还没有收藏文章，这里什么都没有...</p><a class="btn btn-info" href="/">去首页看看</a></div>';
				}
            }else{ 
	            global $wp_query;
	            $args = array_merge( $wp_query->query_vars, array( 'post__in' => $collects_array, 'post_status' => 'publish' ) );
	            query_posts( array( 'post__not_in'=>get_option('sticky_posts'), 'post__in' => $collects_array, 'post_status' => 'publish' ) );
                get_template_part( 'template/excerpt' );      
            } ?>				
            </div>
        </div>
    </div>
