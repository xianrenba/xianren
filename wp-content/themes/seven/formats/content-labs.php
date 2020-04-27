<?php
    $thumb = zrz_get_post_thumb();
    $join_count = get_post_meta(get_the_id(),'zrz_join_num',true);
    $join_count = $join_count ? $join_count : 0;
 ?>
 <div ref="labsList" class="labs-list l0 fd pos-r">
    <div class="pos-a">
        <div class="labs-info pos-a w100 preview thumb-in" <?php if($thumb) echo 'style="background-image:url('.zrz_get_thumb($thumb,565,370).')"'; ?>>
            <a class="link-block" href="<?php echo esc_url( get_permalink() ); ?>"></a>
            <div class="post-header pos-r mar10-b fs13 clearfix pd20">
                <?php echo get_labs_icon_7b2(); ?>
            </div>
            <div class="labs-join pos-a t-c pd10">
                <i class="zrz-icon-font-ziyuan3 iconfont"></i>
                <p class="fs14"><b><?php echo $join_count; ?></b></p>
                <p class="fs14 gray">人已参与</p>
            </div>
            <div class="labs-list-in pos-a white w100 pd20">
                <h2 class="entry-title pos-r shadow"><?php echo get_the_title(); ?></h2>
                <div class="post-ex mar10-t pos-r"><?php echo zrz_get_content_ex(); ?></div>
            </div>
        </div>
    </div>
</div>
