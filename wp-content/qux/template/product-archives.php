<?php get_header(); ?>
<!-- Main Wrap -->
<div id="main-wrap">
<div class="sub-billboard billboard shopping">
  <div class="wrapper">
    <div class="inner">
    <h1><?php echo _hui('store_archive_title','WordPress商店'); ?></h1>
    <p><?php echo _hui('store_archive_subtitle','Theme - Service - Resource'); ?></p>
    </div>
  </div>
</div>
<div class="container shop centralnav">
	<div id="guide" class="navcaret">
        <div class="group">
            <?php wp_nav_menu( array( 'theme_location' => 'shopcatbar', 'container' => '', 'menu_id' => '', 'menu_class' => 'clr', 'depth' => '1', 'fallback_cb' => ''  ) ); ?>
        </div>
	</div>
	<div id="goodslist" class="goodlist" role="main">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
          $discount_arr = product_smallest_price($post->ID); 
     ?>
        <div class="col span_1_of_4" role="main">
         <?php if($discount_arr[4]!=0){?> <span class="onsale">促销!</span><?php } ?>
			<div class="shop-item">
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark" class="fancyimg">
					<div class="thumb-img">
						<?php echo _get_post_thumbnail(375, 250, 'large'); ?>
						<span><i class="fa fa-shopping-cart"></i></span>
					</div>
				</a>
				<h3>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</h3>
				<p>
					<?php $contents = get_the_excerpt(); $excerpt = wp_trim_words($contents,50,'...'); echo $excerpt;?>
				</p>
				<div class="pricebtn"><?php $currency = get_post_meta($post->ID,'pay_currency',true); if($currency==1) echo '￥'; else echo '<i class="fa fa-gift">&nbsp;</i>'; ?><?php if ($discount_arr[0] == 0){echo'免费';}else{ if($discount_arr[4]!=0){?><strong><del><?php echo $discount_arr[0]; ?></del></strong><strong style="color:#ed5c30;"><?php echo '&nbsp;'.$discount_arr[2]; ?></strong><?php }else{ ?><strong><?php echo $discount_arr[0]; ?></strong><?php } }?><a class="buy" href="<?php the_permalink(); ?>">前往购买</a></div>
			</div>
		</div>
	<?php 
	endwhile;
	else :
		echo '<div class="empty-product"><h1>该分类还没有发布任何商品！</h1></div>';
	endif;
	?>
    </div>

<!-- pagination -->
<div class="pagination">
<?php um_pagenavi(); ?>
</div>
<!-- /.pagination -->
</div>
</div>
<!--/.Main Wrap -->
<?php get_footer(); ?>