<?php
/**
 * 写文章页面 js 对应，write.js
 */

get_header();

$post_id = isset($_GET['pid']) ? (int)$_GET['pid'] : '';
$user_id = get_current_user_id();

if($post_id && user_can_edit($post_id)){
	//获取分类
	$cats = get_the_category($post_id);
	$cat_arr = array();
	if(!empty($cats)){
		foreach ($cats as $cat) {
			$cat_arr[$cat->term_id] = $cat->cat_name;
		}
	}
	//获取标题
	$title = get_the_title($post_id);

	//获取文章内容
	$content =  wpautop(get_post_field('post_content',$post_id));

	//获取标签
	$tags = wp_get_post_tags($post_id);
	$tag_arr = array();
	foreach ($tags as $tag) {
		$tag_arr[] = $tag->name;
	}

	//获取阅读权限
	$capabilities = '';
	$capabilities_value = array();

	$cap = get_post_meta($post_id,'capabilities',true);
	if(isset($cap['key']) && isset($cap['val'])){
		$capabilities = $cap['key'];
		$capabilities_value = $cap['val'];
	}

	//获取相关文章
	$related = get_post_meta($post_id,'zrz_related',true);
	$related_arr = array();
	if(is_array($related) && !empty($related)){
		foreach ($related as $id) {
			$related_arr[] = array(
				'id'=>$id,
				'img'=>zrz_get_thumb(zrz_get_post_thumb($id),200,160),
				'href'=>get_permalink($post_id),
				'title'=>get_the_title($post_id),
			);
		}
	}

	//获得文章描述
	$excerpt = get_post_field('post_excerpt',$post_id);

	//获得特色图ID
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	$video = get_post_meta($post_id,'zrz_thumb_video',true);
	$video_dom = apply_filters('the_content', $video);
	if(strpos($video_dom,'smartideo') !== false){
		$video_dom = $video_dom;
	}else{
		$video_dom = '<video src="'.$video.'" controls="controls"></video>';
	}

	$files_id = array();

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

	wp_localize_script( 'ziranzhi2-write', 'zrz_write_edit_data', array(
		'cat'=>$cat_arr,
		'title'=>$title,
		'content'=>$content,
		'tags'=>$tag_arr,
		'capabilities'=>array(
			'redCapabilities'=>$capabilities,
			'rmb'=>$capabilities == 'rmb' ? $capabilities_value : 0,
			'credit'=>$capabilities == 'credit' ? $capabilities_value : 0,
			'lv'=>$capabilities == 'lv' ? $capabilities_value : array(),
		),
		'related'=>$related_arr,
		'excerpt'=>$excerpt,
		'thumb'=>$post_thumbnail_id,
		'post_id'=>$post_id,
		'thumbSrc'=>get_the_post_thumbnail_url($post_id),
		'thumbVideo'=>$video,
		'thumbVideoDom'=>$video_dom,
		'format' => get_post_format($post_id) ?: 'none',
		'filesArg'=>$files_id
	));
	$user_id = get_post_field('post_author',$post_id);
}

