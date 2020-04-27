<?php
//财富页面
get_header();
$user_id = get_current_user_id();
$_user_id = isset($_GET['uid']) ? $_GET['uid'] : '';
$allow = true;
if(($_user_id && current_user_can('delete_users')) && $_user_id != $user_id){
	$user_id = $_user_id;
	$allow = false;
}
$name = zrz_get_credit_settings('zrz_credit_name');
$rmb = zrz_get_credit_settings('zrz_credit_rmb');
$y = zrz_get_rmb($user_id);
$rmb_nub = get_user_meta($user_id,'zrz_rmb',true);

$tx = zrz_get_credit_settings('zrz_tx_min');
$cc = (float)zrz_get_credit_settings('zrz_cc');
$tx_allowed = zrz_get_credit_settings('zrz_tx_allowed');
$qcode = get_user_meta($user_id,'zrz_qcode',true);

wp_localize_script( 'ziranzhi2-gold', 'zrz_gold',array(
	'change_credit'=>$rmb,
	'name'=>$name,
	'tx'=>$tx,
	'cc'=>$cc,
	'tx_allowed'=>$tx_allowed,
	'allow_withdraw'=>isset($qcode['weixin']) ? 1 : isset($qcode['alipay']) ? true : false
));
?>
<div id="primary" class="content-area fd">
	<main id="gold" class="site-main gold-page box pos-r" role="main" ref="gold" data-uid="<?php echo $user_id;?>">
		<?php if(!is_user_logged_in()){ ?>
			<div class="loading-dom pos-r box" ref="nologin">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先登录</p></div>
			</div>
		<?php }else{ ?>
		<div class="pd10 box-header b-b fs12 clearfix" data-rmb="<?php echo $rmb_nub; ?>" ref="rmb">
			<div class="fl"><?php echo !$allow ? get_the_author_meta('display_name',$user_id) : '我'; ?>的财富</div>
			<div class="fr">
				<a href="<?php echo esc_url(zrz_get_custom_page_link('top')); ?>">财富排行</a>
			</div>
		</div>
		<div class="pd20 clearfix b-b gold-less">
		<span class="gold-title mar20-r">余额：</span><?php echo $y; ?><?php if($allow) { ?><div class="fr"><button class="empty mar10-r" v-show="txAllowed == 1" @click="show = true;type = 'tx'" v-cloak>提现</button><button @click="show = true;type = 'cz'">充值</button></div><?php } ?>
		</div>
		<div class="pd20 clearfix b-b gold-less">
			<span class="gold-title mar20-r"><?php echo $name; ?>：</span><?php echo zrz_coin($user_id); ?><?php if($allow) { ?><button class="fr" @click="show = true;type = 'gm'">购买<?php echo $name; ?></button><?php } ?>
		</div>
		<div class="pd20 clearfix gold-list-form" v-cloak ref="header">
			<button :class="['tab-title','mar5-r','text',{'picked':acType == 'credit'},'mar10-l']" @click="changeType('credit')" v-cloak><?php echo $name; ?>明细</button>
		<?php if($tx_allowed) { ?><button :class="['tab-title','mar5-r','text',{'picked':acType == 'tx'}]" @click="changeType('tx')" v-cloak>提现记录</button><?php } ?>
			<div class="loading-dom pos-r box" v-if="locked">
				<div class="lm"><span class="loading"></span></div>
			</div>
			<div class="" v-else-if="data.length < 1">
				<div class="loading-dom pos-r box" ref="nologin" v-cloak>
					<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">没有数据</p></div>
				</div>
			</div>
			<div v-else>
		        <table cellpadding="5" cellspacing="0" border="0" width="100%" class="gold-tab b-r b-b l1 fs13" v-cloak>
					<tbody v-if="acType == 'credit'">
						<tr class="tab-head">
							<td width="100" class="mobile-hide">时间</td>
							<td width="120" class="mobile-hide">类型</td>
							<td width="80">数额</td>
							<td width="80"><?php echo $name; ?>余额</td>
							<td width="auto">描述</td>
						</tr>
						<template v-for="item in data">
							<tr v-if="item.type == 2 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">评论奖励</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">创建了评论 <span class="dot">›</span> <span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 24 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">冒泡奖励</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">冒了一个泡泡 <span class="dot">›</span> <span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 4 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">注册奖励</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><?php echo zrz_get_html_code(zrz_get_display_settings('sigup_welcome')); ?></td>
							</tr>
							<tr v-if="item.type == 9 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">评论喜欢</td>
								<td class="shu red">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">你喜欢<span class="dot" v-html="item.users"></span>的评论<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 17 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">创建话题</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">创建了话题<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 18 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">回复话题</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">回复了话题<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 19 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">话题被回复</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>参与了你的话题讨论<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 20 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">话题中被提起</td>
								<td class="shu">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>在话题中提到你<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 1 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">评论被回复</td>
								<td class="shu blue">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>回复了你的评论<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 3 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">文章被评论</td>
								<td class="shu blue">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>评论了你的文章<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 5 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">发表文章</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">你的文章通过审核<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 11 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">被关注</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>关注了你</td>
							</tr>
							<tr v-if="item.type == 15 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">取消关注</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>取消了对你的关注</td>
							</tr>
							<tr v-if="item.type == 10 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">评论被喜欢</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>喜欢你的评论<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 14 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide"><?php echo $name; ?>变更</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>对你的<?php echo $name; ?>进行了变更</td>
							</tr>
							<tr v-if="item.type == 33 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">购买文章</td>
								<td class="shu red">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">你用<?php echo $name; ?>购买了文章<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 34 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">文章出售</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span>用<?php echo $name; ?>购买了你的文章<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 36 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">发表研究</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">你的研究项目通过了审核<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 28 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide"><?php echo $name; ?><?php echo $name; ?>购买</td>
								<td class="shu red">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><?php echo $name; ?>购买了商品<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 29 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide"><?php echo $name; ?>抽奖</td>
								<td class="shu red">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><?php echo $name; ?>抽奖<span class="dot">›</span><span v-html="item.post_title"></span></td>
							</tr>
							<tr v-if="item.type == 38 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide"><?php echo $name; ?>兑换</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您成功兑换了<?php echo $name; ?></td>
							</tr>
							<tr v-if="item.type == 16 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">签到奖励</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">签到奖励</td>
							</tr>
							<tr v-if="item.type == 39 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">邀请奖励</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您邀请了<span class="dot" v-html="item.users"></span>在本站注册</td>
							</tr>
							<tr v-if="item.type == 40 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">接受邀请</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您接受了<span class="dot" v-html="item.users"></span>的邀请在本站注册</td>
							</tr>
							<tr v-if="item.type == 30 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">商品购买奖励</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">购买<span class="dot"></span><span v-html="item.post_title"></span> 获得奖励</td>
							</tr>
							<tr v-if="item.type == 42 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">关注某人奖励</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您关注了 <span v-html="item.users"></span> 获得奖励</td>
							</tr>
							<tr v-if="item.type == 43 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">取消关注</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您取消了对 <span v-html="item.users"></span> 的关注</td>
							</tr>
							<tr v-if="item.type == 44 && item.credit_total != 0 && item.credit !=0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide"><?php echo $name; ?>活动参与</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he"><span v-html="item.users"></span> 参与了您发起的活动 <span v-html="item.post_title"></td>
							</tr>
							<tr v-if="item.type == 45 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide"><?php echo $name; ?>活动报名</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您报名参加了<span v-html="item.users"></span> 的活动 <span v-html="item.post_title"></td>
							</tr>
							<tr v-if="item.type == 46 && item.credit_total != 0">
								<td class="mobile-hide"><time :data-timeago="item.date" class="timeago fs12 dot">{{item.date}}</time></td>
								<td class="mobile-hide">邀请码注册奖励</td>
								<td class="shu green">{{item.credit}}</td>
								<td class="yue">{{item.credit_total}}</td>
								<td class="he">您使用了邀请码注册</td>
							</tr>
						</template>
					</tbody>
					<tbody v-else-if="acType == 'tx'">
						<tr class="tab-head">
							<td width="100" class="mobile-hide">交易ID</td>
							<td width="80">时间</td>
							<td width="80">提现金额</td>
							<td width="80">状态</td>
						</tr>
						<tr v-for="item in data">
							<td class="mobile-hide">{{item.id}}</td>
							<td class="">{{item.date}}</td>
							<td class="shu">{{item.credit}}</td>
							<td class="yue"><span class="green" v-if="item.status == 1">已支付</span><span class="red" v-else>未支付</span></td>
						</tr>
					<tbody>
				</table>
			</div>
			<page-nav class="b-b b-l b-r" nav-type="gold" :paged="paged" :pages="pages" :locked-nav="1" v-show="!locked && pages > 1" v-cloak></page-nav>
		</div>
		<?php } ?>
		<payment :show="show" :type-text="type == 'gm' ? '购买<?php echo $name; ?>' : type == 'cz' ? '余额充值' : '余额提现'" :type="type" :price="price" :data="price" @close-form="closeForm"></payment>
    </main>
</div><?php
get_sidebar();
get_footer();
