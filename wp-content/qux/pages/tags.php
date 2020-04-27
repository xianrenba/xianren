<?php 
/**
 * Template name: Tags
 * Description:   A tags page
 */

get_header();

?>
	<div class="container container-no-sidebar">
        <h1 class="tagstitle"><?php echo get_the_title() ?></h1>
		<ul class="tagslist">
			<?php 
			$tags_list = get_tags('orderby=count&order=DESC');
			if ($tags_list) { 
				foreach($tags_list as $tag) {
					echo '<li><a class="name" href="'.get_tag_link($tag).'">'. $tag->name .'</a><small>x '. $tag->count .'</small><p>'; 
					$posts = get_posts( "tag_id=". $tag->term_id ."&numberposts=1" );
					if( $posts ){
						foreach( $posts as $post ) {
							setup_postdata( $post );
							echo '<a href="'.get_permalink().'">'.get_the_title().'</a></p>';
						}
					}
					echo '</li>';
				} 
			} 
			?>
		</ul>
	</div>
<?php

get_footer();