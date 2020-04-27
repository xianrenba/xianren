<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi
 */

get_header(); ?>

	<div id="primary-home" class="content-area" style="width:100%">
		<main id="main" class="site-main l-8 pos-r" role="main">
            	<?php
				if(zrz_get_display_settings('labs_show')){
	            	if ( have_posts()) :

	            		/* Start the Loop */
	            		while ( have_posts() ) : the_post();
	            			/*
	            			 * Include the Post-Format-specific template for the content.
	            			 * If you want to override this in a child theme, then include a file
	            			 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
	            			 */
	            			get_template_part( 'formats/content','labs');

	            		endwhile;

	            		the_posts_navigation();

	            	else :

	            		echo '<div class="loading-dom pos-r box l0">
							<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">没有研究，请发布一个！</p></div>
						</div>';

	            	endif;
				}else{
					get_template_part( 'template-parts/content','none');
				}
				 ?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();
