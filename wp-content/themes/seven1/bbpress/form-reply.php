<?php

/**
 * New/Edit Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

if ( bbp_is_reply_edit() ) : ?>

<div id="bbpress-forums" class="bbpress-wrapper">

<?php endif; ?>

<?php if(zrz_current_user_can('reply')) : ?>

	<?php if ( bbp_current_user_can_access_create_reply_form() ) : ?>

		<div id="new-reply-<?php bbp_topic_id(); ?>" class="bbp-reply-form box">

			<form id="new-post" name="new-post" method="post" action="<?php the_permalink(); ?>" >

				<?php do_action( 'bbp_theme_before_reply_form' ); ?>

				<fieldset class="bbp-form">
					<div class="pd10 b-b gray-d fs12 gray"><?php echo bbp_is_reply_edit() ? '编辑话题回复' : '参与讨论' ?></div>

					<?php do_action( 'bbp_theme_before_reply_form_notices' ); ?>

					<?php if ( ! bbp_is_topic_open() && ! bbp_is_reply_edit() ) : ?>

						<div class="bbp-template-notice">
							<ul>
								<li><?php esc_html_e( 'This topic is marked as closed to new replies, however your posting capabilities still allow you to do so.', 'bbpress' ); ?></li>
							</ul>
						</div>

					<?php endif; ?>

					<?php do_action( 'bbp_template_notices' ); ?>

						<?php bbp_get_template_part( 'form', 'anonymous' ); ?>

						<?php do_action( 'bbp_theme_before_reply_form_content' ); ?>

						<?php bbp_the_content( array( 'context' => 'reply' ,'before'=>'<div class="bbs-toolbar-par"><div id="bbs-toolbar" class="pos-r clearfix" data-margin-top="60"></div></div><div id="bbs-forum" class="entry-content"></div><div  class="bbp-the-content-wrapper">','editor_class' => 'bbp-the-content text-box') ); ?>

						<input type="file" accept="image/jpg,image/jpeg,image/png,image/gif" ref="getFile" @change="imgUpload($event)" style="display:none"/>
						<input ref="nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">

						<?php do_action( 'bbp_theme_after_reply_form_content' ); ?>

						<?php do_action( 'bbp_theme_before_reply_form_submit_wrapper' ); ?>

						<div class="bbp-submit-wrapper clearfix pd10 b-t">

							<?php do_action( 'bbp_theme_before_reply_form_submit_button' ); ?>

							<?php bbp_cancel_reply_to_link(); ?>

							<div class="gray fl pd5-b-t fs12">论坛已经启用垃圾过滤机制，请不要发布垃圾信息</div><div class="fr"><span class="red mar10-r fs12" v-text="error" style="vertical-align: -4px;"></span><button type="submit" id="bbp_reply_submit" name="bbp_reply_submit" class="button submit fr" @click.stop.lazy="sendData"><?php esc_html_e( 'Submit', 'bbpress' ); ?></button></div>

							<?php do_action( 'bbp_theme_after_reply_form_submit_button' ); ?>

						</div>

						<?php do_action( 'bbp_theme_after_reply_form_submit_wrapper' ); ?>

					<?php bbp_reply_form_fields(); ?>

				</fieldset>

				<?php do_action( 'bbp_theme_after_reply_form' ); ?>

			</form>
			<div id="video-form" :class="['dialog', 'video-form',{'dialog--open':showMediaForm}]" ref="mediaForm" v-cloak>
				<div class="dialog__overlay" @click.stop="closeViedoForm"></div>
				<div class="dialog__content">
					<div class="" v-show="showMediaForm == 'video'">
						<div class="pd10 b-b">
							请输入视频网址
						</div>
						<div class="pd10">
							<textarea placeholder="视频网址" ref="videoUrl" v-model="videoUrl"></textarea>
							<p class="fs12 mar5-t">目前支持优酷、搜狐视频、腾讯视频、爱奇艺、哔哩哔哩，酷6、华数、乐视、YouTube 等网站</p>
							<button :class="['empty','mar20-t',{'disabled':locked}]" @click="getVideo()"><b :class="{'loading':locked}"></b>插入</button>
							<span class="red fs12 mar10-l" v-text="videoError"></span>
						</div>
						<div class="bg-blue-light pd20 b-t fs12 gray mar10-t">
							请不要添加无关的视频，详情查看 本站视频使用规范。
							上传视频，即代表你同意《本站用户协议》。
						</div>
					</div>
					<div class="" v-show="showMediaForm == 'image'">
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

	<?php elseif ( bbp_is_topic_closed() ) : ?>

		<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply box">
			<div class="bbp-template-notice">
				<ul>
					<li><?php printf( esc_html__( 'The topic &#8216;%s&#8217; is closed to new replies.', 'bbpress' ), bbp_get_topic_title() ); ?></li>
				</ul>
			</div>
		</div>

	<?php elseif ( bbp_is_forum_closed( bbp_get_topic_forum_id() ) ) : ?>

		<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply box">
			<div class="bbp-template-notice">
				<ul>
					<li><?php printf( esc_html__( 'The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress' ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?></li>
				</ul>
			</div>
		</div>

	<?php else : ?>

		<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply box">
			<div class="bbp-template-notice">
				<ul>
					<li><?php is_user_logged_in()
						? esc_html_e( 'You cannot reply to this topic.',               'bbpress' )
						: esc_html_e( 'You must be logged in to reply to this topic.', 'bbpress' );
					?></li>
				</ul>
			</div>
		</div>
	<?php endif; ?>

<?php else : ?>

	<div id="no-topic" class="bbp-no-topic">
		<?php if(is_user_logged_in()){ ?>
			<div class="loading-dom pos-r">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">您没有权限参与讨论</p></div>
			</div>
		<?php }else{ ?>
			<div class="pd20 t-c box bbp-login">
				<p class="mar20-b gray fs14">参与讨论</p>
				<button class="empty mar10-r" @click="bbpLogin('in')">登陆</button><button @click="bbpLogin('up')">快速注册</button>
			</div>
		<?php } ?>
	</div>
<?php endif; ?>

<?php if ( bbp_is_reply_edit() ) : ?>

</div>

<?php endif;
