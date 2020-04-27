<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ziranzhi2
 */
do_action( 'content_after_7b2' );
?>
	</div><!-- #content -->
	<footer id="colophon" class="site-footer mar20-t pd20-b pd20-t">
		<div class="site-info fs12">

			<?php do_action( 'footer_content_7b2' ); ?>

		</div>
	</footer>
</div><!-- #page -->

<?php do_action( 'footer_before_7b2' ); ?>

<?php wp_footer(); ?>

<?php do_action( 'footer_after_7b2' ); ?>
</body>
</html>
