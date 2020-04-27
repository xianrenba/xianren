<?php
/**
 * 通知页面
 */
get_header();
$user_id = get_current_user_id();
?>
<div id="primary" class="content-area fd">
	<?php if(!is_user_logged_in()){ ?>
		<div class="loading-dom pos-r box" ref="nologin">
			<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先登录</p></div>
		</div>
	<?php }else{ ?>
	<main id="notifications" class="site-main notifications-page box pos-r" role="main" ref="notifications">
		<div class="pd10 li clearfix fs12 b-b box-header" ref="header"><span class="fl">共收到了<i v-text="count"></i>条消息</span><span class="fr"><i v-text="countNew"></i>条新消息</span></div>
		<loading :ac="ac" :msg="msg" v-if="!data"></loading>
		<ul v-else v-cloak>
			<li v-for="item in data" class="pd10 pos-r">
				<div v-if="item.type == 1" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 在<span v-html="item.post_title"></span>中回复了您的评论 <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 3" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 评论了您的<span v-html="item.post_title"></span><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 23" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 申请了友情链接，请审核 <a target="_blank" href="<?php echo home_url('/wp-admin/link-manager.php');?>">查看</a><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-if="item.comment" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 28" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 恭喜，您已经成功兑换了 <span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 5" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 恭喜，您的 <span v-html="item.post_title"></span> 通过了审核。<time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 30" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 恭喜，您已经成功购买了 <span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 29" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> <span v-text="item.comment == 'z' ? ' 恭喜，您抽中奖了，奖品是：' : ' 抱歉，没有抽中：'"></span> <span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 31" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 恭喜，您已经成功购买了付费阅读的内容：<span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 32" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 购买了您的付费内容： <span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 33" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 恭喜， 您使用积分购买了付费内容：<span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 34" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 购买了您的积分阅读内容： <span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 10" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> <b class="green">喜欢</b> 您在 <span v-html="item.post_title"></span> 中的评论。<time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 8" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> <b class="red">不喜欢</b> 您在 <span v-html="item.post_title"></span> 中的评论。<time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 35" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 对您的文章进行了打赏 ：<span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 19" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 回复了您的帖子 ：<span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 20" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 在话题 <span v-html="item.post_title"></span> 中提到了您！<time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 36" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 恭喜，您的研究项目 <span v-html="item.post_title"></span> 通过了审核。<time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 11" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 关注了您 <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 15" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 取消了对您的关注 <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 14" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 对您的<b>积分</b>进行了变更，变更原因： <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><div class="msg-content" v-html="item.comment"></div><p class="fs12 mar10-t">变更数额：{{item.credit}}</p></div>
				</div>
				<div v-if="item.type == 37" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 对您的<b>余额</b>进行了变更，变更原因： <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div><p class="msg-content" v-html="item.comment"></p></div>
				</div>
				<div v-if="item.type == 12" class="pos-r fs14 msg-item gray">
					<span v-html="item.users"></span> 给您发来了私信： <a href="<?php echo home_url('/directmessage'); ?>">查看 ❯</a> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 4" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> <span>欢迎光临本站！</span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
					<div>
						<div class="msg-content"><?php echo zrz_get_html_code(zrz_get_display_settings('sigup_welcome')); ?></div>
					</div>
				</div>
				<div v-if="item.type == 39" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> <span>您已成功邀请 <span v-html="item.users"></span> 来本站注册！</span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 40" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> <span>通过 <span v-html="item.users"></span> 的邀请，您已经成功在本站注册，并获得了奖励！</span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 44" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> <span v-html="item.users"></span> 报名参加了您的活动 <span v-html="item.post_title"></span> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<div v-if="item.type == 41" class="pos-r fs14 msg-item gray">
					<span><?php echo get_avatar($user_id,48); ?></span> 有新的提现申请 <span v-html="item.post_title"></span><a href="<?php echo home_url('/withdraw'); ?>">提现</a> <time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time>
				</div>
				<i class="iconfont zrz-icon-font-new red pos-a l1" v-show="item.new == '0'"></i>
			</li>
			<page-nav class="b-t" nav-type="notifications" :paged="paged" :pages="pages" :locked-nav="1"></page-nav>
		</ul>
		<?php } ?>
	</main>
</div><?php
get_sidebar();
get_footer();
