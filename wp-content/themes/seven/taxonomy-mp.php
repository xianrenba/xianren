<?php
/*
* 冒泡
*/
get_header();
$is_login = is_user_logged_in();
$user_id = get_current_user_id();
global $wp_query;
$nub = get_option('posts_per_page',10);
$ipaged = get_query_var('paged') ? get_query_var('paged') : 1;
$ipages = ceil( $wp_query->found_posts / $nub);
$bubble_id = get_queried_object()->term_id;
$bubble_topic_name = get_queried_object()->name;
?>
<div id="bubble-home" class="content-area fd">
    <main id="main" class="site-main">
        <div class="pd10 box fs12 mar10-b bbp-breadcrumb"><a href="<?php echo home_url('/bubble'); ?>">全部冒泡</a> <span class="dot">›</span> <?php echo $bubble_topic_name; ?></div>
        <div class="bubble-form box bubble-ac">
            <div class="pd10 fs12 bubble-header"><?php echo get_avatar($user_id,26) ?> <?php echo $is_login ? zrz_get_user_page_link($user_id) : __('游客','ziranzhi2'); ?><span v-show="topic">@</span><span :class="['pos-a',{'add-cart':dou}]" v-text="'# '+topic+' #'"></span></div>
            <div class="bubble-textarea">
                <textarea id="textarea" class="textarea" placeholder="水下缺氧，赶紧冒泡！Σ(っ °Д °;)っ" v-model="bubbleText" @blur.stop="changeText($event)" @focus.stop="changeText($event)" ref="textarea"></textarea>
            </div>
            <input ref="security" type="hidden" value="<?php echo wp_create_nonce('pps'.get_current_user_id()); ?>">
            <div class="bubble-img-list" v-show="imglist.length > 0 || showImgList" v-cloak>
                <ul>
                    <li v-for="(img,index) in imglist" class="fd pos-r" :style="'background-image:url('+img+')'"><span class="remove-img-ico click" @click="removeImg(index)">删除</span></li><li class="imgLogin pos-r fd" @click="imageHandler" v-if="showupload"><b class="loading" v-if="uploadLocked"></b><i class=" zrz-icon-font-jiajianchengchu-1 iconfont" v-else></i></li>
                </ul>
            </div>
            <div class="bubble-video-list mar10-t b-b" v-if="videoList.length > 0" v-cloak>
                <ul>
                    <li v-for="(video,index) in videoList" class="pos-r gray b-t pd10 clearfix">
                        <i class="zrz-icon-font-play iconfont"></i> <span v-text="video"></span> <button class="fr text" @click="removeVideo(index)">删除</button>
                    </li>
                </ul>
            </div>
            <div class="pd10 fs12 bubble-tool pos-r clearfix">
                <div class="fd bubble-face"><button class="text" @click.stop.prevent="smileyTopic"><i class="iconfont zrz-icon-font-sentiment_satisfied"></i>表情</button></div>
                <div class="fd bubble-img"><button class="text" @click="imageHandler">
                    <i class="iconfont zrz-icon-font-image"></i>图片</button>
                    <input type="file" accept="image/jpg,image/jpeg,image/png,image/gif" ref="getFile" @change="imgUpload($event)" style="display:none"/>
                    <input ref="TopicNonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
                </div>
                <div class="fd bubble-video"><button class="text" @click="showBubbleForm = 'video'"><i class="iconfont zrz-icon-font-meitib"></i>视频&音乐</button></div>
                <div :class="['fr',{'opt-0':showTopicBox}]" v-cloak><span class="red fs12 mar10-r" v-text="submitError"></span><button :class="[{'disabled':submitLocked}]" @click="submit"><b :class="{'loading':submitLocked}"></b>立刻发布</button></div>
                <div :class="['smile-box','pos-a','box','pjt','transform-out',{'transform-in':smileShowTopic}]">
                    <div class="clearfix" v-html="smilesT"></div>
                </div>

            </div>
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
            <div id="bubble-video-form" :class="['dialog', 'video-form',{'dialog--open':showBubbleForm}]" ref="bubbleForm" v-cloak>
                <div class="dialog__overlay" @click.stop="closeBubbleForm"></div>
                <div class="dialog__content">
                    <div class="" v-show="showBubbleForm == 'video'">
                        <div class="pd10 b-b">
                            请输入视频网址
                        </div>
                        <div class="pd10">
                            <textarea placeholder="视频网址" ref="videoUrl" v-model="videoUrl"></textarea>
                            <p class="fs12 mar5-t">目前支持优酷、搜狐视频、腾讯视频、爱奇艺、哔哩哔哩，酷6、华数、乐视、YouTube 等网站</p>
                            <button :class="['empty','mar20-t',{'disabled':videolocked}]" @click="getVideo()"><b :class="{'loading':videolocked}"></b>插入</button>
                            <span class="red fs12 mar10-l" v-text="videoError"></span>
                        </div>
                        <div class="bg-blue-light pd20 b-t fs12 gray mar10-t">
                            请不要添加无关的视频，详情查看 本站视频使用规范。
                            上传视频，即代表你同意《本站用户协议》。
                        </div>
                    </div>
                    <div class="" v-show="showBubbleForm == 'image'">
                        <div class="videobutton pos-a">
                            <button @click="imageBoxType = 'upload'" :class="['empty',{'picked':imageBoxType == 'upload'}]">上传图片</button><button @click="imageBoxType = 'uri'" :class="['empty',{'picked':imageBoxType == 'uri'}]">插入图片地址</button>
                        </div>
                        <div class="video-upload pd20" v-if="imageBoxType == 'upload'">
                            <label class="mouh">
                                <button class="text" @click="imageUpload()">选择文件</button>
                            </label>
                        </div>
                        <div class="pd20" v-else>
                            <textarea type="text" v-model="insertUri" class="img-insert" placeholder="图片地址"></textarea>
                            <button @click="insetImageUri()" :class="{'disabled':!insertUri}">插入图片</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bubble-list mar10-t" id="bubbleListHome">
            <?php
                if ( have_posts() ) {
                   while ( have_posts() ) {
                        the_post();
                        get_template_part( 'formats/content','bubble');
                   }
                   wp_reset_postdata();
                }else{
                   echo '<div class="pd20 t-c box">没有泡泡，发一个吧！</div>';
                }
            ?>
        </div>
        <div class="bubble-pagenav">
            <page-nav class="box" nav-type="bubble-arc-<?php echo $bubble_id; ?>" :paged="'<?php echo $ipaged; ?>'" :pages="'<?php echo $ipages; ?>'" :show-type="'p'"></page-nav>
        </div>
    </main><!-- #main -->
</div><?php
get_sidebar();
get_footer();
