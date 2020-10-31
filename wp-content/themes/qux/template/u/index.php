
    <div class="page-wrapper">
        <div class="dashboard-main">
            <div class="page-header">
                  <h3 id="info"><?php _e('用户中心','um');?> <small><?php if($curauth->ID==$current_user->ID) echo '亲爱的 <a target="_blank" href="'.get_author_posts_url($curauth->ID).'">'.$curauth->display_name.'</a> 欢迎回来。'; else echo '欢迎 <a target="_blank" href="'.get_author_posts_url($current_user->ID).'">'.$current_user->display_name.'</a> ，您正在查看'.$curauth->display_name.'的个人中心'; ?></small></h3>
            </div>
  <?php
		$avatar_type = array(
			'default' => __('默认头像', 'um'),
			'qq' => __('腾讯QQ头像', 'um'),
			'weibo' => __('新浪微博头像', 'um'),
			'customize' => __('自定义头像', 'um'),
		);
		
		$author_profile = array(
			__('头像来源:','um') => $avatar_type[um_get_avatar_type($user_info->ID)],
			__('昵称:','um') => $user_info->display_name,
			__('站点:','um') => $user_info->user_url,
			__('个人说明:','um') => $user_info->description
		);
		
		$profile_output = '';
		foreach( $author_profile as $pro_name=>$pro_content ){
			$profile_output .= '<tr><td class="title">'.$pro_name.'</td><td>'.$pro_content.'</td></tr>';
		}
		
		$days_num = round(( strtotime(date('Y-m-d')) - strtotime( $user_info->user_registered ) ) /3600/24);
		
		echo '<ul class="user-msg"><li class="tip">'.sprintf(__('%s来%s已经%s天了', 'um') , $user_info->display_name, get_bloginfo('name'), ( $days_num>1 ? $days_num : 1 ) ).'</li></ul>'.'<table id="author-profile"><tbody>'.$profile_output.'</tbody></table>';
       ?>
				<div class="summary">
					<div class="box">
						<div class="title">我的最近发布</div>
						<ul>
						<?php if(!$posts_count>0){ ?>
							<li>您还没发布过任何内容。</li>
						<?php }else{ ?>
						<?php
							$args = array('showposts'=>5,'orderby'=>'date','order'=>'DESC','post_type'=>'post','ignore_sticky_posts'=>1);
							$latest = new wp_query($args);
							while ($latest->have_posts()){
								$latest->the_post();
								echo '<li><a href="'.get_permalink($post->ID).'" target="_blank">'.get_the_title($post->ID).'</a>';
								if($post->post_status!='publish')echo '<span>[审核中]</span>';
								echo '</li>';
							}
						?>
						<?php } ?>
						</ul>
					</div>
					<div class="box">
						<div class="title">我的最近评论</div>
						<ul>
						<?php if(!$comments_count>0){ ?>
							<li>暂无未发布任何评论。</li>
						<?php }else{ ?>
						<?php 
							$comments = get_comments(array('status' => 1,'order' => 'DESC','number' => 5,'offset' => 0,'user_id' => $curauth->ID));
							foreach($comments as $comment){
								echo '<li><a href="'.get_comment_link($comment).'" target="_blank">'.convert_smilies($comment->comment_content).'</a></li>';
							}
						?>
						<?php } ?>
	        	        </ul>
					</div>
				</div>
				<div class="fast-navigation">
					<div class="nav-title">快捷菜单</div>
					<ul>
						<li><a target="_blank" href="<?php echo get_author_posts_url($curauth->ID); ?>"><i class="fa fa-home"></i>我的主页</a></li>
						<li><a href="<?php echo um_get_user_url('order',$curauth->ID); ?>"><i class="fa fa-shopping-cart"></i>我的订单</a></li>
						<li>
						<?php if(is_user_logged_in()){ ?>
						<a href="<?php echo add_query_arg(array('tab'=>'post','action'=>'new'), get_author_posts_url($current_user->ID)); ?>">
						<?php }else{ ?>
						<a href="javascript:" class="user-login">
						<?php } ?>
						<i class="fa fa-pencil-square-o"></i>发布文章</a></li>
						<li><a href="<?php echo um_get_user_url('credit',$curauth->ID).'#creditrechargeform'; ?>"><i class="fa fa-credit-card"></i>充值积分</a></li>
						<li><a href="<?php echo um_get_user_url('membership',$curauth->ID).'#joinvip'; ?>"><i class="fa fa-user-md"></i>加入会员</a></li>
						<li><a href="<?php echo um_get_user_url('profile',$curauth->ID); ?>"><i class="fa fa-cog"></i>修改资料</a></li>
						<?php if(is_user_logged_in()) { ?>
						<li><a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>"><i class="fa fa-power-off"></i>注销登录</a></li>
						<?php }else{ ?>
						<li><a href="javascript:" class="user-login"><i class="fa fa-sign-in"></i>登录/注册</a></li>
						<?php } ?>
					</ul>
				</div>
            </div>
        </div>
	</div>