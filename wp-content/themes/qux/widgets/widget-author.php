<?php
class widget_ui_author extends WP_Widget {

   /*  function Authorinfo() {
        $widget_ops = array('classname' => 'widget_ui_authorinfo', 'description' => '显示当前文章的作者信息！');
        $this->WP_Widget('Authorinfo', '本文作者', $widget_ops);
    } */

   /*  function update($new_instance, $old_instance) {
        return $new_instance;
    } */
    function __construct(){
		parent::__construct( 'widget_ui_author', 'qux 文本作者', array( 'classname' => 'widget_ui_author' ) );
	}
	
    function widget($args, $instance) {
        extract( $args );
        echo $before_widget;
        echo widget_authorinfo();
        //echo um_author_info_module();
        echo $after_widget;
    }
}
function widget_authorinfo(){ 
	//$user_id = get_post($id)->post_author;
	$user_id  =  get_the_author_meta( 'ID' );
	if(user_can($user_id,'install_plugins')) {
        $user_group = '管理员';
    }elseif(user_can($user_id,'edit_others_posts')) {
        $user_group = '编辑';
    }elseif(user_can($user_id,'publish_posts')) {
        $user_group = '作者';
    } elseif(user_can($user_id,'delete_posts')) {
        $user_group = '投稿者';
    }elseif(user_can($user_id,'read')) {
        $user_group = '订阅者';
    }

?>
<div class="widget-content">
    <a class="author-card_bg" href="<?php echo get_author_posts_url( $user_id ) ?>" style="background-image: url(<?php echo _get_user_cover($user_id ,'small',UM_URI . '/img/author-banner.png'); ?>)" tabindex="-1"></a>
    <div class="author-card_content">
		<a class="author_avatar-link" href="<?php echo get_author_posts_url( $user_id ); ?>"><?php echo um_get_avatar($user_id , '40' , um_get_avatar_type($user_id)); ?></a>
        <div class="author-fields">
            <span class="author-name"><?php the_author_posts_link(); ?> </span>
            <span class="author-user_level"><?php echo $user_group; ?></span>
        </div>
        <?php if(_hui('open_ucenter') && get_current_user_id() != $user_id){ ?>
        <div class="author-interact">
            <?php  echo  um_follow_button($user_id),'<small class="pm"><a href="'.add_query_arg('tab', 'message', get_author_posts_url( $user_id )).'"><i class="fa fa-envelope"></i>'.__('私信TA','um').'</a></small>'; ?>
        </div>
        <?php } ?>
        <div class="author_post">
        <div class="title">
            <h4>最新文章</h4>
            <span><?php echo sprintf(__('共 %s 篇','um'),count_user_posts($user_id, 'post')); ?></span>
        </div>
        <ul>
            <?php 
			$args=array( 'author'=> $user_id,'post_type' => 'post','post_status' => 'publish','posts_per_page' => 5,'ignore_sticky_posts'=> 1);
			$my_query = null;
			$my_query = new WP_Query($args);
			if( $my_query->have_posts() ) : while ($my_query->have_posts()) : $my_query->the_post(); ?>
            <li>
                <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" class="imglayout"><?php the_title(); ?></a>
            </li>
            <?php endwhile;endif;wp_reset_query(); ?>
        </ul>
        </div>
        <div class="author-stats">
            <span class="posts"><?php echo count_user_posts($user_id, 'post'); ?><span class="unit">文章</span></span>            
            <span class="stars"><?php echo author_post_field_count('post',$user_id,'um_post_likes'); ?><span class="unit">点赞</span></span>          
            <span class="following"><?php echo um_following_count($user_id); ?><span class="unit">关注</span></span>
            <span class="followers"><?php echo um_follower_count($user_id); ?><span class="unit">粉丝</span></span>
        </div>
    </div>
</div>    
<?php
}
?>