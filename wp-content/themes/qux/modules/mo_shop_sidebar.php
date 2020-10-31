<?php 
function mo_shop_sidebar($p_name = '商品', $p_tag = '商品标签', $showpsot = 5){
    global $post;
    $args = array('post_type' => 'store','post_status' => 'publish', 'showposts'=>$showpsot, 'has_password' => false, 'ignore_sticky_posts' => true, 'orderby' => 'rand', 'order' => 'DESC');
    $rand_query = new WP_Query($args);
?>
<div class="shop-sidebar">
    <div class="shop-widget widget_products">
		<h3><i class="fa fa-gavel"></i><?php echo $p_name; ?></h3>
		<ul>
        <?php 
		if( $rand_query->have_posts() ){
			while ($rand_query->have_posts()) : $rand_query->the_post();
			if ( has_post_thumbnail() ){$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');$imgsrc = $large_image_url[0];}else{$imgsrc = um_catch_first_image();}
			$currency = get_post_meta( $post->ID, 'pay_currency', true) ? 'cash' : 'credit';
			$price = get_post_meta($post->ID, 'product_price', true)
			?>
			<li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><div class="thumb-img"><div class="product-thumb"><?php echo _get_post_thumbnail(60,60); ?></div><div class="product-title"><?php the_title(); ?></div></div></a><div class="price"><?php if($price == 0){echo '免费';}else{ if($currency=='cash')echo '<em>¥</em>'.sprintf('%0.2f',$price).'<em>(元)</em>'; else echo '<em><i class="fa fa-gift"></i></em>'.sprintf('%0.2f',$price).'<em>(积分)</em>';}?></div></li>
			<?php 
			endwhile;
		} 
		wp_reset_query(); 
		?>
		</ul>
    </div>
    <div class="shop-widget widget_products">
        <h3><i class="fa fa-tags"></i><?php echo $p_tag; ?></h3>
        <?php 
        $tag_terms = get_terms('products_tag', array( 'hide_empty' => false,'orderby' => 'count', 'order' => 'DESC'));
        echo '<div class="widget-content tagcloud">';
		if($tag_terms){
            foreach ($tag_terms as $tag_term){
                echo '<a class="product-tag tag-link" href="'.get_term_link($tag_term, 'product_tag').'" title="">'.$tag_term->name.'</a>';
		    }
		} 
        echo '</div>';
        ?>
    </div>
</div>
<?php } ?>