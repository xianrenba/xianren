<?php
$ii = 0;
echo "<div class=\"layout_card\">";
while ( have_posts() ) : the_post(); 
	$ii++;
	
	echo "<article class=\"excerpt-card card card-" . $ii . "\">";
    echo "<div class=\"itemcard\">";
	echo "<div class=\"cardtitle\">";
	echo "<span class=\"bigpic\"><a ". _post_target_blank() ."class=\"focus\" href=". get_permalink() .">";
	if(has_post_format( 'video' )){ echo '<i class="fa fa-play-circle"></i>';} 
	echo _get_post_thumbnail(265,170) ." </a>";
	if (!is_category()) {
		$category = get_the_category();

		if ($category[0]) {
			echo "<a class=\"cat\" href=\"" . get_category_link($category[0]->term_id) . "\">" . $category[0]->cat_name . "</a> ";
		}
	}
	if(get_post_meta($post->ID,'pay_switch',true) == 1) echo'<small><a href="'.get_permalink().'#pay-content" title="'.get_the_title().'" target="_blank">立即购买</small>';
	echo "</span>";        
	echo "</div>";
    echo "<div class=\"entry-detail pictitle\">";
	echo "<h2 class=\"entry-title\"><a" . _post_target_blank() . " href=\"" . get_permalink() . "\" title=\"" . get_the_title() . _get_delimiter() . get_bloginfo("name") . "\">" . get_the_title() . "</a></h2>";
	echo "<div class=\"abstract\"><p>". _get_excerpt() ." </p></div>";
	
	echo "<div class=\"meta\" >";
  
    if(_hui('post_plugin_date')){
            echo '<time><i class="fa fa-clock-o"></i>'.get_the_time('Y-m-d').'</time>';
    }
			
	if (_hui('post_plugin_view')) {
		echo "<span class=\"pv\"><i class=\"fa fa-eye\"></i>" . _format_count(_get_post_views('','')) . "</span>";
	}
	
	if (get_post_meta($post->ID,'pay_switch',true) == 1 && get_post_meta($post->ID,'product_price',true)) {
		$icon = get_post_meta($post->ID,'pay_currency',true) ? '<i class="fa fa-rmb"></i>' : '<i class="fa fa-gift"></i>';
		echo "<span class=\"price\" style=\"color:#fd721f\">" .$icon. get_post_meta($post->ID,'product_price',true) . "</span>";
	}

	if (comments_open() && _hui('post_plugin_comm')) {
		echo "<a class=\"pc\" href=\"" . get_comments_link() . "\"><i class=\"fa fa-comments-o\"></i>" . get_comments_number("0", "1", "%") . "</a>";
	}
		//if(function_exists('ldclite_addPostLike')) ldclite_addPostLike();
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</article>";
endwhile;
echo "</div>";
_moloader("mo_paging");

wp_reset_query();

?>