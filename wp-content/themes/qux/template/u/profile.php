<?php

//~ 个人资料
if( $oneself ){
	$user_id = $curauth->ID;
	$avatar = $user_info->um_avatar;
	$qq = um_is_open_qq();
	$weibo = um_is_open_weibo();
	$weixin = um_is_open_weixin();
	if( isset($_POST['update']) && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
		$message = __('没有发生变化','um');	
		$update = sanitize_text_field($_POST['update']);
		if($update=='info'){
			$update_user_id = wp_update_user( array(
				'ID' => $user_id, 
				'nickname' => sanitize_text_field($_POST['display_name']),
				'display_name' => sanitize_text_field($_POST['display_name']),
				'user_url' => esc_url($_POST['url']),
				'description' => $_POST['description'],
				'um_gender' => $_POST['um_gender']
			 ) );
			if (($_FILES['file']['error'])==0&&!empty($_FILES['file'])) {
				define( 'AVATARS_PATH', ABSPATH.'/wp-content/uploads/avatars/' );
				$filetype=array("jpg","gif","bmp","jpeg","png");
    			$ext = pathinfo($_FILES['file']['name']);
    			$ext = strtolower($ext['extension']);
    			$tempFile = $_FILES['file']['tmp_name'];
    			$targetPath   = AVATARS_PATH;
    			if( !is_dir($targetPath) ){
        			mkdir($targetPath,0755,true);
    			}
    			$new_file_name = 'avatar-'.$user_id.'.'.$ext;
    			$targetFile = $targetPath . $new_file_name;
    			if(!in_array($ext, $filetype)){
    				$message = __('仅允许上传JPG、GIF、BMP、PNG图片','um');
    			}else{
    				move_uploaded_file($tempFile,$targetFile);
    				if( !file_exists( $targetFile ) ){
	        			$message = __('图片上传失败','um');
    				} elseif( !$imginfo=um_getImageInfo($targetFile) ) {
        				$message = __('图片不存在','um');
    				} else {
        				$img = $new_file_name;
        				um_resize($img);
        				$message = __('头像上传成功','um');
        				$update_user_avatar = update_user_meta( $user_id , 'um_avatar', 'customize');
						$update_user_avatar_img = update_user_meta( $user_id , 'um_customize_avatar', $img);
   	 				}
   	 			}
			} else {
	    		$update_user_avatar = update_user_meta( $user_id , 'um_avatar', sanitize_text_field($_POST['avatar']) );
				if ( ! is_wp_error( $update_user_id ) || $update_user_avatar ) $message = __('基本信息已更新','um');	
			}
		}
		if($update=='info-more'){
			$update_user_id = wp_update_user( array(
				'ID' => $user_id, 
				'um_sina_weibo' => $_POST['um_sina_weibo'],
				'um_qq_weibo' => $_POST['um_qq_weibo'],
				'um_twitter' => $_POST['um_twitter'],
				'um_googleplus' => $_POST['um_googleplus'],
				'um_weixin' => $_POST['um_weixin'],
                'wechat_pay' => $_POST['wechat_pay'],
				'um_donate' => $_POST['um_donate'],
				'um_qq' => $_POST['um_qq'],
				'um_alipay_email' => $_POST['um_alipay_email']
			 ) );
			if ( ! is_wp_error( $update_user_id ) ) $message = __('扩展资料已更新','um');
		}	
		if($update=='pass'){
			$email = $user_info->user_email ? $user_info->user_email : sanitize_text_field($_POST['email']);
            $pass_update = _profile_pass_update( $user_id, $email, $_POST['pass1'], $_POST['pass2'] );
			if( $pass_update ) $message = $pass_update; 
			//$data = array();
			//$data['ID'] = $user_id;
			//$data['user_email'] = sanitize_text_field($_POST['email']);
			//if( !empty($_POST['pass1']) && !empty($_POST['pass2']) && $_POST['pass1']===$_POST['pass2'] ) $data['user_pass'] = sanitize_text_field($_POST['pass1']);
			//$user_id = wp_update_user( $data );
			//if ( ! is_wp_error( $user_id ) ) $message = __('安全信息已更新','um');
		}
		
		$message .= ' <a href="'.um_get_current_page_url().'">'.__('点击刷新','um').'</a>';
		
		$user_info = get_userdata($curauth->ID);
	}
}
//~ 个人资料end

