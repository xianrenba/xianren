<?php
global $wp_query;
// Current author
$curauth = $wp_query->get_queried_object();
$user_name = filter_var($curauth->user_url, FILTER_VALIDATE_URL) ? '<a href="'.$curauth->user_url.'" target="_blank" rel="external">'.$curauth->display_name.'</a>' : $curauth->display_name;
$user_info = get_userdata($curauth->ID);
$posts_count =  $wp_query->found_posts;
$comments_count = get_comments( array('status' => '1', 'user_id'=>$curauth->ID, 'count' => true) );
$collects = $user_info->um_collect?$user_info->um_collect:0;
$collects_array = explode(',',$collects);
$collects_count = $collects!=0?count($collects_array):0;
$credit = intval($user_info->um_credit);
$credit_void = intval($user_info->um_credit_void);
$unread_count = intval(get_um_message($curauth->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )"));
// Current user
$current_user = wp_get_current_user();
//me
$is_me = $current_user->ID==$curauth->ID ? 1 : 0;
// Myself?
$oneself = $current_user->ID==$curauth->ID || current_user_can('edit_users') ? 1 : 0;
// Admin ?
$admin = $current_user->ID==$curauth->ID && current_user_can('edit_users') ? 1 : 0;
//unread
$unread = $unread_count && $is_me ? '<i class="badge">'.$unread_count.'</i>' : '';
// Tabs
$top_tabs = array(
	'index' => __('<i class="fa fa-tachometer"></i>首页','um'),
	'forum' => __('<i class="fa fa-cube"></i>社区','um'),
	'post' => __('<i class="fa fa-bookmark"></i>文章','um')."<span class='count'>($posts_count)</span>",
	'comment' => __('<i class="fa fa-comments"></i>评论','um')."<span class='count'>($comments_count)</span>",
	'collect' => __('<i class="fa fa-star"></i>收藏','um')."<span class='count'>($collects_count)</span>",
	'credit' => __('<i class="fa fa-credit-card"></i>积分','um')."<span class='count'>($credit)</span>",
	'message' => __('<i class="fa fa-envelope"></i>消息','um').$unread
);

$manage_tabs = array();
if($oneself)$manage_tabs['profile'] = '<i class="fa fa-cog"></i>资料';
if($oneself)$manage_tabs['membership']='<i class="fa fa-user-md"></i>会员';
if($oneself)$manage_tabs['orders']='<i class="fa fa-shopping-cart"></i>订单';
//if($admin)$manage_tabs['siteorders']='<i class="fa fa-tasks"></i>订单管理';
if($oneself)$manage_tabs['affiliate']='<i class="fa fa-money"></i>推广';
//if($admin)$manage_tabs['coupon']='<i class="fa fa-tags"></i>优惠码';

$other_tabs = array(
	'following' => __('<i class="fa fa-heart-o" ></i>关注','um'),
	'follower' => __('<i class="fa fa-users" ></i>粉丝','um')
);

$tabs = array_merge($top_tabs,$manage_tabs,$other_tabs);
foreach( $tabs as $tab_key=>$tab_value ){
	if( $tab_key ) $tab_array[] = $tab_key;
}

// Current tab
$get_tab = isset($_GET['tab']) && in_array($_GET['tab'], $tab_array) ? $_GET['tab'] : 'index';

// 提示
$message = $pages = '';

$item_html = '<li class="tip">'.__('没有找到记录','um').'</li>';

get_header();
?>
<!-------top pane-------->
<?php  include('u/toppane.php'); ?>

<!-- Main Wrap -->
<div id="main-wrap" >
  <div class="container pagewrapper clr" id="author-page">
	<?php include('u/aside.php'); ?>
	<div class="pagecontent author-area">
		<!-- Tab-index -->
		<?php  if( $get_tab=='index' ) {include('u/TabProfile.php');} ?>
		<!-- Tab-post -->
		<?php if( $get_tab=='post' ) {
			if(isset($_GET['action'])&&in_array($_GET['action'],array('new','edit')))include('u/newpost.php');
			else include('u/post.php');
		} ?>
		<!-- Tab-forum -->
		<?php if($get_tab=='forum'){include('u/forum.php');}?>
		<!-- Tab-comment -->
		<?php  if( $get_tab=='comment' ) {include('u/comment.php');} ?>
		<!-- Tab-collect -->
		<?php if( $get_tab=='collect'){include('u/collect.php');} ?>
		<!-- Tab-message -->
		<?php if( $get_tab=='message' ) {include('u/message.php');} ?>
		<!-- Tab-credit -->
		<?php if( $get_tab=='credit' ) {include('u/credit.php');} ?>
		<!-- Tab-profile -->
		<?php if( $get_tab=='profile' ) {include('u/profile.php');} ?>
		<!-- Tab-membership -->
		<?php if( $get_tab=='membership'&& $oneself ) {include('u/membership.php');} ?>
		<!-- Tab-orders -->
		<?php if( $get_tab=='orders' ){if(isset($_GET['order'])){ include('u/orderbox.php');}else{include('u/order.php');}} ?>
		<!-- Tab-siteorders -->
		<?php if( $get_tab=='siteorders' ) {include('u/siteorder.php');} ?>
		<!-- Tab-coupon -->
		<?php if( $get_tab=='coupon' ) {include('u/coupon.php');} ?>
		<!-- Tab-following -->
		<?php if( $get_tab=='following' ) {include('u/following.php');} ?>
		<!-- Tab-follower -->
        <?php if( $get_tab=='follower' ) {include('u/follower.php');} ?>
		<!-- Tab-affiliate -->
		<?php if( $get_tab=='affiliate' ) {include('u/affiliate.php');} ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>