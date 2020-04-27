<?php 
/*
 * Template Name: Likes Page
 * http://themebetter.com/theme/xiu
*/
get_header();
?>

	<div class="container page-likes no-sidebar">
		<h1 class="title"><strong><?php echo get_the_title() ?></strong></h1>
		<ul class="likepage">
		<?php 
			$args = array(
			    'ignore_sticky_posts' => 1,
			    'meta_key' => 'um_post_likes',
			    'orderby' => 'meta_value_num',
			    'showposts' => 40
			);
			query_posts($args);

			while ( have_posts() ) : the_post(); 
			$umlikes=get_post_meta(get_the_ID(),'um_post_likes',true);
	        $umlikes_array = explode(',',$umlikes);
	        $umlikes_count = $umlikes!=0?count($umlikes_array):0;
		    //$count = hui_post_images_number();
		    $like = get_post_meta( get_the_ID(), 'like', true );
		        echo '<li><a href="'.get_permalink().'">'._get_post_thumbnail(400,300).'<h2>'.get_the_title().'</h2></a>',
		        	$args['meta_key'],//hui_get_post_like($class='post-like'),
		        	'<span class="img-count"><span class="glyphicon glyphicon-picture"></span>'.$umlikes_count.'</span>',
		        '</li>';
		    endwhile; 
		    wp_reset_query();
		    
		?>
		</ul>
		<?php comments_template('', true); ?>
	</div>

<?php get_footer(); ?>