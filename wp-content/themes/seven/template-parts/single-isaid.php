<div id="primary" class="content-area" style="width:100%">
    <main id="main" class="site-main labs-single">

    <?php
        while ( have_posts() ) : the_post();
        $thumb = zrz_get_post_thumb();
        $user_id = get_the_author_meta( 'ID' );
        $user_name = get_the_author_meta('display_name');
        $lv = ZRZ_THEME_DOT.zrz_get_lv($user_id,'lv');
        $media_path = zrz_get_media_path().'/';
        $ipages = ceil( get_comments_number( get_the_id() ) / 20);
    ?>

    <article id="isaid-single" class="box" :data="pages = '<?php echo $ipages; ?>'">
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
        <?php echo zrz_post_edit_button(); ?>
        <div class='isaid-comment' ref="isaidC">

            <ol class="comment-list pd5" ref="commentList">
                <?php
                    if(is_user_logged_in()){
                        $include_unapproved = get_current_user_id();
                    }else{
                        $guest = wp_get_current_commenter();
                        $include_unapproved = $guest['comment_author_email'] ? $guest['comment_author_email'] : 'empty';
                    }

                    $arr = array(
                        'post_id'=>get_the_id(),
                        'status'=>'approve',
                        'include_unapproved'=>$include_unapproved,
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                        'meta_query' => array(
                            'relation' => 'OR',
                            array(
                             'key' => 'zrz_isaid_vote_count',
                             'compare' => 'NOT EXISTS',
                             'value' => ''
                            ),
                            array(
                             'key' => 'zrz_isaid_vote_count',
                            )
                        )
                    );

                    $comments = get_comments($arr);
                    if($comments){
                        wp_list_comments( array(
                            'short_ping' => true,
                            'per_page'=>20,
                            'callback' => 'zrz_isaid_comment_callback',
                            'end-callback' => 'zrz_comment_callback_close',
                            'max_depth'=>1
                        ),$comments);
                    }else{
                        echo '<div class="pd20 fs14 l0 t-c">没有参与者，您可以成为第一个参与者！</div>';
                    }

                ?>
            </ol>

        </div>
        <?php if(count($comments) >20) : ?><div class="pd10 t-c"><button v-show="showButton" :class="['empty',{'disabled':locked}]" @click="loadMoreComment"><b :class="{'loading':locked}"></b><?php _e('加载更多','ziranzhi2'); ?></button></div><?php endif; ?>
    </article><!-- #post-<?php the_ID(); ?> -->
    <?php 
        $comment_sign = get_option('comment_registration') && !is_user_logged_in();
        $can_comment = !zrz_current_user_can('comment') && is_user_logged_in();
    ?>
    <div id="comments" class="isaid-comment-form" ref="isaidComment">
        <?php if ( ! comments_open() ) { ?>
            <p class="no-comments"><?php esc_html__( '禁止参与', 'ziranzhi2' ); ?></p>
        <?php }else{ ?>
            <div id="respond" class="respond pos-r" rel="commentForm">
            <?php 
					if($comment_sign){
						echo '<div class="sign-comment">
						<div class="lm">
							<button class="empty" @click.stop="sign(\'in\')">登录</button><button @click.stop="sign(\'up\')">快速注册</button>
						</div>
						</div>';
					}elseif($can_comment){
						echo '<div class="sign-comment">
							<p class="lm">您没有权限参与讨论</p>
						</div>';
					}
				?>
                <form id="commentform" class="comment-form clearfix box mar10-t" @submit.stop.prevent="submit">
                    <?php if ( is_user_logged_in() ) { $login = true;?>
                        <div class="com-info fl mobile-hide <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>">
                            <a href="<?php echo zrz_get_user_page_url(); ?>">
                                <?php echo get_avatar(get_current_user_id(),48); ?>
                            </a>
                        </div>
                    <?php } else { $login = false; ?>
                        <div class="com-info fl mobile-hide <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>">
                            <img style="background-color:<?php echo zrz_get_avatar_background_by_id(rand(0,9)); ?>" class="avatar" :src="avatar" />
                        </div>
                    <?php } ?>
                    <div class="comment-input <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>">
                        <?php if(!$login){ ?>
                            <div :class="['comment-user-info','pd10','fs12','pos-r',{'cbb':!editBool}]" v-cloak><span v-if="!email && !author">欢迎您，新朋友，感谢参与互动！</span><span v-else>欢迎您 {{author}}，您在本站有{{commentsCount}}条评论</span> <button class="text pos-a" v-text="editText" @click.stop.prevent="edit"></button></div>
                            <div v-show="editBool">
                                <input id="author" :class="['fd','pd10',{'comment-input-error':errorName}]" name="author" v-model="author" type="text" value="" placeholder="称呼" @blur.stop.lazy="changeAvatar($event)" @focus.stop.lazy="changeAvatar($event)"><input id="email" :class="['fd','email','pd10',{'comment-input-error':errorEmail}]" name="email" type="email" value="" v-model="email" placeholder="邮箱" @focus.stop.lazy="emptyError">
                            </div>
                        <?php } ?>
                        <textarea id="textarea" maxlength="50" :class="['textarea','pd10','<?php if($login) echo 'bt'; ?>',{'comment-input-error':errorContent}]"  placeholder="说说你的看法" @blur.stop.lazy="contentBlur($event)" @focus.stop.lazy="contentBlur($event)" v-model="commentContent" ref="content"></textarea>
                        <div class="t-r mar10-t pos-r clearfix" v-cloak>
                            <input ref="commentImgInput" id="comment-image" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="hide">
                            <span class="fl gray">{{commentStrCount.cCount}}/{{commentStrCount.count}}</span>
                            <div class="fr">
                                <span class="red fs12 mar10-r" v-text="errorMsg"></span>
                                <button type="submit" :class="['small',submitLocked ? 'disabled' : '']" name="button" ><?php echo __('写好了，发送!','ziranzhi2'); ?><b :class="[submitLocked ? 'loading' : '']"></b></button>
                            </div>
                        </div>
                    </div>

                    <input ref="comment_nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
                </form>
            </div>
        <?php } ?>
    </div>
    <?php
    endwhile; // End of the loop.
    ?>

    </main><!-- #main -->
</div><!-- #primary --><?php
get_footer();
