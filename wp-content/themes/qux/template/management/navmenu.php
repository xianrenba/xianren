<?php 
global $mg_vars; 
$current_user = wp_get_current_user(); 
$mg_vars['user'] = $current_user;
$mg_vars['user_id'] = $current_user->ID;
$mg_vars['paged'] = get_query_var('paged') ? : 1; 
global $wp_query; 
$query_vars=$wp_query->query_vars; 
$mg_tab = isset($query_vars['manage_child_route']) && in_array($query_vars['manage_child_route'], array_keys((array)json_decode(ALLOWED_MANAGE_ROUTES))) ? $query_vars['manage_child_route'] : 'status'; 
$mg_vars['manage_child_route'] = $mg_tab; 

function _conditional_class($base_class, $condition, $active_class = 'active') {
    if($condition) {
        return $base_class . ' ' . $active_class;
    }
    return $base_class;
}
?>
<aside class="pagesidebar">
   <ul class="pagesider-menu user-tab">
       <li class="<?php echo _conditional_class('status', $mg_tab == 'status'); ?>"><a href="<?php echo _url_for('manage_status'); ?>"><i class="fa fa-pie-chart"></i>统计</a></li>
       <li class="<?php echo _conditional_class('posts', $mg_tab == 'posts'); ?>"><a href="<?php echo _url_for('manage_posts'); ?>"><i class="fa fa-stack-overflow"></i>文章</a></li>
       <li class="<?php echo _conditional_class('products', $mg_tab == 'products'); ?>"><a href="<?php echo _url_for('manage_products'); ?>"><i class="fa fa-shopping-cart"></i>商品</a></li>
       <li class="<?php echo _conditional_class('orders', $mg_tab == 'orders'); ?>"><a href="<?php echo _url_for('manage_orders'); ?>"><i class="fa fa-exchange"></i>订单</a></li>
       <li class="<?php echo _conditional_class('users', $mg_tab == 'users'); ?>"><a href="<?php echo _url_for('manage_users'); ?>"><i class="fa fa-user"></i>用户</a></li>
       <li class="<?php echo _conditional_class('members', $mg_tab == 'members'); ?>"><a href="<?php echo _url_for('manage_members'); ?>"><i class="fa fa-user-md"></i>会员</a></li>
       <li class="<?php echo _conditional_class('coupons', $mg_tab == 'coupons'); ?>"><a href="<?php echo _url_for('manage_coupons'); ?>"><i class="fa fa-ticket"></i>优惠码</a></li>
    </ul>
</aside>