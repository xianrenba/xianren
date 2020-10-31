<?php 
$mg_uid = get_query_var('manage_grandchild_route');
$user_data = get_userdata($mg_uid);
//if(!$user_data) return null;

$user_info = array();
$user_info['ID'] = $mg_uid;
$user_info['display_name'] = $user_data->display_name;
$user_info['nickname'] = $user_data->nickname; //get_user_meta($author->ID, 'nickname', true);
$user_info['email'] = $user_data->user_email;
$user_info['member_since'] = mysql2date('Y/m/d H:i:s', $user_data->user_registered);
$user_info['member_days'] = max(1, round(( strtotime(date('Y-m-d')) - strtotime( $user_data->user_registered ) ) /3600/24));
$user_info['site'] = $user_data->user_url;
$user_info['description'] = $user_data->description;

//$user_info['avatar'] = tt_get_avatar($user_data->ID, 'medium');

$user_info['latest_login'] = $user_data->um_latest_login ? mysql2date('Y/m/d H:i:s', $user_data->um_latest_login) : 'N/A';


// 用户的近期订单
$latest_orders = get_um_orders($user_data->ID,'','',10);
$orders = array();
if($latest_orders) {
     foreach ($latest_orders as $latest_order) {
          $order = array();
          $order['time'] = $latest_order->order_time;
          $order['title'] = $latest_order->product_name;
          $order['mgUrl'] = _url_for('manage_order', $latest_order->id);
          $orders[] = $order;
     }
}
//$latest_orders = $orders;

// 会员start

// 会员信息
$member = getUserMemberInfo($user_data->ID); 
$member_info = array(
  'member_type'=>$member['user_type'],
  'member_status'=>$member['user_status'],
  'join_time'=>$member['startTime'],
  'end_time'=>$member['endTime']
);

if( isset($_POST['promoteVipNonce']) && current_user_can('edit_users') ){
	if ( ! wp_verify_nonce( $_POST['promoteVipNonce'], 'promotevip-nonce' ) ) {
		$message = __('安全认证失败，请重试！','um');
	}else{
		if( isset($_POST['vip_member_type']) && sanitize_text_field($_POST['vip_member_type'])=='4' ){
			$pv_type = 4;
			$pv_type_title = __('终身会员','um');
		}elseif( isset($_POST['vip_member_type']) && sanitize_text_field($_POST['vip_member_type'])=='3' ){
			$pv_type = 3;
			$pv_type_title = __('年费会员','um');
		}elseif(isset($_POST['vip_member_type']) && sanitize_text_field($_POST['vip_member_type'])=='2'){
			$pv_type = 2;
			$pv_type_title = __('季费会员','um');
		}else{
			$pv_type = 1;
			$pv_type_title = __('月费会员','um');
		}
		
		um_manual_promotevip($user_data->ID,$user_data->display_name,$user_data->user_email,$pv_type,0);			 
		$message = sprintf(__('操作成功！已成功将%1$s提升至%2$s。','um'), $user_data->display_name, $pv_type_title);
		$message .= ' <a href="'.um_get_current_page_url().'">'.__('点击刷新','um').'</a>';            
	}
}
// 会员end

// 积分start

// 积分信息
$credit_info = array(
    'credit_balance' => intval($user_data->um_credit),
    'credit_consumed' => intval($user_data->um_credit_void)
);

if( isset($_POST['creditNonce']) && current_user_can('edit_users') ){
	if ( ! wp_verify_nonce( $_POST['creditNonce'], 'credit-nonce' ) ) {
		$message = __('安全认证失败，请重试！','um');
	}else{
		$c_user_id =  $user_data->ID;
		if( isset($_POST['creditChange']) && sanitize_text_field($_POST['creditChange'])=='add' ){
			$c_do = 'add';
			$c_do_title = __('增加','um');
		}else{
			$c_do = 'cut';
			$c_do_title = __('减少','um');
		}

		$c_num =  intval($_POST['creditNum']);
		$c_desc =  sanitize_text_field($_POST['creditDesc']);
			
		$c_desc = empty($c_desc) ? '' : __('备注','um') . ' : '. $c_desc;

		update_um_credit( $c_user_id , $c_num , $c_do , 'um_credit' , sprintf(__('%1$s将你的积分%2$s %3$s 分。%4$s','um') , $mg_vars['user']->display_name, $c_do_title, $c_num, $c_desc) );
			
		$message = sprintf(__('操作成功！已将%1$s的积分%2$s %3$s 分。','um'), $user_data->display_name, $c_do_title, $c_num);
	}
}		
//~ 积分end

