<?php 
$paged = max( 1, get_query_var('page') );
$limit = 20; // 每页20条
$members = get_vip_members(-1, $limit, ($paged - 1) * $limit);
$count = $members ? count($members) : 0;
$total_count = count_vip_members(-1);
$pages = ceil($total_count / $limit);
$message = '';

// 会员start
	if( isset($_POST['promoteVipNonce']) && current_user_can('edit_users') ){
		if ( ! wp_verify_nonce( $_POST['promoteVipNonce'], 'promotevip-nonce' ) ) {
			$message = __('安全认证失败，请重试！','um');
		}else{
			if( isset($_POST['member_type']) && sanitize_text_field($_POST['member_type'])=='4' ){
				$pv_type = 4;
				$pv_type_title = __('终身会员','um');
			}elseif( isset($_POST['member_type']) && sanitize_text_field($_POST['member_type'])=='3' ){
				$pv_type = 3;
				$pv_type_title = __('年费会员','um');
			}elseif(isset($_POST['member_type']) && sanitize_text_field($_POST['member_type'])=='2'){
				$pv_type = 2;
				$pv_type_title = __('季费会员','um');
			}else{
				$pv_type = 1;
				$pv_type_title = __('月费会员','um');
			}
			//$pv_expire_date =  $pv_expire_date ? $pv_expire_date : date('Y-m-d',strtotime("$date"));
			$user_name_or_id =  sanitize_text_field($_POST['user']);
            if(is_numeric($user_name_or_id)) {
                $user = get_user_by('ID', $user_name_or_id);
            }else{
                $user = get_user_by('login', $user_name_or_id);
            }

            if(!$user){
                 $message =  __('指定的用户不存在','um');
            }else if(!in_array($pv_type, array(1, 2, 3, 4))){
                 $message =  __('会员类型不正确', 'um');
            }else{
                //$message = __('<a>'.$user->ID.'</a>','um');
			   um_manual_promotevip($user->ID,$user->display_name,$user->user_email,$pv_type,0);			 
			   $message = sprintf(__('操作成功！已成功将%1$s提升至%2$s。','um'), $user->display_name, $pv_type_title);
			   $message .= ' <a href="'.um_get_current_page_url().'">'.__('点击刷新','um').'</a>';            
            }

		}
	}

get_header();
?>
<div class="wrapper">
    <!-- 主要内容区 -->
    <div class="container pagewrapper clr"  id="management-page">
            <?php include('navmenu.php'); ?>
            <div class="pagecontent">
            <div class="page-wrapper">
            <div class="tab-content">
            <!-- 添加会员 -->
            <section class="mg-member clearfix">             
                <div class="page-header"><h3 id="info">添加会员</h3></div>
                <!-- Page global message -->
			    <?php if($message) echo '<div class="alert alert-success">'.$message.'</div>'; ?>
                <form id="promotevipform" role="form"  method="post">
                <input type="hidden" name="promoteVipNonce" value="<?php echo  wp_create_nonce( 'promotevip-nonce' );?>" >
                <div class="form-group info-group clearfix">
                    <div class="member-radios">
                        <label class="radio-inline" style="padding:0;margin:0;">会员类型：</label>
                        <label class="radio-inline"><input type="radio" name="member_type" value="1" aria-required='true' required checked>月费会员</label>
                        <label class="radio-inline"><input type="radio" name="member_type" value="2" aria-required='true' required>季费会员</label>
                        <label class="radio-inline"><input type="radio" name="member_type" value="3" aria-required='true' required>年费会员</label>
                        <label class="radio-inline"><input type="radio" name="member_type" value="4" aria-required='true' required>终生会员</label>
                    </div>
                </div>
                <div class="form-group info-group clearfix">
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="input-group active">
                                <div class="input-group-addon">用户名或ID</div>
                                <input class="form-control" type="text" name="user" value="" aria-required="true" required>
                            </div>
                        </div>
                        <button class="btn btn-inverse" type="submit" id="promotevipform-submit">添加</button>
                    </div>
                    <p class="help-block">请提供要提升会员用户的登录名或用户ID</p>
                </div>
                </form>
            </section>
            <!-- 会员列表 -->
            <section class="mg-members clearfix">
                <div class="page-header"><h3 id="info">会员列表</h3></div>
                <?php if($count > 0) { ?>
                    <div class="table-wrapper">
                        <table class="table table-striped table-framed table-centered users-table">
                            <thead>
                            <tr>
                                <th class="th-sid">序号</th>
                                <th class="th-uid">用户ID</th>
                                <th class="th-uname">用户名</th>
                                <th class="th-type">会员类型</th>
                                <th class="th-effect">开始时间</th>
                                <th class="th-expire">到期时间</th>
                                <th class="th-actions">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $seq = 0; ?>
                            <?php foreach ($members as $member){ ?>
                                <?php $seq++; ?>
                                <tr id="mid-<?php echo $member->id; ?>">
                                    <td><?php echo $seq; ?></td>
                                    <td><?php echo $member->user_id; ?></td>
                                    <td><?php echo get_user_meta($member->user_id, 'nickname', true); ?></td>
                                    <td><?php echo output_order_vipType($member->user_type); ?></td>
                                    <td><?php echo $member->startTime ?></td>
                                    <td><?php echo $member->endTime ?></td>
                                    <td>
                                        <div class="member-actions">
                                            <a class="view-detail" href="<?php echo get_author_posts_url($member->user_id); ?>" title="访问用户主页" target="_blank">查看</a>
                                            <span class="text-explode">|</span>
                                            <a class="delete-member" href="javascript:;" data-member-action="delete" data-member-id="<?php echo $member->id; ?>" title="删除会员">删除</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php echo um_pager($paged, $pages); ?>
                <?php }else{ ?>
                    <div class="empty-content">
                        <span class="tico tico-users"></span>
                        <p>这里什么也没有..</p>
                    </div>
                <?php } ?>
            </section>
            </div>
            </div>  
        </div>
    </div>
</div>
<?php get_footer(); ?>