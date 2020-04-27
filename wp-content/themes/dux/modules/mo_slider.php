<?php
/**
 * [mo_slider description]
 * @param  string $id   [description]
 * @return html         [description]
 */
function mo_slider( $id='slider' ){
    $indicators = '';
    $inner = '';

    $sort = _hui($id.'_sort') ? _hui($id.'_sort') : '1 2 3 4 5';
    $sort = array_unique(explode(' ', trim($sort)));
    $i = 0;
    foreach ($sort as $key => $value) {
        if( _hui($id.'_src_'.$value) && _hui($id.'_href_'.$value) && _hui($id.'_title_'.$value) ){
            $inner .= '<div class="swiper-slide"><a'.( _hui($id.'_blank_'.$value) ? ' target="_blank"' : '' ).' href="'._hui($id.'_href_'.$value).'"><img src="'._hui($id.'_src_'.$value).'"></a></div>';
            $i++;
        }
    }

    echo '<div id="'.$id.'" class="swiper-container">
        <div class="swiper-wrapper">'.$inner.'</div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next swiper-button-white"><i class="fa fa-chevron-right"></i></div>
        <div class="swiper-button-prev swiper-button-white"><i class="fa fa-chevron-left"></i></div>
    </div>';
}