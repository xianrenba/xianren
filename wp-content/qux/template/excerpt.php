<?php
/**
 * Used for UCenter.
 *
 */
$ii = 0;
$p_meta = _hui('post_plugin');
while ( have_posts() ) : the_post(); 

    $_thumb = _get_post_thumbnail();
    $_excerpt_text = '';
    if( _hui('list_type')=='text' || (_hui('list_type') == 'thumb_if_has' && strstr($_thumb, 'data-thumb="default"')) ){
        $_excerpt_text = ' excerpt-text';
    }

    $ii++;
    echo '<article class="excerpt excerpt-'.$ii. $_excerpt_text .' wow fadeInUp">';

        if( _hui('list_type') == 'thumb' ){
            echo '<a'._post_target_blank().' class="focus" href="'.get_permalink().'">'.$_thumb.'</a>';
        }else if( _hui('list_type') == 'thumb_if_has' && !strstr($_thumb, 'data-thumb="default"') ){
            echo '<a'._post_target_blank().' class="focus" href="'.get_permalink().'">'.$_thumb.'</a>';
        }
        echo '<header>'; ?>
        <h2><?php if( $post->post_status==='draft'){echo '[草稿]';}else if( $post->post_status==='pending'){echo '[待审]';} ?><a <?php echo _post_target_blank() ?> href="<?php echo get_permalink() ?>" title="<?php echo get_the_title().get_the_subtitle(false)._get_delimiter().get_bloginfo('name') ?>"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>
        <?php 
        echo '</header>';
        if( $post->post_status=='publish' ){ ?>	
        <p class="meta">
		<span class="author"><i class="fa fa-user"></i><?php the_author();?></span>
		<time><i class="fa fa-clock-o"></i><?php echo date(__('Y-m-j','tinection'),get_the_time('U'));?></time> 
        <?php $category = get_the_category();?>
		<span class="author"><i class="fa fa-folder-open"></i><?php if($category[0]){ echo  $category[0]->cat_name; } ?></span>
		<span><?php if ( comments_open() ): ?><a href="<?php comments_link(); ?>"><i class="fa fa-comments"></i>评论(<?php comments_number( '0', '1', '%' ); ?>)</a><?php  endif; ?></span>
        <?php get_template_part('template/action-meta'); 
        echo '</p>';
        }                                   
        echo '<p class="note">'._get_excerpt().'</p>';
        if( _hui('post_link_excerpt_s') ) _moloader('mo_post_link');
        if( $post->post_status!='publish' ){ 
        	$meta_output = '<div class="entry-meta">';
        	if( $post->post_status==='pending' ) $meta_output .= sprintf(__('你可以 <a href="%1$s">预览</a> 或 <a href="%2$s">重新编辑</a> 。','um'), get_permalink(), get_edit_post_link($post->ID));
        	if( $post->post_status==='draft' ) $meta_output .= sprintf(__('你可以 <a href="%1$s">预览</a> 或 <a href="%2$s">继续编辑</a> 。','um'), get_permalink(), get_edit_post_link($post->ID));
        	$meta_output .= '</div>';
        	echo $meta_output;
        } 
    echo '</article>'; 

endwhile; 

//um_paginate();
_moloader('mo_paging');
wp_reset_query();