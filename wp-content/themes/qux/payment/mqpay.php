
<?php 
	require_once(dirname(__FILE__)."/../../../../wp-config.php");
    //require_once(dirname(__FILE__)."/../../../../wp-load.php");
	if(!is_user_logged_in()){wp_die('请先登录！');}
	$zfbjk_alipay  = _hui('zfbjk_alipay');
	$zfbjk_name  = _hui('zfbjk_name');
	$zfbjk_qr  = _hui('alipay_qrcode');

	$product_id = $_POST['product_id'];
    $product_name = '';
    $product_des = '';
    if($product_id>0){$product_name = $_POST['order_name'];$product_des = get_post_field('post_excerpt',$product_id);}elseif($product_id==-1){$product_name='开通VIP月费会员';$product_des='VIP月费会员';}elseif($product_id==-2){$product_name='开通VIP季费会员';$product_des='VIP季费会员';}elseif($product_id==-3){$product_name='开通VIP年费会员';$product_des='VIP年费会员';}elseif($product_id==-4){$product_name='开通VIP终身会员';$product_des='VIP终身会员';}elseif($product_id==-5){$product_name='积分充值';$product_des=isset($_POST['creditrechargeNum'])?'充值'.$_POST['creditrechargeNum']*(100).'积分':'充值积分';}else{}
    $product_url = ($product_id>0)?get_permalink($product_id):get_bloginfo('url');
    $order_id = $_POST['order_id'];
    if(empty($product_id)||empty($order_id))wp_die('获取商品信息出错,请重试或联系卖家!');
    global $wpdb;
    $prefix = $wpdb->prefix;
    $table = $prefix.'um_orders';
    $order = $wpdb->get_row("select * from ".$table." where product_id=".$product_id." and order_id=".$order_id);
	if($order && is_user_logged_in()){

     ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>付款中心</title>
    <?php wp_head(); ?>
</head>
<div id="header">
    <div class="header-container fn-clear">
        <div class="header-title">
            <a class="alipay-logo" herf="<?php echo bloginfo('url'); ?>"></a>
            <span class="logo-title">支付中心</span>
        </div>
    </div>
</div>
<body>			
<section class="payment">
            <div class="payment-wrapper">
                <h1>订单<?php echo $order->order_id;  ?>需支付<?php echo $order->order_total_price; ?>元</h1>
                <p class="introduction">当前我们只支持扫描二维码转账付款购买, 当您转账给我们时, 请备注一些重要的信息方便后台程序或管理员自动更新订单状态并发货,</p>
                <p class="remark">你的备注信息为: <strong><?php echo $order->id; ?></strong></p>
                <div class="pay-qr-images row">
                    <div class="qrcode col-md-12 col-sm-6 col-xs-12 alipay">
                        <h4 style="color: #07b6e8;">支付宝</h4>
                        <div class="ali-qr"><img src="<?php echo $zfbjk_qr;?>" title="扫一扫二维码向我付款"></div>
                        <p>推荐, 支持自动发货,付款后等待15秒左右(需正确备注)</p>
                    </div>
                </div>
                <div class="pay-qr-images row">
                    <p class="mb20" style="color: red;">电脑用户直接向我转账, 我的收款账户为 <strong><?php echo $zfbjk_alipay; ?></strong></p>
                    <!--<img src="http://127.0.0.1/wp-content/themes/tint/assets/img/pay-tip.jpg">-->
                </div>
                <div class="actions"><a class="btn btn-success btn-wide go-order-detail" href="<?php echo add_query_arg('tab', 'orders', esc_url( get_author_posts_url( $order->user_id) )).'&order='.$order->id; ?>" target="_blank">查看订单详情</a></div>
            </div>
        </section>
		<?php
		}else{
			wp_die('获取订单出错,请重试或联系卖家!');
		}

?>
<script>
	paymonitor_timer = setInterval(function() {
		$.ajax({  
            type: 'POST',  
            url: um.ajax_url,
            dataType: 'json',
            data: {
                action: 'check_order',
                order_id: '<?php echo $order->order_id;?>',
                uid: '<?php echo $order->user_id;?>'
            }, 			
            success: function(data){  
                if( data.success == 1 ){
                    clearInterval(paymonitor_timer);
                    swal("支付成功！","","success",true,false);
                    setTimeout(function(){window.location.replace(data.redirect);}, 2500);
                }  
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                //alert(errorThrown);
            }
        });

	}, 2500);  
</script>
<style>
#header{height:65px;background-color:#fff;border-bottom:1px solid #d9d9d9;margin-top:0px}
#header .header-title{width:380px;height:60px;float:left}
#header .logo{float:left;height:31px;width:95px;margin-top:14px;text-indent:-9999px;background:none;!important}
#header .logo-title{font-size:16px;font-weight:normal;border-left:1px solid #676d70;color:#676d70;float:left;margin-top:15px;margin-left:10px;padding-top:10px;padding-left:10px}
.header-container{width:1200px;margin:0 auto}
.payment{width:1200px;background:#fff;margin:20px auto;}
.wrapper .main .result .result-wrapper{padding:50px 20px;text-align:center}
.wrapper .main .result .result-wrapper h1{font-weight:500}
.wrapper .main .result .result-wrapper p{padding:10px 0}
.payment .payment-wrapper{padding:50px 20px;text-align:center}
.payment .payment-wrapper h1{font-weight:500}
.payment .payment-wrapper p{padding:10px 0}
.payment .payment-wrapper .remark{font-size:1.4rem}
.payment .payment-wrapper .remark strong{font-size:2rem;color:#F64540}
.payment .payment-wrapper .contact-qr-images,body.qrpay .wrapper .main .payment .payment-wrapper .pay-qr-images{text-align:center;margin-bottom:20px}
.payment .payment-wrapper .actions{text-align:center;border-top:1px solid #eaeaea}
.payment .payment-wrapper .actions a{display:inline-block;margin:30px auto 20px}
 </style>
<?php get_footer();   ?>
</body>
</html>