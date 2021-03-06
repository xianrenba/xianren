<?php
class widget_ui_credits extends WP_Widget {
/*  Widget
/* ------------------------------------ */
	function __construct(){
		parent::__construct(false,'qux 积分排行',array( 'description' => '显示用户积分排行榜' ,'classname' => 'widget_credits'));
	}

	function widget($args,$instance){
		extract($args);
	?>
		<?php echo $before_widget; ?>
        <?php if($instance['title'])echo $before_title.$instance['title']. $after_title; ?>
		<?php
			$limit = $instance['ranks_num'];
			$creditsranks = um_credits_rank($limit);
			echo '<div class="widget-content"><ul>';
			$i=0;
			foreach ( $creditsranks as $creditsrank) {
				$i++;
				$user_name = get_user_meta($creditsrank->user_id,'nickname',true);
				$avatar = um_get_avatar( $creditsrank->user_id , '40' , um_get_avatar_type($creditsrank->user_id) );
				echo '<li class="umcreditsrank-list"><span class="index">'.$i.'.</span><span class="avatar">'.$avatar.'</span><span class="name"><a href="'.get_author_posts_url($creditsrank->user_id).'" target="_blank" title="'.$user_name.'">'.$user_name.'</a></span><span class="credits"><span class="num">'.$creditsrank->meta_value.'</span>积分</span></li>';
			}
			echo '</ul></div>';
		?>
		<?php echo $after_widget; ?>

	<?php }

	function update($new,$old){
		$instance = $old;
		$instance['ranks_num'] = strip_tags($new['ranks_num']);
		return $new;
	}

	function form($instance){
		$title = esc_attr($instance['title']);
		$num = absint($instance['ranks_num']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题：','um'); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('ranks_num'); ?>"><?php _e('数量：','um'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('ranks_num'); ?>" name="<?php echo $this->get_field_name('ranks_num'); ?>" type="text"  value="<?php echo $num; ?>" /></p>
	<?php
	}
}
/*  Register widget
/* ------------------------------------ */
/* if ( ! function_exists( 'um_register_widget_creditsrank' ) ) {

	function um_register_widget_creditsrank() { 
		register_widget( 'umcreditsrank' );
	}	
}
add_action( 'widgets_init', 'um_register_widget_creditsrank' ); */
