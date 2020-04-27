<?php get_header();  ?>
<!-- Main Wrap -->
<div id="main-wrap">
	<div id="single-blog-wrap" class="container shop">
        <div class="breadcrumb">
        	<?php 
        	$cat_terms = get_the_terms($post, 'products_category'); 
        	if($cat_terms){
        		foreach ($cat_terms as $cat_term){
        			$cat_link = get_term_link($cat_term, 'products_category');
        			$cat_name = $cat_term->name;
        			$cat_parent = $cat_term->parent;
        		}
        	}
        	?>
			<a href="<?php echo get_bloginfo('url'); ?>"><?php _e('首页 ','um'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;<a href="<?php echo get_bloginfo('url').'/'._hui('store_archive_slug','store'); ?>"><?php _e(' 商店 ','um'); ?></a><?php if($cat_terms) echo '&nbsp;<i class="fa fa-angle-right"></i>&nbsp;<a href='.$cat_link.'>'.$cat_name.'</a>';//get_the_term_list($post,'products_category',' ','|'); ?>
		</div>
		<div class="area">
		<!-- Content -->
		<div class="product-content">
			<?php while ( have_posts() ) : the_post(); ?>
			<article id="<?php echo 'product-'.$post->ID; ?>" class="product">
             <?php $discount_arr = product_smallest_price($post->ID); if($discount_arr[4]!=0){?> <span class="onsale">促销!</span><?php } ?>
				<div class="preview">
					<div class="product-thumb"><?php echo _get_post_thumbnail(375, 300, 'large'); ?></div>
					<div class="view-share">
						<p class="view"><?php _e('人气：','um'); ?><?php echo (int)get_post_meta($post->ID,'um_post_views',true); ?></p>
						<div class="bdshare">
							<div id="bdshare" class="shares"><span><?php _e('分享：','um'); ?></span>
								<a etap="share" data-share="weibo" class="bds_tsina share-tsina" title="<?php _e('分享到新浪微博','um'); ?>"></a>
								<a etap="share" data-share="qq" class="bds_sqq share-qq"  title="<?php _e('分享到QQ好友','um'); ?>"></a>
								<a etap="share" data-share="qzone" class="bds_qzone share-qzone"  title="<?php _e('分享到QQ空间','um'); ?>"></a>
								<a href="javascript:;" data-url="<?php echo get_the_permalink(); ?>" class="bds_weixin share-weixin" title="<?php _e('分享到微信','um'); ?>"></a>
							</div>
						</div>
					</div>
				</div>
				<div class="property">
					<div class="title row">
						<h1><?php the_title(); ?></h1>
						<p><?php $contents = get_the_excerpt(); $excerpt = wp_trim_words($contents,60,'...'); echo $excerpt;?></p>
					</div>
					<div class="summary row">
						<ul>
							<?php 
							$currency = get_post_meta($post->ID,'pay_currency',true); if($currency==1){$ico = '<em><i class="fa fa-jpy"></i></em>'; $type = '<em>(元)</em>';}else{$ico = '<em><i class="fa fa-gift"></i></em>';$type = '<em>(积分)</em>';} ?>
							<?php if($discount_arr[3]==0  && $discount_arr[4]==0  ){?>
							<li class="summary-price"><span class="dt"><?php _e('商品售价','um'); ?></span><strong><?php if($discount_arr[0]==0){echo '免费';}else{ echo $discount_arr[0].$type; }?></strong></li>
							<?php }else{ ?>
							<li class="summary-price"><span class="dt"><?php _e('商品售价','um'); ?></span><strong><?php if($discount_arr[0]==0){echo '免费';}else{ if($discount_arr[4]!=0 || getUserMemberType()){echo $ico .'<del>'.$discount_arr[0].'</del>'. $type; }else{echo $ico .$discount_arr[0].$type;}}?></strong><?php if($discount_arr[4]!=0){?><strong><?php echo '&nbsp;'.$discount_arr[2]; ?></strong><span><?php _e('(限时特惠)','um'); ?></span><?php }?></li>
                            <?php if($discount_arr[3]!=0){ ?>
                            <li class="summary-vip-price"><span class="dt"><?php _e('会员特惠','um'); ?></span><?php if(getUserMemberType()) {echo '<strong>';if($discount_arr[1] ==0 ){echo '免费';}else{ echo $ico.$discount_arr[1].$type; } echo '</strong>';}else{ echo get_product_vip_price($ico,$discount_arr[0],$post->ID); } ?><?php }?>
                            <?php }?>
							<li class="summary-amount"><span class="dt"><?php _e('商品数量','um'); ?></span><span class="dt-num"><?php $amount = get_post_meta($post->ID,'product_amount',true) ? (int)get_post_meta($post->ID,'product_amount',true):0; echo $amount; ?></span></li>
							<li class="summary-sales"><span class="dt"><?php _e('商品销量','um'); ?></span><span class="dt-num"><?php $sales = get_post_meta($post->ID,'product_sales',true) ? (int)get_post_meta($post->ID,'product_sales',true):0; echo $sales; ?></span></li>
							<li class="summary-market"><span class="dt"><?php _e('商品编号','um'); ?></span><?php echo $post->ID; ?></li>
                        </ul>
					</div>
					<div class="buygroup row">
						<div class="amount-number">
							<span class="num"><?php _e('数量：','um'); ?></span>
							<input type="text" name="amountquantity" class="amount-input" value="1" maxlength="5" title="<?php _e('请输入购买量','um'); ?>">
							<div class="number-handler">
								<a href="javascript:" hidefocus="true" field="amountquantity" id="plus" class="control plus"><i class="fa fa-angle-up"></i></a>
								<a href="javascript:" hidefocus="true" field="amountquantity" id="minus" class="control minus"><i class="fa fa-angle-down"></i></a>
							</div>
						</div>
						<?php if($amount<=0){ ?>
						<a class="buy-btn sold-out"><i class="fa fa-shopping-cart"></i><?php _e('已售完','um'); ?></a>
						<?php }else{ ?>
							<?php if(is_user_logged_in()&&$amount>0){ ?>
							<a class="buy-btn" data-top="true" data-pop="order"><i class="fa fa-shopping-cart"></i><?php _e('立即购买','um'); ?></a>
							<?php }elseif($amount>0&&!is_user_logged_in()){ ?>
							<a data-sign="0" class="user-signin buy-btn user-login"><i class="fa fa-shopping-cart"></i><?php _e('登录购买','um'); ?></a>
							<?php }else{ ?>
							<a class="buy-btn free-buy" data-top="false"><i class="fa fa-shopping-cart"></i><?php _e('立即购买','um'); ?></a>
							<?php } ?>			
                        <?php } ?>
                    </div>
					<div class="tips row">
						<p><?php _e('注意：本站为本商品唯一销售点，请勿在其他途径购买，以免遭受安全损失。','um'); ?></p>
					</div>
				</div>
				<div class="main-content">
					<div class="shop-content">
						<div class="mainwrap">
							<div id="wrapnav">
								<?php $order_records = get_user_order_records($post->ID); $order_num = count($order_records); ?>
								<ul class="nav">
									<div class="intro"></div>
									<li class="active"><a href="#description" rel="nofollow" hidefocus="true"><?php _e('商品详情','um'); ?></a></li>
									<li><a href="#reviews" rel="nofollow" hidefocus="true"><?php _e('商品评价','um'); ?><em><?php $count_comments = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->comments  WHERE comment_approved='1' AND comment_post_ID = %d AND comment_type not in ('trackback','pingback')", $post->ID ) ); echo $count_comments; ?></em></a></li>
                                    <li class="nav-history"><a href="#history" rel="nofollow" hidefocus="true"><i class="fa fa-history"></i><?php _e('我的购买记录','um'); ?><em><?php echo $order_num; ?></em></a></li>
                                    <a class="fixed-buy-btn buy-btn" data-top="true" data-pop="order"><i class="fa fa-shopping-cart"></i><?php _e('立即购买','um'); ?></a>
                                </ul>
							</div>
							<div id="wrapnav-container">
								<div id="description" class="wrapbox single-content single-text">
								<?php echo store_pay_content_show(get_the_content()); ?>
								</div>
								<div id="reviews" class="wrapbox">
								<?php if (comments_open()) comments_template( '', true ); ?>
								</div>
								<div id="history" class="wrapbox">
									<?php if(!is_user_logged_in()){ ?>
									<p class="history-tip"><?php _e('我的购买记录，登陆后可见，','um'); ?><a class="user-signin user-login" href="#" title="<?php _e('点击登录','um'); ?>"><?php _e('立即登录','um'); ?></a>。</p>
									<?php }else{ ?>
                         	    	<div class="pay-history">
										<div class="greytip"><?php _e('Tips：若商品可循环使用则无须多次购买','um'); ?></div>
										<table width="100%" border="0" cellspacing="0">
										<thead>
											<tr>
												<th scope="col"><?php _e('订单号','um'); ?></th>
												<th scope="col"><?php _e('购买时间','um'); ?></th>
												<th scope="col"><?php _e('数量','um'); ?></th>
												<th scope="col"><?php _e('价格','um'); ?></th>
												<th scope="col"><?php _e('金额','um'); ?></th>
												<th scope="col"><?php _e('交易状态','um'); ?></th>
											</tr>
										</thead>
										<tbody class="the-list">
											<?php foreach($order_records as $order_record){ ?>
                                            <tr>
												<td><?php echo $order_record['order_id']; ?></td>
												<td><?php echo $order_record['order_time']; ?></td>
												<td><?php echo $order_record['order_quantity']; ?></td>
												<td><?php echo $order_record['order_price']; ?></td>
												<td><?php echo $order_record['order_total_price']; ?></td>
												<td><?php if($order_record['order_status']){echo output_order_status($order_record['order_status']);}; ?></td>
											</tr>
											<?php } ?>
                                        </tbody>
										</table>
									</div>
									<?php } ?>
                            	</div>
                              <?php _moloader('mo_shop_relates');?>
                            </div>
						</div>
					</div>
                  <?php _moloader('mo_shop_sidebar'); ?>
				</div>                        
			</article>
          <?php endwhile; ?>
		</div>
		<!-- /.Content -->
		</div>
	</div>
</div>
<!--/.Main Wrap -->
<?php get_footer(); ?>