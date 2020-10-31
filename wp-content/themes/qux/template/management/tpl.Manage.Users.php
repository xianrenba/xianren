<?php 
// pages
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;
$um_filter_type = get_query_var('manage_grandchild_route');
$args = array(
        //'role'         => '',
        'orderby'      => 'ID', //'login',
        'order'        => 'DESC',
        'offset'       => ($paged - 1) * $number,
        'number'       => $number,
        'count_total'  => true
    );
if($um_filter_type != 'all') {
      $args['role'] = $um_filter_type;
}

$users_query = new WP_User_Query($args);
$users = $users_query->get_results(); //get_users($args);
$count = $users ? count($users) : 0;
$total_count = $users_query->get_total();
$pages = ceil($total_count / $number);
  
get_header(); 
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
        <?php include('navmenu.php'); ?>
        <div class="pagecontent">
            <div class="tab-content page-wrapper">
            <!-- 用户列表 -->
            <section class="mg-users clearfix">
                <div class="page-header"><h3 id="info"><?php _e('用户列表','um');?></h3></div>
                <div class="info-group clearfix" style="margin-left: -15px; margin-right: -15px;">
                    <div class="col-md-6 users-info">
                        <span><?php printf(__('共有 %d 位用户', 'um'), $total_count); ?></span>
                    </div>
                    <div class="col-md-6 users-filter">
                        <label><?php _e('用户角色', 'um'); ?></label>
                        <select class="form-control select select-primary" data-toggle="select" onchange="document.location.href=this.options[this.selectedIndex].value;" style="display:inline;width:auto;">
                            <option value="<?php echo _url_for('manage_users'); ?>" <?php if(strtolower($um_filter_type) == 'all') echo 'selected'; ?>><?php _e('全部', 'um'); ?></option>
                            <option value="<?php echo _url_for('manage_admins'); ?>" <?php if(strtolower($um_filter_type) == 'administrator') echo 'selected'; ?>><?php _e('管理员', 'um'); ?></option>
                            <option value="<?php echo _url_for('manage_editors'); ?>" <?php if(strtolower($um_filter_type) == 'editor') echo 'selected'; ?>><?php _e('编辑', 'um'); ?></option>
                            <option value="<?php echo _url_for('manage_authors'); ?>" <?php if(strtolower($um_filter_type) == 'author') echo 'selected'; ?>><?php _e('作者', 'um'); ?></option>
                            <option value="<?php echo _url_for('manage_contributors'); ?>" <?php if(strtolower($um_filter_type) == 'contributor') echo 'selected'; ?>><?php _e('投稿者', 'um'); ?></option>
                            <option value="<?php echo _url_for('manage_subscribers'); ?>" <?php if(strtolower($um_filter_type) == 'subscriber') echo 'selected'; ?>><?php _e('订阅者', 'um'); ?></option>
                        </select>
                    </div>
                </div>
                <?php if($count > 0) { ?>
                    <div class="table-wrapper site-users">
                        <table class="table table-striped table-framed table-centered users-table">
                            <thead>
                            <tr>
                                <th class="th-uid"><?php _e('ID', 'um'); ?></th>
                                <th class="th-name"><?php _e('用户名', 'um'); ?></th>
                                <th class="th-email"><?php _e('邮箱', 'um'); ?></th>
                                <th class="th-role"><?php _e('角色', 'um'); ?></th>
                                <th class="th-time"><?php _e('注册时间', 'um'); ?></th>
                                <th class="th-last"><?php _e('上次登录', 'um'); ?></th>
                                <th class="th-actions" style="min-width:80px;"><?php _e('操作', 'um'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user){ ?>
                                <tr id="uid-<?php echo $user->ID; ?>">
                                    <td><?php echo $user->ID; ?></td>
                                    <td><?php echo $user->display_name; ?></td>
                                    <td><?php echo $user->user_email; ?></td>
                                    <td><?php _get_user_roles($user); ?></td>
                                    <td><?php echo $user->user_registered; ?></td>
                                    <td><?php echo mysql2date('Y-m-d H:i:s', get_user_meta($user->ID, 'um_latest_login', true));; ?></td>
                                    <td>
                                        <div class="user-actions">
                                            <a class="view-detail" href="<?php echo _url_for('manage_user', $user->ID); ?>" title="<?php _e('管理用户', 'um'); ?>" target="_blank"><?php _e('管理', 'um'); ?></a>
                                            <span class="text-explode">|</span>
                                            <a class="view-home" href="<?php echo get_author_posts_url($user->ID); ?>" title="<?php _e('访问用户主页', 'um'); ?>" target="_blank"><?php _e('主页', 'um'); ?></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php echo um_pager($paged, $pages); ?> 
                <?php }else{ ?>
                    <div class="empty-content">
                        <i class="fa fa-users"></i>
                        <p><?php _e('这里什么都没有...', 'um'); ?></p>
                    </div>
                <?php } ?>
            </section>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>