get_header(); 
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
        <?php include('navmenu.php'); ?>
        <div class="pagecontent user">
            <div class="dashboard-main">
            <div class="tab-content page-wrapper">
            <?php if($user_data) { ?>
                <!-- Page global message -->
				<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
                <!-- 用户信息 -->
                <section class="mg-user clearfix">
                    <div class="page-header"><h3 id="info">用户详情</h3></div>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('用户ID', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $user_data->ID; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('显示名称', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $user_data->display_name; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('用户邮箱', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $user_info['email']; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('注册时间', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $user_info['member_since']; ?><?php printf(__(' <b>(%d days)</b>', 'tt'), $user_info['member_days'] ); ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('上次登录', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $user_info['latest_login']; ?></p></div>
                        </div>
                    </div>
                </section>
                <!-- 积分管理 -->
                <section class="mg-credits clearfix">
                    <div class="page-header"><h3 id="info">用户积分</h3></div>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('当前积分', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $credit_info['credit_balance']; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('消费积分', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $credit_info['credit_consumed']; ?></p></div>
                        </div>
                    <div class="panel">
					<div class="panel-body" style="padding:0">
						<form id="creditform" role="form"  method="post">
							<input type="hidden" name="creditNonce" value="<?php echo  wp_create_nonce( 'credit-nonce' );?>" >
							<p>
								<label class="radio-inline"><input type="radio" name="creditChange" value="add" aria-required='true' required checked=""><?php _e('增加积分','um');?></label>
								<label class="radio-inline"><input type="radio" name="creditChange" value="cut" aria-required='true' required><?php _e('减少积分','um');?></label>
							</p>
							<div class="form-inline">
								<div class="form-group">
									<div class="input-group" style="width:220px;">
										<div class="input-group-addon"><?php _e('积分','um');?></div>
										<input class="form-control" type="text" name="creditNum" aria-required='true' required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?php _e('备注','um');?></div>
										<input class="form-control" type="text" name="creditDesc" aria-required='true' required>
									</div>
								</div>
								<button class="btn btn-primary" type="submit"><?php _e('提交','um');?></button>
							</div>
							<p class="help-block"><?php _e('请谨慎操作！积分数只能填写数字，备注将显示在用户的积分记录中。','um');?></p>
						</form>
					</div>
				    </div>
                    </div>
                </section>
                <!-- 会员管理 -->
                <section class="mg-membership clearfix">
                    <div class="page-header">
	                    <h3 id="info">会员信息</h3>
                    </div>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('会员类型', 'um'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $member_info['member_type']; ?></p></div>
                        </div>
                        <?php if( $member_info['member_status'] != '未开通过会员'){ ?>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('会员状态', 'um'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $member_info['member_status']; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('加入时间', 'um'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $member_info['join_time']; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('过期时间', 'um'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $member_info['end_time']; ?></p></div>
                        </div>
                        <?php } ?>
                        <form id="promotevipform" role="form"  method="post">
                        <input type="hidden" name="promoteVipNonce" value="<?php echo  wp_create_nonce( 'promotevip-nonce' );?>" >
                        <div class="form-group promote-vip-form">
                            <label class="radio-inline"><input type="radio" name="vip_member_type" value="1" aria-required="true" required checked>月费会员</label>
                            <label class="radio-inline"><input type="radio" name="vip_member_type" value="2" aria-required="true" required>季度会员</label>
                            <label class="radio-inline"><input type="radio" name="vip_member_type" value="3" aria-required="true" required>年费会员</label>
                            <label class="radio-inline"><input type="radio" name="vip_member_type" value="4" aria-required="true" required>终身会员</label>
                            <button class="btn btn-success" type="submit" id="promotevipform-submit" data-uid="<?php echo $user_data->ID; ?>"><?php _e('提升会员', 'um'); ?></button>
                            <p class="help-block"><?php _e('提示:若已开通会员则按照选择开通的类型自动续费,若会员已到期,则按重新开通计算有效期', 'tt'); ?></p>
                        </div>
                        </form>
                    </div>
                </section>
                <?php if($latest_orders = $orders) { ?>
                <!-- 近期订单 -->
                <section class="mg-orders clearfix">
                    <div class="page-header"><h3 id="info">近期订单</h3></div>
                    <div class="info-group clearfix">
                        <ul>
                        <?php foreach ($latest_orders as $latest_order) { ?>
                            <li style="margin-bottom:10px;">
                                <span class="order-time" style="margin-right: 10px;"><?php echo $latest_order['time']; ?></span>
                                <span class="order-title"><a href="<?php echo $latest_order['mgUrl']; ?>" target="_blank"><?php echo $latest_order['title']; ?></a></span>
                            </li>
                        <?php } ?>
                        </ul>
                    </div>
                </section>
                <?php } ?>
                <?php }else{ ?>
                <section class="mg-user clearfix">
                    <div class="page-header"><h3 id="info">用户详情</h3></div>
                    <div class="empty-content">
                        <i class="fa fa-user"></i>
                        <p>不存在该用户</p>
                    </div>
				</section>
                <?php } ?>
            </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>