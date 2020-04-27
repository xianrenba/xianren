<?php
/**
 * The template for displaying all pages.
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
	<div id="primary" class="content-area fd">
		<main id="main" class="site-main" role="main">
			<?php 
				$menu = wp_nav_menu( array(
					'theme_location' => 'bbs-top-menu',
					'container_id'=>'zrz-menu-bbp',
					'menu_id' => 'nav-menu',
					'container_class'=> 'zrz-menu-in ',
					'menu_class'=>'zrz-menu-post',
					'depth'=>0,
					'echo' => FALSE,
					'fallback_cb' => '__return_false' )
				);
		
				if ( ! empty ( $menu ) ){
					echo '<div id="home-menu-bbp" class="home-menu-post pos-r box mar16-b clearfix">' . $menu . '</div>';
				}
			?>
			<?php while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><?php get_sidebar(); ?>
<?php get_footer(); ?>
