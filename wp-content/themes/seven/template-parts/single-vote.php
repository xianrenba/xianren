<div id="primary" class="content-area" style="width:100%">
    <main id="main" class="site-main labs-single">

    <?php
        while ( have_posts() ) : the_post();
        $thumb = zrz_get_post_thumb();
        $user_id = get_the_author_meta( 'ID' );
        $user_name = get_the_author_meta('display_name');
        $lv = ZRZ_THEME_DOT.zrz_get_lv($user_id,'lv');
        $votelist = get_post_meta(get_the_id(),'zrz_vote_list',true);
        $media_path = zrz_get_media_path().'/';
    ?>

    <article id="labs-single" class="box">
        <header class="entry-header pos-r">
            <div class="single-post-meta pos-a fs13 mar10-b w100 pd20">
                <a href="<?php echo zrz_get_user_page_url(); ?>">
                    <?php echo get_avatar($user_id,'22'); ?>
                    <span><?php echo $user_name.$lv ?></span>
                </a>
                <?php
                        echo zrz_time_ago();
                ?>
            </div>
            <?php if($thumb){ ?>
                <div class="img-bg pos-a" style="background-image:url('<?php echo zrz_get_thumb($thumb,zrz_get_theme_settings('page_width'),450); ?>')"></div>
            <?php } ?>
            <div class="single-header pd20 w100">
                <p class="mar10-b"><?php echo zrz_get_first_category(); ?></p>
                <?php
                    if ( is_singular() ) :
                        the_title( '<h1 class="entry-title shadow" ref="postTitle">', '</h1>' );
                    else :
                        the_title( '<h2 class="entry-title shadow" ref="postTitle"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                    endif;
                ?>
                <div class="mar10-t gray"><?php the_content(); ?></div>
            </div>
        </header><!-- .entry-header -->

        <div id="vote-entry-content" class="entry-content">
            <?php echo zrz_post_edit_button(); ?>
            <ul ref="listNone">
            <?php foreach ($votelist as $key => $val) { ?><li class="fd single-vote-list pos-r mouh"><div class="single-vote-list-in">
                <div class="bg-blue-light">

                    <h2 class="pd10"><?php echo $val['t']; ?></h2>
                </div>
            </div></li><?php } ?>
            </ul>
            <ul v-if="!showResout">
                <li class="fd single-vote-list pos-r mouh" v-for="(val,index) in list" @click.stop="picked(index)">
                    <div class="single-vote-list-in">
                        <div class="bg-blue-light pos-r">
                            <div :class="val.c ? 'show' : 'hide'">
                                <i class="iconfont zrz-icon-font-29 lm"></i>
                            </div>
                            <img class="w100" :src="val['i']" />
                            <h2 class="pd10" v-text="val['t']"></h2>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="vote-resout-page pd20" v-else v-cloak>
                <h2 class="t-c"><?php _e('大家的态度','ziranzhi2'); ?></h2>
                <ul class="mar20-t">
                    <li v-for="(val,index) in list" class="fd vote-list-resout pd20">
                        <div class="vote-por pos-r">
                            <span :class="val.c ? 'red' : ''" v-text="(parseInt(val.p)/parseInt(total)*100).toFixed(1)+'%'"></span>
                        </div>
                        <p v-text="val.t"></p>
                    </li>
                </ul>
            </div>
            <div class="t-c mar10-t b-t pd10"><button :class="['mar10-t',{'empty':voteCount == 0},{'disabled':voteCount < 1}]" @click="resout(showResout ? 'pre' : 'next')" v-text="showResout ? '返回' : '查看结果'">查看结果</button></div>
        </div><!-- .entry-content -->
    </article><!-- #post-<?php the_ID(); ?> -->


    <?php
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;

    endwhile; // End of the loop.
    ?>

    </main><!-- #main -->
</div><!-- #primary --><?php
get_footer();
