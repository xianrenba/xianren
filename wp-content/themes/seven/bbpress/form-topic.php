<?php

/**
 * New/Edit Topic
 *
 * @package bbPress
 * @subpackage Theme
 */
$user_id = get_current_user_id();
if ( ! bbp_is_single_forum() ) : ?>

<div id="bbpress-forums" class="bbpress-wrapper">
<?php endif; ?>

<?php if ( zrz_user_can($user_id,'topic') ) : ?>

	<div id="new-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-form box">

		<form id="new-post" name="new-post" method="post" action="<?php if(zrz_is_page('newtopic',false)){ echo home_url('/newtopic'); }else{ echo get_the_permalink(); }; ?>">

			<?php do_action( 'bbp_theme_before_topic_form' ); ?>

			<fieldset class="bbp-form">
				<div class="pd10 b-b gray fs12">

					<?php
						if ( bbp_is_topic_edit() ) :
							echo '正在编辑帖子';
						else :
							( bbp_is_single_forum() && bbp_get_forum_title() )
								? printf( esc_html__( 'Create New Topic in &ldquo;%s&rdquo;', 'bbpress' ), bbp_get_forum_title() )
								: esc_html_e( 'Create New Topic', 'bbpress' );
						endif;
					?>

				</div>

				<?php do_action( 'bbp_theme_before_topic_form_notices' ); ?>

				<?php if ( ! bbp_is_topic_edit() && bbp_is_forum_closed() ) : ?>

					<div class="bbp-template-notice">
						<ul>
							<li><?php esc_html_e( 'This forum is marked as closed to new topics, however your posting capabilities still allow you to do so.', 'bbpress' ); ?></li>
						</ul>
					</div>

				<?php endif; ?>

				<?php do_action( 'bbp_template_notices' ); ?>

				<div class="w100">

					<?php bbp_get_template_part( 'form', 'anonymous' ); ?>

					<?php do_action( 'bbp_theme_before_topic_form_title' ); ?>

					<p>
						<input type="text" id="bbp_topic_title" class="pd10" ref="title" value="<?php bbp_form_topic_title(); ?>" size="40" placeholder="标题" name="bbp_topic_title" maxlength="<?php bbp_title_max_length(); ?>" />
					</p>

					<?php do_action( 'bbp_theme_after_topic_form_title' ); ?>

					<?php do_action( 'bbp_theme_before_topic_form_content' ); ?>

					<?php bbp_the_content( array( 'context' => 'topic','before'=>'<div class="bbs-toolbar-par"><div id="bbs-toolbar" class="pos-r clearfix b-t"  data-margin-top="60"></div></div><div id="bbs-forum" class="entry-content"></div><div class="bbp-the-content-wrapper">','editor_class' => 'bbp-the-content text-box' ) ); ?>

					<input type="file" accept="image/jpg,image/jpeg,image/png,image/gif" ref="getFile" @change="imgUpload($event)" style="display:none"/>
					<input ref="nonce" type="hidden" value="<?php echo wp_create_nonce(get_current_user_id()); ?>">

					<?php do_action( 'bbp_theme_after_topic_form_content' ); ?>

					<?php if ( ! bbp_is_single_forum() ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_forum' ); ?>

						<div class="pd10 b-t">
							<div class="mar10-b"><?php esc_html_e( 'Forum:', 'bbpress' ); ?></div>
							<?php
								bbp_dropdown( array(
									'selected'  => bbp_get_form_topic_forum()
								) );
							?>
						</div>

						<?php do_action( 'bbp_theme_after_topic_form_forum' ); ?>

					<?php endif; ?>

					<?php if ( current_user_can( 'moderate', bbp_get_topic_id() ) ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_type' ); ?>

						<div class="pd10 <?php echo bbp_is_topic_edit() ? '' : 'b-t'; ?>">

							<div class="mar10-b"><?php esc_html_e( 'Topic Type:', 'bbpress' ); ?></div>

							<?php bbp_form_topic_type_dropdown(); ?>

						</div>

						<?php do_action( 'bbp_theme_after_topic_form_type' ); ?>

						<?php do_action( 'bbp_theme_before_topic_form_status' ); ?>

						<div class="pd10">

							<div class="mar10-b"><?php esc_html_e( 'Topic Status:', 'bbpress' ); ?></div>

							<?php bbp_form_topic_status_dropdown(); ?>

						</div>

						<?php do_action( 'bbp_theme_after_topic_form_status' ); ?>

					<?php endif; ?>

					<?php if ( bbp_is_subscriptions_active() && ! bbp_is_anonymous() && ( ! bbp_is_topic_edit() || ( bbp_is_topic_edit() && ! bbp_is_topic_anonymous() ) ) ) : ?>

						<?php do_action( 'bbp_theme_before_topic_form_subscriptions' ); ?>

						<div class="pd10">
							<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" class="radio" value="bbp_subscribe" <?php bbp_form_topic_subscribed(); ?> />

							<?php if ( bbp_is_topic_edit() && ( bbp_get_topic_author_id() !== bbp_get_current_user_id() ) ) : ?>

								<label for="bbp_topic_subscription"><?php esc_html_e( 'Notify the author of follow-up replies via email', 'bbpress' ); ?></label>

							<?php else : ?>

								<label for="bbp_topic_subscription"><?php esc_html_e( 'Notify me of follow-up replies via email', 'bbpress' ); ?></label>

							<?php endif; ?>
						</div>

						<?php do_action( 'bbp_theme_after_topic_form_subscriptions' ); ?>

					<?php endif; ?>

					<?php do_action( 'bbp_theme_before_topic_form_submit_wrapper' ); ?>

					<div class="bbp-submit-wrapper clearfix pd10 b-t">

						<?php do_action( 'bbp_theme_before_topic_form_submit_button' ); ?>

						<div class="gray fl pd5-b-t fs12">论坛已经启用垃圾过滤机制，请不要发布垃圾信息</div><div class="fr"><span class="red mar10-r fs12" v-text="error" style="vertical-align: -4px;"></span><button type="submit" id="bbp_topic_submit" name="bbp_topic_submit" class="button submit fr" @click.stop.lazy="sendData"><?php esc_html_e( 'Submit', 'bbpress' ); ?></button></div>

						<?php do_action( 'bbp_theme_after_topic_form_submit_button' ); ?>

					</div>

					<?php do_action( 'bbp_theme_after_topic_form_submit_wrapper' ); ?>

				</div>

				<?php bbp_topic_form_fields(); ?>

			</fieldset>

			<?php do_action( 'bbp_theme_after_topic_form' ); ?>

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

<?php elseif ( bbp_is_forum_closed() ) : ?>

	<div id="forum-closed-<?php bbp_forum_id(); ?>" class="bbp-forum-closed">
		<div class="bbp-template-notice pd10 box">
			<ul>
				<li><?php printf( esc_html__( 'The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress' ), bbp_get_forum_title() ); ?></li>
			</ul>
		</div>
	</div>

<?php else : ?>

	<div id="no-topic-<?php bbp_forum_id(); ?>" class="bbp-no-topic">
		<?php if(is_user_logged_in()){ ?>
			<div class="loading-dom pos-r">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">您没有权限发表话题</p></div>
			</div>
		<?php }else{ ?>
			<div class="pd20 t-c box bbp-login">
				<p class="mar20-b gray fs14">创建话题</p>
				<button class="empty mar10-r" @click="bbpLogin('in')">登陆</button><button @click="bbpLogin('up')">快速注册</button>
			</div>
		<?php } ?>
	</div>

<?php endif; ?>

<?php if ( ! bbp_is_single_forum() ) : ?>

</div>

<?php endif;
