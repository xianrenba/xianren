<div id="primary" class="content-area fd">
    <main id="main" class="site-main labs-single">
    <article id="relay-single">
        <?php
            while ( have_posts() ) : the_post();
            $thumb = zrz_get_post_thumb();
            $user_id = get_the_author_meta( 'ID' );
            $user_name = get_the_author_meta('display_name');
            $lv = ZRZ_THEME_DOT.zrz_get_lv($user_id,'lv');
            $media_path = zrz_get_media_path().'/';
            $current_user_id = get_current_user_id();
            //获取当前用户的头像
            $user_data = new zrz_get_user_data($current_user_id,30);
    		$avatar = $user_data->get_avatar();
        ?>
        <header class="entry-header pos-r box">
            <div class="single-post-meta pos-a fs13 mar16-b w100 pd20">
                <a href="<?php echo zrz_get_user_page_url(); ?>">
                    <?php echo get_avatar($user_id,'22'); ?>
                    <span><?php echo $user_name.$lv ?></span>
                </a>
                <?php
                    echo zrz_time_ago();
                ?>
            </div>
            <?php if($thumb){ ?>
                <div class="img-bg pos-a" style="background-image:url('<?php echo zrz_get_thumb($thumb,ceil(zrz_get_theme_settings('page_width')*0.74751),375); ?>')"></div>
            <?php } ?>
            <div class="single-header pd20 w100">
                <?php
                    the_title( '<h1 class="entry-title shadow" ref="postTitle">', '</h1>' );
                ?>
                <div class="mar10-t gray"><?php the_content(); ?></div>
            </div>
        </header><!-- .entry-header -->
        <?php echo zrz_post_edit_button(); ?>
        <?php
        endwhile; // End of the loop.
        ?>
        <div class="mar16-t relay-box" ref="relayBox">
            <?php
                $nub = get_option('posts_per_page');
                $args = array(
                    'post_parent' => get_the_id(),
                    'post_type'   => 'labsc',
                    'posts_per_page' => $nub,
                    'post_status'=>'any',
                    'orderby'=>'date',
                    'order'=>'ASC'
                );
                $the_query = new WP_Query( $args );
                $ipages = ceil( $the_query->found_posts / $nub);
                // The Loop
                if ( $the_query->have_posts() ) {
                    $i = 0;
                    $html = '';
                	while ( $the_query->have_posts() ) {
                    	$the_query->the_post();
                        get_template_part( 'formats/content','relay');
                    	wp_reset_postdata();
                    }
                }
            ?>
        </div>
        <page-nav class="box" nav-type="relay" :paged="paged" :pages="<?php echo $ipages; ?>" :show-type="'p'"></page-nav>
        <?php if(is_user_logged_in()){ ?>
        <div class="box mar16-t">
            <div class="box-header pd10 b-b">接力</div>
            <div class="pd10">
                <div class="relay-title pos-r">
                    <input type="text" class="pd10 w100" placeholder="标题" v-model="title">
                </div>
                <div class="pd20 labs-image mar10-t mar10-b pos-r">
                    <div class="relay-img-list pos-a thumb-in" :style="'background-image:url(\''+img+'\')'"></div>
                    <label class="mouh">
                        <span class="lm t-c"><i class="zrz-icon-font-zhaoxiangji iconfont"></i><b v-text="uploadText"></b></span>
                        <input type="file" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event)" class="hide"/>
                    </label>
                </div>
                <textarea placeholder="描述" class="pd10 relay-des" v-model="des"></textarea>
                <div class="mar10-t t-r"><button class="" @click="submit($event)" data-name="<?php echo get_the_author_meta('display_name',$current_user_id); ?>" data-url="<?php echo zrz_get_user_page_url($current_user_id); ?>" data-avatar="<?php echo $avatar; ?>" v-text="text"></button></div>
            </div>
        </div>
        <input ref="nonce" type="hidden" value="<?php echo wp_create_nonce($current_user_id); ?>">
        <?php } ?>
    </article><!-- #post-<?php the_ID(); ?> -->
    <?php
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
    ?>
    </main><!-- #main -->
</div><!-- #primary --><?php
get_sidebar();
get_footer();
