<?php
    $post_id = get_the_id();
    $thumb = get_the_post_thumbnail_url($post_id,'full');

    //商品图片
    $images = get_post_meta($post_id,'zrz_shop_images',true);
    $imgs = array();
    if(is_array($images) && !empty($images)){
        foreach ($images as $val) {
            $src = wp_get_attachment_image_src($val,'full');
            $imgs[] = $src[0];
        }
    }else{
        $imgs[] = $thumb;
    }
    $user_id = get_current_user_id();
    //所需积分
    $credit = zrz_get_shop_lottery($post_id,'credit');

    //商品属性
    $attributes = get_post_meta($post_id,'zrz_shop_attributes',true);

    //当前用户的积分
    $user_credit = (int)zrz_coin($user_id,'nub');

    //当前用户所在的用户组
    $user_lv = zrz_get_lv($user_id,'');

    //中奖概率
    $probability = zrz_get_shop_lottery($post_id,'probability');

    //允许抽奖的用户组
    $capabilities = zrz_get_shop_lottery($post_id,'capabilities');
    $lv = '';

    //商品剩余
    $remaining = zrz_shop_count_remaining($post_id);
    $remaining = $remaining > 0 ? true : false;

    //已经抽奖的用户
    $users = get_post_meta($post_id,'zrz_buy_user',true);

    //是虚拟物品还是实物
    $commodity = get_post_meta($post_id, 'zrz_shop_commodity', true);
    $commodity = $commodity ? 1 : 0;
    $buyed = false;

    if(!empty($capabilities)){
        foreach ($capabilities as $value) {
            $lv .= '<span class="user-lv '.$value.' mar10-l"><i class="zrz-icon-font-'.$value.' iconfont"></i></span>';
        }
    }else{
        $lv = __('全体用户','ziranzhi2');
    }

?>
<div id="shop-single" class="box pd10 shop-single-head pos-r">
    <div class="shop-image fd">
        <div class="shop-img-big img-bg" style="background-image:url(<?php echo zrz_get_thumb($imgs[0],332,332); ?>)" ref="big" ></div>
        <div class="shop-image-small-list">
            <?php foreach ($imgs as $key => $val){
                echo '<div class="shop-img-small mar10-t"><img src="'.zrz_get_thumb($val,332,332).'" @mouseOver="mouseOver($event)" @mouseOut="mouseOut($event)"/></div>';
             } ?>
        </div>
    </div><div class="shop-info fd">
        <div class="shop-info-in pd20 lottery-form">
            <h1><?php echo get_the_title(); ?></h1>
            <div class="pd10-t mar10-t b-t">
                <p class="shop-single-credit fs12"><?php echo __('商品类型','ziranzhi2').'：<span class="l0">'.($commodity ? '实物' : '虚拟物品').'</span>'; ?></p>
            </div>
            <div class="pd10-t mar10-t b-t">
                <div class="shop-single-credit fs12"><?php _e('所需积分：','ziranzhi2'); ?><?php echo zrz_coin(0,0,$credit); ?></div>
            </div>
            <div class="pd10-t mar10-t b-t">
                <div class="shop-single-credit fs12"><?php _e('允许用户：','ziranzhi2'); ?><?php echo $lv; ?></div>
            </div>
            <div class="pd10-t mar10-t b-t shop-count">
                <p class="mar10-b"><?php echo __('总&nbsp;&nbsp;数','ziranzhi2').'：<span class="l0">'.zrz_get_shop_count($post_id,'total').'</span>'; ?></p>
                <p><?php echo __('已抽中','ziranzhi2').'：<span class="l0">'.zrz_get_shop_count($post_id,'sell').'</span>'; ?></p>
            </div>
            <div class="lottery-p pd10-t mar10-t b-t pos-r t-c"><span class="lottery-num fd" v-text="rand1">0</span><span class="fd" v-text="dy">=</span><span class="lottery-num fd" v-text="rand2">0</span></div>
            <div class="pd20-t mar10-t b-t pos-r">
                <?php if(!in_array($user_lv,$capabilities) && is_user_logged_in()){ ?>
                    <button class="shop-buy-button disabled">权限不足</button>
                <?php }elseif($user_credit < $credit && is_user_logged_in()){ ?>
                    <button class="shop-buy-button disabled">积分不足</button>
                <?php }elseif(!$remaining){ ?>
                    <button class="shop-buy-button disabled">抽奖已结束</button>
                <?php }else{ ?>
                    <button :class="['shop-buy-button','mar20-r',{'disabled':locked}]" @click.stop="lottery">立刻抽奖</button> <span class="fs12" v-html="lotteryMsg"></span>
                <?php } ?>

                <button class="pos-a shop-buy-love text" @click.stop="favorites()"><i :class="['iconfont' ,favorited.loved ? 'zrz-icon-font-collect' : 'zrz-icon-font-shoucang2']"></i> <span v-text="favorited.count"></span> 收藏</button>
            </div>
        </div>
    </div>
    <div class="post-views pos-a l1"><i class="iconfont zrz-icon-font-fire1"></i><span ref="postViews">0</span>°</div>
