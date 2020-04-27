<?php 
// 优惠码start	
$message = '';
if( isset($_POST['couponNonce']) && current_user_can('edit_users') ){
	if ( ! wp_verify_nonce( $_POST['couponNonce'], 'coupon-nonce' ) ) {
		$message = __('安全认证失败，请重试！','um');
	}else{
		if( isset($_POST['coupon_type']) && sanitize_text_field($_POST['coupon_type'])=='once' ){
			$p_type = 'once';
			$p_type_title = __('一次性','um');
		}else{
			$p_type = 'multi';
			$p_type_title = __('可重复使用','um');
		}
		$p_discount =  sprintf('%0.2f',intval($_POST['discount_value']*100)/100);
		$p_expire_date =  sanitize_text_field($_POST['expire_date']);
		$p_code = sanitize_text_field($_POST['coupon_code']);

		add_um_couponcode($p_code,$p_type,$p_discount,$p_expire_date);
			
		$message = sprintf(__('操作成功！已成功添加优惠码%1$s，类型：%2$s 折扣：%3$s 有效期至：%4$s。','um'), $p_code, $p_type_title, $p_discount, date('Y年m月d日 H时i分s秒',strtotime($p_expire_date)));
	}
}
	
if( isset($_POST['dcouponNonce']) && current_user_can('edit_users') ){
	if ( ! wp_verify_nonce( $_POST['dcouponNonce'], 'dcoupon-nonce' ) ) {
		$message = __('安全认证失败，请重试！','um');
	}else{
		$coupon_id = intval($_POST['coupon_id']);
		delete_um_couponcode($coupon_id);
		$message = __('操作成功！已成功删除指定优惠码','um');
	}		
}
//~ 优惠码end

// pages
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

$pcodes=output_um_couponcode(); 

get_header(); 
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
        <?php include('navmenu.php'); ?>
        <div class="pagecontent">
            <div class="page-wrapper">
                <div class="dashboard-main">
		        <div class="page-header">
	                <h3 id="info">添加优惠码</h3>
                </div>
			    <!-- Page global message -->
			    <?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
                <div class="dashboard-wrapper select-coupon">
                <div id="coupon">
					<div class="panel">
						<div class="panel-body" style="padding:0">
							<form id="couponform" role="form"  method="post">
								<input type="hidden" name="couponNonce" value="<?php echo  wp_create_nonce( 'coupon-nonce' );?>" >
								<p>
									<label class="radio-inline"><input type="radio" name="coupon_type" value="once" aria-required='true' required checked><?php _e('一次性','um');?></label>
									<label class="radio-inline"><input type="radio" name="coupon_type" value="multi" aria-required='true' required><?php _e('重复使用','um');?></label>
								</p>
								<div class="form-inline">
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?php _e('优惠码','um');?></div>
											<input class="form-control" type="text" name="coupon_code" aria-required='true' required>
										</div>
									</div>
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?php _e('折扣','um');?></div>
											<input class="form-control" type="text" name="discount_value" aria-required='true' style="width:150px;" required>
										</div>
									</div>
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?php _e('过期时间','um');?></div>
											<input class="form-control" type="datetime-local" name="expire_date" aria-required='true' required>
										</div>
									</div>
									<button class="btn btn-primary" type="submit"><?php _e('添加','um');?></button>
								</div>
								<p class="help-block"><?php _e('请谨慎操作！折扣只能填写0~1之间的数字并精确到2位小数点，有效期格式2015-01-01 10:20:30。','um');?></p>
							</form>
						</div>
					</div>
                  <?php
                  if($pcodes){
                  ?>
					<table class="table table-bordered coupon-table">
					  <input type="hidden" name="dcouponNonce" value="<?php echo  wp_create_nonce( 'dcoupon-nonce' );?>" >
					  <thead>
						<tr class="active">
						  <th><?php _e('优惠码','um');?></th>
						  <th><?php _e('类型','um');?></th>
						  <th><?php _e('折扣','um');?></th>
						  <th><?php _e('截止有效期','um');?></th>
						  <th><?php _e('操作','um');?></th>
						</tr>
					  </thead>
					  <tbody>
					  <?php
						foreach($pcodes as $pcode){
					  ?>
						<tr>
						  <input type="hidden" name="coupon_id" value="<?php echo $pcode['id']; ?>" >
							<td><?php echo $pcode['coupon_code'];?></td>
							<td><?php if($pcode['coupon_type']=='once')echo '一次性'; else echo '可重复'; ?></td>
							<td><?php echo $pcode['discount_value'];?></td>
							<td><?php echo date('Y年m月d日 H时i分s秒',strtotime($pcode['expire_date'])) ;?></td>
							<td class="delete_couponcode"><a><?php _e('删除','um');?></a></td>
						</tr>
					  <?php	}  ?>
					  </tbody>
					</table>	
                  <?php }else{ ?>                  
                    <div class="empty-content">
                        <i class="fa fa-ticket"></i>
                        <p><?php _e('这里什么也没有...', 'um'); ?></p>
                    </div>
                 <?php } ?>			
				</div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>