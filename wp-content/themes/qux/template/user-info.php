<?php if( is_user_logged_in() ): global $current_user; ?>
<?php $unread_count = intval(get_um_message($current_user->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )"));  ?>
Hi, <?php echo $current_user->display_name;echo um_member_icon($current_user->ID) ?>
<?php if( _hui('user_page_s') ){ ?>
&nbsp;&nbsp;<a href="<?php echo (_hui('open_ucenter') ?  um_get_user_url('index') : _url_for('user') ) ?>"><i class="fa fa-user"></i>进入用户中心<?php if($unread_count){ echo '&nbsp;&nbsp;<i class="badge">'.$unread_count.'</i>'; } ?></a>
&nbsp;&nbsp;<a href="<?php echo add_query_arg('redirect_to', qux_get_current_page_url(), _url_for('signout')); ?>"><i class="fa fa-power-off"></i>注销登录</a>
<?php } ?>
<?php if( is_super_admin() ){ ?>
&nbsp;&nbsp;<a href="<?php echo _url_for('manage_status'); ?>"><i class="fa fa-th"></i>站务管理</a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('/wp-admin/') ?>">后台管理</a>
<?php } ?>
<?php elseif( _hui('user_page_s') ): ?>
<?php _moloader('mo_get_user_rp', false) ?>
<a href="javascript:;" class="user-reg" data-sign="0">Hi, 请登录</a>
<?php if(get_option('users_can_register')==1){ ?>
&nbsp;&nbsp;<a href="javascript:;" class="user-reg" data-sign="1">我要注册</a>
<?php } ?>
&nbsp;&nbsp;<a href="<?php echo mo_get_user_rp() ?>">找回密码</a>
<?php endif; ?>