?>

	<div id="primary" class="content-area fd">
		<?php if(!is_user_logged_in()) : ?>
			<div class="loading-dom pos-r box">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先登录</p></div>
			</div>
		<?php elseif(!zrz_user_can($user_id,'post') && !user_can_edit($post_id)) : ?>
			<div class="loading-dom pos-r box">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">您没有权限发表文章</p></div>
			</div>
		<?php else : ?>
			<main id="write" class="site-main box write-page">
				<div class="box-header fs12 pd10 b-b"><i class="iconfont zrz-icon-font-write1"></i>  <?php _e('写文章','ziranzhi2'); ?></div>
				<div v-if="draftEdit" v-cloak>
					<div class="t-c pd20 has-autosave mar10">
						<p class="fs16 red mar10-b">您有一个未完成的草稿<time class="timeago green mar10-l fs12" :data-timeago="date" v-text="date"></time></p>
						<button @click="editAgain">继续编辑</button><button class="empty mar10-l" @click="deleteDraft">删除，重新开始</button>
					</div>
				</div>
				<div class="bg-w pos-r" v-cloak>
					<div class="content-hide" v-if="draftEdit"></div>
					<div :class="['labs-image','pos-r',{'write-video':thumbVideo}]">
						<div class="video-dom" v-if="thumbVideo" v-html="thumbVideoDom"></div>
						<div class="pos-a thumb-img thumb-in" v-else="thumbSrc" :style="'background-image:url('+thumbSrc+')'"></div>
						<div class="t-c pos-r thumb-button pos-a">
							<button class="text" @click="uploadThumb()"><i class="zrz-icon-font-tupian iconfont"></i> 封面图片</button>
							<?php echo ZRZ_THEME_DOT; ?>
							<button class="text" @click="uploadThumbVideo()"><i class="zrz-icon-font-shipin iconfont"></i> 封面视频</button>
						</div>
					</div>
					<div class="write-cat pos-r b-b b-t">
						<div class="write-tips-cat pos-a fs12 mobile-hide">分类</div>
						<div class="write-cat-list pd10 b-l b-r">
							<ul class="clearfix">
								<li v-for="(val,key) in catArr">
									<span @click.stop="catRemove(key)" class="click">
										{{val}}
									</span>
								</li>
							</ul>
						</div>
						<div class="pos-a pd10 cat-select">
							<select id="select" v-model="cat">
								<option v-for="(val,key) in cats" :value="key">
									{{val}}
								</option>
							</select>
						</div>
					</div>
					<div class="b-b pos-r write-title">
						<div class="write-tips-title pos-a fs12 mobile-hide"><?php echo get_avatar($user_id,20); ?> 标题</div>
						<div class="write-title-center b-l mobile-full-width"><textarea id="textarea" class="textarea" ref="title" placeholder="标题" v-model="title" @blur="saveData"></textarea></div>
					</div>
					<input type="file" multiple ref="getFile" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event)" class="hide"/>
					<input type="file" ref="getFileOne" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event)" class="hide"/>
					<input ref="nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
					<div id="toolbarfix">
						<div id="toolbar" class="pos-r" data-sticky-class="menu-fixed" data-margin-top="60">
							<span class="fs12 pos-a" v-show="date">本地草稿 <time class="timeago green mar10-l" :data-timeago="date" v-text="date"></time></span>
						</div>
					</div>
		            <div id="editor" class="entry-content" ref="editor"></div>
					<div class="mar20 write-more-setting">
						<div class="pos-r" v-if="postFormat == 1">
							<div class="write-tips-formats fs12 clearfix pd10 mouh" @click="showFormats = !showFormats"><span class="fl">文章形式</span><span class="fr gray ">{{formatText}}（选填）</span></div>
							<div class="write-formats-center pd10" v-show="showFormats">
								<button :class="['text',{'active':format == 'none'}]" @click="format = 'none'"><i class="iconfont zrz-icon-font-ziyuan2"></i></button>
								<button :class="['text',{'active':format == 'status'}]" @click="format = 'status'"><i class="iconfont zrz-icon-font-ziyuan1"></i></button>
								<button :class="['text',{'active':format == 'image'}]" @click="format = 'image'"><i class="iconfont zrz-icon-font-ziyuan"></i></button>
							</div>
						</div>
						<div class="pos-r write-related" v-if="relateChose == 1">
							<div class="write-related-title fs12 pd10 clearfix mouh" @click="showRealted = !showRealted">相关文章<span class="mobile-hide fs12 gray">（若指定，则优先显示指定的相关文章，否则自动生成）</span><span class="gray fr">{{relatedPost.length}}/5（选填）</span></div>
							<div class="write-related-list" v-show="showRealted">
								<div class="pd10"><input type="text" placeholder="请输入文章的网址或者ID" class="bor-3 mar10-r" v-model="realtedId" @focus="relatedError = ''"></input> <button :class="['empty ',{'disabled':relatedLocked}]" @click="getRelatePost(realtedId)"><b :class="{'loading':relatedLocked}"></b>添加</button><span class="red mar10-l fs12" v-text="relatedError"></span></div>
								<ul class="pd5" v-if="relatedPost.length > 0">
									<li class="pos-r fd pos-r" v-for="post in relatedPost">
										<a target="_blank" :href="post.href"><img :src="post.img" />
										<h2 v-text="post.title"></h2></a>
										<span class="remove-img-ico click pos-a" @click="deleteRelated(post.id)">删除</span>
									</li>
								</ul>
							</div>
						</div>
						<div class="write-tags pos-r">
							<div class="fs12 pd10 clearfix bg-blue-light mouh" @click="showTags = !showTags">标签<span class="fr gray">{{tags.length}}/{{tagsCount}}（选填）</span></div>
							<div class="tag-list pd10" v-show="showTags">
								<label for="tag-inp" class="tag-label l1">
									<i class="iconfont zrz-icon-font-tag"></i> <button :class="['tag-list-in','text',{'tag-wor':(tag == tagI || dubb == tag)}]" v-for="(tag,index) in tags" @click="removeTag(index)">{{tag}}</button>
								</label>
								<input type="text" class="write-tag-input" @input="tagChange($event)" @keydown="tagChange($event)" @blur="tagChange($event)" v-model="tagI" id="tag-inp" placeholder="请输入标签">
								<div class="custom_tags pos-r fs12 mar20-t" v-show="customTags[0]">
									<span class="mar10-r gray">推荐标签（点击自动添加）：</span><button v-for="tag in customTags" class="text" @click="addTag(tag)">{{tag}}</button>
								</div>
							</div>

						</div>

						<div class="red-select fs14">
							<div class="pd10 clearfix mouh fs12" @click="showCapabilities = !showCapabilities">阅读权限<span class="fr gray">（选填）</span></div>
							<div class="pd10" v-show="showCapabilities">
								<div class="mar20-b">
									<label><input type="radio" value="default" v-model="redCapabilities">无限制</label>
									<label><input type="radio" value="login" v-model="redCapabilities">登录可见</label>
									<label><input type="radio" value="credit" v-model="redCapabilities">积分阅读</label>
									<label><input type="radio" value="rmb" v-model="redCapabilities">付费阅读</label>
									<label><input type="radio" value="lv" v-model="redCapabilities">允许阅读的用户组</label>
								</div>
								<div class="capabilities-input" v-if="redCapabilities == 'credit'">
									<input type="number" class="pd10" placeholder="请设置阅读此文所需的积分" v-model="credit" oninput="value = parseInt(Math.min(Math.max(value, 0), 1000000), 10)" @blur="saveData"> <span class="mar10-l" v-html="cion(credit)"></span>
								</div>
								<div class="capabilities-input" v-if="redCapabilities == 'rmb'">
									<input type="number" class="pd10" placeholder="请设置阅读此文所需的金额" v-model="rmb" @blur="saveData"> <span  class="mar10-l rmb" v-text="'¥'+(rmb ? rmb : 0)"></span>
								</div>
								<div class="fs14" v-if="redCapabilities == 'lv'">
									<?php
										$lv = zrz_get_lv_settings();
										foreach ($lv as $key => $val) {
											if((isset($val['open']) && $val['open'] == 0)) continue;
										?>
											<label><input type="checkbox" value="<?php echo $key; ?>" v-model="lv" @click="saveData"><?php echo $val['name']; ?></label>
										<?php } ?>
								</div>
								<div class="bg-blue-light pd10 mar20-t">
									<p class="fs12 gray">若需限制阅读，请在编辑器工具栏中使用隐藏按钮，或使用 <code>[content_hide]</code> 和 <code>[/content_hide]</code> 短代码，隐藏的内容需包裹其中。</p>
								</div>
							</div>
						</div>
						<div class="write-excerpt">
							<div class="pd10 clearfix mouh fs12" @click="showExcerpt = !showExcerpt">摘要<span class="fr gray">（选填）</span></div>
							<div class="write-post-excerpt mar10" v-show="showExcerpt">
								<textarea id="textarea" class="post-excerpt textarea pd10" placeholder="将会显示在文章的最顶部" v-model="excerpt" @blur="saveData" ref="title"></textarea>
							</div>
						</div>
					</div>
					<div class="pd10 t-r clearfix b-t">
						<span class="fs12 gray write-tips fl mobile-hide">请尊重自己和别人的时间，不要发布垃圾和广告内容。</span>
						<div class="fr">
							<span class="red mar10-r fs12" v-text='insertPostMsg'></span>
							<button :class="['border', 'mar10-r',{'disabled':(insertPostLocked && postType == 'draft') || postType == 'padding'}]"  @click="postInsert('draft')"><b :class="{'loading':insertPostLocked && postType == 'draft'}"></b>保存草稿到服务器</button>
							<button :class="['small',{'disabled':(insertPostLocked && postType == 'padding') || postType == 'draft'}]" @click="postInsert('padding')"><b :class="{'loading':insertPostLocked && postType == 'padding'}"></b><span v-text="subMsg">立刻发布</span></button>
						</div>
					</div>
					<div class="box-header b-t pd20 t-r"></div>
				</div>
				<div id="imgtoolbar-set" class="pos-r hide" ref="imgtoolbarSet">
					<div id="imgtoolbar" class="imgtoolbar pos-a" ref="imgtoolbar">
						<div class="imgtoolbar-in pjt">
							<button :class="['text','bl',{'toolbarH':toolbarHs}]" @click.stop="size($event,'small')"><i class="iconfont zrz-icon-font-ziyuan7"></i></button>
							<button :class="['text','br','mar10-l',{'toolbarH':toolbarHb}]" @click.stop="size($event,'big')"><i class="iconfont zrz-icon-font-ziyuan6"></i></button>
					 	</div>
					</div>
				</div>
				<div id="video-form" :class="['dialog', 'video-form',{'dialog--open':showMediaForm}]" ref="mediaForm" v-cloak>
					<div class="dialog__overlay" @click.stop="closeViedoForm"></div>
					<div class="dialog__content">
						<div style="margin-top:20px" v-show="mediaType == 'video'">
							<div class="videobutton pos-a">
								<button :class="['empty',{'picked':insertVideoUrl}]" @click="table('url')">插入视频网址</button><button :class="['empty',{'picked':insertVideoFile}]" @click="table('file')">上传视频或音频</button>
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
									<input type="file" ref="getVideoFile" accept="video/*,audio/*" @change="updateVideo($event,'file')"  class="hide">
									<span class="red fs12 pos-a" v-text="videoError"></span>
								</label>
								<div class="fs12 mar5-t gray">请确文件小于 {{videoSize}}M</div>
							</div>
							<div class="bg-blue-light pd20 b-t fs12 gray mar10-t">
								请不要添加无关的视频或者音频，详情查看 本站视频使用规范。
								上传视频，即代表你同意《本站用户协议》。
							</div>
						</div>
						<div v-show="mediaType == 'post'">
							<div class="t-c b-b pd10"><span>引入文章或商品</span></div>
							<p class="mar20-b"></p>
							<div class="pd10">
								<p class="mar10-b fs14">请输入文章、商品连接或者文章、商品ID</p>
								<input type="text" class="pd10" v-model="postIdOrUrl">
								<button :class="['empty','mar20-t',{'disabled':getPostLocked}]" @click="getPost('post')"><b :class="{'loading':getPostLocked}"></b>插入</button><span class="red mar10-l fs12" v-text="getPostError"></span>
							</div>
							<div class="bg-blue-light pd20 b-t fs12 gray mar10-t">
								添加本站文章、商品专用，外部网址不支持。
							</div>
						</div>
						<div class="file-insert" v-show="mediaType == 'files'">
							<div class="videobutton pos-a">
								<button :class="['empty',{'picked':fileData.type == 'url'}]" @click="fileData.type = 'url'">外链</button><button :class="['empty',{'picked':fileData.type == 'netdisc'}]" @click="fileData.type = 'netdisc'">网盘</button><button :class="['empty',{'picked':fileData.type == 'localhost'}]" @click="fileData.type = 'localhost'">本地</button>
							</div>
							<div class="file-form pd20">
								<div class="" v-if="fileData.type == 'url'">
									<input type="text" placeholder="附件地址，需要带http://" v-model="fileData.link"/>
									<input type="text" placeholder="附件名称" v-model="fileData.name"/>
									<input type="text" placeholder="解压码" v-model="fileData.code"/>
								</div>
								<div class="" v-if="fileData.type == 'netdisc'">
									<input type="text" placeholder="附件地址，需要带http://" v-model="fileData.link"/>
									<input type="text" placeholder="附件名称" v-model="fileData.name"/>
									<input type="text" placeholder="提取码" v-model="fileData.pass"/>
									<input type="text" placeholder="解压码" v-model="fileData.code"/>
								</div>
								<div class="" v-if="fileData.type == 'localhost'">
									<label class="mar10-b file-upload-button" for="file" :style="{backgroundImage:'linear-gradient(to right,#C0C7CB 0%,#C0C7CB '+progress+',#E1E6E9 '+progress+',#E1E6E9 100%)'}">
										<span v-html="uploadLocked && progress != '100%' ? progress : uploadText"></span>
									</label>
									<div class="red fs12 mar5-b" v-text="uploadError"></div>
									<input type="text" disabled placeholder="附件地址" v-model="fileData.link"/>
									<input type="text" placeholder="附件名称" v-model="fileData.name"/>
									<input id="file" type="file" class="hide" @change="updatefile($event)">
									<input type="text" placeholder="解压码" v-model="fileData.code"/>
								</div>
								<div class="mar20-t">
									<button class="mar10-r" @click="insertFile('hide')">插入隐藏附件</button><button class="" @click="insertFile()">插入附件</button>
								</div>
							</div>
							<div class="bg-blue-light pd20 b-t fs12 gray mar10-t">
								如果没有解压码或提取码请留空！
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
			</main><!-- #main -->
		<?php endif; ?>
	</div><?php
get_sidebar();
get_footer();
