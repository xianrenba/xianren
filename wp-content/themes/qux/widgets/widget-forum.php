<?php

class widget_ui_forum extends WP_Widget {


	function __construct(){
		parent::__construct( 'widget_ui_forum', 'qux 问答列表', array( 'classname' => 'widget_ui_forum' ) );
	}

    public function widget( $args, $instance ) {
        $title = $instance['title'];
        $orderby_id = empty( $instance['orderby'] ) ? 0 :  $instance['orderby'];
        $number = empty( $instance['number'] ) ? 10 : absint( $instance['number'] );

        $orderby = 'date';
        if($orderby_id==1){
            $orderby = 'comment_count';
        }else if($orderby_id==2){
            $orderby = 'meta_value_num';
        }else if($orderby_id==3){
            $orderby = 'rand';
        }

        $parg = array(
            'showposts' => $number,
            'orderby' => $orderby,
            'post_type' => 'forum'
        );
        if($orderby=='meta_value_num') $parg['meta_key'] = 'views';

        $posts = new WP_Query( $parg );

        echo $args['before_widget'];

        if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ( $posts->have_posts() ) : ?>
            <ul>
                <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
                <li>
                    <a target="_blank" href="<?php the_permalink();?>" title="<?php echo esc_attr(get_the_title());?>">
                        <?php the_title(); ?>
                    </a>
                </li>
                <?php endwhile; wp_reset_postdata();?>
            </ul>
        <?php
        else:
            echo '<p style="color:#999;font-size: 12px;text-align: center;padding: 10px 0;margin:0;">暂无内容</p>';
        endif;

        echo $args['after_widget'];
    }

    function update( $new_instance, $instance ) {
        $instance['title'] = $new_instance['title'];
        $instance['number'] = $new_instance['number'];
        $instance['orderby'] = $new_instance['orderby'];
        return $instance;
    }

    function form( $instance ) {
        $title = isset($instance['title']) && $instance['title'] ? $instance['title'] :  '';
        $number = isset($instance['number']) && $instance['number'] ? $instance['number'] :  '10';
        $orderby = isset($instance['orderby']) && $instance['orderby'] ? $instance['orderby'] :  '0';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">标题：</label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">显示数量：</label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">排序：</label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
                <option value="0" <?php selected( 0, $orderby ); ?>>发布时间</option>
                <option value="1" <?php selected( 1, $orderby ); ?>>回答数量</option>
                <option value="2" <?php selected( 2, $orderby ); ?>>浏览数</option>
                <option value="3" <?php selected( 3, $orderby ); ?>>随机排序</option>
            </select>
        </p>
    <?php
    }
}