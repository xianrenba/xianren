<?php
class widget_ui_homeblocktwo extends WP_Widget {


	function __construct() {

		// Set up the widget options.
		$widget_options = array(
			'classname'   => 'widget_ui_homeblocktwo',
			'description' => '只能添加到“首页内容(栏目)”'
		);

		// Create the widget.
		parent::__construct(
			'widget_ui_homeblocktwo',          // $this->id_base
			'qux 首页内容 - 2个栏目',          // $this->name
			$widget_options                    // $this->widget_options
		);
	}


	function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		echo "<div class=\"home-column clear\">";

		// Column first post
		echo "<div class=\"content-wrap\">";
		qux_home_posts_one( $args, $instance );
		echo "</div>";
		// Column first post
		qux_home_posts_two( $args, $instance );


		echo "</div>";

		echo $after_widget;

	}


	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;

		$instance['limit'] = (int) $new_instance['limit'];
		$instance['limit2'] = (int) $new_instance['limit2'];
		$instance['cat']   = (int) $new_instance['cat'];
		$instance['cat_2']   = (int) $new_instance['cat_2'];
		$instance['text1']   = strip_tags( $new_instance['text1'] );
		$instance['text2']   = strip_tags( $new_instance['text2'] );
		$instance['text3']   = strip_tags( $new_instance['text3'] );
		$instance['link1']   = strip_tags( $new_instance['link1'] );
		$instance['link2']   = strip_tags( $new_instance['link2'] );
		$instance['link3']   = strip_tags( $new_instance['link3'] );
		$instance['html']   = $new_instance['html'];
		$instance['html2']   = $new_instance['html2'];
		$instance['show_thumb'] = isset( $new_instance['show_thumb'] ) ? (bool) $new_instance['show_thumb'] : false;

		return $instance;
	}


	function form( $instance ) {

		// Default value.
		$defaults = array(
			'limit' => 14,
			'limit2' => 9,
			'cat'   => '',
			'cat_2'   => '',
			'text1' => '',
			'text2' => '',
			'text3' => '',
			'link1' => home_url(),
			'link2' => home_url(),
			'link3' => home_url(),
			'html' => '',
			'html2' => '',
			'show_thumb' => true
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><strong><?php _e( '左侧栏目: 选择一个分类目录', 'qux' ); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'cat' ); ?>" name="<?php echo $this->get_field_name( 'cat' ); ?>" style="width:100%;">
				<?php $categories = get_terms( 'category' ); ?>
				<option value="0"><?php _e( '所有分类目录 &hellip;', 'qux' ); ?></option>
				<?php foreach( $categories as $category ) { ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $instance['cat'], $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">
				<?php _e( '要显示的文章数', 'qux' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['limit'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text1' ); ?>">
				<?php _e( '子菜单标题 1', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text1' ); ?>" name="<?php echo $this->get_field_name( 'text1' ); ?>" value="<?php echo esc_attr( $instance['text1'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link1' ); ?>">
				<?php _e( '链接地址 1', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link1' ); ?>" name="<?php echo $this->get_field_name( 'link1' ); ?>" value="<?php echo esc_attr( $instance['link1'] ); ?>" placeholder="http://"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text2' ); ?>">
				<?php _e( '子菜单标题 2', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text2' ); ?>" name="<?php echo $this->get_field_name( 'text2' ); ?>" value="<?php echo esc_attr( $instance['text2'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link2' ); ?>">
				<?php _e( '链接地址 2', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link2' ); ?>" name="<?php echo $this->get_field_name( 'link2' ); ?>" value="<?php echo esc_attr( $instance['link2'] ); ?>" placeholder="http://" />
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id( 'text3' ); ?>">
				<?php _e( '子菜单标题 3', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text3' ); ?>" name="<?php echo $this->get_field_name( 'text3' ); ?>" value="<?php echo esc_attr( $instance['text3'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link3' ); ?>">
				<?php _e( '链接地址 3', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link3' ); ?>" name="<?php echo $this->get_field_name( 'link3' ); ?>" value="<?php echo esc_attr( $instance['link3'] ); ?>" placeholder="http://"/>
		</p>										

		<p>
			<label for="<?php echo $this->get_field_id( 'html' ); ?>">
				<?php _e( '左侧栏目底部HTML/广告代码', 'qux' ); ?>
			</label>
			<textarea style="height: 100px;" class="widefat" id="<?php echo $this->get_field_id( 'html' ); ?>" name="<?php echo $this->get_field_name( 'html' ); ?>"><?php echo $instance['html']; ?></textarea> 
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_2' ); ?>"><strong><?php _e( '右侧栏目: 选择一个分类目录', 'qux' ); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'cat_2' ); ?>" name="<?php echo $this->get_field_name( 'cat_2' ); ?>" style="width:100%;">
				<?php $categories = get_terms( 'category' ); ?>
				<option value="0"><?php _e( '所有分类目录 &hellip;', 'qux' ); ?></option>
				<?php foreach( $categories as $category ) { ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $instance['cat_2'], $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_thumb'] ); ?> id="<?php echo $this->get_field_id( 'show_thumb' ); ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_thumb' ); ?>">
				<?php _e( '显示文章缩略图?', 'qux' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit2' ); ?>">
				<?php _e( '要显示的文章数', 'qux' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit2' ); ?>" name="<?php echo $this->get_field_name( 'limit2' ); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['limit2'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'html2' ); ?>">
				<?php _e( '右侧栏目底部HTML/广告代码', 'qux' ); ?>
			</label>
			<textarea style="height: 100px;" class="widefat" id="<?php echo $this->get_field_id( 'html2' ); ?>" name="<?php echo $this->get_field_name( 'html2' ); ?>"><?php echo $instance['html2']; ?></textarea> 
		</p>								

	<?php

	}

}

/**
 * Column one posts
 */
function qux_home_posts_one( $args, $instance ) {


	// Pull the selected category.
	$cat_id = isset( $instance['cat'] ) ? absint( $instance['cat'] ) : 0;

	// Get the category.
	$category = get_category( $cat_id );

	// Get the category archive link.
	$cat_link = get_category_link( $cat_id );

	// Limit to category based on user selected tag.
	if ( ! $cat_id == 0 ) {
		$args['cat'] = $cat_id;
	}

?>
		<div class="home-content">

		<div class="content-block content-block-1 clear">

			<div class="section-heading">

			<?php
				if ( ( ! empty( $instance['title'] ) ) && ( $cat_id != 0 ) ) {
					echo '<h2 class="section-title"><a target="_blank" href="' . esc_url( $cat_link ) . '">' . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . '</a></h2>';
				} elseif ( $cat_id == 0 ) {
					echo '<h2 class="section-title"><span>' . __( '最新文章', 'qux' ) . '</span></h2>';
				} else {
					echo '<h2 class="section-title"><a target="_blank" href="' . esc_url( $cat_link ) . '">' . esc_attr( $category->name ) . '</a></h2>';
				}
			?>
			<ul class="section-more">

				<?php if ( ( ! empty( $instance['text1'] ) ) && ( ! empty( $instance['link1'] ) ) ) { ?>
				<li><a href="<?php echo esc_url($instance['link1']); ?>" target="_blank"><?php echo esc_html($instance['text1']); ?></a></li>
				<?php } ?>
				<?php if ( ( ! empty( $instance['text2'] ) ) && ( ! empty( $instance['link2'] ) ) ) { ?>
				<li><a href="<?php echo esc_url($instance['link2']); ?>" target="_blank"><?php echo esc_html($instance['text2']); ?></a></li>
				<?php } ?>
				<?php if ( ( ! empty( $instance['text3'] ) ) && ( ! empty( $instance['link3'] ) ) ) { ?>
				<li><a href="<?php echo esc_url($instance['link3']); ?>" target="_blank"><?php echo esc_html($instance['text3']); ?></a></li>
				<?php } ?>
			</ul>

			</div><!-- .section-heading -->			

			<div class="col-left">

			<?php

				// Define custom query args
				$args = array( 
					'post_type'      => 'post',
					//'posts_per_page' => ( ! empty( $instance['limit'] ) ) ? absint( $instance['limit'] ) : 5,
					'posts_per_page' => 4,
					'post__not_in' => get_option( 'sticky_posts' ),
					'cat' => $cat_id
				);  

				global $wp_query;

				$merged_query_args = array_merge( $wp_query->query, $args );

				query_posts( $merged_query_args );

				$i = 1;

				if ( $wp_query->have_posts() ) :

				while ( $wp_query->have_posts() ) : $wp_query->the_post(); 

				if ($i < 5) { 
			?>

			<div class="post-big hentry">

				<a class="thumbnail-link" href="<?php the_permalink(); ?>" target="_blank">
					<div class="thumbnail-wrap">
						<?php echo _get_post_thumbnail(190,120); ?>			
					</div>
				</a>

				<div class="entry-header">
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>
				</div>

			</div>

			<?php } else { ?>

			<div class="post-small hentry <?php echo( $wp_query->current_post + 1 === $wp_query->post_count ) ? 'last' : ''; ?>">

				<div class="entry-header">

					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>

				</div>

			</div>

			<?php 
				}
				$i++;
				endwhile; 
			?>

			</div>

			<div class="col-right">

			<?php

				// Define custom query args
				$args = array( 
					'post_type'      => 'post',
					//'posts_per_page' => ( ! empty( $instance['limit'] ) ) ? absint( $instance['limit'] ) : 5,
					'posts_per_page' => (absint( $instance['limit'] ) - 4),
					'offset' => 4,
					'post__not_in' => get_option( 'sticky_posts' ),
					'cat' => $cat_id
				);  

				global $wp_query;

				$merged_query_args = array_merge( $wp_query->query, $args );

				query_posts( $merged_query_args );

				$i = 1;

				if ( $wp_query->have_posts() ) :

				while ( $wp_query->have_posts() ) : $wp_query->the_post(); 

				if ($i < 25) { 
			?>

			<div class="post-small hentry <?php echo( $wp_query->current_post + 1 === $wp_query->post_count ) ? 'last' : ''; ?>">


				<div class="entry-header">

					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>

				</div>

			</div>

			<?php 
				}
				$i++;
				endwhile; 
				endif;
			?>

			</div>			

		</div>

		<?php if ($instance['html'] != null) { ?>
			<div class="home-left-ad clear">
				<?php echo $instance['html']; ?>
			</div>
		<?php } ?>		

	</div>

	<?php 
	endif;
	wp_reset_query();
	wp_reset_postdata();
}

/**
 * Column second posts
 */
function qux_home_posts_two( $args, $instance ) {


	// Pull the selected category.
	$cat_id = isset( $instance['cat_2'] ) ? absint( $instance['cat_2'] ) : 0;

	// Get the category.
	$category = get_category( $cat_id );

	// Get the category archive link.
	$cat_link = get_category_link( $cat_id );

		// Limit to category based on user selected tag.
		if ( ! $cat_id == 0 ) {
			$args['cat'] = $cat_id;
		}

		// Define custom query args
		$args = array( 
			'post_type'      => 'post',
			'posts_per_page' => ( ! empty( $instance['limit2'] ) ) ? absint( $instance['limit2'] ) : 10,
			//'posts_per_page' =>'10',
			'post__not_in' => get_option( 'sticky_posts' ),
			'cat' => $cat_id
		);  

		global $wp_query;

		$merged_query_args = array_merge( $wp_query->query, $args );

		query_posts( $merged_query_args );

		$i = 1;

		if ( $wp_query->have_posts() ) : ?>

		<div class="home-right style-one">

			<div class="home-content-right">
				<div class="section-heading">
				<?php
				if ( $cat_id == 0 ) {
					echo '<h2 class="section-title"><span>' . __( '最新文章', 'qux' ) . '</span></h2>';
				} else {
					echo '<h2 class="section-title"><a target="_blank" href="' . esc_url( $cat_link ) . '">' . esc_attr( $category->name ) . '</a></h2>';
				}	
				?>
				</div>			
				<ul class="posts-wrap clear">
				<?php
				// The Loop
				while ( $wp_query->have_posts() ) : $wp_query->the_post();
				if( ($i <= 2)&& ($instance['show_thumb'] == true) ) {
				?>	
				<li class="post-big">
					<a class="thumbnail-link" href="<?php the_permalink(); ?>" target="_blank">
						<div class="thumbnail-wrap"><?php echo _get_post_thumbnail(); ?></div><!-- .thumbnail-wrap -->
					</a>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>
				</li>
				<?php
				} else {
				?>
				<li class="list <?php if( ($instance['show_thumb'] == true) && ($i == 3) ) { echo 'has-border'; } ?>">
					<a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle();  ?></a>
				</li>
				<?php
				}
				$i++;
				endwhile;
				?>
				</ul>
				</div>
				<?php if ($instance['html2'] != null) { ?>
				<div class="home-right-ad">
					<?php echo $instance['html2']; ?>
				</div>
				<?php } ?>
		</div>

	<?php endif;

	wp_reset_query();
	wp_reset_postdata();

}