if($get_tab=='profile' && ($current_user->ID!=$curauth->ID && current_user_can('edit_users')) ) $message = sprintf(__('你正在查看的是%s的资料，修改请慎重！', 'um'), $curauth->display_name);

?>
<div class="page-wrapper">

<!-- Page global message -->
<?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
<?php if( $oneself ){ ?>
<form id="info-form" class="form-horizontal" role="form" method="POST" action="">
	<input type="hidden" name="update" value="info">
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
	<div class="page-header">
		<h3 id="info"><?php _e('基本信息','um');?> <small><?php _e('公开资料','um');?></small></h3>
	</div>
    <!--<div class="form-group">
         <label class="col-md-2 control-label"><?php _e('用户ID','um'); ?></label>
         <div class="col-md-10"><?php echo $user_info->ID; ?></div>
    </div>-->
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php _e('头像','um');?></label>
		<div class="col-sm-10">                                                                                      
			<div class="avatar-box">
                <?php $customize = get_user_meta($user_info->ID,'um_customize_avatar',true);?>
                <label class="local-avatar-label" title="<?php _e('上传头像', 'tt'); ?>">
                     <?php if (!empty($customize)) { echo um_get_avatar( $user_info->ID , '40' , 'customize' ); }else{ echo um_get_avatar( $user_info->ID , '40' , 'default' ); }?>
                     <div id="upload-avatar" style="position: absolute;top: 0px;left: 0px;width: 1px;height: 1px;overflow: hidden;"><input type="file" name="file" class="webuploader-element-invisible" accept="image/*"><label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255);"></label></div>
                     <svg class="svgIcon-use" width="40" height="40" viewBox="-8 -8 80 80"><g fill-rule="evenodd"><path d="M10.61 44.486V23.418c0-2.798 2.198-4.757 5.052-4.757h6.405c1.142-1.915 2.123-5.161 3.055-5.138L40.28 13.5c.79 0 1.971 3.4 3.073 5.14 0 .2 6.51 0 6.51 0 2.856 0 5.136 1.965 5.136 4.757V44.47c-.006 2.803-2.28 4.997-5.137 4.997h-34.2c-2.854.018-5.052-2.184-5.052-4.981zm5.674-23.261c-1.635 0-3.063 1.406-3.063 3.016v19.764c0 1.607 1.428 2.947 3.063 2.947H49.4c1.632 0 2.987-1.355 2.987-2.957v-19.76c0-1.609-1.357-3.016-2.987-3.016h-7.898c-.627-1.625-1.909-4.937-2.28-5.148 0 0-13.19.018-13.055 0-.554.276-2.272 5.143-2.272 5.143l-7.611.01z"></path><path d="M32.653 41.727c-5.06 0-9.108-3.986-9.108-8.975 0-4.98 4.047-8.966 9.108-8.966 5.057 0 9.107 3.985 9.107 8.969 0 4.988-4.047 8.974-9.107 8.974v-.002zm0-15.635c-3.674 0-6.763 3.042-6.763 6.66 0 3.62 3.089 6.668 6.763 6.668 3.673 0 6.762-3.047 6.762-6.665 0-3.616-3.088-6.665-6.762-6.665v.002z"></path></g></svg>
                     <input type="radio" name="avatar" value="customize" <?php if($avatar=='customize') echo 'checked';?>><?php _e('自定义头像', 'um'); ?>
                </label>
				<label>
                    <?php echo um_get_avatar( $user_info->ID , '40' , 'default' ); ?>
					<input type="radio" name="avatar"  value="default" <?php if( $avatar!='customize' && ($avatar!='qq' || um_is_open_qq($user_info->ID)===false) && ($avatar!='weibo' || um_is_open_weibo($user_info->ID)===false) ) echo 'checked';?>><?php _e('默认头像','um'); ?>
				</label>
                <?php if(um_is_open_qq($user_info->ID)){ ?>
			    <label>
                    <?php echo um_get_avatar( $user_info->ID , '40' , 'qq' ); ?>
					<input type="radio" name="avatar" value="qq" <?php if($avatar=='qq') echo 'checked';?>><?php _e('QQ头像','um');?>
				</label>
                <?php } ?>
				<?php if(um_is_open_weibo($user_info->ID)){ ?>
				<label>
					<?php echo um_get_avatar( $user_info->ID , '40' , 'weibo' ); ?>                        
					<input type="radio" name="avatar" value="weibo" <?php if($avatar=='weibo') echo 'checked';?>><?php _e('微博头像','um');?>
				</label>
				<?php } ?>
			</div>                                                                                                             
		</div>
	</div>						
	<div class="form-group">
		<label for="display_name" class="col-sm-2 control-label"><?php _e('性别','um');?></label>
		<div class="col-sm-10">
			<select name="um_gender">
				<option value ="male" <?php if($user_info->um_gender=='male') echo 'selected = "selected"'; ?>><?php _e('男','um');?></option>
				<option value ="female" <?php if($user_info->um_gender=='female') echo 'selected = "selected"'; ?>><?php _e('女','um');?></option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="display_name" class="col-sm-2 control-label"><?php _e('昵称','um');?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="display_name" name="display_name" value="<?php echo $user_info->display_name;?>">
		</div>
	</div>
	<div class="form-group">
		<label for="url" class="col-sm-2 control-label"><?php _e('站点','um');?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="url" name="url" value="<?php echo $user_info->user_url;?>">
		</div>
	</div>					
	<div class="form-group">
		<label for="description" class="col-sm-2 control-label"><?php _e('个人说明','um');?></label>
		<div class="col-sm-10">
			<textarea class="form-control" rows="3" name="description" id="description"><?php echo $user_info->description;?></textarea>
		</div>
	</div>
  	<!-- Cover -->
	<div class="form-group">
	    <label for="description" class="col-sm-2 control-label"><?php _e('个人封面','um');?></label>
	    <div id="cover" class="col-sm-10">
		    <img src="<?php if(get_user_meta($curauth->ID,'um_cover',true)) echo get_user_meta($curauth->ID,'um_cover',true); else echo UM_URI.'/img/cover/1-full.jpg'; ?>" alt="个人封面">
		    <?php if($current_user->ID==$curauth->ID){ ?><a href="javascript:" id="custom-cover">自定义封面</a><?php } ?>
	    </div>
	</div>
	<!-- Cover change -->
    <div id="cover-change">
		<div id="cover-c-header"><strong>自定义封面</strong><a href="javascript:" id="cover-close">X</a></div>
		<div id="cover-list">
			<div id="cover-change-inner">
				<ul class="clx">
					<li><a href="#" class="basic"><img src="<?php echo UM_URI.'/img/cover/1-small.jpg'; ?>" ></a></li>
					<li><a href="#" class="basic"><img src="<?php echo UM_URI.'/img/cover/2-small.jpg'; ?>" ></a></li>
					<li><a href="#" class="basic"><img src="<?php echo UM_URI.'/img/cover/3-small.jpg'; ?>" ></a></li>
					<li><a href="#" class="basic"><img src="<?php echo UM_URI.'/img/cover/4-small.jpg'; ?>" ></a></li>
					<li><a href="#" class="basic"><img src="<?php echo UM_URI.'/img/cover/5-small.jpg'; ?>" ></a></li>
				    <li><a href="#" class="basic"><img src="<?php echo UM_URI.'/img/cover/6-small.jpg'; ?>" ></a></li>
					<?php if(get_user_meta($curauth->ID,'um_cover',true)){ ?>
					<li><a href="#" id="uploaded-cover" class="basic"><img src="<?php echo get_user_meta($curauth->ID,'um_cover',true); ?>" width="240" height="64"></a></li>
					<?php } ?>
					<?php if($current_user->ID==$curauth->ID){ ?><li><a href="#" id="upload-cover"><span>+</span></a></li>
					<?php } ?>
				</ul>
				<div id="cover-c-footer">
					<a href="#" id="cover-sure" curuserid="<?php echo $current_user->ID; ?>">确定</a>
					<a href="#" id="cover-cancle">取消</a>
				</div>
			</div>
		</div>	
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php _e('保存更改','um');?></button>
		</div>
	</div>						
