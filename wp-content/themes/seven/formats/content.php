<?php
    $thumb = zrz_get_post_thumb();
    $cap_icon = zrz_get_post_cap();
 ?><div class="pos-r pd10 post-list box mar10-b <?php echo zrz_get_theme_style() === 'pinterest' ? 'content-card grid-item' : 'content'; ?>">
    <div class="pos-r cart-list">
            <?php if($thumb) { ?>
                <div class="thumb pos-r">
                    <?php echo $cap_icon; ?>
                    <div style="background-image:url('<?php echo zrz_get_thumb($thumb,300,200); ?>')" class="preview thumb-in"></div>
                    <a <?php echo zrz_open_new() ? 'target="_blank"' : ''; ?> href="<?php echo get_permalink(); ?>" class="link-block"></a>
                </div>
            <?php }else{ ?>
                <div class="thumb text-thumb pos-r">
                    <?php echo $cap_icon;?>
                    <div class="preview thumb-in">
                        <span class="lm"><?php echo zrz_get_content_ex(); ?></span>
                    </div>
                    <a <?php echo zrz_open_new() ? 'target="_blank"' : ''; ?> href="<?php echo get_permalink(); ?>" class="link-block"></a>
                </div>
            <?php } ?>
        <div class="post-info pos-r pd10 <?php echo $thumb ? 'post-side' : 'post-none'; ?>">
            <div class="post-header pos-r mar10-b fs13">
                <span class="pos-a">
                    <a href="<?php echo zrz_get_user_page_url(); ?>"><?php echo get_avatar(get_the_author_meta( 'ID' ),'50'); ?></a>
                </span>
                <?php echo zrz_get_user_page_link().ZRZ_THEME_DOT.'<span class="gray">'.zrz_time_ago().'</span>'; ?>
            </div>
            <?php if(get_the_title()){ ?>
                <h2 class="entry-title"><?php the_title( '<a '.(zrz_open_new() ? 'target="_blank"' : '').' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?></h2>
            <?php }else{ ?>
                <h2 class="entry-title"><a href="<?php echo get_permalink(); ?>"><span class="red">*</span> 此文暂未设置标题</a></h2>
            <?php } ?>
            <div class="mar10-b post-ex mar10-t mobile-hide"><?php echo zrz_get_content_ex(); ?></div>
            <?php echo zrz_post_meta(); ?>
        </div>
    </div>
</div>
