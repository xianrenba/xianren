<?php
$posts_per_page = get_option('posts_per_page', 10);
$paged = max( 1, get_query_var('page') );
$args = array(
      'post_type' => 'post',
      'post_status' => 'draft,pending,publish',
      'posts_per_page' => $posts_per_page,
      'paged' => $paged,
//    'has_password' => false,
      'ignore_sticky_posts' => true,
      'orderby' => 'date', // modified - 如果按最新编辑时间排序
      'order' => 'DESC'
);

$query = new WP_Query($args);
$query->is_home = false;
$query->is_author = false;
$GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query) //TODO 缓存时无效

$manage_posts = array();
$count = $query->found_posts;
$max_pages = $query->max_num_pages; //ceil($count / $posts_per_page);
//$pagination_base = tt_url_for('manage_posts') . '/page/%#%';

while ($query->have_posts()) : $query->the_post();
    $manage_post = array();
    global $post;
    $manage_post['ID'] = $post->ID;
    $manage_post['title'] = get_the_title($post);
    $manage_post['permalink'] = get_permalink($post);
    //$manage_post['comment_count'] = $post->comment_count;
    $manage_post['excerpt'] = get_the_excerpt($post);
    $manage_post['category'] = get_the_category_list(' ', '', $post->ID);
    $manage_post['author'] = get_the_author();
    $manage_post['author_url'] = get_author_posts_url(get_the_author_meta('ID'));
    $manage_post['time'] = get_post_time('Y-m-d H:i:s', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
    $manage_post['datetime'] = get_the_time(DATE_W3C, $post);
    $manage_post['thumb'] = _get_post_thumbnail();
    $manage_post['format'] = get_post_format($post) ? : 'standard';

    $manage_post['edit_link'] = _url_for('edit_post', $post->ID);

    $manage_post['post_status'] = $post->post_status;

    $manage_post['status_string'] = '';
    if($post->post_status == 'pending') {
         $manage_post['status_string'] = __('待审', 'tt');
    }elseif($post->post_status == 'draft') {
         $manage_post['status_string'] = __('草稿', 'tt');
    }

    $actions = array();
    $actions[] = array(
         'class' => 'btn btn-inverse act-edit',
         'url' => $manage_post['edit_link'],
         'text' => __('编辑', 'tt'),
         'action' => ''
    );

    if($post->post_status == 'publish') {
       $actions[] = array(
         'class' => 'btn btn-warning post-act act-draft',
         'url' => 'javascript:;',
         'text' => __('存稿', 'tt'),
         'action' => 'draft'
       );
    }elseif($post->post_status == 'draft') {
       $actions[] = array(
         'class' => 'btn btn-primary post-act act-publish',
         'url' => 'javascript:;',
         'text' => __('发布', 'tt'),
         'action' => 'publish'
       );
    }elseif($post->post_status == 'pending'){
       $actions[] = array(
          'class' => 'btn btn-success post-act act-approve',
          'url' => 'javascript:;',
          'text' => __('通过', 'tt'),
          'action' => 'publish'
       );
    }
    $actions[] = array(
         'class' => 'btn btn-danger post-act act-trash',
         'url' => 'javascript:;',
         'text' => __('删除', 'tt'),
         'action' => 'trash'
    );
    $manage_post['actions'] = $actions;

    $manage_posts[] = $manage_post;
endwhile;

wp_reset_query();
//wp_reset_postdata();

// ----文章操作---
if( isset($_POST['postNonce']) && current_user_can('edit_users') ){
    $msg = '';
    $ico = 'error';
	if ( ! wp_verify_nonce( $_POST['postNonce'], 'post-nonce' ) ) {
        $msg = __('安全认证失败，请重试！','um');
	}else{
		$post_id = intval($_POST['post_id']);
        $act = $_POST['act'];
        $action = in_array($act, array('publish', 'draft', 'pending', 'trash')) ? $act : 'draft';
        $new_post = wp_update_post( array(
		    'ID' => intval($post_id),
			'post_status'   => $action,
		) );
        if( is_wp_error( $new_post ) ){
			$msg = __('操作文章失败','um');
		}else{
            $ico = 'success';
            $msg = __('操作文章成功','um');
        }          
	}
    $return = array('msg'=>$msg,'ico'=>$ico);
	echo json_encode($return);
	exit;
}

get_header(); 
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
            <?php include('navmenu.php'); ?>
            <div class="pagecontent">
              <div class="page-wrapper posts">
        <div class="tab-content">
            <!-- 全站文章列表 -->
            <section class="mg-posts clearfix">
                <div class="page-header">
	                <h3 id="info">文章列表</h3>
                </div>
                <?php if($count > 0) { ?>
                    <div class="loop-wrap loop-rows posts-loop-rows clearfix">
                        <input type="hidden" name="postNonce" value="<?php echo  wp_create_nonce( 'post-nonce' );?>" >
                        <?php foreach ($manage_posts as $post) { ?>
                            <article id="<?php echo 'post-' . $post['ID']; ?>" class="post type-post status-<?php echo $post['post_status']; ?> <?php echo 'format-' . $post['format']; ?>">
                                <div class="entry-thumb hover-scale">
                                    <?php echo $post['thumb']; ?>
                                    <?php echo $post['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title"><?php if(!empty($post['status_string'])){echo '[' . $post['status_string'] . ']&nbsp;'; } ?><a href="<?php echo $post['permalink']; ?>" rel="bookmark"><?php echo $post['title']; ?></a></h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="text-muted"><?php _e('日期: ', 'tt'); ?></span><span class="entry-date"><time class="entry-date" datetime="<?php echo $post['datetime']; ?>" title="<?php echo $post['datetime']; ?>"><?php echo $post['time']; ?></time></span>
                                            <span class="text-muted"><?php _e('作者: ', 'tt'); ?></span><span class="entry-author"><a href="<?php echo $post['author_url']; ?>" target="_blank"><?php echo $post['author']; ?></a></span>
                                        </div>
                                    </header>
                                    <div class="entry-excerpt">
                                        <div class="post-excerpt"><?php echo $post['excerpt']; ?></div>
                                    </div>
                                </div>
                                <div class="actions transition">
                                    <?php foreach ($post['actions'] as $action) { ?>
                                        <a class="<?php echo $action['class']; ?>" href="<?php echo $action['url']; ?>" data-post-id="<?php echo $post['ID']; ?>" data-act="<?php echo $action['action']; ?>"><?php echo $action['text']; ?></a>
                                    <?php } ?>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                    <?php echo um_pager($paged,$max_pages); ?>
                <?php }else{ ?>
                    <div class="empty-content">
                        <i class="fa fa-dropbox"></i>
                        <p><?php _e('这里什么也没有..', 'tt'); ?></p>
                        <a class="btn btn-info" href="/"><?php _e('返回首页', 'tt'); ?></a>
                    </div>
                <?php } ?>
            </section>
        </div>
</div>
            </div>
    </div>
</div>
<?php get_footer(); ?>