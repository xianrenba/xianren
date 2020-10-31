<?php 
/* 
 * post focus
 * ====================================================
*/
function mo_posts_focus(){
    $html = '';
  	/* if( _hui('thumbnail_src') ){
        $html .= '<li class="large"><a'._focus_target_blank().' href="'._hui('focus_href').'"><img data-src="'._hui('focus_src').'" class="lazy thumb" src="'.get_stylesheet_directory_uri().'/img/thumbnail.png" style="display: inline;"><h4>'._hui('focus_title').'</h4></a></li>';
    }else{
        $html .= '<li class="large"><a'._focus_target_blank().' href="'._hui('focus_href').'"><img class="thumb" src="'._hui('focus_src').'" style="display: inline;"><h4>'._hui('focus_title').'</h4></a></li>';
    } */
	
	$inner = '';
	
	$id = 'focusslide';

    $sort = _hui($id.'_sort') ? _hui($id.'_sort') : '1 2 3 4 5';
    $sort = array_unique(explode(' ', trim($sort)));
    
    foreach ($sort as $key => $value) {
        if( _hui($id.'_src_'.$value) && _hui($id.'_href_'.$value) && _hui($id.'_title_'.$value) ){
            $inner .= '<div class="swiper-slide"><a'.( _hui($id.'_blank_'.$value) ? ' target="_blank"' : '' ).' href="'._hui($id.'_href_'.$value).'"><img src="'._hui($id.'_src_'.$value).'"><h4>'._hui($id.'_title_'.$value).'</h4></a></div>';
        }
    }
    

    $focus = '<div id="'.$id.'-1" class="swiper-container">
        <div class="swiper-wrapper">'.$inner.'</div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next swiper-button-white"></div>
        <div class="swiper-button-prev swiper-button-white"></div>
    </div>';
	
    $sticky = get_option('sticky_posts'); rsort( $sticky ); 
    $num = count($sticky);
    query_posts( array( 'post__in' => $sticky, 'ignore_sticky_posts' => 1, 'showposts' => 4 ) );
    $html .= qux_show_post();
    if ($num >= 1 && $num < 4){
    	query_posts( array( 'post__not_in' => $sticky, 'ignore_sticky_posts' => 1, 'showposts' => (4 - $num) ) );
    	$html .= qux_show_post();
    };
    echo '<div class="focusmo">'.$focus.'<div class="right-item"><ul>'.$html.'</ul></div></div>';
}


function qux_show_post(){
	$html = '';
	if( have_posts() ) : 
		while (have_posts()) : the_post(); 
		$html .= '<li><a'._focus_target_blank().' href="'.get_permalink().'">';
		if(is_sticky()) $html .= '<span>置顶</span>';
		$html .= _get_post_thumbnail();
		$html .= '<h4>'.get_the_title().get_the_subtitle().'</h4>';
		$html .= '</a></li>';
        endwhile; 
    endif;
    wp_reset_query();
    return $html;
}

/* if( _hui('list_type') == 'thumb' ){
    echo '<a'._post_target_blank().' class="focus" href="'.get_permalink().'">'.$_thumb.'</a>';
}else if( _hui('list_type') == 'thumb_if_has' && !strstr($_thumb, 'data-thumb="default"') ){
    echo '<a'._post_target_blank().' class="focus" href="'.get_permalink().'">'.$_thumb.'</a>';
} */