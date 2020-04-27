<?php

// pages
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

?>

    <div class="page-wrapper">
        <div class="dashboard-main">
			<div class="page-header">
                <h3 id="info"><?php _e('站内订单','um');?> <small>提示：如对订单有任何的疑问，请立即联系我们。</small></h3>
            </div>
            <div class="dashboard-wrapper select-orders">
                <div id="orders">
<?php if($oneself){
		$oall = get_um_orders($curauth->ID, 'count');
		$pages = ceil($oall/$number);
		$oLog = get_um_orders($curauth->ID, '', '', $number,$offset);
		//$order_records = get_user_order_records(0,$curauth->ID);
?>
					<ul class="site-order-list">
					<div class="shop">
						<div id="history" class="wrapbox">
							<form id="continue-pay" name="continue-pay" method="post" style="height:0;">
								<input type = "hidden" id="product_id" name="product_id" readonly="" value="">
								<input type = "hidden" id="order_id" name="order_id" readonly="" value="0">
								<input type = "hidden" id="order_name" name="order_name" readonly="" value="0">
							</form>
							<li class="contextual" style="background:#ceface;color:#44a042;"><?php echo sprintf(__('与 %1$s 相关订单记录（该栏目仅自己和管理员可见）。','um'), $curauth->display_name); ?></li>
							<div class="pay-history">
								<table width="100%" border="0" cellspacing="0" class="table table-bordered orders-table">
									<thead>
										<tr>
											<th scope="col"><?php _e('订单号','um'); ?></th>
 											<th scope="col"><?php _e('商品名','um'); ?></th>                                         
											<th scope="col" style="min-width: 100px;"><?php _e('购买时间','um'); ?></th>
											<!--<th scope="col" style="min-width: 50px;"><?php _e('数量','um'); ?></th>-->
											<th scope="col" style="min-width: 80px;"><?php _e('总价','um'); ?></th>
											<th scope="col" style="min-width: 80px;"><?php _e('交易状态','um'); ?></th>
											<th scope="col" style="min-width: 100px;"><?php _e('操作','um'); ?></th>
										</tr>
									</thead>
									<tbody class="the-list">
									<?php if($oLog)foreach($oLog as $order_record){ ?>
										<tr>
											<td><?php echo '<a href="'.add_query_arg('tab', 'orders', esc_url( get_author_posts_url( $curauth->ID ) )).'&order='.$order_record->id.'">'.$order_record->order_id.'</a>'?></td>
											<td><?php if($order_record->product_id>0){echo '<a style="color:#45B6F7;" href="'.get_permalink($order_record->product_id).'" target="_blank" title="'.$order_record->product_name.'">'.wp_trim_words($order_record->product_name, 25, '...').'</a>';}else{echo $order_record->product_name;} ?></td>
											<td><?php echo $order_record->order_time; ?></td>
											<!--<td><?php echo $order_record->order_quantity; ?></td>-->
											<td><?php if($order_record->order_currency =='credit'){ echo '<i class="fa fa-gift"></i> '.intval($order_record->order_total_price);}else{ echo '￥'.$order_record->order_total_price;}; ?></td>
											<td><?php echo output_order_status($order_record->order_status); ?></td>
											<td><?php echo '<a href="'.add_query_arg('tab', 'orders', esc_url( get_author_posts_url( $curauth->ID ) )).'&order='.$order_record->id.'">查看</a>';if($order_record->order_status==1){echo ' | <a href="javascript:" data-id="'.$order_record->id.'" class="continue-pay">付款</a>';}?><?php if(!in_array($order_record->order_status,array(2,3,4))) {echo ' | <a  class="delete-order" href="javascript:;" data-id="'.$order_record->order_id.'">删除</a>'; } ?></td>
											</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
						</div>	
					</div>
					</ul>
<?php echo um_pager($paged, $pages); ?>
<?php	}	?>				
				</div>
            </div>
        </div>
    </div>