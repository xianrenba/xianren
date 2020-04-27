<?php
    $thumb = zrz_get_first_img(get_post_field('post_content',$post->ID),'all');
    $cap_icon = zrz_get_post_cap();
 ?><div class="pos-r formats-image box mar10-b post-list <?php echo zrz_get_theme_style() === 'pinterest' ? 'content-card grid-item' : 'content'; ?>">
    <div class="pos-r cart-list">
        <div class="post-info pos-r pd10">
            <div class="post-header pos-r mar10-b fs13">
                <span class="pos-a">
                    <a href="<?php echo zrz_get_user_page_url(); ?>"><?php echo get_avatar(get_the_author_meta( 'ID' ),'50'); ?></a>
                </span>
                <?php echo zrz_get_user_page_link().zrz_get_lv(get_the_author_meta( 'ID' ),'lv').ZRZ_THEME_DOT.'<span class="gray">'.zrz_time_ago().'</span>'; ?>
            </div>
            <?php if(get_the_title()){ ?>
                <h2 class="entry-title"><?php the_title( '<a '.(zrz_open_new() ? 'target="_blank"' : '').' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?></h2>
            <?php }else{ ?>
                <h2 class="entry-title"><a href="<?php echo get_permalink(); ?>"><span class="red">*</span> 此文暂未设置标题</a></h2>
            <?php } ?>
            <div class="mar10-b post-ex mar10-t mobile-hide"><?php echo zrz_get_content_ex(); ?></div>
            <?php if(!empty($thumb)) { ?>
                <div class="thumb-image pos-r mar10-b">
                    <div class="preview">
                        <?php
                            $i = 0;
                            foreach ($thumb as $key => $value) {
                                if($i >=4 ) break;
                                echo '<div class="fd thumb-list pos-r"><div class="thumb-in" style="background-image:url('.zrz_get_thumb($value,205,134).')"></div></div>';
                                $i++;
                            }
                        ?>
                        <a <?php echo zrz_open_new() ? 'target="_blank"' : ''; ?> href="<?php echo get_permalink(); ?>" class="link-block"></a>
                    </div>
                </div>
            <?php }else{ ?>
                <div class="thumb-image thumb text-thumb mar10-b">
                    <?php echo $cap_icon; ?>
                    <div class="preview thumb-in">
                        <span class="lm"><?php echo zrz_get_content_ex(); ?></span>
                    </div>
                    <a <?php echo zrz_open_new() ? 'target="_blank"' : ''; ?> href="<?php echo get_permalink(); ?>" class="link-block"></a>
                </div>
            <?php } ?>
            <?php echo zrz_post_meta(); ?>
        </div>
    </div>
</div>
