<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi2
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
$comment_sign = get_option('comment_registration') && !is_user_logged_in();
$can_comment = !zrz_current_user_can('comment') && is_user_logged_in();
?>

<div id="comments" class="comments-area mar16-t">
	<div class="box">
			<div class="comments-title clearfix l1 box-header">
				<span ref="commentCount">
					<?php
						printf(
							esc_html__( '%1$s 条回复', 'ziranzhi2' ),
							get_comments_number()
						);
					?>
				</span>
				<span class="dot"></span>
				<span class="comment-auth">A</span> <?php echo __('作者','ziranzhi2'); ?> <span class="dot"></span>
				<span class="comment-mod">M</span> <?php echo __('管理员','ziranzhi2'); ?>
				<div class="fr pos-r juzi mobile-hide mouh click" @click.stop="changeHellow"><i :class="[!helloLocked ? 'iconfont zrz-icon-font-write' : 'loading', 'pos-a']"></i> <span v-html="hellowMsg"></span></div>
			</div><!-- .comments-title -->
			<?php
				if ( have_comments() ) {
			?>
			<ol class="comment-list" ref="commentList">
				<?php
					wp_list_comments( array(
						'short_ping' => true,
						'callback' => 'zrz_comment_callback',
						'end-callback' => 'zrz_comment_callback_close',
						'max_depth'=>2
					) );
				?>
			</ol><!-- .comment-list -->
			<page-nav nav-type="comment" :paged="<?php echo (int)get_query_var('cpage'); ?>" :pages="<?php echo (int)get_comment_pages_count(); ?>" :show-type="'c'"></page-nav>
		<?php }else{ ?>
			<ol class="comment-list" ref="commentList">
				<div class="t-c fs14 b-t pd20 gray" ref="commentTips">所有的伟大，都源于一个勇敢的开始！</div>
			</ol>
		<?php } ?>
	</div>
	<div>
		<?php if ( ! comments_open() ) { ?>
			<p class="no-comments"><?php esc_html__( '评论已经关闭', 'ziranzhi2' ); ?></p>
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
							<p class="lm">您没有权限参与评论</p>
						</div>';
					}
				?>
				<form id="commentform" class="comment-form clearfix box mar16-t" @submit.stop.prevent="submit">
					<?php if ( is_user_logged_in() ) { $login = true;?>
						<div class="com-info fl mobile-hide <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>">
							<a href="<?php echo zrz_get_user_page_url(get_current_user_id()); ?>">
								<?php echo get_avatar(get_current_user_id(),48); ?>
							</a>
						</div>
					<?php } else { $login = false; ?>
						<div class="com-info fl mobile-hide <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>">
							<img class="avatar" style="background-color:<?php echo zrz_get_avatar_background_by_id(rand(0,9)); ?>" :src="avatar" />
						</div>
					<?php } ?>
					<div class="comment-input <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>">
						<?php if(!$login){ ?>
							<div :class="['comment-user-info','pd10','fs12','pos-r',{'cbb':!editBool}]" v-cloak><span v-if="!email && !author">欢迎您，新朋友，感谢参与互动！</span><span v-else>欢迎您 {{author}}，您在本站有{{commentsCount}}条评论</span> <button class="text pos-a" v-text="editText" @click.stop.prevent="edit"></button></div>
							<div v-show="editBool">
								<input id="author" :class="['fd','pd10',{'comment-input-error':errorName}]" name="author" v-model="author" type="text" value="" placeholder="称呼" @blur.stop.lazy="changeAvatar($event)" @focus.stop.lazy="changeAvatar($event)"><input id="email" :class="['fd','email','pd10',{'comment-input-error':errorEmail}]" name="email" type="email" value="" v-model="email" placeholder="邮箱" @focus.stop.lazy="emptyError">
							</div>
						<?php } ?>
						<textarea id="textarea" :class="['textarea','pd10','<?php if($login) echo 'bt'; ?>',{'comment-input-error':errorContent}]"  placeholder="说说你的看法" @blur.stop="contentBlur($event)" @focus.stop="contentBlur($event)" v-model="commentContent" ref="content"></textarea>
					</div>
					<div class="t-r mar10-t pos-r <?php echo $comment_sign || $can_comment ? 'bubble-blur' : ''; ?>" v-cloak>
						<div class="smile-image pos-a">
							<button class="text comment-smile mar10-r" type="text" @click.stop.prevent="smiley"><i :class="['iconfont', smileShow || smileShowF ? 'zrz-icon-font-sentiment_satisfied' : 'zrz-icon-font-sentiment_neutral']"></i></button>
							<label for="comment-image" class="comment-image mouh click" v-show="isLogin"><i class="iconfont zrz-icon-font-image click"></i></label>
							<div :class="['smile-box','pos-a','box','pjt','transform-out',{'transform-in':smileShow}]">
								<div class="clearfix" v-html="smiles"></div>
							</div>
							<div for="comment-image" class="image-box pos-a pjt" v-if="imageRemove">
								<span v-html="commentImage"></span>
								<div class="lm fs12 hide" v-if="!imageLocked"><label for="comment-image" class="mar10-r click mouh">更换</label><label class="click mouh" @click.stop="removeImg">删除</label></div>
							</div>
							<input ref="commentImgInput" id="comment-image" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="hide" @change="getFile($event)">
						</div>
						<span class="red fs12 mar10-r" v-text="errorMsg"></span>
						<button type="submit" :class="['small',submitLocked ? 'disabled' : '']" name="button" ><?php echo __('提交评论','ziranzhi2'); ?><b :class="[submitLocked ? 'loading' : '']"></b></button>
					</div>
					<input ref="comment_nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">
				</form>
			</div>
		<?php } ?>
	</div>
</div><!-- #comments -->