</div>
<?php
    //中奖用户
    $users = get_post_meta($post_id,'zrz_buy_user',true);
    if(is_array($users) && !empty($users)){
?>
    <div class="mar10-b pos-r fs12 buyed pd10 bg-b mar10-t">
        <p class="l0">中奖用户：</p>
        <ul class="mar10-t">
            <?php
                $buyed = false;
                $users = array_reverse($users);
                if(in_array($user_id,$users)){
                    $buyed = true;
                }
                $i = 0;
                foreach ($users as $user) {
                    echo '<li><a href="'.zrz_get_user_page_url($user).'">'.get_avatar($user,'25').'</a></li>';
                    $i ++;
                    if($i == 25) break;
                }
            ?>
        </ul>
    </div>
<?php
    }
    if($buyed && $commodity == 0){ ?>
    <div class="mar10-t box">
    <div class="pd10 b-b fs12 bg-blue-light red"><?php _e('您已中奖，中奖结果：','ziranzhi2'); ?></div>
        <?php
            $type = zrz_get_shop_virtual($post_id,'type');
            $title = zrz_get_shop_virtual($post_id,'title');
            $content = zrz_get_shop_virtual($post_id,'content');
        ?>
        <div class="pd10">
            <?php if($content){ ?>
            <h2 class="mar10-b"><?php echo $title; ?></h2>
            <div class="exchange-content fs14">
                <?php
                    if($type == 1){
                        echo $content;
                    }else{
                        echo $content;
                    }
                ?>
            </div>
            <?php }else{ ?>
                <div class="pd10 fs14">
                    没有内容，请联系管理员
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php if(is_array($attributes) && !empty($attributes)){ ?>
    <div class="box shop-single-sx mar10-t">
        <div class="pd10 b-b fs12 gray"><?php _e('商品属性','ziranzhi2'); ?></div>
        <div class="pd20">
            <ul>
            <?php
                foreach ($attributes as $val) {
                    if($val['title'] && $val['track']){
                        echo '<li class="shop-attributes-itme fd">
                            <span class="shop-attributes-title">'.$val['title'].'：</span>
                            <span class="shop-attributes-val">'.$val['track'].'</span>
                        </li>';
                    }
                }
            ?>
            </ul>
        </div>
    </div>
<?php } ?>
<div class="box shop-single-content mar10-t">
    <div class="pd10 b-b fs12 gray"><?php _e('商品详情','ziranzhi2'); ?></div>
    <div id="entry-content" class="entry-content pd20">
        <?php $exc = get_post_field('post_excerpt', $post_id); if($exc){ ?>
        <div class="post-excerpt mar30-b t-c pos-r">
            <?php echo $exc; ?>
        </div>
        <?php } ?>
        <?php the_content(); ?>
    </div>
</div>
