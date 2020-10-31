<?php
function mo_shop_relates($title = '相关商品', $showpost = 4){
    global $post;
    $tags = get_the_terms($post->ID,'products_tag');
    $tagcount = $tags ? count($tags):0;
    $tagIDs = array();for ($i = 0;$i <$tagcount;$i++) {$tagIDs[] = $tags[$i]->term_id;};
    $args = array('term__in'=>$tagIDs,'post_type'=>'store','post__not_in'=>array($post->ID),'showposts'=>$showpost,'orderby'=>'rand','ignore_sticky_posts'=>1);
    $my_query = new WP_Query($args); 
?>
<div class="related-products">
    <h2><span><?php echo $title; ?></span></h2>
    <ul class="products row">
        <?php if( $my_query->have_posts() ){
			while ($my_query->have_posts()) : $my_query->the_post();
                
			    $currency = get_post_meta( $post->ID, 'pay_currency', true) ? 'cash' : 'credit';
                $price = get_post_meta($post->ID, 'product_price', true);
			?>
            <li class="col-md-3 col-sm-4 col-xs-6 product">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark">
                    <div class="product-thumb"><?php echo _get_post_thumbnail(); ?></div>
                    <h3><?php the_title(); ?></h3>
                    <div class="price"><?php if($price == 0){echo '免费';}else{ if($currency=='cash')echo '<em>¥</em>'.sprintf('%0.2f',$price).'<em>(元)</em>'; else echo '<em><i class="fa fa-gift"></i></em>'.sprintf('%0.2f',$price).'<em>(积分)</em>';}?></div>
                </a>
            </li>
        <?php endwhile;} wp_reset_query(); ?>
    </ul>
</div>
<?php } ?>