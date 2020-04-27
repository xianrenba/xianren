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

    $credit = (int)get_post_meta($post_id,'zrz_shop_need_credit',true);

    //当前用户的积分
    $current_user_id = get_current_user_id();
    $user_credit = (int)zrz_coin($current_user_id,'nub');
    $attributes = get_post_meta($post_id,'zrz_shop_attributes',true);

    //已经兑换的用户
    $users = get_post_meta($post_id,'zrz_buy_user',true);

    //是虚拟物品还是实物
    $commodity = get_post_meta($post_id, 'zrz_shop_commodity', true);
    $commodity = $commodity ? 1 : 0;
    $buyed = false;

    $title = get_the_title();
    wp_localize_script( 'ziranzhi2-shop', 'zrz_shop_data',array(
        'title'=>'<a href="'.get_permalink().'" target="_blank"><img src="'.zrz_get_thumb($thumb,100,100).'" /><span>'.$title.'</span></a>',
        'type'=>'d',
        'commodity'=>$commodity,
        'price'=> $credit,
    ));

?>
<div id="shop-single" class="box pd10 shop-single-head pos-r">
    <div class="shop-image fd">
        <div class="shop-img-big img-bg" style="background-image:url(<?php echo zrz_get_thumb($imgs[0],333,333); ?>)" ref="big" ></div>
        <div class="shop-image-small-list">
            <?php foreach ($imgs as $key => $val){
                echo '<div class="shop-img-small mar10-t"><img src="'.zrz_get_thumb($val,333,333).'" @mouseOver="mouseOver($event)" @mouseOut="mouseOut($event)"/></div>';
             } ?>
        </div>
    </div><div class="shop-info fd">
        <div class="shop-info-in pd20">
            <h1><?php echo $title; ?></h1>
            <div class="pd10-t mar10-t b-t shop-count">
                <p><?php echo __('商品类型','ziranzhi2').'：<span class="l0">'.($commodity ? '实物' : '虚拟物品').'</span>'; ?></p>
            </div>
            <div class="pd10-t mar10-t b-t">
                <div class="shop-single-credit fs12"><?php _e('所需积分：','ziranzhi2'); ?><?php echo zrz_coin(0,0,$credit); ?></div>
            </div>
            <div class="pd10-t mar10-t b-t shop-count">
                <p class="mar10-b"><?php echo __('总&nbsp;&nbsp;数','ziranzhi2').'：<span class="l0">'.zrz_get_shop_count($post_id,'total').'</span>'; ?></p>
                <p><?php echo __('已兑换','ziranzhi2').'：<span class="l0">'.zrz_get_shop_count($post_id,'sell').'</span>'; ?></p>
            </div>
            <?php if($commodity == 1){ ?>
                <div class="pd10-t mar10-t b-t shop-count">
                    <div class="fd shop-count-title"><?php _e('兑换数量：','ziranzhi2'); ?></div>
                    <div class="shop-buy-count">
                        <button class="mouh empty" @click="less"><i class="iconfont zrz-icon-font-jiajianchengchu-"></i></button>
                        <span class="shop-buy-count-input"><input type="number" value="0" v-model="count"></span>
                        <button class="mouh empty" @click="more"><i class="iconfont zrz-icon-font-jiajianchengchu-1"></i></button>
                    </div>
                </div>
            <?php } ?>
            <div class="pd20-t pd20-b mar10-t b-t pos-r">
                <?php if(zrz_shop_count_remaining($post_id) <= 0){ ?>
                    <button class="disabled shop-buy-button">兑换结束</button>
                <?php }elseif($user_credit < $credit && is_user_logged_in()){ ?>
                    <button class="disabled shop-buy-button">积分不足</button>
                <?php }else{ ?>
                    <button class="shop-buy-button" @click.stop="exchange">立刻兑换</button>
                    <button class="shop-buy-button empty" @click.stop="addToCart"><i class="iconfont zrz-icon-font-goumai"></i> <span v-text="addToCartText">加入购物车</span></button>
                <?php } ?>
                <button class="pos-a shop-buy-love text" @click.stop="favorites()"><i :class="['iconfont' ,favorited.loved ? 'zrz-icon-font-collect' : 'zrz-icon-font-shoucang2']"></i> <span v-text="favorited.count"></span> 收藏</button>
            </div>
            <?php if(is_array($users) && !empty($users)){ ?>
                <div class="mar10-b pos-r fs12 buyed pd10 bg-b">
                    <p class="l0">已经兑换用户：</p>
                    <ul class="mar10-t">
                        <?php
                            $users = array_reverse($users);
                            if(in_array($current_user_id,$users)){
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
            <?php } ?>
        </div>
    </div>
    <div class="post-views pos-a l1"><i class="iconfont zrz-icon-font-fire1"></i><span ref="postViews">0</span>°</div>
</div>
<?php if($buyed && $commodity == 0){ ?>
    <div class="mar10-t box">
    <div class="pd10 b-b fs12 bg-blue-light red"><?php _e('您已成功兑换，兑换结果：','ziranzhi2'); ?></div>
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
