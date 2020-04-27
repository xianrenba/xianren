<?php
class widget_ui_homeblockthree extends WP_Widget {

	function __construct() {

		// Set up the widget options.
		$widget_options = array(
			'classname'   => 'widget_ui_homeblockthree',
			'description' => '只能添加到“首页内容(栏目)”'
		);

		// Create the widget.
		parent::__construct(
			'widget_ui_homeblockthree',          // $this->id_base
			'qux 首页内容 - 3个栏目',          // $this->name
			$widget_options                    // $this->widget_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.0.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

			echo "<div class=\"home-column clear\">";
			echo '<div class="content-wrap"><div class="home-content"><div class="content-block content-block-2 clear">';

				// Column first post
				_home_posts_first( $args, $instance );

				// Column first post
				_home_posts_second( $args, $instance );

			echo '</div></div></div><!-- .home-content -->';

			_home_posts_third( $args, $instance );

			echo "</div>";

		echo $after_widget;

	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;

		$instance['limit']   = (int) $new_instance['limit'];
		$instance['limit2']   = (int) $new_instance['limit2'];
		$instance['limit3']   = (int) $new_instance['limit3'];
		$instance['cat']     = $new_instance['cat'];
		$instance['cat_2']   = $new_instance['cat_2'];
		$instance['cat_3']   = $new_instance['cat_3'];
		$instance['text1_1']   = strip_tags( $new_instance['text1_1'] );
		$instance['text1_2']   = strip_tags( $new_instance['text1_2'] );
		$instance['text1_3']   = strip_tags( $new_instance['text1_3'] );
		$instance['link1_1']   = strip_tags( $new_instance['link1_1'] );
		$instance['link1_2']   = strip_tags( $new_instance['link1_2'] );
		$instance['link1_3']   = strip_tags( $new_instance['link1_3'] );	
		$instance['text2_1']   = strip_tags( $new_instance['text2_1'] );
		$instance['text2_2']   = strip_tags( $new_instance['text2_2'] );
		$instance['text2_3']   = strip_tags( $new_instance['text2_3'] );
		$instance['link2_1']   = strip_tags( $new_instance['link2_1'] );
		$instance['link2_2']   = strip_tags( $new_instance['link2_2'] );
		$instance['link2_3']   = strip_tags( $new_instance['link2_3'] );	
		$instance['html']	   = $new_instance['html'];
		$instance['show_thumb'] = isset( $new_instance['show_thumb'] ) ? (bool) $new_instance['show_thumb'] : false;

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 1.0.0
	 */
	function form( $instance ) {

		// Default value.
		$defaults = array(
			'limit'   => 7,
			'limit2'   => 10,
			'limit3'   => 9,
			'cat'     => '',
			'cat_2'   => '',
			'cat_3'   => '',
			'text1_1' => '',
			'text1_2' => '',
			'text1_3' => '',
			'link1_1' => home_url(),
			'link1_2' => home_url(),
			'link1_3' => home_url(),
			'text2_1' => '',
			'text2_2' => '',
			'text2_3' => '',
			'link2_1' => home_url(),
			'link2_2' => home_url(),
			'link2_3' => home_url(),
			'html'    => '',
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
			<label for="<?php echo $this->get_field_id( 'text1_1' ); ?>">
				<?php _e( '子菜单标题 1', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text1_1' ); ?>" name="<?php echo $this->get_field_name( 'text1_1' ); ?>" value="<?php echo esc_attr( $instance['text1_1'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link1_1' ); ?>">
				<?php _e( '链接地址 1', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link1_1' ); ?>" name="<?php echo $this->get_field_name( 'link1_1' ); ?>" value="<?php echo esc_attr( $instance['link1_1'] ); ?>" placeholder="http://"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text1_2' ); ?>">
				<?php _e( '子菜单标题 2', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text1_2' ); ?>" name="<?php echo $this->get_field_name( 'text1_2' ); ?>" value="<?php echo esc_attr( $instance['text1_2'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link1_2' ); ?>">
				<?php _e( '链接地址 2', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link1_2' ); ?>" name="<?php echo $this->get_field_name( 'link1_2' ); ?>" value="<?php echo esc_attr( $instance['link1_2'] ); ?>" placeholder="http://" />
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id( 'text1_3' ); ?>">
				<?php _e( '子菜单标题 3', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text1_3' ); ?>" name="<?php echo $this->get_field_name( 'text1_3' ); ?>" value="<?php echo esc_attr( $instance['text1_3'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link1_3' ); ?>">
				<?php _e( '链接地址 3', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link1_3' ); ?>" name="<?php echo $this->get_field_name( 'link1_3' ); ?>" value="<?php echo esc_attr( $instance['link1_3'] ); ?>" placeholder="http://"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">
				<?php _e( '要显示的文章数', 'qux' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['limit'] ); ?>" />
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_2' ); ?>"><strong><?php _e( '中间栏目: 选择一个分类目录', 'qux' ); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'cat_2' ); ?>" name="<?php echo $this->get_field_name( 'cat_2' ); ?>" style="width:100%;">
				<?php $categories_2 = get_terms( 'category' ); ?>
				<option value="0"><?php _e( '所有分类目录 &hellip;', 'qux' ); ?></option>
				<?php foreach( $categories_2 as $category_2 ) { ?>
					<option value="<?php echo esc_attr( $category_2->term_id ); ?>" <?php selected( $instance['cat_2'], $category_2->term_id ); ?>><?php echo esc_html( $category_2->name ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text2_1' ); ?>">
				<?php _e( '子菜单标题 1', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text2_1' ); ?>" name="<?php echo $this->get_field_name( 'text2_1' ); ?>" value="<?php echo esc_attr( $instance['text2_1'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link2_1' ); ?>">
				<?php _e( '链接地址 1', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link2_1' ); ?>" name="<?php echo $this->get_field_name( 'link2_1' ); ?>" value="<?php echo esc_attr( $instance['link2_1'] ); ?>" placeholder="http://"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text2_2' ); ?>">
				<?php _e( '子菜单标题 2', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text2_2' ); ?>" name="<?php echo $this->get_field_name( 'text2_2' ); ?>" value="<?php echo esc_attr( $instance['text2_2'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link2_2' ); ?>">
				<?php _e( '链接地址 2', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link2_2' ); ?>" name="<?php echo $this->get_field_name( 'link2_2' ); ?>" value="<?php echo esc_attr( $instance['link2_2'] ); ?>" placeholder="http://" />
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id( 'text2_3' ); ?>">
				<?php _e( '子菜单标题 3', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text2_3' ); ?>" name="<?php echo $this->get_field_name( 'text2_3' ); ?>" value="<?php echo esc_attr( $instance['text2_3'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link2_3' ); ?>">
				<?php _e( '链接地址 3', 'qux' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'link2_3' ); ?>" name="<?php echo $this->get_field_name( 'link2_3' ); ?>" value="<?php echo esc_attr( $instance['link2_3'] ); ?>" placeholder="http://"/>
		</p>	

		<p>
			<label for="<?php echo $this->get_field_id( 'limit2' ); ?>">
				<?php _e( '要显示的文章数', 'qux' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit2' ); ?>" name="<?php echo $this->get_field_name( 'limit2' ); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['limit2'] ); ?>" />
		</p>		

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_3' ); ?>"><strong><?php _e( '右侧栏目: 选择一个分类目录', 'qux' ); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'cat_3' ); ?>" name="<?php echo $this->get_field_name( 'cat_3' ); ?>" style="width:100%;">
				<?php $categories_3 = get_terms( 'category' ); ?>
				<option value="0"><?php _e( '所有分类目录 &hellip;', 'qux' ); ?></option>
				<?php foreach( $categories_3 as $categories_3 ) { ?>
					<option value="<?php echo esc_attr( $categories_3->term_id ); ?>" <?php selected( $instance['cat_3'], $categories_3->term_id ); ?>><?php echo esc_html( $categories_3->name ); ?></option>
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
			<label for="<?php echo $this->get_field_id( 'limit3' ); ?>">
				<?php _e( '要显示的文章数', 'qux' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit3' ); ?>" name="<?php echo $this->get_field_name( 'limit3' ); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['limit3'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'html' ); ?>">
				<?php _e( '自定义HTML/广告代码', 'qux' ); ?>
			</label>
			<textarea style="height: 100px;" class="widefat" id="<?php echo $this->get_field_id( 'html' ); ?>" name="<?php echo $this->get_field_name( 'html' ); ?>"><?php echo $instance['html']; ?></textarea> 
		</p>	

	<?php

	}

}

/**
 * Column first posts
 */
function _home_posts_first( $args, $instance ) {


	$cat_id = isset( $instance['cat'] ) ? absint( $instance['cat'] ) : 0;

	$category = get_category( $cat_id );

	$cat_link = get_category_link( $cat_id );

	if ( ! $cat_id == 0 ) { $args['cat'] = $cat_id; }

	$args = array( 
		'post_type'      => 'post',
		'posts_per_page' => ( ! empty( $instance['limit'] ) ) ? absint( $instance['limit'] ) : 5,
		'post__not_in' => get_option( 'sticky_posts' ),
		'cat' => $cat_id
	);  

	global $wp_query;

	$merged_query_args = array_merge( $wp_query->query, $args );

	query_posts( $merged_query_args );

	$i = 1;

	if ( $wp_query->have_posts() ) : ?>

	<div class="block-left">

		<div class="section-heading">
			<?php

			if ( $cat_id == 0 ) {
				echo '<h2 class="section-title"><span>' . __( '最新文章', 'qux' ) . '</span></h2>';
			} else {
				echo '<h2 class="section-title"><a target="_blank" href="' . esc_url( $cat_link ) . '">' . esc_attr( $category->name ) . '</a></h2>';
			}	
			?>
			<ul class="section-more">
			    <?php if ( ( ! empty( $instance['text1_1'] ) ) && ( ! empty( $instance['link1_1'] ) ) ) { ?>
				<li><a href="<?php echo esc_url($instance['link1_1']); ?>" target="_blank"><?php echo esc_html($instance['text1_1']); ?></a></li>
				<?php } ?>

				<?php if ( ( ! empty( $instance['text1_2'] ) ) && ( ! empty( $instance['link1_2'] ) ) ) { ?>
				<li><a href="<?php echo esc_url($instance['link1_2']); ?>" target="_blank"><?php echo esc_html($instance['text1_2']); ?></a></li>
				<?php } ?>

				<?php if ( ( ! empty( $instance['text1_3'] ) ) && ( ! empty( $instance['link1_3'] ) ) ) { ?>
				<li><a href="<?php echo esc_url($instance['link1_3']); ?>" target="_blank"><?php echo esc_html($instance['text1_3']); ?></a></li>
				<?php } ?>
			</ul>
		</div>			
			
		<div class="posts-wrap">
			<?php 
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
			
			if ($i < 3) {
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
			</div><!-- .hentry -->
			
			<?php } else { ?>
			
			<div class="post-small hentry clear <?php echo( $wp_query->current_post + 1 === $wp_query->post_count ) ? 'last' : ''; ?>">
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

	</div>	

	<?php endif;

	wp_reset_query();
	wp_reset_postdata();
}

/**
 * Column second posts
 */
function _home_posts_second( $args, $instance ) {

	$cat_id = isset( $instance['cat_2'] ) ? absint( $instance['cat_2'] ) : 0;

	$category = get_category( $cat_id );

	$cat_link = get_category_link( $cat_id );

	if ( ! $cat_id == 0 ) { $args['cat'] = $cat_id; }

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

	<div class="block-right">

		<div class="section-heading">

<?php
	if ( $cat_id == 0 ) {
		echo '<h2 class="section-title"><span>' . __( '最新文章', 'damenhu' ) . '</span></h2>';
	} else {
		echo '<h2 class="section-title"><a target="_blank" href="' . esc_url( $cat_link ) . '">' . esc_attr( $category->name ) . '</a></h2>';
	}	
?>

<ul class="section-more">

	<?php if ( ( ! empty( $instance['text2_1'] ) ) && ( ! empty( $instance['link2_1'] ) ) ) { ?>
	<li><a href="<?php echo esc_url($instance['link2_1']); ?>" target="_blank"><?php echo esc_html($instance['text2_1']); ?></a></li>
	<?php } ?>

	<?php if ( ( ! empty( $instance['text2_2'] ) ) && ( ! empty( $instance['link2_2'] ) ) ) { ?>
	<li><a href="<?php echo esc_url($instance['link2_2']); ?>" target="_blank"><?php echo esc_html($instance['text2_2']); ?></a></li>
	<?php } ?>

	<?php if ( ( ! empty( $instance['text2_3'] ) ) && ( ! empty( $instance['link2_3'] ) ) ) { ?>
	<li><a href="<?php echo esc_url($instance['link2_3']); ?>" target="_blank"><?php echo esc_html($instance['text2_3']); ?></a></li>
	<?php } ?>
</ul>

</div>			

<div class="posts-wrap">

<?php
	// The Loop
	while ( $wp_query->have_posts() ) : $wp_query->the_post();

?>	

<div class="post-small hentry clear <?php echo( $wp_query->current_post + 1 === $wp_query->post_count ) ? 'last' : ''; ?>">

	<div class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>				
	</div>

</div>

<?php
	$i++;
	endwhile;

?>

</div>

	</div>
	
	<?php endif;

	wp_reset_query();
	wp_reset_postdata();

}

/**
 * Column third posts
 */
function _home_posts_third( $args, $instance ) {

	$cat_id = isset( $instance['cat_3'] ) ? absint( $instance['cat_3'] ) : 0;

	$category = get_category( $cat_id );

	$cat_link = get_category_link( $cat_id );

	if ( ! $cat_id == 0 ) { $args['cat'] = $cat_id; }

	$args = array( 
		'post_type'      => 'post',
		'posts_per_page' => ( ! empty( $instance['limit3'] ) ) ? absint( $instance['limit3'] ) : 9,
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
			    		<div class="thumbnail-wrap"><?php echo _get_post_thumbnail(); ?></div>
			    	</a>	
			    	<h2 class="entry-title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a></h2>
			    </li>
			<?php  } else {  ?>
			<li class="list<?php if( ($instance['show_thumb'] == true) && ($i == 3) ) { echo ' has-border'; } ?><?php echo( $wp_query->current_post + 1 === $wp_query->post_count ) ? ' last' : ''; ?>">
				<a href="<?php the_permalink(); ?>" target="_blank"><?php echo get_the_title().get_the_subtitle(); ?></a>
			</li>
			<?php

			}
			$i++;
			endwhile;
			?>
			</ul>
		</div>
		
		<?php if ($instance['html'] != null) { ?>
		<div class="home-right-ad">
			<?php echo $instance['html']; ?>
		</div>
		<?php } ?>

	</div><!-- .block-right -->	

	<?php endif;

	wp_reset_query();
	wp_reset_postdata();

}