</form>

<form id="info-more-form" class="form-horizontal" role="form" method="post">
	<input type="hidden" name="update" value="info-more">
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
	<div class="page-header">
		<h3 id="info"><?php _e('扩展资料','um');?> <small><?php _e('社会化信息等','um');?></small></h3>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php _e('新浪微博','um');?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="um_sina_weibo" name="um_sina_weibo" value="<?php echo $user_info->um_sina_weibo;?>">
			<span class="help-block"><?php _e('请填写新浪微博账号','um');?></span>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label"><?php _e('腾讯微博','um');?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="um_qq_weibo" name="um_qq_weibo" value="<?php echo $user_info->um_qq_weibo;?>">
			<span class="help-block"><?php _e('请填写腾讯微博账号','um');?></span>
		</div>
	</div>
	<div class="form-group">
	    <label class="col-sm-2 control-label"><?php _e('微信二维码','um');?></label>
        <div class="col-sm-10 cos_field">
            <div class="cos_file_button<?php if($user_info->um_weixin){ echo ' active'; } ?>">
                <a href="#" class="cos_upload_button"><b>+</b><span><?php _e('上传二维码','um'); ?></span></a>
                <div class="cos_file_preview">
                    <?php if($user_info->um_weixin){ ?>
                    <img src="<?php echo $user_info->um_weixin; ?>" alt="<?php echo sprintf(__('%s 的微信二维码','um'),$user_info->name); ?>">
                    <?php } ?>
                </div>
                <div class="bg"></div>
                <input class="cos_field_upload" type="hidden" value="<?php if($user_info->um_weixin){ echo $user_info->um_weixin; } ?>" id="um_weixin" name="um_weixin" />
            </div>
			<span class="help-block"><?php _e('上传您的微信账号二维码图片,用于交流。','um');?></span>
        </div>
	</div>
  
  	<div class="form-group">
	    <label class="col-sm-2 control-label"><?php _e('微信收款维码','um');?></label>
        <div class="col-sm-10 cos_field">
            <div class="cos_file_button<?php if($user_info->wechat_pay){ echo ' active'; } ?>">
                <a href="#" class="cos_upload_button"><b>+</b><span><?php _e('上传二维码','um'); ?></span></a>
                <div class="cos_file_preview">
                    <?php if($user_info->wechat_pay){ ?>
                    <img src="<?php echo $user_info->wechat_pay; ?>" alt="<?php echo sprintf(__('%s 的微信收款二维码','um'),$user_info->name); ?>">
                    <?php } ?>
                </div>
                <div class="bg"></div>
                <input class="cos_field_upload" type="hidden" value="<?php if($user_info->wechat_pay){ echo $user_info->wechat_pay; } ?>" id="wechat_pay" name="wechat_pay" />
            </div>
			<span class="help-block"><?php _e('上传您的微信收款账号二维码图片,如果有文章的话，用户可以打赏。','um');?></span>
        </div>
	</div>
  
    <div class="form-group">
	    <label class="col-sm-2 control-label"><?php _e('支付宝收款维码','um');?></label>
        <div class="col-sm-10 cos_field">
            <div class="cos_file_button<?php if($user_info->um_donate){ echo ' active'; } ?>">
                <a href="#" class="cos_upload_button"><b>+</b><span><?php _e('上传二维码','um'); ?></span></a>
                <div class="cos_file_preview">
                    <?php if( $user_info->um_donate){ ?>
                    <img src="<?php echo $user_info->um_donate; ?>" alt="<?php echo sprintf(__('%s 的支付宝收款二维码','um'),$user_info->name); ?>">
                    <?php } ?>
                </div>
                <div class="bg"></div>
                <input class="cos_field_upload" type="hidden" value="<?php if($user_info->um_donate){ echo $user_info->um_donate; } ?>" id="um_donate" name="um_donate" />
            </div>
			<span class="help-block"><?php _e('上传您的支付宝收款账号二维码图片,如果有文章的话，用户可以打赏。','um');?></span>
        </div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php _e('提交资料','um');?></button>
		</div>
	</div>	
