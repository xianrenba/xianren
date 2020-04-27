<?php

get_header();

global $wp_query;
$curauth = $wp_query->get_queried_object();

?>

<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php 
		$pagedtext = '';
		if( $paged && $paged > 1 ){
			$pagedtext = ' <small>第'.$paged.'页</small>';
		}
		echo '<div class="authorleader">';
			echo um_get_avatar($curauth->ID, 50, um_get_avatar_type($curauth->ID));
			echo '<h1>'.$curauth->display_name.'的文章</h1>';
			echo '<div class="authorleader-desc">'.get_the_author_meta('description', $curauth->ID).'</div>';
		echo '</div>';
		
		get_template_part( 'excerpt' );
		?>
	</div>
	</div>
	<?php get_sidebar() ?>
</section>

<?php get_footer(); ?>