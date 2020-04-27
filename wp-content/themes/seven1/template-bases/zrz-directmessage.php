<?php
//私信页面
get_header();
$can_dmsg = !zrz_current_user_can('message');
?>
<div id="primary" class="content-area fd">
	<main id="directmessage" class="site-main gold-page pos-r" role="main" ref="gold">
		<?php if(!is_user_logged_in()){ ?>
			<div class="loading-dom pos-r box" ref="nologin">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先登录</p></div>
			</div>
		<?php }else{ ?>
			<template v-if="showList">
				<div class="box" v-if="type=='index'" v-cloak>
					<div class="clearfix pd10 b-b"><div class="mar5-t fl fs16">收件箱</div><button class="fr empty" @click="showBox">写消息</button></div>
				        <div class="dmsg-list" v-if="contextUsers.length > 0" v-cloak>
							<ul>
								<li v-for="user in contextUsers" :class="['pd10','pos-r',{'unread':user.read == 0}]" :id="'dmsg'+user.user_id">
									<a :href="user.link" class="davatar pos-a"><img :style="'background-color:'+user.color" :src="user.avatar" /></a>
									<div class="dmsg-content">
										<span class="fs14"><a :href="user.link">{{user.name}}</a>：</span>
										<p v-html="user.msg" class="fs14"></p>
										<div class="clearfix mar10-t gray">
											<time :data-timeago="user.date" class="timeago fs12 fl">{{user.date}}</time>
											<span class="fr"><a class="text mar10-l button" :href="'<?php echo home_url('directmessage/'); ?>'+user.user_id">查看对话</a><button class="text mar10-l" @click="reply(user.name,user.user_id)">回复</button><button class="text mar10-l" @click="removeDmsg(user.user_id)">删除</button></span>
										</div>
									</div>
								</li>
							</ul>
							<page-nav class="b-t" :nav-type="'dmsg'" :paged="paged" :pages="pages" :locked-nav="1"></page-nav>
				        </div>
						<div v-else v-cloak ref="noMsg">
							<div class="loading-dom pos-r" style="">
								<div class="lm">
									<i class="iconfont zrz-icon-font-wuneirong"></i>
									<p class="mar10-t">没有私信</p>
								</div>
							</div>
						</div>
					<msg-box :show="show" :title="'私信'" :tid="userId" :tname="userName" :mtype="mtype" @close-form="closeForm"></msg-box>
				</div>
			</template>
			<template v-else-if="type=='single'" v-cloak>
				<div class="dmsg-single" v-if="msgData.length > 0">
					<div class="box">
						<div class="pd10 b-b clearfix">
							<span class="mar5-t fs12 gray">您与<a :href="duserData.link" class="dot" target="_blank"><b v-text="duserData.name"></b></a>共有<span v-text="countDh" class="dot"></span> 条对话</span>
						</div>
						<ul class="pd10">
							<li v-for="data in msgData" class="pos-r">
								<div v-if="data.msg_key == cuserData.id" class="t-r">
									<span v-html="cuserData.avatar" class="tavatar pos-a"></span>
									<div class="dmsgu-content">
										<div class="fs12 gray">
											<span v-text="cuserData.name"></span><span class="dot"></span><time :data-timeago="data.msg_date" class="timeago fs12">{{data.msg_date}}</time>
										</div>
										<div class="fs14 content-r bor-3" v-html="data.msg_value"></div>
									</div>
								</div>
								<div class="t-l" v-else>
									<span v-html="duserData.avatar" class="tavatar pos-a"></span>
									<div class="dmsgu-content">
										<div class="fs12 gray">
											<span><a :href="duserData.link" v-text="duserData.name"></a></span><span class="dot"></span><time :data-timeago="data.msg_date" class="timeago fs12">{{data.msg_date}}</time>
										</div>
										<div class="fs14 content-l bor-3" v-html="data.msg_value"></div>
									</div>
								</div>
							</li>
						</ul>
						<page-nav class="b-t" :nav-type="'dmsgUser'" :paged="paged" :pages="pages" :locked-nav="1"></page-nav>
					</div>
					<div class="box mar10-t pos-r">
						<?php if($can_dmsg){ ?> 
							<div class="sign-comment">
								<p class="lm">您没有权限发私信</p>
							</div>
						<?php } ?>
						<div class="pd10 <?php echo $can_dmsg ? 'bubble-blur' : ''; ?>"><span class="mar5-t fs12 gray">发私信给<a :href="duserData.link" class="dot" target="_blank"><b v-text="duserData.name"></b></a></div>
						<div class="pd10 <?php echo $can_dmsg ? 'bubble-blur' : ''; ?>">
							<textarea id="textarea" placeholder="私信内容" class="pd10 textarea" ref="msgContent" v-model="dmsgContent" @blur.lazy="content($event)" @focus.lazy="content($event)"></textarea>
							<div class="t-r mar10-t pos-r dmsg-submit clearfix">
								<button class="text comment-smile mar10-r fl" type="text" @click.stop.prevent="smiley"><i :class="['iconfont', smileShow || smily ? 'zrz-icon-font-sentiment_satisfied' : 'zrz-icon-font-sentiment_neutral']"></i></button>
								<div class="fr">
									<span v-html="sedErrorMsg" class="fs12 mar10-r"></span>
									<button :class="['empty',{'disabled':sedLocked}]" @click="msgSubmit"><b :class="sedLocked ? 'loading' : ''"></b>立刻送出</button>
								</div>
								<div :class="['smile-box','pos-a','box','pjt','transform-out',{'transform-in':smileShow}]">
									<div class="clearfix" v-html="smiles"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div v-else>
					<div class="loading-dom pos-r hide" ref="showTips"><div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有私信往来</p></div></div>
				</div>
			</template>
		<?php } ?>
		<div class="loading-dom pos-r" ref="loading">
			<div class="lm"><div class="loading"></div></div>
		</div>
    </main>
</div><?php
get_sidebar();
get_footer();
