<?php
// 积分start
if( isset($_POST['creditNonce']) && current_user_can('edit_users') ){
	if ( ! wp_verify_nonce( $_POST['creditNonce'], 'credit-nonce' ) ) {
		$message = __('安全认证失败，请重试！','um');
	}else{
		$c_user_id =  $curauth->ID;
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
		
		update_um_credit( $c_user_id , $c_num , $c_do , 'um_credit' , sprintf(__('%1$s将你的积分%2$s %3$s 分。%4$s','um') , $current_user->display_name, $c_do_title, $c_num, $c_desc) );
		$message = sprintf(__('操作成功！已将%1$s的积分%2$s %3$s 分。','um'), $user_name, $c_do_title, $c_num);

	}
}	
	
//~ 积分end

// pages
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

// Item html
$item_html = '<li class="tip">'.__('没有找到记录','um').'</li>';

?>

    <div class="page-wrapper">
        <div class="dashboard-main">
		    <div class="page-header">
                <h3 id="info"><?php _e('积分管理','um');?> <small>提示：投稿、评论、参与互动获取积分</small></h3>
            </div>
				<!-- Page global message -->
				<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
            <div class="dashboard-wrapper select-credit">
<?php if ( current_user_can('edit_users') ) { ?>
				<div class="panel panel-danger">
					<div class="panel-heading"><?php echo $curauth->display_name.__('积分变更（仅管理员可见）','um');?></div>
					<div class="panel-body">
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
								<button class="btn btn-default" type="submit"><?php _e('提交','um');?></button>
							</div>
							<p class="help-block"><?php _e('请谨慎操作！积分数只能填写数字，备注将显示在用户的积分记录中。','um');?></p>
						</form>
					</div>
				</div>
<?php } 				
	//~ 积分充值
if ( $current_user->ID==$curauth->ID ) { ?>
                 <div class="form-horizontal">
			        <div class="form-group">
					 <label class="col-sm-3 control-label">积分余额</label>
                     <div class="col-sm-9"><p class="form-control-static"><?php echo $credit; ?></p></div>
                     <label class="col-sm-3 control-label">已消费</label>
                     <div class="col-sm-9"><p class="form-control-static"><?php echo $credit_void; ?></p></div>
                     <label class="col-sm-3 control-label">签到</label>
                     <div class="col-sm-9"><p class="form-control-static"><?php echo um_whether_signed($current_user->ID); ?></p></div>						
                  </div>
			    </div>
				<div class="panel panel-success">
					<div class="panel-heading"><?php echo __('积分充值（仅自己可见）','um');?></div>
					<div class="panel-body">
						<form id="creditrechargeform" role="form"  method="post">
							<input type="hidden" name="creditrechargeNonce" value="<?php echo  wp_create_nonce( 'creditrecharge-nonce' );?>" >
							<input type = "hidden" id="order_id" name="order_id" readonly="" value="0">
							<input type = "hidden" id="product_id" name="product_id" readonly="" value="-5">
							<p>
								<label><?php echo sprintf(__('当前积分兑换比率为：1元 = %1$s 积分','um'),_hui('exchange_ratio',50));?></label>
							</p>
							<div class="form-inline">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?php _e('积分*100','um');?></div>
										<input class="form-control" type="text" name="creditrechargeNum" value="10" aria-required='true' required>
									</div>
								</div>
								<button class="btn btn-default" type="submit" id="creditrechargesubmit"><?php _e('充值','um');?></button>
							</div>
							<p class="help-block"><?php _e('积分数以100为单位起计算,请填写整数数值，如填1即表明充值100积分，所需现金根据具体兑换比率计算。','um');?></p>
						</form>
					</div>
				</div>
<?php } 
              
				$item_html = '<li class="tip">' . sprintf(__('共有 %1$s 个积分，其中 %2$s 个已消费， %3$s 个可用。','um'), ($credit+$credit_void), $credit_void, $credit) . '</li>';
				if($oneself){
					$all = get_um_message($curauth->ID, 'count', "msg_type='credit'");
					$pages = ceil($all/$number);
					
					$creditLog = get_um_credit_message($curauth->ID, $number,$offset);

					if($creditLog){
						foreach( $creditLog as $log ){
							$item_html .= '<li>'.$log->msg_date.' <span class="message-content" style="background:transparent;">'.$log->msg_title.'</span></li>';
						}
						if($pages>1) $item_html .= '<li class="tip">' . sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','um'),$paged, $pages, $number). '</li>';
					}
				}
				echo '<ul class="user-msg">'.$item_html.'</ul>';
				if($oneself) echo um_pager($paged, $pages);
?>
				<table class="table table-bordered credit-table">
				  <thead>
					<tr class="active">
					  <th><?php _e('积分方法','um');?></th>
					  <th><?php _e('一次得分','um');?></th>
					  <th><?php _e('可用次数','um');?></th>
					</tr>
				  </thead>
				  <tbody>
					<tr>
					  <td><?php _e('注册奖励','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('new_reg_credit','50'));?></td>
					  <td><?php _e('只有 1 次','um');?></td>
					</tr>
					<tr>
					  <td><?php _e('文章投稿','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('contribute_credit','50'));?></td>
					  <td><?php printf( __('每天 %1$s 次','um'), _hui('contribute_credit_times','5'));?></td>
					</tr>
					<tr>
					  <td><?php _e('评论回复','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('comment_credit','5'));?></td>
					  <td><?php printf( __('每天 %1$s 次','um'), _hui('comment_credit_times','50'));?></td>
					</tr>
					<tr>
					  <td><?php _e('访问推广','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('aff_visit_credit','5'));?></td>
					  <td><?php printf( __('每天 %1$s 次','um'), _hui('aff_visit_credit_times','50'));?></td>
					</tr>
					<tr>
					  <td><?php _e('注册推广','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('aff_reg_credit','50'));?></td>
					  <td><?php printf( __('每天 %1$s 次','um'), _hui('aff_reg_credit_times','5'));?></td>
					</tr>
					<tr>
					  <td><?php _e('每日签到','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('daily_sign_credit','10'));?></td>
					  <td><?php _e('每天 1 次','um');?></td>
					</tr>
					<tr>
					  <td><?php _e('文章互动','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('like_article_credit','10'));?></td>
					  <td><?php printf( __('每天 %1$s 次','um'), _hui('like_article_credit_times','5'));?></td>
					</tr>
					<tr>
					  <td><?php _e('发布资源','um');?></td>
					  <td><?php printf( __('%1$s 分','um'), _hui('source_download_credit','5'));?></td>
					  <td><?php _e('不限次数,收费资源额外返还价格100%积分','um');?></td>
					</tr>
					<tr>
					  <td><?php _e('积分兑换','um');?></td>
					  <td colspan="2"><?php printf( __('兑换比率：1 元 = %1$s 积分','um'), _hui('exchange_ratio','100'));?></td>
					</tr>
				  </tbody>
				</table>			
            </div>
        </div>
    </div>
