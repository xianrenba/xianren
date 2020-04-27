<?php
/**
 * 发起研究页面，js 对应 add-labs.js
 */

get_header();
$post_id = isset($_GET['pid']) ? (int)$_GET['pid'] : '';
if($post_id){

	//获取研究类型
	$type = zrz_get_labs_terms('slugs',$post_id);
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	$youguess = get_post_meta($post_id,'zrz_youguess_list',true);
	$guessResouts = get_post_meta($post_id,'zrz_youguess_resout',true);
	$vote = get_post_meta($post_id,'zrz_vote_list',true);

	wp_localize_script( 'ziranzhi2-add-labs', 'labs_data', array(
		'path'=>zrz_get_media_path().'/',
		'type'=>$type,
		'title'=>get_the_title($post_id),
		'content'=>get_post_field('post_content',$post_id),
		'img'=>zrz_get_post_thumb($post_id),
		'attid' => $post_thumbnail_id,
		'youguess'=>$youguess,
		'guessResouts'=>$guessResouts,
		'post_id'=>$post_id,
		'vote'=>$vote
	));
}
$user_id = get_current_user_id();
?>

	<div id="primary" class="content-area fd">
		<main id="labs" class="site-main labs">
			<?php if(!is_user_logged_in()) : ?>
				<div class="loading-dom pos-r">
					<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先登录</p></div>
				</div>
			<?php elseif(!zrz_user_can($user_id,'labs')) : ?>
				<div class="loading-dom pos-r">
					<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">您没有权限发表研究</p></div>
				</div>
			<?php else : ?>
			<div class="box">
				<div class="box-header fs12 clearfix pos-r b-b">
					<div id="box-header-labs" data-sticky-class="labs-fixed" data-margin-top="62">
						<div class="pd10">
							<i class="iconfont iconfont zrz-icon-font-shiyan"></i> <?php _e('发起研究','ziranzhi2'); ?><span v-cloak> ❯ {{pickedText}}</span>
							<div class="fr fs14" v-if="showHeadDraft" v-cloak>
								草稿已经保存 <time class="timeago fs12 green mar10-l" :data-timeago="draftDate">{{draftDate}}</time>
							</div>
						</div>
						<div class="labs-error pd10 b-t bg-w pos-r" v-if="labError" v-cloak>
							<span class="red" v-if="labError.w == 's'" v-text="labError.msg"></span>
							<div class="red" v-for="(error,key) in labError.msg" v-else>
								{{error}}
							</div>
							<button class="pos-a text" @click.stop="closeError"><i class="iconfont zrz-icon-font-icon-x" @click.stop="closeError"></i></button>
						</div>
					</div>
				</div>
				<div ref="loading" class="loading-bg"></div>
				<div class="bg-w pd10" v-if="showAutoSave" v-cloak>
					<div class="t-c pd20 has-autosave mar10-b">
						<p class="mar20-b red">您有未完成的草稿<time class="timeago fs12 green mar10-l" :data-timeago="draft.date">{{draft.date}}</time></p>
							<button class="mar10-r empty" @click="editData">继续编辑</button><button class="empty" @click="deleteAutoSave">删除，重新开始</button>
					</div>
				</div>
				<input ref="nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
				<div class="pd10 pos-r" v-cloak>
					<div class="content-hide" v-show="showAutoSave"></div>

					<div class="labs-cat bor mar10-b">
						<label :class="['fd','pos-r',{'picked':picked === 'isaid'}]">
							<span class="lm"><i class="iconfont zrz-icon-font-kantushuohua"></i><?php _e('我说','ziranzhi2'); ?></span>
							<i class="zrz-icon-font-29 iconfont hide pickedi"></i>
							<input type="radio" class="hide" value="isaid" v-model="picked">
						</label><label :class="['fd','pos-r',{'picked':picked === 'youguess'}]">
							<span class="lm"><i class="iconfont zrz-icon-font-touzi"></i><?php _e('你猜','ziranzhi2'); ?></span>
							<i class="zrz-icon-font-29 iconfont hide pickedi"></i>
							<input type="radio" class="hide" value="youguess" v-model="picked">
						</label><label :class="['fd','pos-r',{'picked':picked === 'vote'}]">
							<span class="lm"><i class="iconfont zrz-icon-font-toupiao1"></i><?php _e('投票','ziranzhi2'); ?></span>
							<i class="zrz-icon-font-29 iconfont hide pickedi"></i>
							<input type="radio" class="hide" value="vote" v-model="picked">
						</label><label :class="['fd','pos-r',{'picked':picked === 'relay'}]">
							<span class="lm"><i class="iconfont zrz-icon-font-taolun"></i><?php _e('接力','ziranzhi2'); ?></span>
							<i class="zrz-icon-font-29 iconfont hide pickedi"></i>
							<input type="radio" class="hide" value="relay" v-model="picked">
						</label>
					</div>

		            <div class="title mar10-b">
		                <textarea class="textarea title-tex pd10" v-model="labRequired.title" @blur="autoSave($event)" placeholder="标题"></textarea>
		            </div>

		            <div class="labs-image pos-r mar10-b">
						<div class="thumb-in" v-show="labRequired.img" :style="'background-image:url('+labRequired.img+')'">
						</div>
		                <label class="mouh" v-show="!labRequired.img || imageError || imageLocked">
		                    <span :class="['lm','t-c',{'white':labRequired.img}]"><i class="zrz-icon-font-zhaoxiangji iconfont"></i><b v-html="uploadText"></b></span>
		                    <input type="file" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event,'labsThumbnail')" class="hide"/>
		                </label>
		            </div>

		            <div class="labs-des lin0">
		                <textarea class="textarea des-tex pd10" v-model="labRequired.content" @blur="autoSave($event)" placeholder="请进一步描述你的研究项目"></textarea>
		        	</div>
				</div>
			</div>
			<!-- 你猜开始 -->
			<div class="guess-list mar10-b" v-if="picked === 'youguess'" v-cloak>
				<div ref="guess-item" class="guess-list-item box mar10-t pos-r" v-for="(guessItem,index) in guessItems">
					<div class="content-hide" v-show="showAutoSave"></div>
					<div class="fs12 pd10 box-header b-b clearfix">第 <span class="red">{{index + 1}}</span> 个问题<button class="text fr" @click="deleteGuessItem(index)"><i class="iconfont zrz-icon-font-delete8e"></i></button></div>
					<div class="lin0 pd10">
						<textarea class="textarea guess-item-tex pd10 fs14" v-model="guessItem.q" placeholder="问题描述"></textarea>
					</div>
					<ul>
						<li class="fd" v-for="(l,key) in guessItem.l">
							<div class="guess-itme-in pd5">
								<label class="pos-r mouh">
									<span class="guess-nub pos-a">{{key}}</span>
									<div class="thumb-in" :style="edit ? 'background-image:url('+path+l.i+')' : 'background-image:url('+l.i+')'" v-show="l.i"></div>
									<span :class="['lm','fs12',l.i ? 'white': 'gray']" v-show="!l.i || (imageLocked && guessKey == key)">{{imageLocked && guessKey == key ? '上传中...' : (l.i ? '修改图片' : '添加图片')}}</span>
									<span class="correct-icon pos-a" v-show="guessItem.a === key">正确答案</span>
									<input type="file" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event,'guess',index,key)" class="hide"/>
								</label>
								<textarea class="textarea guess-item-tex pd5 fs14" @blur="autoSave($event)" v-model="l.t" placeholder="描述"></textarea>
							</div>
						</li>
					</ul>
					<div class="pd10-l pd10-r pd10-b mar5-t">
						<p class="fs12 mar5-b">正确答案应是：</p>
						<input class="pd10 fs12" v-model="guessItem.a" @blur="autoSave($event)" placeholder="请输入 a 或 b 或 c 或 d" />
					</div>
				</div>
				<div class="pd10 t-c guess-more box mar10-b pos-r" style="margin-top:-1px">
					<div class="content-hide" v-show="showAutoSave"></div>
					<button class="text" @click.stop="addMoreGuess"><i class="iconfont zrz-icon-font-jia1"></i></button>
				</div>
				<div class="guess-correct box pos-r">
					<div class="content-hide" v-show="showAutoSave"></div>
					<div class="box-header pd10 fs12 b-b">最终结果</div>
					<div class="guess-correct-list pos-r pd10" v-for="(guessResout,key) in guessResouts">
						<p class="fs12 mar5-b">{{key == 33 ? '正确率 0-33% 时说：' : (key == 99 ? '正确率 66%-100% 时说：' : '正确率 34%-65% 时说：')}}</p>
						<label class="pos-r mouh">
							<div class="thumb-in" :style="edit ? 'background-image:url('+path+guessResout.i+')' : 'background-image:url('+path+guessResout.i+')'" v-show="guessResout.i"></div>
							<input type="file" ref="getFile" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event,'guessResouts','',key)" class="hide"/>
							<span :class="['lm','fs12',guessResout.i ? 'white': 'gray']" v-show="!guessResout.i || (imageLocked && resoutKey == key)">{{imageLocked && resoutKey == key ? '上传中...' : (guessResout.i ? '修改图片' : '添加图片')}}</span>
						</label>
						<div class="lin0">
							<textarea class="textarea guess-item-tex pd5 fs14" @blur="autoSave($event)" v-model="guessResout.t" placeholder="描述"></textarea>
						</div>
					</div>
				</div>
			</div>
			<!-- 你猜结束 -->
			<!-- 投票开始 -->
			<div class="vote-list box mar10-t mar10-b pos-r" v-if="picked === 'vote'" v-cloak>
				<div class="content-hide" v-show="showAutoSave"></div>
				<div class="box-header pd10 fs12 b-b">添加投票项</div>
				<ul class="pd5">
					<li ref="vote-item" v-for="(itme,index) in voteList" class="fd">
						<div class="pd5 pos-r">
							<button class="text fr" @click.stop="deleteVoteItem(index)"><i class="iconfont zrz-icon-font-delete8e"></i></button>
							<label class="pos-r mouh" @click.stop="">
								<div class="thumb-in" :style="edit ? 'background-image:url('+path+itme.i+')' : 'background-image:url('+itme.i+')'" v-show="itme.i"></div>
								<input type="file" ref="getFile" accept="image/jpg,image/jpeg,image/png,image/gif" @change="imgUpload($event,'vote',index,'')" class="hide"/>
								<span :class="['lm','fs12',itme.i ? 'white': 'gray']" v-show="!itme.i || (imageLocked && voteIndex == index)">{{imageLocked && voteIndex == index ? '上传中...' : (itme.i ? '修改图片' : '添加图片')}}</span>
							</label>
							<div class="lin0">
								<textarea class="textarea guess-item-tex pd5 fs14" v-model="itme.t" placeholder="描述"></textarea>
							</div>
						</div>
					</li><li class="fd pos-r mouh pd5" @click="addVote">
							<div class="vote-add">
								<span class="lm"><i class="iconfont zrz-icon-font-jia1"></i></span>
							</div>
					</li>
				</ul>
			</div>
			<!-- 投票结束 -->
			<div class="pos-r box" :style="'margin-top:'+(picked == 'isaid' || picked == 'relay' ? '-1px' : 0)" v-cloak>
				<div class="content-hide" v-show="showAutoSave"></div>
                <div class="pd10 t-r clearfix">
					<span class="fs12 gray write-tips fl mobile-hide">请尊重自己和别人的时间，不要发布垃圾和广告内容。</span><div class="fr"><button :class="['small',{'disabled':locked}]" @click="submit"><b :class="{'loading':locked}"></b><span v-text="submitMsg"></span></button></div>
				</div>
			</div>
		<?php endif; ?>
		</main><!-- #main -->
	</div><?php
get_sidebar();
get_footer();
