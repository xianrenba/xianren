<?php _the_menu('nav') ?>
<?php if( !is_search() ){ ?>
	<li class="navto-search"><a href="javascript:;" class="search-show active"><i class="fa fa-search"></i></a></li>
<?php } ?>
<?php if( _hui('user_page_s') ){ ?>
    <?php if( is_user_logged_in() ):global $current_user; ?>
        <?php $unread_count = intval(get_um_message($current_user->ID, 'count', "( msg_type='unread' OR msg_type='unrepm' )")); ?>
		<li class="nav-user dropdown">
            <a href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php if($unread_count){ echo '<i class="badge"></i>'; } ?>
                <?php echo um_get_avatar(  $current_user->ID , '30' , um_get_avatar_type( $current_user->ID) );  ?>
            </a>
            <ul class="nav-user-menu dropdown-menu">
                <?php if( is_super_admin() ){ ?>
					<li><a target="_blank" href="<?php echo site_url('/wp-admin/') ?>"><i class="fa fa-tachometer"></i>后台管理</a></li>
                    <li><a href="<?php echo _url_for('manage_status'); ?>"><i class="fa fa-th"></i>站务管理</a></li>
				<?php } ?>
                <li><a href="<?php echo um_get_user_url('post&action=new') ?>"><i class="fa fa-pencil"></i>新建文章</a></li>
				<li><a href="<?php echo um_get_user_url('post') ?>"><i class="fa fa-cube"></i>我的文章</a></li>
				<li><a href="<?php echo um_get_user_url('orders') ?>"><i class="fa fa-exchange"></i>我的订单</a></li>
				<li><a href="<?php echo um_get_user_url('credit') ?>"><i class="fa fa-diamond"></i>我的积分</a></li>
				<li><a href="<?php echo um_get_user_url('message') ?>"><i class="fa fa-comments"></i>站内消息<?php if($unread_count){ echo '<i class="badge">'.$unread_count.'</i>'; } ?></a></li>
				<li><a href="<?php echo um_get_user_url('profile') ?>"><i class="fa fa-cog"></i>个人设置</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="<?php echo add_query_arg('redirect_to', qux_get_current_page_url(), _url_for('signout')); ?>"><i class="fa fa-power-off"></i>注销</a></li>
            </ul>
        </li>
	<?php else : ?>
	<li class="login-actions">
        <a href="javascript:;" class="user-reg" data-sign="0"><i class="fa fa-sign-in"></i></a>
    </li>
	<?php endif; ?>
<?php } ?>