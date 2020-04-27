<?php $info = get_userdata($curauth->ID); ?>
<div class="author-tab-box profile-tab">
    <div class="tab-content author-profile">
        <section class="author-card">
            <div class="inner">
                <?php echo um_get_avatar( $curauth->ID , '80' , um_get_avatar_type($curauth->ID) ); ?>
                <div class="card-text">
                    <div class="display-name"><?php echo $curauth->display_name; ?></div>
                    <div class="register-time"><?php printf(__('加入时间 %s', 'tt'), date( 'Y/m/d', strtotime( $user_info->user_registered ) ) ); ?><?php printf(__(' <b>(第 %d位成员)</b>', 'tt'), $info->ID); ?></div>
					<div class="login-time"><?php if($is_me){printf(__('上次登录 %s', 'tt'), $info->um_latest_login_before ? date( 'Y/m/d h:i:s A', strtotime( $info->um_latest_login_before ) )  : 'N/A');} else {printf(__('最近登录 %s', 'tt'), $info->um_latest_login ? date( 'Y/m/d h:i:s A', strtotime( $info->um_latest_login ) ) : 'N/A'); }; ?></div>
                    <?php if($is_me) { ?>
                    <div class="login-ip"><?php echo sprintf(__('本次登录IP %s', 'tt'), $info->um_latest_ip ? : $_SERVER['REMOTE_ADDR']) . '&nbsp;&nbsp;&nbsp;&nbsp;' . sprintf(__('上次登录IP %s', 'tt'), $user_info->um_latest_ip_before ? : 'N/A'); ?></div>
                    <?php } ?>
                    <?php if(!$is_me && current_user_can('edit_users')) { ?>
                        <div class="login-ip"><?php echo sprintf(__('上次登录IP %s', 'tt'), $info->um_latest_ip ? : 'N/A'); ?></div>
                    <?php } ?>			
                </div>
            </div>
        </section>
        <!-- 基本信息 -->
        <section class="info-basis clearfix">
            <header><h2><?php _e('基本信息', 'tt'); ?></h2></header>
            <div class="info-group clearfix">
                <label class="col-md-3 control-label"><?php _e('昵称', 'tt'); ?></label>
                <p class="col-md-9"><?php echo $info->nickname; ?></p>
            </div>
            <?php if( $oneself || current_user_can('edit_users')) { ?>
                <div class="info-group clearfix">
                    <label class="col-md-3 control-label"><?php _e('邮箱', 'tt'); ?></label>
                    <p class="col-md-9"><?php echo $user_info->user_email; ?></p>
                </div>
            <?php } ?>
            <div class="info-group clearfix">
                <label class="col-md-3 control-label"><?php _e('网页', 'tt'); ?></label>
                <p class="col-md-9"><?php echo $user_info->user_url; ?></p>
            </div>
            <div class="info-group clearfix">
                <label class="col-md-3 control-label"><?php _e('个人描述', 'tt'); ?></label>
                <p class="col-md-9"><?php echo $user_info->description; ?></p>
            </div>
        </section>
        <!-- 扩展信息 -->
        <section class="info-extends clearfix">
            <header><h2><?php _e('扩展信息', 'tt'); ?></h2></header>
            <div class="info-group clearfix">
                <?php if($user_info->um_qq) { ?>
                <a class="btn btn-wide btn-social-qq" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $info->qq; ?>&amp;site=qq&amp;menu=yes" target="_blank"><i class="fa fa-qq"></i><?php _e('QQ交谈', 'tt'); ?></a>
                <?php } ?>
                <?php if($user_info->um_sina_weibo) { ?>
                    <a class="btn btn-wide btn-social-weibo" href="<?php echo $user_info->um_sina_weibo; ?>" target="_blank"><i class="fa fa-weibo"></i><?php _e('关注微博', 'tt'); ?></a>
                <?php } ?>
                <?php if($user_info->um_weixin) { ?>
                    <a class="btn btn-wide btn-social-weixin popover-qr" href="javascript: void 0" data-trigger="focus" data-container=".info-extends" data-toggle="popover" data-placement="top" data-content='<?php echo '<img width=175 height=175 src="' . $user_info->um_weixin . '">'; ?>'><i class="fa fa-wechat"></i><?php _e('微信交谈', 'tt'); ?></a>
                <?php } ?>
                <?php if($user_info->um_twitter) { ?>
                    <a class="btn btn-wide btn-social-twitter" href="<?php echo $info->twitter; ?>" target="_blank"><i class="fa fa-twitter"></i><?php _e('Follow on Twitter', 'tt'); ?></a>
                <?php } ?>
                <?php if($user_info->um_facebook) { ?>
                    <a class="btn btn-wide btn-social-facebook" href="<?php echo $info->facebook; ?>" target="_blank"><i class="fa fa-facebook"></i><?php _e('Find on Facebook', 'tt'); ?></a>
                <?php } ?>
                <?php if($user_info->um_googleplus) { ?>
                    <a class="btn btn-wide btn-social-googleplus" href="<?php echo $info->googleplus; ?>" target="_blank"><i class="fa fa-google-plus"></i><?php _e('Google+', 'tt'); ?></a>
                <?php } ?>
            </div>
        </section>
        <?php if($user_info->um_donate || $info->wechat_pay) { ?>
        <!-- 收款信息 -->
        <section class="info-donate clearfix">
            <header><h2><?php _e('收款信息', 'tt'); ?><small><?php _e('打赏我', 'tt'); ?></small></h2></header>
            <div class="info-group clearfix">
                <?php if(!empty($user_info->um_donate)) { ?>
                    <a class="btn btn-wide btn-alipay_pay popover-qr" href="javascript: void 0" data-trigger="focus" data-container=".info-donate" data-toggle="popover" data-placement="top" data-content='<?php echo '<img width=225 height=225 src="' . $user_info->um_donate . '">'; ?>'><?php _e('支付宝打赏', 'tt'); ?></a>
                <?php } ?>
                <?php if($user_info->wechat_pay) { ?>
                    <a class="btn btn-wide btn-wechat_pay popover-qr" href="javascript: void 0" data-trigger="focus" data-container=".info-donate" data-toggle="popover" data-placement="top" data-content='<?php echo '<img width=225 height=225 src="' . $user_info->wechat_pay . '">'; ?>'><?php _e('微信打赏', 'tt'); ?></a>
                <?php } ?>
            </div>
        </section>
        <?php } ?>
        <!-- 推广信息 -->
        <section class="info-referral clearfix">
            <header><h2><?php _e('推广信息', 'tt'); ?></h2></header>
            <div class="info-group clearfix">
                <label class="col-md-3 control-label"><?php _e('推广链接', 'tt'); ?></label>
                <p class="col-md-9"><?php echo get_bloginfo('url').'/?aff='.$user_info->ID; ?><!--a href="javascript: void 0" class="copy"><i class="fa fa-copy"></i><?php _e('复制', 'tt'); ?></a--></p>
            </div>
        </section>
              <?php if(current_user_can('edit_users')) { ?>
        <!-- 禁用或解禁账户操作 -->
        <section class="admin-operation clearfix">
            <header><h2><?php _e('账户管理', 'um'); ?><small><?php _e('仅管理员可见', 'um'); ?></small></h2></header>
            <div class="info-group clearfix">
                <?php if($user_info->um_banned) { ?>
                <a class="btn btn-wide btn-border-success ban-btn" href="javascript:void 0" data-action="ban_user" data-type="unban" data-uid="<?php echo $info->ID; ?>"><?php _e('解禁账户', 'um'); ?></a>
                <p><?php _e('此操作将恢复该账户的正常功能', 'um'); ?></p>
                <?php }else{ ?>
                <a class="btn btn-wide btn-border-danger ban-btn" href="javascript:void 0" data-action="ban_user" data-type="ban" data-uid="<?php echo $info->ID; ?>"><?php _e('封禁账户', 'um'); ?></a>
                <p><?php _e('警告: 此操作将封禁该账户, 所有功能不可使用直至手动解禁', 'um'); ?></p>
                <?php } ?>
            </div>
            <div class="info-group clearfix">
                <a class="btn btn-wide btn-border-danger" href="<?php echo _url_for('manage_user', $info->ID); ?>" title="管理用户" target="_blank" style="float: right;"><?php _e('管理用户', 'um'); ?></a>
                <p>提示：这里可以给用户增加积分，余额，开通会员。</p>
            </div>
        </section>
        <?php } ?>
    </div>
</div>
