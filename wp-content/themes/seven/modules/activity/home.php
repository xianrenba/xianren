<?php
/*
*活动首页
*/
	get_header(); 
?>
	<?php do_action( 'seven_activity_home_before' ); ?>

	<div class="content-in">
		<div id="primary" class="content-area activity-content-area">
			<main id="activity" class="site-main page pos-r">
				<?php 
					do_action( 'seven_activity_home_list_before' );
					if ( have_posts() ) :
						
						while ( have_posts() ) : the_post();

						do_action( 'seven_activity_home_list' );
						
						endwhile;
						
					else :
				
						get_template_part( 'template-parts/content', 'none' );
				
					endif;
					echo zrz_pagenavi();
				?>
			</main>
			<?php do_action( 'seven_activity_home_sidebar' ); ?>
		</div>
	</div>
<?php
get_footer();
