<?php
//财富排行
get_header();
?>
<div id="primary" class="content-area fd">
	<main id="top" class="site-main top-page pos-r" role="main" ref="gold">
        <article class="box page-top">
            <div class="b-b pd10 clearfix bg-d">
                <span class="gray fs12">财富排行<span class="dot">❯</span><?php echo date("Y-m-d"); ?>
            </div>
            <ul class="top-list">
                <?php
                    $user_fields = array( 'ID','display_name', 'user_url' );
                    $args = array(
                        'number' => 15,
                        'fields' => $user_fields,
                        'order' => 'DESC',
                        'meta_key' => 'zrz_credit_total',
                        'orderby'   => 'meta_value_num',
                    );

                    $user_query = new WP_User_Query( $args );
                    $i=0;
                    if ( ! empty( $user_query->results ) ) {
                        foreach ( $user_query->results as $user ) { $i++; ?>
                            <li class="pd10 b-b pos-r clearfix">
                                <div class="top-avatar pos-a">
                                    <a href="<?php echo zrz_get_user_page_url($user->ID,true); ?>"><?php echo get_avatar( $user->ID , '42' ); ?></a>
                                </div>
                                <div class="top-info">
                                    <h2 class="mar10-b"><span class="gray"><?php echo $i; ?>. </span> <?php echo zrz_get_user_page_link($user->ID); ?></h2>
                                    <?php
                                    $des = get_user_meta( $user->ID, 'description', true);
                                    if($des){
                                        echo '<div class="gray pd10 bor-3 mar10-b bg-blue-light fd fs13">'.mb_strimwidth(strip_tags($des), 0, 150 ,"...").'</div>';
                                    }?>

                                    <?php if($user->user_url){
                                        echo '<div class="gray mar10-b fs12"><a target="_blank" class="top-url" href="'.$user->user_url.'">'.$user->user_url.'</a></div>';
                                    }?>
                                    <span class="gray fs12" style="display:block">第 <?php echo $user->ID; ?> 号会员</span>
                                </div>
                                <div class="top-gold pos-a">
                                    <?php echo zrz_coin($user->ID); ?>
                                </div>
                            </li>
                <?php
                        }
                    } else {
                        echo '<div class="loading-dom pos-r">
                				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">没有排名数据</p></div>
                			</div>';
                    }
                ?>
            </ul>
            <div class="pd10 gray fs12 t-c bg-light">前15名</div>
        </article>
    </main>
</div><?php
get_sidebar();
get_footer();
