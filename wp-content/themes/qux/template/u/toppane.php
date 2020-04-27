
<!-- 作者头像及背景图 -->
<section class="billboard author-header mb20" style="background-image: url('<?php if(get_user_meta($curauth->ID,'um_cover',true)) echo get_user_meta($curauth->ID,'um_cover',true); else echo UM_URI.'/img/cover/1-full.jpg'; ?>')">
    <div class="container text-center">
        <div class="avatar-wrap"><?php echo um_get_avatar( $curauth->ID , '80' , um_get_avatar_type($curauth->ID) ); ?></div>
        <h2><?php echo $curauth->display_name;  ?><?php echo um_member_icon($curauth->ID) ?></h2><!-- TODO vip gender icon -->
        <p class="author-bio" title="<?php echo $user_info->description; ?>"><?php echo  $user_info->description ? $user_info->description : "这家伙很懒，什么简介都没有写.....";  ?></p>
        <?php if(/*  is_user_logged_in() ||  */$current_user->ID != $curauth->ID ) { ?>
        <div class="author-interact">
            <?php echo um_follow_button($curauth->ID);  ?>
            <a class="pm-btn" href="<?php echo add_query_arg('tab', 'message', get_author_posts_url( $curauth->ID )); ?>" title="发送私信"><i class="fa fa-envelope"></i> 私信</a>
        </div>
        <?php } ?>
    </div>
</section>