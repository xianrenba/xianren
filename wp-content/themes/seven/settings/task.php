<?php
function zrz_options_task_page(){
if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {

    $options = array(
        'comment'=>array(
            'count'=>trim_value($_POST['comment_count']),
            'open'=>$_POST['comment_open'],
            'type'=>1
        ),//评论，主动任务
        'post'=>array(
            'count'=>trim_value($_POST['post_count']),
            'open'=>$_POST['post_open'],
            'type'=>1
        ),//发文，主动任务
        'post_commented'=>array(
            'count'=>trim_value($_POST['post_commented_count']),
            'open'=>$_POST['post_commented_open'],
            'type'=>0
        ),//文章被回复，回复被评论，被动任务
        'comment_vote_up'=>array(
            'count'=>trim_value($_POST['comment_vote_up_count']),
            'open'=>$_POST['comment_vote_up_open'],
            'type'=>0
        ),//评论被点赞，被动任务
        'comment_vote_up_deduct'=>array(
            'count'=>trim_value($_POST['comment_vote_up_deduct_count']),
            'open'=>$_POST['comment_vote_up_deduct_open'],
            'type'=>1
        ),//给其他人的评论点赞，主动任务
        'followed'=>array(
            'count'=>trim_value($_POST['followed_count']),
            'open'=>$_POST['followed_open'],
            'type'=>0
        ),//关注他人，主动任务
        'follow'=>array(
            'count'=>trim_value($_POST['follow_count']),
            'open'=>$_POST['follow_open'],
            'type'=>0
        ),//被关注，被动任务
         'reply'=>array(
             'count'=>trim_value($_POST['reply_count']),
            'open'=>$_POST['reply_open'],
            'type'=>1
        ),//回复帖子，主动任务
        'topic'=>array(
            'count'=>trim_value($_POST['topic_count']),
            'open'=>$_POST['topic_open'],
            'type'=>1
        ),//发表帖子，主动任务
        'pps'=>array(
            'count'=>trim_value($_POST['pps_count']),
            'open'=>$_POST['pps_open'],
            'type'=>1
        ),//发表冒泡，主动任务
        'labs'=>array(
            'count'=>trim_value($_POST['labs_count']),
            'open'=>$_POST['labs_open'],
            'type'=>1
        ),//发表研究，主动任务
        'invitation'=>array(
            'count'=>trim_value($_POST['invitation_count']),
            'open'=>$_POST['invitation_open'],
            'type'=>1
        ),//邀请注册，主动任务
    );

    update_option( 'zrz_task_setting',$options );

    zrz_settings_error('updated');

}

	$option = new zrzOptionsOutput();

	?>
<div class="wrap">

	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('任务设置','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php zrz_admin_tabs('task'); ?>
        <p style="color:red">以下列出的是网站中所有任务的类型，您可以选择是否开启，也可以设置允许每天可完成的次数，奖励积分请在<a href="<?php echo home_url('/wp-admin/admin.php?page=zrz_options&tab=credit'); ?>">财富设置</a>中进行设置</p>
        <?php
            echo '<h2 class="title">评论任务设置（主动任务）</h2>';
            $comment = zrz_get_task_setting('comment');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启评论任务？','ziranzhi2'),
                    'key' => 'comment_open',
                    'value' => array(
                        'default' => array($comment['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'comment_count',
                    'value' => $comment['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">发文任务设置（主动任务）</h2>';
            $post = zrz_get_task_setting('post');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启发文任务？','ziranzhi2'),
                    'key' => 'post_open',
                    'value' => array(
                        'default' => array($post['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'post_count',
                    'value' => $post['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">文章被评论或者评论被回复设置（被动任务）</h2>';
            $post_commented = zrz_get_task_setting('post_commented');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启文章被评论或者评论被回复的被动任务？','ziranzhi2'),
                    'key' => 'post_commented_open',
                    'value' => array(
                        'default' => array($post_commented['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'post_commented_count',
                    'value' => $post_commented['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">评论被点赞设置（被动任务）</h2>';
            $comment_vote_up = zrz_get_task_setting('comment_vote_up');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启评论被点赞的被动任务？','ziranzhi2'),
                    'key' => 'comment_vote_up_open',
                    'value' => array(
                        'default' => array($comment_vote_up['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'comment_vote_up_count',
                    'value' => $comment_vote_up['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">给他人的评论点赞设置（主动任务）</h2>';
            $comment_vote_up_deduct = zrz_get_task_setting('comment_vote_up_deduct');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启给他人的评论点赞的任务？','ziranzhi2'),
                    'key' => 'comment_vote_up_deduct_open',
                    'value' => array(
                        'default' => array($comment_vote_up_deduct['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'comment_vote_up_deduct_count',
                    'value' => $comment_vote_up_deduct['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">关注他人（主动任务）</h2>';
            $followed = zrz_get_task_setting('followed');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启关注他人的任务？','ziranzhi2'),
                    'key' => 'followed_open',
                    'value' => array(
                        'default' => array($followed['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'followed_count',
                    'value' => $followed['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">被他人关注（被动任务）</h2>';
            $follow = zrz_get_task_setting('follow');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启被他人的任务？','ziranzhi2'),
                    'key' => 'follow_open',
                    'value' => array(
                        'default' => array($follow['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'follow_count',
                    'value' => $follow['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">发布论坛帖子的任务（主动任务）</h2>';
            $topic = zrz_get_task_setting('topic');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启发布论坛帖子的任务？','ziranzhi2'),
                    'key' => 'topic_open',
                    'value' => array(
                        'default' => array($topic['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'topic_count',
                    'value' => $topic['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">回复论坛帖子任务（主动任务）</h2>';
            $reply = zrz_get_task_setting('reply');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启回复论坛帖子的任务？','ziranzhi2'),
                    'key' => 'reply_open',
                    'value' => array(
                        'default' => array($reply['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'reply_count',
                    'value' => $reply['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">发布冒泡的任务（主动任务）</h2>';
            $pps = zrz_get_task_setting('pps');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启发布冒泡的任务？','ziranzhi2'),
                    'key' => 'pps_open',
                    'value' => array(
                        'default' => array($pps['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'pps_count',
                    'value' => $pps['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">发布研究的任务（主动任务）</h2>';
            $labs = zrz_get_task_setting('labs');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启发布冒泡的任务？','ziranzhi2'),
                    'key' => 'labs_open',
                    'value' => array(
                        'default' => array($labs['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'labs_count',
                    'value' => $labs['count']
                ),
    		) );
		?>
        <?php
            echo '<h2 class="title">邀请注册的任务（主动任务）</h2>';
            $invitation = zrz_get_task_setting('invitation');
    		$option->table( array(
                array(
                    'type' => 'select',
                    'th' => __('是否开启邀请注册的任务？','ziranzhi2'),
                    'key' => 'invitation_open',
                    'value' => array(
                        'default' => array($invitation['open']),
                        'option' => array(
                            1 => __( '开启', 'ziranzhi2' ),
                            0 => __( '关闭', 'ziranzhi2' ),
                        )
                    )
    			),
                array(
                    'type' => 'input',
                    'th' => __('每天可完成的次数','ziranzhi2'),
                    'key' => 'invitation_count',
                    'value' => $invitation['count']
                ),
    		) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>

</div>
	<?php
}
