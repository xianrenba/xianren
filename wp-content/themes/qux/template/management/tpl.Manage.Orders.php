<?php 
$paged = max( 1, get_query_var('page') );
$number = get_option('posts_per_page', 10);
$offset = ($paged-1)*$number;
$_filter_type = get_query_var('manage_grandchild_route'); 
//$orders = get_um_orders(0, '', '', $number,$offset);

$orders = get_orders($number,$offset,$_filter_type);
$count = $orders ? count($orders) : 0;
$total_count = get_count_orders($_filter_type);
$pages = ceil($total_count / $number);
get_header(); 
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
        <?php include('navmenu.php'); ?>
        <div class="pagecontent">
            <div class="page-wrapper orders-tab">
            <!-- 订单列表 -->
            <section class="mg-orders clearfix">
                <div class="page-header">
	                <h3 id="info">订单列表</h3>
                </div>
                <div class="info-group clearfix" style="margin-left: -15px; margin-right: -15px;">
                    <div class="col-md-6 orders-info">
                        <span><?php printf(__('共有 %d 条订单记录', 'tt'), $total_count); ?></span>
                    </div>
                    <div class="col-md-6 orders-filter">
                        <label>订单类型</label>
                        <select class="form-control select select-primary" data-toggle="select" onchange="document.location.href=this.options[this.selectedIndex].value;" style="display:inline;width:auto;">
                            <option value="<?php echo _url_for('manage_orders'); ?>" <?php if(strtolower($_filter_type) == 'all') echo 'selected'; ?>>全部订单</option>
                            <option value="<?php echo _url_for('manage_cash_orders'); ?>" <?php if(strtolower($_filter_type) == 'cash') echo 'selected'; ?>>现金订单</option>
                            <option value="<?php echo _url_for('manage_credit_orders'); ?>" <?php if(strtolower($_filter_type) == 'credit') echo 'selected'; ?>>积分订单</option>
                            <option value="<?php echo _url_for('manage_completed_orders'); ?>" <?php if(strtolower($_filter_type) == 'completed') echo 'selected'; ?>>完成订单</option>
                        </select>
                    </div>
                </div>
                <?php if($count > 0) { ?>
                    <div class="table-wrapper site-orders" >
                        <table class="table table-striped table-framed table-centered orders-table">
                            <thead>
                            <tr>
                                <th class="th-oid">订单号</th>
                                <th class="th-title">商品名称</th>
                                <th class="th-buyer">买家名</th>
                                <th class="th-time" style="min-width: 100px;">下单时间</th>
                                <th class="th-sumprice" style="min-width: 80px;">订单总价</th>
                                <th class="th-status" style="min-width: 80px;">订单状态</th>
                                <th class="th-actions" style="min-width: 100px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $order){ ?>
                                <tr id="oid-<?php echo $order->order_id; ?>">
                                    <td><?php echo $order->order_id; ?></td>
                                    <td><?php echo $order->product_name; ?></td>
                                    <td><a href="<?php echo _url_for('manage_user', $order->user_id); ?>" target="_blank"><?php echo $order->user_name; ?></a></td>
                                    <td><?php echo $order->order_time; ?></td>
                                    <td><?php if($order->order_currency == 'credit'){ echo '<i class="fa fa-gift"></i> ' . intval($order->order_total_price); }else{ echo '￥' . sprintf('%0.2f', $order->order_total_price); } ?></td>
                                    <td><?php if($order->order_status){echo output_order_status($order->order_status);} ?></td>
                                    <td>
                                        <div class="order-actions">
                                            <a class="view-detail" href="<?php echo _url_for('manage_order', $order->id); ?>" title="查看订单详情" target="_blank">查看</a>
                                            <?php if(!in_array($order->order_status,array(2,3,4))) {echo ' | <a  class="delete-order" href="javascript:;" data-id="'.$order->order_id.'">删除</a>'; } ?>
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
                        <i class="fa fa-truck"></i>
                        <p>这里什么都没有...</p>
                    </div>
                <?php } ?>
            </section>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>