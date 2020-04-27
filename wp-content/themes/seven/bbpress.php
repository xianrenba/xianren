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

			<?php while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><?php get_sidebar(); ?>
<?php get_footer(); ?>
