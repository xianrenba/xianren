<?php
/**
 *Template Name: 全屏页面
 *
 *这里可用作友情链接，网址导航等功能
 *
 */

get_header(); ?>

	<div id="primary" class="content-area w100">
		<main id="main" class="site-main box page">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header pd20 b-b">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->

				<div class="entry-content pd20">
					<?php
					while ( have_posts() ) : the_post();
						the_content();

						wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ziranzhi2' ),
							'after'  => '</div>',
						) );
					endwhile;
					?>
				</div><!-- .entry-content -->
			</article><!-- #post-<?php the_ID(); ?> -->
		</main><!-- #main -->
		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		?>
	</div>
	<div class="h20"></div>
<?php
get_footer();
