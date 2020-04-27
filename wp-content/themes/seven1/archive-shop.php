<?php
/*
* 此页面对应的js 文件是 js/main.js 中的  mainHome
*/
get_header();
$user_id = get_current_user_id();
$user_credit = (int)zrz_coin($user_id,'nub');
$login = is_user_logged_in();
$img = get_option('zrz_shop_img',array('normal'=>'','lottery'=>'','exchange'=>''));
$shop_style = zrz_get_display_settings('shop_style');
$open_new = zrz_open_new();
?>

	<div id="primary-home" class="content-area fd">
		<main id="main" class="site-main <?php echo $shop_style ? 'shop-card' : 'shop-list'; ?>" ref="shoplist">
			<?php if(zrz_get_display_settings('shop_show')){ ?>
				<?php if(zrz_get_display_settings('shop_show_g')) { ?>
					<div class="box mar16-b">
						<div class="shop-title pd10 pos-r" :style="'background-image:url('+(normalImg ? normalImg : '<?php echo zrz_get_thumb($img['normal'],900,180,'',true); ?>')+')'">
							<div class="shop-title-text shadow"><i class="iconfont zrz-icon-font-2"></i>  <?php _e('出售','ziranzhi2'); ?></div>
							<label class="button empty pos-a" v-if="isAdmin">
								<?php _e('上传封面图片','ziranzhi2'); ?><b class="" ref="normalLoding"></b>
								<input id="shop-input" type="file" accept="image/jpg,image/jpeg,image/png" class="hide" ref="input" @change="getFile($event,'normal')">
							</label>
						</div>
						<?php
							$args = array(
								'post_type' => 'shop',
								'orderby'  => 'date',
								'order'=>'DESC',
								'meta_key' => 'zrz_shop_type',
								'meta_value' => 'normal',
								'posts_per_page'=>6
							);
							$the_query = new WP_Query( $args );
						?>
						<div class="pd10 shop-title-count box-header b-b fs12 clearfix">共有<?php echo $the_query->found_posts; ?>个出售商品 <a class="fr" href="<?php echo home_url('/shop/buy'); ?>">全部 ❯</a></div>
						<div class="shop-normal">
							<?php
								if ( $the_query->have_posts()) {
									echo '<ul class="grid-bor">';
									while ( $the_query->have_posts() ) {
										$the_query->the_post();
										$post_id = get_the_id();
										$price = zrz_get_shop_price_dom($post_id);
										$remaining = zrz_shop_count_remaining($post_id);
										$remaining = $remaining > 0 ? true : false;
										$views = get_post_meta($post_id,'views',true);
										$views = $views ? $views : 0;

										//特色图
										$thumb = zrz_get_post_thumb();
										//数量
										echo '<li class="fd pd10 shop-list-li shop-g">
											<div class="pos-r">
											'.$price['msg'].'
												<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'">
													<div class="thumb">
													<div class="preview thumb-in" style="background-image:url('.zrz_get_thumb($thumb,363,363).')"></div>
													</div>
												</a>

											<div class="shop-list-content">
												<h2><a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'">' . get_the_title() . '</a></h2>
												<div class="shop-buy clearfix mar10-t mar10-b pd10-b pd10-t">
													<div class="fl">
														'.$price['dom'].'
													</div>
													<div class="fr">
														'.($remaining ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty" style="margin:0">购买</a>' : '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty disabled" style="margin:0">售罄</a>').'
													</div>
												</div>
												<div class="shop-normal-meta fs12 gray clearfix">
													<div class="fl">
														<span>总数：<b>'.zrz_get_shop_count($post_id,'total').'</b> <span class="dot"></span>已售：<b>'.zrz_get_shop_count($post_id,'sell').'</b></span>
													</div>
													<div class="fr mobile-hide">
													人气：<b>'.$views.'</b>
													</div>
												</div>
											</div>
											</div>
										</li>';
									}
									echo '</ul>';
									wp_reset_postdata();
								} else {
									echo '<div class="pd20 t-c box">没有商品！</div>';
								}

							?>
						</div>
					</div>
				<?php } ?>
				<?php if(zrz_get_display_settings('shop_show_c')) { ?>
					<div class="box mar16-b">
						<div class="shop-title pd10 pos-r" :style="'background-image:url('+(lotteryImg ? lotteryImg : '<?php echo zrz_get_thumb($img['lottery'],900,180,'',true); ?>')+')'">
							<div class="shop-title-text shadow"><i class="iconfont zrz-icon-font-liwu"></i>  <?php _e('积分抽奖','ziranzhi2'); ?></div>
							<label class="button empty pos-a" v-if="isAdmin">
								<?php _e('上传封面图片','ziranzhi2'); ?><b class="" ref="lotteryLoding"></b>
								<input id="shop-input" type="file" accept="image/jpg,image/jpeg,image/png" class="hide" ref="input" @change="getFile($event,'lottery')">
							</label>
						</div>
						<?php
							$args = array(
								'post_type' => 'shop',
								'orderby'  => 'date',
								'order'=>'DESC',
								'meta_key' => 'zrz_shop_type',
								'meta_value' => 'lottery',
								'posts_per_page'=>6
							);
							$the_query = new WP_Query( $args );
						?>
						<div class="pd10 shop-title-count b-b fs12 box-header clearfix">共有<?php echo $the_query->found_posts; ?>个抽奖商品 <a class="fr" href="<?php echo home_url('/shop/lottery'); ?>">全部 ❯</a></div>
						<div class="shop-normal">
							<?php
								if ( $the_query->have_posts() ) {
									echo '<ul class="grid-bor">';
									while ( $the_query->have_posts() ) {
										$the_query->the_post();
										$post_id = get_the_id();
										//特色图
										$thumb = zrz_get_post_thumb();
										//参与所需积分
										$credit = (int)zrz_get_shop_lottery($post_id,'credit');
										$remaining = zrz_shop_count_remaining($post_id);
										$remaining = $remaining > 0 ? true : false;
										$views = get_post_meta($post_id,'views',true);
										$views = $views ? $views : 0;

										//允许抽奖的用户组
										$capabilities = zrz_get_shop_lottery($post_id,'capabilities');

										//当前用户的用户组
										$user_lv = zrz_get_lv($user_id,'');
										echo '<li class="fd pd10 shop-list-li">
											<div class="pos-r">
												<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'">
													<div class="thumb">
													<div class="preview thumb-in" style="background-image:url('.zrz_get_thumb($thumb,363,363).')"></div>
													</div>
												</a>

												<div class="shop-list-content">
													<h2><a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'">' . get_the_title() . '</a></h2>
													<div class="shop-buy shop-center t-c clearfix mar10-t mar10-b pd10-b pd10-t">
														'.zrz_coin(0,0,$credit).'
														<div class="fr">'.((!in_array($user_lv,$capabilities) && $login) ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'"
														class="button empty disabled" style="margin:0">权限不足</a>' : ($remaining ? (($user_credit < $credit && $login) ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty disabled" style="margin:0">积分不足</a>' : '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty" style="margin:0">抽奖</a>') : '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty disabled" style="margin:0">抽奖结束</a>')).'</div>
													</div>
													<div class="shop-normal-meta fs12 gray clearfix">
														<div class="fl">
															<span>总数：<b>'.zrz_get_shop_count($post_id,'total').'</b><span class="dot"></span> 已被领走：<b>'.zrz_get_shop_count($post_id,'sell').'</b></span>
														</div>
														<div class="fr mobile-hide">
														人气：<b>'.$views.'</b>
														</div>
													</div>
												</div>
											</div>
										</li>';
									}
									echo '</ul>';
									wp_reset_postdata();
								} else {
									echo '<div class="pd20 t-c box">没有商品！</div>';
								}

							?>
						</div>
					</div>
				<?php } ?>
				<?php if(zrz_get_display_settings('shop_show_d')) { ?>
	            <div class="box">
					<div class="shop-title pd10 pos-r" :style="'background-image:url('+(exchangeImg ? exchangeImg : '<?php echo zrz_get_thumb($img['exchange'],900,180,'',true); ?>')+')'">
						<div class="shop-title-text shadow"><i class="iconfont zrz-icon-font-duihuan"></i>  <?php _e('积分兑换','ziranzhi2'); ?></div>
						<label class="button empty pos-a" v-if="isAdmin">
							<?php _e('上传封面图片','ziranzhi2'); ?><b class="" ref="exchangeLoding"></b>
							<input id="shop-input" type="file" accept="image/jpg,image/jpeg,image/png" class="hide" ref="input" @change="getFile($event,'exchange')">
						</label>
					</div>
					<?php
						$args = array(
							'post_type' => 'shop',
							'orderby'  => 'date',
							'order'=>'DESC',
							'meta_key' => 'zrz_shop_type',
							'meta_value' => 'exchange',
							'posts_per_page'=>6
						);
						$the_query = new WP_Query( $args );
					?>
					<div class="pd10 shop-title-count b-b fs12 box-header clearfix">共有<?php echo $the_query->found_posts; ?>个兑换商品 <a class="fr" href="<?php echo home_url('/shop/exchange'); ?>">全部 ❯</a></div>
	                <div class="shop-normal">
	                    <?php
	                        // The Loop
	                        if ( $the_query->have_posts() ) {
	                            echo '<ul class="grid-bor">';
	                            while ( $the_query->have_posts() ) {
	                                $the_query->the_post();
	                                $post_id = get_the_id();
	                                //特色图
	                                $thumb = zrz_get_post_thumb();
	                                //所需积分
	                                $credit = get_post_meta($post_id,'zrz_shop_need_credit',true);

									//剩余数量
									$remaining = zrz_shop_count_remaining($post_id);
									$remaining = $remaining > 0 ? true : false;
									$views = get_post_meta($post_id,'views',true);
									$views = $views ? $views : 0;
	                                echo '<li class="fd pd10 shop-list-li">
											<div class="pos-r">
												<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'">
													<div class="thumb">
		                                        	<div class="preview thumb-in" style="background-image:url('.zrz_get_thumb($thumb,363,363).')"></div>
													</div>
												</a>

												<div class="shop-list-content">
		                                            <h2><a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'">' . get_the_title() . '</a></h2>
		                                            <div class="shop-buy shop-center t-c clearfix mar10-t mar10-b pd10-b pd10-t">
		                                                '.zrz_coin(0,0,$credit).'<div class="fr">'.($remaining ?
															(($user_credit < $credit && $login) ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty disabled" style="margin:0">积分不足</a>' : '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty" style="margin:0">兑换</a>')
															 : '<a '.($open_new ? 'target="_blank"' : '').' href="'.get_permalink().'" class="button empty disabled" style="margin:0">兑换结束</a>').'</div>
		                                            </div>
		                                            <div class="shop-normal-meta fs12 gray clearfix">
		                                                <div class="fl">
		                                                    <span>总数：<b>'.zrz_get_shop_count($post_id,'total').'</b> <span class="dot"></span>已兑：<b>'.zrz_get_shop_count($post_id,'sell').'</b></span>
		                                                </div>
		                                                <div class="fr mobile-hide">
		                                                人气：<b>'.$views.'</b>
		                                                </div>
		                                            </div>
		                                        </div>
											</div>
	                                </li>';
	                            }
	                            echo '</ul>';
	                            wp_reset_postdata();
	                        } else {
								echo '<div class="pd20 t-c box">没有商品！</div>';
	                        }
	                    ?>
	                </div>
	            </div>
				<?php } ?>
			<?php }else{
				get_template_part('template-parts/content','none');
			} ?>
		</main><!-- #main -->
	</div><?php
get_sidebar();
get_footer();
