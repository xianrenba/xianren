<?php 
$posts_per_page = get_option('posts_per_page', 10);
$paged = max( 1, get_query_var('page') );
$args = array(
     'post_type' => 'store',
     'post_status' => 'draft,pending,publish',
     'posts_per_page' => $posts_per_page,
     'paged' => $paged,
//   'has_password' => false,
     'ignore_sticky_posts' => true,
     'orderby' => 'modified', // modified - 如果按最新编辑时间排序 or date
     'order' => 'DESC'
);

$query = new WP_Query($args);
$query->is_home = false;
$query->is_author = false;
$GLOBALS['wp_query'] = $query; // 取代主循环(query_posts只返回posts，为了获取其他有用数据，使用WP_Query) //TODO 缓存时无效

$products = array();
$count = $query->found_posts;
$max_pages = $query->max_num_pages; //ceil($count / $posts_per_page);
//$pagination_base = _url_for('manage_posts') . '/page/%#%';

while ($query->have_posts()) : $query->the_post();
    $product = array();
    global $post;
    $product['ID'] = $post->ID;
    $product['title'] = get_the_title($post);
    $product['permalink'] = get_permalink($post);
    //$product['comment_count'] = $post->comment_count;
    $product['excerpt'] = get_the_excerpt($post);
    $product['category'] = get_the_term_list($post->ID, 'products_category', ' ', '');
    //$product['author'] = get_the_author();
    //$product['author_url'] = get_author_posts_url(get_the_author_meta('ID'));
    $product['time'] = get_post_time('Y-m-d H:i:s', false, $post, false); //get_post_time( string $d = 'U', bool $gmt = false, int|WP_Post $post = null, bool $translate = false )
    $product['datetime'] = get_the_time(DATE_W3C, $post);
    $product['modified_time'] = get_post_modified_time('Y-m-d H:i:s', false, $post);
    // $product['thumb'] = tt_get_thumb($post, 'medium');
    $product['thumb'] = _get_post_thumbnail();
    //$product['format'] = get_post_format($post) ? : 'standard';

    // 支付类型
    $product['currency'] = get_post_meta( $post->ID, 'pay_currency', true) ? 'cash' : 'credit';

    // 价格
    $product['price'] = $product['currency'] == 'cash' ? sprintf('%0.2f', get_post_meta($post->ID, 'product_price', true)) : (int)get_post_meta($post->ID, 'product_price', true);

    // 单位
    $product['price_unit'] = $product['currency'] == 'cash' ? __('元', 'tt') : __('积分', 'tt');

    // 价格图标
    $product['price_icon'] = !($product['price'] > 0) ? '' : $product['currency'] == 'cash' ? '<i class="fa fa-cny"></i> ' : '<i class="fa fa-diamond"></i> ';

    // 折扣
    //$product['discount'] = maybe_unserialize(get_post_meta($post->ID, 'tt_product_discount', true)); // array 第1项为普通折扣, 第2项为会员(月付)折扣, 第3项为会员(年付)折扣, 第4项为会员(永久)折扣

    // 库存
    $product['amount'] = (int)get_post_meta($post->ID, 'product_amount', true);

    // 销量
    $product['sales'] = absint(get_post_meta($post->ID, 'product_sales', true));

    $product['edit_link'] = get_edit_post_link($post->ID);//tt_url_for('edit_post', $post->ID);

    $product['post_status'] = $post->post_status;

    $product['status_string'] = __('在售', 'tt');
    if($post->post_status == 'pending') {
       $product['status_string'] = __('待售', 'tt');
    }elseif($post->post_status == 'draft') {
       $product['status_string'] = __('编辑中', 'tt');
    }

    $actions = array();
    $actions[] = array(
        'class' => 'btn btn-inverse act-edit',
        'url' => $product['edit_link'],
        'text' => __('编辑', 'tt'),
        'action' => ''
    );

    if($post->post_status == 'publish') {
       $actions[] = array(
           'class' => 'btn btn-warning product-act act-draft',
           'url' => 'javascript:;',
           'text' => __('下架', 'tt'),
           'action' => 'draft'
       );
    }elseif($post->post_status == 'draft') {
       $actions[] = array(
           'class' => 'btn btn-primary product-act act-publish',
           'url' => 'javascript:;',
           'text' => __('上架', 'tt'),
           'action' => 'publish'
       );
    }elseif($post->post_status == 'pending'){
       $actions[] = array(
           'class' => 'btn btn-success product-act act-approve',
           'url' => 'javascript:;',
           'text' => __('上架', 'tt'),
           'action' => 'publish'
       );
    }
    $actions[] = array(
         'class' => 'btn btn-danger product-act act-trash',
         'url' => 'javascript:;',
         'text' => __('删除', 'tt'),
         'action' => 'trash'
    );
    $product['actions'] = $actions;

    $products[] = $product;
