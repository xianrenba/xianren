<?php 
get_header(); 
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
        <?php include('navmenu.php'); ?>
        <div class="pagecontent">
            <div class="page-wrapper">
            <?php 
                $order_seq = get_query_var('manage_grandchild_route');
                // $id = $_GET['order'];
                global $wpdb;

                $table_name = $wpdb->prefix . 'um_orders';
                $check = $wpdb->get_results( "SELECT * FROM $table_name WHERE id='$order_seq' LIMIT 1 ");		
	            if($check){
	                foreach($check as $order_record){ ?>
                    <section class="my-order clearfix">
				    <div class="page-header">
                        <h3 id="info"><?php _e('订单信息','um');?></h3>
                    </div>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('订单号', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order_record->order_id; ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('商品名', 'tt'); ?></label>
                            <div class="col-md-9">
                                <p class="form-control-static"><?php if($order_record->product_id> 0) { ?><a href="<?php echo get_permalink($order_record->product_id); ?>" target="_blank"><?php echo $order_record->product_name; ?></a><?php }else{ echo $order_record->product_name; } ?></p>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('下单时间', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order_record->order_time; ?></p></div>
                        </div>
                        <?php if($order_record->order_status == 9 || $order_record->order_status == 4) { ?>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('支付时间', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order_record->order_success_time; ?></p></div>
                        </div>
                        <?php } ?>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('支付状态', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static order-actions"><?php echo output_order_status($order_record->order_status); ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('付款方式', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php if($order_record->order_currency =='credit')echo '积分支付'; else echo '现金支付';  ?></p></div>
                        </div>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('付款金额', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php if($order_record->order_currency =='credit')echo $order_record->order_price.' 积分'; else echo $order_record->order_price .' 元'; ?></p></div>
                        </div>
					    <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('买家信息', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo get_user_meta($order_record->user_id, 'nickname', true) . ' <strong>(ID: ' . $order_record->user_id . ')</strong>'; ?></p></div>
                        </div>
					    <?php if($order_record->user_message){?>
					    <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('买家留言', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php echo $order_record->user_message; ?></p></div>
                        </div>
					    <?php } ?>
					    <?php if($order_record->user_address) { ?>
                        <div class="row clearfix">
                            <label class="col-md-3 control-label"><?php _e('收货信息', 'tt'); ?></label>
                            <div class="col-md-9"><p class="form-control-static"><?php printf('%1$s<br>%2$s<br>%3$s %4$s %5$s', $order_record->user_name, $order_record->user_email, $order_record->user_address, $order_record->user_zip, $order_record->user_cellphone); ?></p></div>
                        </div>
                        <?php } ?>
                    </div>
		            </section>
			        <!-- 状态管理 -->
                    <section class="mg-status clearfix">
                    <div class="page-header">
                        <h3 id="info"><?php _e('状态管理','um');?></h3>
                    </div>
                    <div class="info-group clearfix">
                        <div class="row clearfix">
                            <?php if($order_record->order_status==1) { ?>
                            <a class="btn btn-success btn-wide order-status-act completed-order" href="javascript:" title="完成该订单" data="<?php echo $order_record->id; ?>"><?php _e('完成订单', 'um'); ?></a>
                            <?php } ?>
                            <?php if($order_record->order_status==1) { ?>
                            <a class="btn btn-danger btn-wide order-status-act close-order" href="javascript:" title="关闭过期交易" data="<?php echo $order_record->id;  ?>"><?php _e('关闭订单', 'um'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    </section>
					<?php }
		        }else { ?>
                <section class="my-order clearfix">
				    <div class="page-header">
                        <h3 id="info"><?php _e('订单信息','um');?></h3>
                    </div>
                    <div class="empty-content">
                    	<i class="fa fa-shopping-cart"></i>
                    	<p><?php _e('没找到该订单', 'um'); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>