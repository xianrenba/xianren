<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package ziranzhi2
 */

get_header(); ?>

	<div id="primary" class="content-area w100">
		<main id="main" class="site-main">

			<section class="error-404 not-found box pos-r" style="min-height:500px">
				<div class="page-content">
					<div class="lm t-c">
						<img class="mar10-b" src="<?php echo ZRZ_THEME_URI.'/images/404.png'; ?>">
						<div class="mar10-b">
							<p class="mar10-b"></p>
							<h1 class="mar20-t mar10-b">未找到页面 - 404</h1>
							<p class="mar10-b">右上角有个搜索框，你可以试试</p>
							<p class="mar10-b">或者随便点击上面的链接吧，总会有你需要的</p>
						</div>
					</div>
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
