<?php 
get_header(); 
$statistic = array();

global $wpdb;
// 用户总数
$statistic['user_count'] = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
// 会员总数
$statistic['monthly_member_count'] = count_vip_members(1);
$statistic['quarter_member_count'] = count_vip_members(2);
$statistic['annual_member_count'] = count_vip_members(3);
$statistic['permanent_member_count'] = count_vip_members(4);
$statistic['member_count'] = $statistic['monthly_member_count'] + $statistic['quarter_member_count'] + $statistic['annual_member_count'] + $statistic['permanent_member_count'];
// 文章总数
$count_posts = wp_count_posts();
$statistic['publish_post_count'] = $count_posts->publish;
$statistic['pending_post_count'] = $count_posts->pending;
$statistic['draft_post_count'] = $count_posts->draft;
$statistic['post_count'] = $statistic['publish_post_count'] + $statistic['pending_post_count'] + $statistic['draft_post_count'];
// 页面总数
$count_pages = wp_count_posts('page');
$statistic['page_count'] = $count_pages->publish;
// 商品总数
$count_products = wp_count_posts('store');
$statistic['product_count'] = $count_products && isset($count_products->publish) ? $count_products->publish : 0;
// 评论总数
$statistic['comment_count'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved=1 AND comment_type=''");
// 分类总数
$statistic['category_count'] = wp_count_terms('category');
// 标签总数
$statistic['tag_count'] = wp_count_terms('post_tag');
// 商品分类总数
$statistic['product_category_count'] = wp_count_terms('products_category');
// 商品标签总数
$statistic['product_tag_count'] = wp_count_terms('products_tag');

// 友情链接数量
$statistic['links_count'] =  $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'");

// 建站日期
$statistic['site_open_date'] = _hui('site_open_date');
// 运营天数
$statistic['site_open_days'] = floor((time() - strtotime($statistic['site_open_date'])) / 86400);

// 最后更新
$modified_post_dates = $wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page' OR post_type = 'product') AND (post_status = 'publish' OR post_status = 'private')");
$statistic['last_modified'] = date('Y年n月j日', strtotime($modified_post_dates[0]->MAX_m));
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
        <?php include('navmenu.php'); ?>
        <div class="pagecontent">
        <div class="page-wrapper">
            <!-- 统计信息 -->
            <section class="statistic-info clearfix">
                <div class="page-header">
					<h3 id="info">站点统计</h3>
				</div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">建站日期</label>
                    <p class="col-md-10"><?php printf(__('%s (上线 %d 天)', 'tt'), $statistic['site_open_date'],  $statistic['site_open_days']) ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">友情链接</label>
                    <p class="col-md-10"><?php printf(__('%d 个链接', 'tt'), $statistic['links_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">最后更新</label>
                    <p class="col-md-10"><?php printf(__('%s', 'tt'), $statistic['last_modified']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">网站用户</label>
                    <p class="col-md-10"><?php printf(__('%d 注册用户', 'tt'), $statistic['user_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">网站会员</label>
                    <p class="col-md-10"><?php printf(__('%d 位付费会员 (其中 %d 月份会员, %d 季度会员, %d 年费会员, %d 终生会员)', 'tt'), $statistic['member_count'], $statistic['monthly_member_count'], $statistic['quarter_member_count'], $statistic['annual_member_count'], $statistic['permanent_member_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">文章总数</label>
                    <p class="col-md-10"><?php printf(__('%d 篇 (%d 已发布, %d 篇草稿, %d 篇待审)', 'tt'), $statistic['post_count'], $statistic['publish_post_count'], $statistic['draft_post_count'], $statistic['pending_post_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">页面总数</label>
                    <p class="col-md-10"><?php printf(__('%d 个页面', 'tt'), $statistic['page_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">上架商品</label>
                    <p class="col-md-10"><?php printf(__('%d 个在售', 'tt'), $statistic['product_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">用户评论</label>
                    <p class="col-md-10"><?php printf(__('%d 个评论', 'tt'), $statistic['comment_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">分类文章</label>
                    <p class="col-md-10"><?php printf(__('%d 个分类', 'tt'), $statistic['category_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">文章标签</label>
                    <p class="col-md-10"><?php printf(__('%d 个标签', 'tt'), $statistic['tag_count'] ); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">商品分类</label>
                    <p class="col-md-10"><?php printf(__('%d 个分类', 'tt'), $statistic['product_category_count']); ?></p>
                </div>
                <div class="form-group info-group clearfix">
                    <label class="col-md-2 control-label">商品标签</label>
                    <p class="col-md-10"><?php printf(__('%d 个标签', 'tt'), $statistic['product_tag_count']); ?></p>
                </div>
            </section>
        </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>