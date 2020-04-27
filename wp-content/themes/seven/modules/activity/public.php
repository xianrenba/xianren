<?php
/*
*发布活动
*/
get_header();

$video_size = zrz_get_writing_settings('video_size');
$activity = new ZRZ_ACTIVITY();
$post_id = isset($_GET['id']) ? $_GET['id'] : NULL;

$thumb = $title = $content = $thumbSrc = '';
$width = ceil(zrz_get_theme_settings('page_width')*0.75);

$files_id = array();

if($post_id){
    $thumbSrc = zrz_get_post_thumb($post_id);
    $thumbSrc = zrz_get_thumb($thumbSrc,$width,'full');
    $thumb = get_post_thumbnail_id($post_id);
    $content = wpautop(get_post_field('post_content',$post_id));
    $title = get_the_title($post_id);

	$args = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_parent' => $post_id, // any parent
	);

	$attachments = get_posts($args);
	if ($attachments) {
		foreach ($attachments as $post) {
			$files_id[] = $post->ID;
		}
	}
}

wp_localize_script( 'ziranzhi2-avtivity-js', 'activity_script', array(
    'post_id'=>$post_id,
    'videoSize'=>$video_size,
    'thumbSrc'=>$thumbSrc,
    'thumb'=>$thumb,
    'title'=>$title,
    'content'=>$content,
    'filesArg'=>$files_id
));

?>
<?php if(!$activity->check_role($post_id)){ ?>
    <div class="mar20-t">
        <div id="primary" class="content-area">
        <div class="loading-dom pos-r box" ref="nologin">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先获取相应的权限再进行操作</p></div>
			</div>
        </div>
    </div>
<?php }else{ ?>
<div id="activity-public" data-videoSize="<?php echo $video_size; ?>">
    <div class="activity-public-thumb pos-r">
        <div class="puiblic-thumb" style="background-color:#ccc;width:<?php echo $width; ?>px;margin:0 auto;min-height:300px">
            <img v-if="thumbSrc" :src="thumbSrc" class="activity-public-thumbsrc" v-cloak/>
            <div class="lm">
                <button class="empty" @click="uploadThumb()">添加封面图片</button>
            </div>
        </div>
    </div>
    <div class="content-in activity-content">
        <div id="primary" class="content-area">
            <main id="main" class="site-main">
                <div class="activity-public-title pd20">
                    <input type="text" placeholder="活动标题" v-model="title">
                </div>

                <div class="activity-public-role" >
                    <?php $activity->activity_time($post_id); ?>
                    <div class="fs0">
                        <p class="activity-address"><span>活动地点：</span>
                        <?php $activity->activity_address($post_id); ?></p>
                        <p class="activity-address"><span>计划参与人数：</span>
                        <?php $activity->activity_people_count($post_id); ?></p>
                    </div>
                    <p class="activity-address no-bor"><span>参与条件：</span>
                    <?php $activity->activity_role($post_id); ?></p>
                </div>
                <div id="toolbar" class="pos-r b-t" data-sticky-class="menu-fixed" data-margin-top="60"></div>
                <div id="editor" class="entry-content" ref="editor"></div>
                <div class="pd20 b-t clearfix" v-cloak>
                    <span class="mar20-r red" v-if="error" v-text="error"></span>
                    <button v-show="type != 'draft' || publicLocked == false" :class="['fr',{'disabled':publicLocked}]" @click="public('publish')"><b :class="{'loading':publicLocked}"></b><span v-text="buttonText ? buttonText : '立刻发布'"></span></button>
                    <button v-show="type != 'publish' || publicLocked == false" :class="['fr','mar20-r',{'disabled':publicLocked}]" @click="public('draft')"><b :class="{'loading':publicLocked}"></b><span v-text="buttonText ? buttonText : '保存草稿'"></span></button>
                </div>
            </main>
        </div>
    </div>
    <input type="file" multiple ref="getFile" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event)" class="hide"/>
    <input type="file" ref="getFileOne" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event)" class="hide"/>
    <input ref="nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
    <div id="video-form" :class="['dialog', 'video-form',{'dialog--open':showMediaForm}]" ref="mediaForm" v-cloak>
        <div class="dialog__overlay" @click.stop="closeViedoForm"></div>
        <div class="dialog__content">
            <div style="margin-top:20px" v-show="mediaType == 'video'">
                <div class="videobutton pos-a">
                    <button :class="['empty',{'picked':insertVideoUrl}]" @click="table('url')">插入视频网址</button><button :class="['empty',{'picked':insertVideoFile}]" @click="table('file')">上传视频</button>
                </div>
                <div class="pd20" v-show="insertVideoUrl">
                    <textarea placeholder="视频网址" ref="videoUrl"></textarea>
                    <p class="fs12 mar5-t">目前支持优酷、搜狐视频、腾讯视频、爱奇艺、酷6、华数、乐视、YouTube 等网站</p>
                    <button :class="['empty','mar20-t',{'disabled':insertVideoButton}]" @click="updateVideo($event,'url')"><b :class="{'loading':insertVideoButton}"></b>插入</button>
                    <span class="red fs12 mar10-l" v-text="videoError"></span>
                </div>
                <div class="pd20 video-upload" v-show="insertVideoFile" ref="dropbox">
                    <label :class="['mouh','pos-r',{'active':dropenter}]" :style="{backgroundImage:'linear-gradient(to right,#C0C7CB 0%,#C0C7CB '+progress+',#E1E6E9 '+progress+',#E1E6E9 100%)'}">
                        <span v-html="!thumbUPlocked && progress != '0%' && progress != '100%' ? progress : videoText"></span>
                        <input type="file" ref="getVideoFile" accept="video/*" @change="updateVideo($event,'file')"  class="hide">
                        <span class="red fs12 pos-a" v-text="videoError"></span>
                    </label>
                    <div class="fs12 mar5-t gray">请确保视频文件小于 <?php echo $video_size; ?>M</div>
                </div>
                <div class="bg-blue-light pd20 b-t fs12 gray mar10-t">
                    请不要添加无关的视频，详情查看 本站视频使用规范。
                    上传视频，即代表你同意《本站用户协议》。
                </div>
            </div>
            <div class="" v-show="mediaType == 'image'">
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
<?php } ?>
<?php
    get_footer();
?>