<?php
    $thumb = zrz_get_post_thumb();
    $cap = get_post_meta(get_the_id(),'capabilities',true);
    $cap_icon = '';
    if(isset($cap['key']) && isset($cap['val'])){
        switch ($cap['key']) {
            case 'rmb':
                $cap_icon = '<i class="iconfont zrz-icon-font-fufeilianjie"></i>';
                break;
            case 'credit':
                $cap_icon = '<i class="iconfont zrz-icon-font-credit"></i>';
                break;
            case 'lv':
                $cap_icon = '<i class="iconfont zrz-icon-font-quanxian"></i>';
                break;
            case 'login':
                $cap_icon = '<i class="iconfont zrz-icon-font-denglu"></i>';
                break;
            default:
                $cap_icon = '';
                break;
        }
    }
 ?><div class="pos-r pd10 box mar10-b formats-status post-list <?php echo zrz_get_theme_style() === 'pinterest' ? 'content-card grid-item' : 'content'; ?>">
    <div class="pos-r cart-list">
            <?php if($thumb) { ?>
                <div class="thumb">
                    <?php echo '<span class="pos-a post-cap fs12 shadow">'.$cap_icon.'</span>'; ?>
                    <div style="background-image:url('<?php echo zrz_get_thumb($thumb,831,324); ?>')" class="preview thumb-in"></div>
                    <a <?php echo zrz_open_new() ? 'target="_blank"' : ''; ?> href="<?php echo get_permalink(); ?>" class="link-block"></a>
                </div>
            <?php }else{ ?>
                <div class="thumb text-thumb">
                    <?php echo '<span class="pos-a post-cap fs12 shadow">'.$cap_icon.'</span>'; ?>
                    <div class="preview thumb-in">
                        <span class="lm"><?php echo zrz_get_content_ex(); ?></span>
                    </div>
                    <a <?php echo zrz_open_new() ? 'target="_blank"' : ''; ?> href="<?php echo get_permalink(); ?>" class="link-block"></a>
                </div>
            <?php } ?>

        <div class="post-info pos-r pd10 <?php echo $thumb ? 'post-side' : ''; ?>">
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
            <?php echo zrz_post_meta(); ?>
        </div>
    </div>
</div>
