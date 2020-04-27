<?php
    $views = get_post_meta(get_the_id(),'views',true);
    $views = '<span class="dot">•</span>'.( $views ? $views : '0').' 次点击';
    $user_id = get_post_field('post_author',get_the_id());
    $forum_id = wp_get_post_parent_id(get_the_id());
?>
<li <?php echo bbp_get_topic_class('','pos-r topic'); ?>>
   <div class="topic-list clearfix pd10 b-b">
       <div class="topic-list-left">
           <?php echo get_avatar($user_id,36); ?>
           <?php echo zrz_get_lv($user_id,'lv'); ?>
       </div>

       <div class="topic-user-meta mar10-b fs12 gray">
           <?php echo zrz_get_user_page_link($user_id); ?>
           <span class="dot">•</span>
           <?php echo zrz_time_ago(bbp_get_topic_last_active_id()); ?>
       </div>
       <div class="topic-list-center">

           <h2 class="topic-title mar10-b">
               <a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>
           </h2>

           <?php if(zrz_get_reading_settings('show_topic_thumb'))
               echo zrz_get_bbp_content_img();
           ?>
       </div>

       <div class="topic-list-right pos-a fs12">
           <a class="topic-list-forum mobile-hide" href="<?php echo bbp_get_forum_permalink( $forum_id ); ?>"><?php echo bbp_get_forum_title( $forum_id ); ?></a>
       </div>
   </div>
</li>
