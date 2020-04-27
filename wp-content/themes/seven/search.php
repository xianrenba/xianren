<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package ziranzhi2
 */

get_header();
$nub = get_option('posts_per_page',10);
$ipaged = get_query_var('paged') ? get_query_var('paged') : 1;
$ipages = ceil( $wp_query->found_posts / $nub);
?>

	<section id="primary-home" class="content-area fd">
		<div class="box pd10 mar10-b fs14 search-title">关键词<span class="red dot"><?php echo get_search_query(); ?></span>的搜索结果：</div>
		<main id="main" class="site-main grid">

		<?php
		if ( have_posts() ) : ?>

			<?php
			$theme_style = zrz_get_theme_style();
			echo '<div ref="postList"  class="'.($theme_style === 'pinterest' ? 'grid-bor' : 'box').'">';
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				switch (get_post_type()) {
					//研究所
					case 'post':
						get_template_part( 'formats/content');
						break;
					//研究所
					case 'labs':
						get_template_part( 'formats/content', 'labs');
						break;
					//商城
					case 'shop':
						get_template_part( 'formats/content', 'shop');
						break;
					case 'pps':
						get_template_part( 'formats/content', 'bubble');
						break;
					case 'topic':
						get_template_part( 'formats/content','topic');
						break;
					default:
						break;
				}

			endwhile;
			echo '</div>';

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
		<?php if($ipages > 0) {echo zrz_pagenavi();}; ?>
	</section><?php
get_sidebar();
get_footer();
