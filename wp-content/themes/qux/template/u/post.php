<?php

// 页码start
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

?>

    <div class="page-wrapper">
        <div class="dashboard-main">
            <div class="page-header">
				<h3 id="info">文章列表<small>  提示：发布文章可以赚取积分哦！</small></h3>
			</div>
            <div class="dashboard-header">
              <?php if($current_user->ID==$curauth->ID){ ?>
				<p class="sub-title">您已发布<span><?php echo $posts_count; ?></span>篇文章作品<?php if(_hui('tougao_s')){?><a <?php echo is_user_logged_in() ? 'href="'.add_query_arg(array('tab'=>'post','action'=>'new'), get_author_posts_url($current_user->ID)).'" ' : 'href="javascript:" class="user-login"' ; ?>><span class="new-post-btn">写文章</span></a><?php }?></p>
				<?php }else{ ?>
                <p class="sub-title">TA已发布<span><?php echo $posts_count; ?></span>篇文章作品</p>
               <?php } ?>
				<!-- Page global message -->
				<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
			</div>			
            <div class="dashboard-wrapper select-posts">
				<article class="content panel panel-default archive" role="main">
<?php  
	global $wp_query;
	$args = is_user_logged_in() && $oneself ? array_merge( $wp_query->query_vars, array( 'post_status' => array( 'publish', 'pending', 'draft' ) ) ) : $wp_query->query_vars;
	query_posts( $args );
      if(query_posts( $args )){
            get_template_part( 'template/excerpt' );
      }else{
         if($current_user->ID==$curauth->ID){ ?>
           <div class="empty-content"><i class="fa fa-inbox"></i><p>还没有发布文章，马上发布一篇...</p><a <?php echo is_user_logged_in() ? 'class="btn btn-info" href="'.add_query_arg(array('tab'=>'post','action'=>'new'), get_author_posts_url($current_user->ID)).'" ' : 'class="btn btn-info user-login" href="javascript:"' ;?>>写文章</a></div>        
         <?php }else{ 
            echo '<div class="empty-content"><i class="fa fa-inbox"></i><p>TA还没有发布文章，这里什么都没有...</p><a class="btn btn-info" href="/">去首页看看</a></div>';
          }
      }
?>		
				</article>
            </div>
        </div>
    </div>