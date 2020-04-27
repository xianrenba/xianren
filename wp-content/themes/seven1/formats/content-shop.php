<?php
    $thumb = zrz_get_post_thumb();
    $post_id = get_the_id();
    $type = get_post_meta($post_id,'zrz_shop_type',true);
    $user_id = get_current_user_id();
    $user_credit = (int)zrz_coin($user_id,'nub');
    $login = is_user_logged_in();
    //剩余数量
    $remaining = zrz_shop_count_remaining($post_id);
    $remaining = $remaining > 0 ? true : false;
    $views = get_post_meta($post_id,'views',true);
    $views = $views ? $views : 0;

    $open_new = zrz_open_new();
    $link = get_permalink();

    $credit = get_post_meta($post_id,'zrz_shop_need_credit',true);
    $price = zrz_get_shop_price_dom($post_id);
 ?>
 <div ref="postList" class="pos-r pd10 box post-list <?php echo zrz_get_theme_style() === 'pinterest' ? 'content-card grid-item' : 'content mar10-b'; ?>">
    <div class="pos-r cart-list">

        <a <?php echo $open_new ? 'target="_blank"' : ''; ?> href="<?php echo $link; ?>">
            <div class="thumb">
                <?php echo $price['msg']; ?>
                <div style="background-image:url('<?php echo zrz_get_thumb($thumb,300,200); ?>')" class="preview thumb-in"></div>
            </div>
        </a>
        <div class="post-info pos-r pd10-l <?php echo $thumb ? 'post-side' : ''; ?>">
            <h2 class="entry-title"><?php the_title( '<a '.($open_new ? 'target="_blank"' : '').' href="' . esc_url( $link ) . '" rel="bookmark">', '</a>' ); ?></h2>
            <?php
                if($type == 'exchange'){
                    echo '<div class="shop-buy shop-center t-c clearfix mar10-t mar10-b pd10-b pd10-t">
                        '.zrz_coin(0,0,$credit).'<div class="fr">'.($remaining ?
                            (($user_credit < $credit && $login) ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button empty disabled" style="margin:0">积分不足</a>' : '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button empty" style="margin:0">兑换</a>')
                             : '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button empty disabled" style="margin:0">兑换结束</a>').'</div>
                    </div>
                    <div class="shop-normal-meta fs12 gray clearfix">
                        <div class="fl">
                            <span>总数：<b>'.zrz_get_shop_count($post_id,'total').'</b> <span class="dot"></span>已兑：<b>'.zrz_get_shop_count($post_id,'sell').'</b></span>
                        </div>
                        <div class="fr mobile-hide">
                        人气：<b>'.$views.'</b>
                        </div>
                    </div>
                    ';
                }elseif($type == 'normal'){
                    echo '<div class="shop-buy clearfix mar10-t mar10-b pd10-b pd10-t">
                        <div class="fl">
                            '.$price['dom'].'
                        </div>
                        <div class="fr">
                            '.($remaining ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button" style="margin:0">购买</a>' : '<span class="button empty">售罄</span>').'
                        </div>
                    </div>
                    <div class="shop-normal-meta fs12 gray clearfix">
                        <div class="fl">
                            <span>总数：<b>'.zrz_get_shop_count($post_id,'total').'</b> <span class="dot"></span>已售：<b>'.zrz_get_shop_count($post_id,'sell').'</b></span>
                        </div>
                        <div class="fr mobile-hide">
                        人气：<b>'.$views.'</b>
                        </div>
                    </div>';
                }elseif($type == 'lottery'){
                    $capabilities = zrz_get_shop_lottery($post_id,'capabilities');
                    $credit = (int)zrz_get_shop_lottery($post_id,'credit');
                    $user_lv = zrz_get_lv($user_id,'');
                    echo '<div class="shop-buy shop-center t-c clearfix mar10-t mar10-b pd10-b pd10-t">
                        '.zrz_coin(0,0,$credit).'<div class="fr">'.((!in_array($user_lv,$capabilities) && $login) ? '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button empty disabled" style="margin:0">权限不足</a>' : ($remaining ? (($user_credit < $credit && $login) ? '<a href="'.$link.'" class="button empty disabled" style="margin:0">积分不足</a>' : '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button empty" style="margin:0">抽奖</a>') : '<a '.($open_new ? 'target="_blank"' : '').' href="'.$link.'" class="button empty disabled" style="margin:0">抽奖结束</a>')).'</div>
                    </div>
                    <div class="shop-normal-meta fs12 gray clearfix">
                        <div class="fl">
                            <span>总数：<b>'.zrz_get_shop_count($post_id,'total').'</b><span class="dot"></span> 已被领走：<b>'.zrz_get_shop_count($post_id,'sell').'</b></span>
                        </div>
                        <div class="fr mobile-hide">
                        人气：<b>'.$views.'</b>
                        </div>
                    </div>';
                }
            ?>
        </div>
    </div>
</div>