endwhile;

wp_reset_query();
//wp_reset_postdata();

// ----商品操作---
if( isset($_POST['productsNonce']) && current_user_can('edit_users') ){
    $msg = '';
    $ico = 'error';
	if ( ! wp_verify_nonce( $_POST['productsNonce'], 'products-nonce' ) ) {
		//$message = __('安全认证失败，请重试！','um');
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
			$msg = __('操作商品失败','um');
		}else{
            $ico = 'success';
            $msg = __('操作商品成功','um');
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
          <div class="page-wrapper posts-tab products-tab">
        <div class="tab-content">
            <!-- 全站商品列表 -->
            <section class="mg-posts mg-products clearfix">
                <div class="page-header">
	                <h3 id="info">商品列表</h3>
                </div>
                <?php if($count > 0) { ?>
                    <div class="loop-wrap loop-rows posts-loop-rows clearfix">
                        <input type="hidden" name="productsNonce" value="<?php echo  wp_create_nonce( 'products-nonce' );?>" >
                        <?php foreach ($products as $product) { ?>
                            <article id="<?php echo 'product-' . $product['ID']; ?>" class="post product type-product status-<?php echo $product['post_status']; ?>">
                                <div class="entry-thumb hover-scale">
                                    <?php echo $product['thumb']; ?>
                                    <?php echo $product['category']; ?>
                                </div>
                                <div class="entry-detail">
                                    <header class="entry-header">
                                        <h2 class="entry-title"><?php if(!empty($product['status_string'])){echo '[' . $product['status_string'] . ']&nbsp;'; } ?><a href="<?php echo $product['permalink']; ?>" rel="bookmark"><?php echo $product['title']; ?></a></h2>
                                        <div class="entry-meta entry-meta-1">
                                            <span class="text-muted"><?php _e('日期:', 'tt'); ?></span><span class="entry-date"><time class="entry-date" datetime="<?php echo $product['datetime']; ?>" title="<?php echo $product['datetime']; ?>"><?php echo $product['time']; ?></time></span>
                                            <span class="text-muted"><?php _e('更新:', 'tt'); ?></span><span class="entry-date"><time class="entry-date" datetime="<?php echo $product['modified_time']; ?>" title="<?php echo $product['modified_time']; ?>"><?php echo $product['modified_time']; ?></time></span>
                                            <span class="text-muted"><?php _e('价格:', 'tt'); ?></span><span class="entry-author"><?php echo $product['price_icon'] . $product['price'] . $product['price_unit']; ?></span>
                                            <span class="text-muted"><?php _e('库存:', 'tt'); ?></span><span class="entry-author"><?php echo $product['amount']; ?></span>
                                            <span class="text-muted"><?php _e('销量:', 'tt'); ?></span><span class="entry-author"><?php echo $product['sales']; ?></span>
                                        </div>
                                    </header>
                                    <div class="entry-excerpt">
                                        <div class="post-excerpt"><?php echo $product['excerpt']; ?></div>
                                    </div>
                                </div>
                                <div class="actions transition">
                                    <?php foreach ($product['actions'] as $action) { ?>
                                        <a class="<?php echo $action['class']; ?>" href="<?php echo $action['url']; ?>" data-product-id="<?php echo $product['ID']; ?>" data-act="<?php echo $action['action']; ?>"><?php echo $action['text']; ?></a>
                                    <?php } ?>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                    <?php echo um_pager($paged,$max_pages); ?>
                <?php }else{ ?>
                    <div class="empty-content">
                        <i class="fa fa-dropbox"></i>
                        <p><?php _e('这里什么也没有...', 'tt'); ?></p>
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