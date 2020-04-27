<?php 
/**
 * Template Name: 专题列表 (全宽3列)
 * The template for displaying full width pages.
 *
 */
get_header(); 

?>
<section class="container full-width">
	<div class="content">
		<header class="entry-header">
			<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
				while ( have_posts() ) : the_post();
					the_content();
				endwhile;
			?>

			<?php
			        $string = '<div class="zhuanti-list clear">';
					$catlist = get_terms( 'tcat' );

					if ( ! empty( $catlist ) ) {

					  foreach ( $catlist as $key => $item ) {

					   // if ( ! empty( z_taxonomy_image_url($item->term_id) ) ) {

					       $string .= '<div class="zhuanti-block ht_grid_1_3"><a class="thumbnail-link" href='. get_term_link($item->term_id) .'><div class="thumbnail-wrap"><img src="' . get_template_directory_uri() . '/func/timthumb.php?src='. z_taxonomy_image_url($item->term_id).'&w=385&h=220" alt="'.$item->name.'"/><h3 class="zhuanti-title">'. $item->name . '</h3></div></a>';
					       $string .= '<div class="zhuanti-desc">'. wp_trim_words($item->description, '68') . '</div> ';

					       $args = array(
                                    'posts_per_page' => 5,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'tcat',
                                            'field' => 'term_id',
                                            'terms' => $item->term_id
                                        )
                                    )
                                );
					        $postslist = new WP_Query($args);//query_posts($args);

					        $string .= '<ul>';

					        while($postslist->have_posts()) : $postslist->the_post(); 

					            $string .= '<li><a href=' . get_permalink() . ' target="_blank">' . get_the_title().'</a></li>';

					        endwhile;

					        $string .= '</ul>';

					        wp_reset_postdata();

					        $string .= '</div>';

					   // } // End if z_taxonomy_image_url

					  } //End foreach

					} //End if catlist


					$string .= '</div>';
					 
					echo $string; 

			?>

		</div><!-- .entry-content -->

		<?php if ( get_edit_post_link() ) : ?>
			<footer class="entry-footer">
				<?php
					edit_post_link(
						sprintf(
							/* translators: %s: Name of current post */
							esc_html__( '编辑 %s', 'damenhu' ),
							the_title( '<span class="screen-reader-text">"', '"</span>', false )
						),
						'<span class="edit-link">',
						'</span>'
					);
				?>
			</footer><!-- .entry-footer -->
		<?php endif; ?>
	</div>
</section>		
<?php get_footer(); ?>