<?php
function mo_home_topic(){ ?>
    <div class="home-zhuanti">
        <div class="zhuanti-heading clear">
	        <div class="custom-wrap clear">
	            <h2><?php echo _hui('home_topic_title'); ?></h2>
	            <div class="desc"><?php echo _hui('home_topic_desc'); ?></div>
	            <div class="section-more"><a href="<?php echo get_page_link(_hui('home_topic_page')); ?>">查看更多 <i class="fa fa-angle-right"></i></a></div>
		    </div>
	    </div>
	<?php 
	    $html = '<div class="home-zhuanti-list clear">';
	    //$catlist = get_terms( 'tcat' );
	    $num = _hui('home_topic_num') ? _hui('home_topic_num') : 4;
	    $paged = 1;
	    $catlist = get_terms( array(
	    	'taxonomy' => 'tcat',
	    	'orderby' => 'id',
	    	'order' => 'DESC',
	    	'number' => $num,
	    	'hide_empty' => false,
	    	'offset' => $num*($paged-1)
	    ) );
		if ( ! empty( $catlist ) ) {
			foreach ( $catlist as $key => $item ) {
				$html .= '	<div class="zhuanti-list-item ht_custom_grid_1_4"><a class="thumbnail-link" href="'.get_term_link($item->term_id).'"><div class="thumbnail-wrap"><img src="' . THEME_URI . '/func/timthumb.php?src='. z_taxonomy_image_url($item->term_id).'&w=186&h=120" alt="'.$item->name.'" class="thumb"></div></a><h3 class="zhuanti-title"><a href="'.get_term_link($item->term_id).'">'. $item->name .'</a></h3></div>';
			}
		}
		$html .='</div>';
		echo $html;
	
	?>
    </div>
<?php	
}
?>