<?php
// pages
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;

// Item html
$item_html = '<li class="tip">'.__('没有找到记录','um').'</li>';
?>

    <div class="page-wrapper">
        <div class="dashboard-main">
    <?php if(current_user_can('edit_users')){ 
    if(isset($_GET['order'])){
		$id = $_GET['order'];
        global $wpdb;

        $table_name = $wpdb->prefix . 'um_orders';
        $check = $wpdb->get_results( "SELECT * FROM $table_name WHERE id='$id' LIMIT 1 ");
		
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
                        <div class="col-md-9">
                            <p class="form-control-static order-actions">
                                <?php echo output_order_status($order_record->order_status); ?>
                            </p>
                        </div>
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
                        <div class="col-md-9">
                            <p class="form-control-static"><?php echo $order_record->user_message; ?></p>
                        </div>
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
                            <a class="btn btn-success btn-wide order-status-act completed-order" href="javascript:" title="完成该订单" data="<?php echo $order_record->id; ?>"><?php _e('完成订单', 'tt'); ?></a>
                            <?php } ?>
                            <?php if($order_record->order_status==1) { ?>
                            <a class="btn btn-danger btn-wide order-status-act close-order" href="javascript:" title="关闭过期交易" data="<?php echo $order_record->id;  ?>"><?php _e('关闭订单', 'tt'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
             </section><?php
			}
		}else{	?>
            <section class="my-order clearfix">
              <div class="page-header">
                  <h3 id="info"><?php _e('订单信息','um');?></h3>
              </div>
              <div class="empty-content">
                  <i class="fa fa-shopping-cart"></i>
                  <p><?php _e('该订单不存在', 'tt'); ?></p>
             </div>
            </section><?php
		    } 	
	}else{
		?>
			<div class="page-header">
	           <h3 id="info">站点订单<small>  提示：管理员管理站内所有订单，仅管理员可见</small></h3>
            </div>
			<!-- Page global message -->
			<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
            <div class="dashboard-wrapper select-siteorder">
                <div id="siteorder"><?php

					$oall = get_um_orders(0, 'count');
					$pages = ceil($oall/$number);
					$oLog = get_um_orders(0, '', '', $number,$offset);
					if($oLog){
						$item_html = '<li class="contextual" style="background:#f2dede;color:#a94442;">' . sprintf(__('全站共有 %1$s 条订单记录（该栏目仅管理员可见）。','um'), $oall) . '</li>';
						$item_html .= '<div class="site-orders">
							<table width="100%" border="0" cellspacing="0" class="table table-bordered orders-table">
								<thead>
									<tr>
										<th scope="col">'.__('订单号','um').'</th>
                                        <th scope="col">'.__('商品名','um').'</th>
										<th scope="col">'.__('买家','um').'</th>
										<th scope="col" style="min-width: 100px;">'.__('购买时间','um').'</th>
										<th scope="col" style="min-width: 80px;">'.__('总价','um').'</th>
										<th scope="col" style="min-width: 80px;">'.__('交易状态','um').'</th>
										<th scope="col" style="min-width:100px;">'.__('操作','um').'</th>
									</tr>
								</thead>
							<tbody class="the-list">';
								foreach($oLog as $Log){
									$item_html .= '
									<tr>
                                        <td>'.$Log->order_id.'</td>
										<td>'.$Log->product_name.'</td>
										<td>'.$Log->user_name.'</td>
										<td>'.$Log->order_time.'</td>
									<td>';
                                    if($Log->order_currency =='credit'){ $item_html .= '<i class="fa fa-gift"></i> '.$Log->order_total_price;}else{ $item_html .= '￥'.$Log->order_total_price;}; 
                                    $item_html .= '</td><td>';
									if($Log->order_status){$item_html .= output_order_status($Log->order_status);}
									$item_html .= '</td><td>';
                                    $item_html .= '<a href="'.add_query_arg('tab', 'siteorders', esc_url( get_author_posts_url( $curauth->ID ) )).'&order='.$Log->id.'">查看</a>';
									if($Log->order_status==1)$item_html .= ' | <a class="close-order" href="javascript:" title="关闭过期交易" data="'.$Log->id.'">关闭</a>';
									$item_html .= '</td></tr>';
								}
									$item_html .= '</tbody>
								</table>
							</div>';
						if($pages>1) $item_html .= '<li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','um'),$paged, $pages, $number).'</li>'; ?>
				<ul class="site-order-list">
				<?php echo $item_html; ?>
				</ul>
      <?php         }else{echo '<p>没有发现任何订单记录</p>'; }	?>
      <?php echo um_pager($paged, $pages); ?>
				</div>
            </div>
<?php   } 
    } 
?>	
        </div>
    </div>