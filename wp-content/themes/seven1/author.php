<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi2
 */
get_header();
$can = current_user_can('delete_users');
$user_id =  get_query_var('author');

$current_user_id = get_current_user_id();

$self = ($user_id == $current_user_id || $can) ? true : false;
$type = get_query_var('zrz_user_page');
$user_data = get_userdata($user_id);
?>
<div class="box mar16-b author-top">
	<div id="user-cover" class="user-cover pos-r" :style="'overflow:'+(toolShow ? 'inherit;' : 'hidden')">
		<label for="cover-input" class="cover-button pos-a fs14 mouh" v-show="coverButton && self == 1">
			<i class="iconfont zrz-icon-font-photo"></i><?php echo __('编辑封面图片','ziranzhi2'); ?>
			<input id="cover-input" type="file" accept="image/jpg,image/jpeg,image/png" class="hide" ref="input" @change="getFile($event)">
		</label>
		<input ref="cover_nonce" type="hidden" value="<?php echo wp_create_nonce($user_id); ?>">
		<div :class="[{'cover-index':toolShow},'cover-img-box','img-bg',{'blur':!coverSrc}]" :style="{backgroundImage:'url('+(isMobile == 1 && coverSrc ? coverSrc : avatar)+')',backgroundColor:'<?php echo zrz_get_avatar_background_by_id($user_id); ?>'}">
			<img :src="coverSrc" :class="[{'hide':!coverSrc},'pos-a','cover-img',{'mou-n':toolShow}]" :style="{'top':coverImgTop+'px'}" @mousedown = "coverDrag($event)" v-if="coverSrc && isMobile == 0"/>
		</div>
		<div :class="[{'cover-index':toolShow},'cover-tool','fs12','pd10','pos-a','st10']" v-show="toolShow" v-cloak>
			<span v-html="toolMsg" class="fs14 pd5 fd"></span>
			<div class="pos-a cover-tool-button">
				<button class="text mar10-r" @click="coverCancle"><?php echo __('取消','ziranzhi2'); ?></button>
				<button :class="['small',{'disabled':locked}]" v-html="save" @click="saveCover"></button>
			</div>
		</div>
	</div>
	<div id="user-avatar" class="user-avatar pos-r">
		<div class="avatar-img-box pos-a bg-w">
			<label :for="[!locked ? 'avatar-input' : '']" :class="['pos-a','fs14',{'mouh':!locked},'avatar-input','t-c',{'show':showMsg}]" v-show="self == 1" v-cloak>
				<span class="lm">
					<i :class="[avatarIcon ? 'zrz-icon-font-photo' : 'zrz-icon-font-error','iconfont']"></i><p class="mar5-t fs14 mobile-hide" v-html="avatarMsg"></p>
				</span>
				<input id="avatar-input" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="hide" @change="getFile($event)">
			</label>
			<img class="avatar" style="background-color:<?php echo zrz_get_avatar_background_by_id($user_id); ?>" :src="[avatarSrc ? avatarSrc : '']"/>
		</div>
		<div class="user-info pd20">
			<h1 class="l1">
				<?php echo esc_attr(zrz_get_content_ex($user_data->display_name,16)); ?><span class="h1-lv fs13 mar10-l"><?php echo zrz_get_lv($user_id,'name'); ?></span>
			</h1>
			<p class="user-des mar10-b mar10-t">
				<span class="fd">
					<?php echo $user_data->description ? mb_strimwidth(strip_tags($user_data->description), 0, 150 ,"...") : __('没有个人说明','ziranzhi2'); ?>
				</span>
			</p>
			<p class="user-info-p fs14 gray">
				<?php
					printf(
						esc_html('%1$s 第 %2$s 号会员，加入于 %3$s'),
						get_bloginfo( 'name' ),
						$user_id,
						get_date_from_gmt($user_data->user_registered)
					);
				?>
			</p>
		</div>
		<?php if($type == 'setting' &&  $self) { ?>
			<span class="fs14 pos-a backuserhome"><a href="<?php echo home_url('/user/'.$user_id); ?>"><?php if(!zrz_wp_is_mobile()) {echo '返回个人主页';}else{echo '返回';}; ?> ❯</a></span>

			<?php
				get_template_part( 'template-parts/user','setting');
			?>

			<?php }elseif($type == 'setting' && (!is_user_logged_in() || !$self)){
				echo '<span class="fs14 pos-a backuserhome"><a href="'.home_url('/user/'.$user_id).'">返回个人主页 ❯</a></span>';
				echo '<div class="loading-dom pos-r b-t"><div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">当前页面无法访问</p></div></div>';
			}else{ ?>
			<div class="user-button-r pos-a t-r" v-cloak>
				<span v-if="self == 1">
					<a class="empty button" href="<?php echo zrz_get_user_page_url($user_id).'/setting'; ?>"> <?php _e('编辑个人资料','ziranzhi2'); ?></a>
				</span>
				<span class="no-login-edit-button" v-else>
					<button @click="follow" :class="[{'followed':followed},{'disabled':followLocked}]">
						<b :class="{'loading':followLocked}"></b>
						<span v-html="followText" :class="{'hide':followed}"></span>
						<span v-if="followed" :class="[{'hide':!followed},'show']"><i class="iconfont zrz-icon-font-duihao"></i> 已关注</span>
					</button>
					<button class="gray mar10-l" @click="msg"><i class="iconfont zrz-icon-font-message1"></i> <?php _e('发私信','ziranzhi2'); ?></button>
				</span>
				<?php if($can && $user_id != $current_user_id){ ?>
					<div class="mar20-t">
						<button @click="follow" :class="[{'followed':followed},{'disabled':followLocked}]">
							<b :class="{'loading':followLocked}"></b>
							<span v-html="followText" :class="{'hide':followed}"></span>
							<span v-if="followed" :class="[{'hide':!followed},'show']"><i class="iconfont zrz-icon-font-duihao"></i> 已关注</span>
						</button>
						<button class="gray mar10-l" @click="msg"><i class="iconfont zrz-icon-font-message1"></i> <?php _e('发私信','ziranzhi2'); ?></button>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<msg-box :show="showBox" :tid="uid" :tname="uname" :mtype="mtype" title="发送私信" @close-form="closeForm"></msg-box>
	</div>
</div>
<?php if($type !== 'setting') : ?>
	<div id="author" class="content-area fd">
		<main id="main" class="site-main box fs12">

			<div class="author-menu b-b">
				<?php echo zrz_user_page_link_nav($type,$self,$user_id); ?>
			</div>

			<?php
				//用户页面信息
				if($type){
					get_template_part( 'template-parts/user',$type);
				}else{
					get_template_part( 'template-parts/user','activities');
				}
			?>

		</main><!-- #main -->
	</div><?php
get_sidebar();
echo '<div class="h20"></div>';
endif;
get_footer();
