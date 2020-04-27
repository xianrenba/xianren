<div id="bubble-home" class="content-area fd bubble-ac">
    <main id="main" class="site-main">

    <?php
    $user_id = get_current_user_id();
    while ( have_posts() ) : the_post();

        get_template_part( 'formats/content','bubble');

    endwhile; // End of the loop.
    ?>
    </main><!-- #main -->
    <div class="hide" ref="bubbleForm">
        <div id="bubble-comment-form" class="clearfix" ref="formInput">
            <div v-if="login" class="bubble-comment-form-avatar fl"><?php echo get_avatar($user_id,40); ?></div>
            <div class="bubble-comment-form-avatar fl" v-else><img :src="commentUser.avatar" /></div>
            <div class="bubble-comment-form-r">
                <div v-if="!login" class="bubble-comment-user">
                    <div v-show="userName" class="clearfix gray">
                        <span class="fl">欢迎您 {{commentUser.user_name}}，您在本站有{{commentUser.comment_count}}条评论</span><span class="fr"><button class="text" @click="showInputAc">{{showInput ? '完成修改' : '修改资料'}}</button></span>
                    </div>
                    <div v-show="showInput">
                        <input type="text" class="bubble-name fd pd10" v-model="commentUser.user_name" placeholder="称呼" @blur.stop="changeAvatar($event)" @focus.stop="changeAvatar($event)"><input @blur.stop="changeAvatar($event)" @focus.stop="changeAvatar($event)" type="text" class="bubble-mail fd pd10" v-model="commentUser.user_email" placeholder="邮箱">
                    </div>
                </div>
                <textarea id="textarea-comments" class="textarea bubble-comment-textarea pd10" placeholder="戳泡泡~~~" @blur.stop="changeAvatar($event)" @focus.stop="changeAvatar($event)" v-model="content"></textarea>
            </div>
            <div class="mar10-t clearfix bubble-comment-form-tool">
                <div class="fl pos-r">
                    <div class="">
                        <button class="text comment-smile" type="text" @click.stop.prevent="smiley"><i :class="['iconfont', smileShow || smileShowF ? 'zrz-icon-font-sentiment_satisfied' : 'zrz-icon-font-sentiment_neutral']"></i></button>
                        <button v-html="'<i class=\'iconfont zrz-icon-font-at\'></i>'+parentName" class="text mar10-l parent-user fs12" v-show="parentName" @click="removeParent"></button>
                    </div>
                    <div :class="['smile-box','pos-a','box','pjt','transform-out',{'transform-in':smileShow}]">
                        <div class="clearfix" v-html="smiles"></div>
                    </div>
                </div>
                <div class="fr"><span class="red fs12  mar10-r mar10-t fd" v-html="commentError"></span><button :class="['sub','fr',{'disabled':sendCommentLocked}]" @click="sendComment">发布<b :class="{'loading':sendCommentLocked}"></b></button></div>
            </div>
        </div>
    </div>
</div><!-- #primary --><?php
get_sidebar();
get_footer();
