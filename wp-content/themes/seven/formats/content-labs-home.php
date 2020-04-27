<?php
    $thumb = zrz_get_post_thumb();
 ?><div ref="postList" class="pos-r pd10 post-list labs-list-home <?php echo zrz_get_theme_style() === 'pinterest' ? 'content-card grid-item' : 'content'; ?>">
    <div class="pos-r">
        <a href="<?php echo get_permalink(); ?>">
            <?php if($thumb) { ?>
                <div class="thumb">
                    <div style="background-image:url('<?php echo zrz_get_thumb($thumb,300,200); ?>')" class="preview thumb-in"></div>
                </div>
            <?php } ?>
        </a>
        <div class="post-info pos-r pd10 <?php echo $thumb ? 'post-side' : ''; ?>">
            <div class="post-header pos-r mar10-b fs13">
                <span class="pos-a">
                    <a href="<?php echo zrz_get_user_page_url(); ?>"><?php echo get_avatar(get_the_author_meta( 'ID' ),'24'); ?></a>
                </span>
                <?php echo zrz_get_user_page_link().zrz_get_lv(get_the_author_meta( 'ID' ),'lv').ZRZ_THEME_DOT.'<span class="gray">'.zrz_time_ago().'</span>'; ?>
            </div>
            <h2 class="entry-title"><?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?></h2>
            <div class="mar10-b post-ex mar10-t mobile-hide"><?php echo zrz_get_content_ex(); ?></div>
            <?php echo zrz_post_meta(); ?>
        </div>
    </div>
</div>
