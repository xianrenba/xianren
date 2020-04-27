<div id="primary" class="content-area" style="width:100%">
    <main id="main" class="site-main labs-single">

    <?php
        while ( have_posts() ) : the_post();
        $thumb = zrz_get_post_thumb();
        $user_id = get_the_author_meta( 'ID' );
        $user_name = get_the_author_meta('display_name');
        $lv = ZRZ_THEME_DOT.zrz_get_lv($user_id,'lv');
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
        <div id="youguess-entry-content" class="entry-content youguess-entry-content" v-cloak>
            <?php echo zrz_post_edit_button(); ?>
            <ul>
                <transition>
                    <li class="single-guess-list pos-r" v-for="(val,index) in list" v-if="thisIndex == index">
                        <div class="guess-current t-c gray pd10 b-b" v-text="index+1+'/'+list.length"></div>
                        <div class="single-guess-list-in">
                            <div class="guessQ t-c pd10" v-text="val.q"></div>
                            <div class="guess-list-resout t-c">
                                &nbsp;<span class="green" v-show="choseLocked && right">正确</span><span class="red" v-show="choseLocked && !right">错误</span>
                            </div>
                            <ul>
                                <li v-for="(l,key) in val.l" class="fd t-c pd20 mouh" @click.stop="picked(key,index)">
                                    <div class="guess-li-in">
                                        <img class="w100" :src="path+l.i" />
                                        <h2 class="pd10 bg-blue-light" v-text="l.t"></h2>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <div class="guess-resout" v-else-if="thisIndex === list.length">
                        <div class="guess-resout-title pd20 t-c" v-text="list.length+' 道题答对了 '+rightCount+'题'"></div>
                        <div class="t-c pd20" v-if="rightCount/list.length <= 0.33">
                            <img :src="path+resout['33'].i" />
                            <p v-text="resout['33'].t"></p>
                        </div>
                        <div class="t-c pd20" v-else-if="rightCount/list.length > 0.33 && rightCount/list.length <= 0.66">
                            <img :src="path+resout['66'].i" />
                            <p v-text="resout['66'].t"></p>
                        </div>
                        <div class="t-c pd20" v-else-if="rightCount/list.length > 0.66 && rightCount/list.length <= 1">
                            <img :src="path+resout['99'].i" />
                            <p v-text="resout['99'].t"></p>
                        </div>
                        <div class="t-c mar10-t b-t pd10"><button @click="retry">再来一次</button></div>
                    </div>
                </transition>
            </ul>
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
