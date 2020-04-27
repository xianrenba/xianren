<?php
global $wp_query;
$nub = get_option('posts_per_page');
$ipages = ceil( $wp_query->found_posts / $nub);
$user_id =  get_query_var('author');
$current_user = get_current_user_id();
if ( have_posts() ) :

    echo '<div id="user-posts"><div class="box-header pd10 b-b"><span v-text="uName"></span>的文章</div><div class="l-8 user-posts" ref="postList">';
    /* Start the Loop */
    while ( have_posts() ) : the_post();
        if($user_id == $current_user || current_user_can('delete_users')){
            get_template_part( 'formats/content','user-edit');
        }else{
            get_template_part( 'formats/content','user');
        }
    endwhile;

    echo '</div><page-nav class="b-t" nav-type="user-posts-'.$user_id.'" :paged="paged" :pages="'.$ipages.'" :show-type="\'p\'"></page-nav></div>';

else :

    echo '<div class="loading-dom pos-r"><div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有文章</p></div></div>';

endif;
