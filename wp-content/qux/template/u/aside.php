<?php
	global $wp_query;
	$curauth = $wp_query->get_queried_object();
	$current_user = wp_get_current_user();
?>
<aside class="pagesidebar">
	<?php $tab_output = '';
		foreach( $tab_array as $tab_term ){
			$class = $get_tab==$tab_term ? ' class="active" ' : '';
			$tab_output .= sprintf('<li %s><a href="%s">%s</a></li>', $class, add_query_arg('tab', $tab_term, esc_url( get_author_posts_url( $curauth->ID ) )), $tabs[$tab_term]);
		}
			
       echo '<ul class="pagesider-menu user-tab">'.$tab_output.'</ul>';
	?>
</aside>