</form>

<?php if($current_user&&$current_user->ID==$curauth->ID) { ?>
<form id="aff-form" class="form-horizontal" role="form">
	<div class="page-header">
		<h3 id="open"><?php _e('推广链接','um');?> <small><?php _e('可赚取积分','um');?></small></h>
	</div>
	<div class="form-group">
		<label for="aff" class="col-sm-2 control-label"><?php _e('推广链接','um');?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control um_aff_url" value="<?php echo get_bloginfo('url').'/?aff='.$current_user->ID; ?>">
		</div>
	</div>
</form>
<?php } ?>
<?php if( $qq || $weibo || $weixin) { ?>
<form id="open-form" class="form-horizontal" role="form" method="post">
	<div class="page-header">
		<h3 id="open"><?php _e('绑定账号','um');?> <small><?php _e('可用于直接登录','um');?></small></h>
	</div>			
	<?php if($qq){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?php _e('QQ账号','um');?></label>
			<div class="col-sm-10">
		<?php  if(um_is_open_qq($user_info->ID)) { ?>
			<span class="help-block"><?php _e('已绑定','um');?> <a href="<?php echo home_url('/oauth/qq?action=logout'); ?>"><?php _e('点击解绑','um');?></a></span>
			<?php echo um_get_avatar( $user_info->ID , '100' , 'qq' ); ?>
		<?php }else{ ?>
			<a class="btn btn-primary" href="<?php echo home_url('/oauth/qq?action=login&redirect='.urlencode(get_edit_profile_url())); ?>"><?php _e('绑定QQ账号','um');?></a>
		<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if($weibo){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?php _e('微博账号','um');?></label>
			<div class="col-sm-10">
		<?php if(um_is_open_weibo($user_info->ID)) { ?>
			<span class="help-block"><?php _e('已绑定','um');?> <a href="<?php echo home_url('/oauth/weibo?action=logout'); ?>"><?php _e('点击解绑','um');?></a></span>
			<?php echo um_get_avatar( $user_info->ID , '100' , 'weibo' ); ?>
		<?php }else{ ?>
			<a class="btn btn-danger" href="<?php echo home_url('/oauth/weibo?action=login&redirect='.urlencode(get_edit_profile_url())); ?>"><?php _e('绑定微博账号','um');?></a>
		<?php } ?>
			</div>
		</div>
	<?php } ?>
	
	<?php if($weixin){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?php _e('微信账号','um');?></label>
			<div class="col-sm-10">
		<?php if(um_is_open_weixin($user_info->ID)) { ?>
			<span class="help-block"><?php _e('已绑定','um');?> <a href="<?php echo home_url('/oauth/weixin?action=logout'); ?>"><?php _e('点击解绑','um');?></a></span>
			<?php echo um_get_avatar( $user_info->ID , '100' , 'weixin' ); ?>
		<?php }else{ ?>
			<a class="btn btn-danger" href="<?php echo home_url('/oauth/weixin?action=login&redirect='.urlencode(get_edit_profile_url())); ?>"><?php _e('绑定微信账号','um');?></a>
		<?php } ?>
			</div>
		</div>
	<?php } ?>
</form>
<?php } ?>
<form id="pass-form" class="form-horizontal" role="form" method="post">
	<input type="hidden" name="update" value="pass">
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
	<div class="page-header">
		<h3 id="pass"><?php _e('账号安全','um');?> <small><?php _e('仅自己可见','um');?></small></h3>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-2 control-label"><?php _e('邮箱(必填)','um');?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="email" name="email" value="<?php echo $user_info->user_email;?>" <?php if(!empty($user_info->user_email)) echo 'disabled'; ?> aria-required='true' required>
			<span class="help-block"><?php _e('邮箱一旦填写无法修改，请认证填写。','um');?></span>
		</div>
	</div>
	<div class="form-group">
		<label for="pass1" class="col-sm-2 control-label"><?php _e('新密码','um');?></label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="pass1" name="pass1" >
			<span class="help-block"><?php _e('如果您想修改您的密码，请在此输入新密码。不然请留空。','um');?></span>
		</div>
	</div>
	<div class="form-group">
		<label for="pass2" class="col-sm-2 control-label"><?php _e('重复新密码','um');?></label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="pass2" name="pass2" >
			<span class="help-block"><?php _e('再输入一遍新密码。 提示：您的密码最好至少包含7个字符。为了保证密码强度，使用大小写字母、数字和符号（例如! " ? $ % ^ & )）。','um');?></span>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary"><?php _e('保存更改','um');?></button>
		</div>
	</div>
</form>
<?php } ?>
</div>