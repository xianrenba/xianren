<?php 
$id = $_GET['order'];
global $wpdb;


$table_name = $wpdb->prefix . 'um_orders';
$check = $wpdb->get_results( "SELECT * FROM $table_name WHERE id='$id' AND user_id='$curauth->ID'  LIMIT 1 ");
if($oneself){
	//$oLog = get_um_orders($curauth->ID, '', '', $number, $offset);
	//$oLog = get_um_orders($curauth->ID, '', '');
?>

<div class="page-wrapper">
<div class="dashboard-main me-order">
<form id="continue-pay" name="continue-pay"  method="post" style="height:0;">
	<input type = "hidden" id="product_id" name="product_id" readonly="" value="">
	<input type = "hidden" id="order_id" name="order_id" readonly="" value="0">
	<input type = "hidden" id="order_name" name="order_name" readonly="" value="0">
</form>
<?php if($check){
	foreach($check as $order_record){ ?>
<!--订单信息-->
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
                <p class="form-control-static"><?php if($order_record->product_id> 0) { ?><a style="color:#45B6F7;"href="<?php echo get_permalink($order_record->product_id); ?>" target="_blank"><?php echo $order_record->product_name; ?></a><?php }else{ echo $order_record->product_name; } ?></p>
            </div>
        </div>
		<?php if($order_record->user_address){?>
			<div class="row clearfix">
                <label class="col-md-3 control-label"><?php _e('订单地址', 'tt'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-static"><?php echo $order_record->user_address; ?></p>
                </div>
            </div>
		<?php } ?>
		<?php if($order_record->user_message){?>
		<div class="row clearfix">
            <label class="col-md-3 control-label"><?php _e('客户留言', 'tt'); ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?php echo $order_record->user_message; ?></p>
            </div>
        </div>
		<?php } ?>
        <div class="row clearfix">
            <label class="col-md-3 control-label"><?php _e('下单时间', 'tt'); ?></label>
            <div class="col-md-9"><p class="form-control-static"><?php echo $order_record->order_time; ?></p></div>
        </div>
        <?php if($order_record->order_status == 4) { ?>
        <div class="row clearfix">
            <label class="col-md-3 control-label"><?php _e('支付时间', 'tt'); ?></label>
            <div class="col-md-9"><p class="form-control-static"><?php echo $order_record->order_success_time; ?></p></div>
        </div>
        <?php } ?>
        <div class="row clearfix">
            <label class="col-md-3 control-label"><?php _e('支付状态', 'tt'); ?></label>
            <div class="col-md-9">
                <p class="form-control-static order-actions">
                    <?php echo output_order_status($order_record->order_status); ?>
                    <?php if($order_record->order_status==1) { ?>
                    (<?php echo '<a href="javascript:" data-id="'.$order_record->id.'" class="continue-pay">付款</a>'?>)
                    <?php } ?>
                </p>
            </div>
        </div>
        <div class="row clearfix">
            <label class="col-md-3 control-label"><?php _e('付款方式', 'tt'); ?></label>
            <div class="col-md-9"><p class="form-control-static"><?php if($order_record->order_currency =='credit')echo '积分支付'; else echo '现金支付';  ?></p></div>
        </div>
        <div class="row clearfix">
            <label class="col-md-3 control-label"><?php _e('付款金额', 'tt'); ?></label>
            <div class="col-md-9"><p class="form-control-static"><?php if($order_record->order_currency =='credit')echo intval($order_record->order_total_price).' 积分'; else echo $order_record->order_total_price .' 元'; ?></p></div>
        </div>
    </div>
</section>
<!--付费内容-->
<section class="pay-content clearfix">
	<div class="page-header">
        <h3 id="info"><?php _e('付费内容','um');?></h3>
    </div>
    <div class="info-group clearfix">
    	<?php if($order_record->order_price == 0 || count(get_user_order_records($order_record->product_id,0,1))>0 && $order_record->order_status == 4){ ?>
        <div class="row clearfix">
            <?php 
            $dl_links = get_post_meta($order_record->product_id,'product_download_links',true);
            $pay_content = get_post_meta($order_record->product_id,'product_pay_content',true);
			if($dl_links){  
				$arr_links = explode(PHP_EOL,$dl_links);
				?>
                <label class="col-md-3 control-label"><?php _e('下载信息', 'um'); ?></label>
                <div class="col-md-9">
                    <div class="form-control-static">
                    <?php foreach($arr_links as $arr_link){
                    	$arr_link = explode('|',$arr_link);
                    	$arr_link[0] = isset($arr_link[0]) ? $arr_link[0]:'';
                    	$arr_link[1] = isset($arr_link[1]) ? $arr_link[1]:'';
                    	$arr_link[2] = isset($arr_link[2]) ? $arr_link[2]:'';	
					?>
                    <p><?php printf(__('资源名: %s', 'um'), $arr_link[0]); ?></p>
                    <p><?php printf(__('下载链接: <a href="%1$s" title="%2$s" target="_blank"><i class="fa fa-cloud-download"></i>点击下载</a>', 'um'), _url_for('download' ,$arr_link[1]),$arr_link[0]); ?></p>
                    <p><?php printf(__('下载密码: %s', 'um'), $arr_link[2]); ?></p>
					<?php } ?> 
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row clearfix">
        <?php if($pay_content) { ?>
            <label class="col-md-3 control-label"><?php _e('付费内容', 'tt'); ?></label>
            <div class="col-md-9">
                <div class="form-control-static"><?php echo wpautop($pay_content); ?></div>
            </div>
        <?php  } ?>
        </div>
        <?php } ?>
    </div>
</section>
<?php }
}else{	
?>
<section class="my-order clearfix">
    <div class="page-header">
        <h3 id="info"><?php _e('订单信息','um');?></h3>
    </div>
    <div class="empty-content">
        <i class="fa fa-shopping-cart"></i>
        <p><?php _e('该订单不存在', 'tt'); ?></p>
    </div>
</section>
<?php } ?>
</div>
</div>
<?php
}
?>