<?php
function zrz_options_credit_page(){
if(isset($_POST['action']) && sanitize_text_field($_POST['action'])=='restart'){
    $times = wp_next_scheduled( 'clear_zrz_rec_daily_event' );
    wp_unschedule_event( $times, 'clear_zrz_rec_daily_event' );

    $date = new DateTime( 'tomorrow', new DateTimeZone('Asia/Shanghai') );
    $timestamp = $date->getTimestamp();
    wp_schedule_event($timestamp, 'daily', 'clear_zrz_rec_daily_event');



}elseif( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {

    $options = array(
        'zrz_credit_signup'=>trim_value($_POST['zrz_credit_signup']),//注册奖励
        'zrz_credit_comment'=>trim_value($_POST['zrz_credit_comment']),//发表评论奖励
        'zrz_credit_post'=>trim_value($_POST['zrz_credit_post']),//投稿奖励
        'zrz_credit_post_commented'=>trim_value($_POST['zrz_credit_post_commented']),//文章被评论或评论被回复或帖子被回复得分
        'zrz_credit_comment_vote_up'=>trim_value($_POST['zrz_credit_comment_vote_up']),//评论点赞
        'zrz_credit_comment_vote_up_deduct'=>trim_value($_POST['zrz_credit_comment_vote_up_deduct']),//评论被踩
        //'zrz_credit_love'=>$_POST['zrz_credit_love'],//文章点赞（收藏）奖励
        'zrz_credit_followed'=>trim_value($_POST['zrz_credit_followed']),//被关注
        'zrz_credit_follow'=>trim_value($_POST['zrz_credit_follow']),//被关注
        //'zrz_rec_credit'=>$_POST['zrz_rec_credit'],//每天可得积分的次数
        'zrz_credit_mission'=>trim_value($_POST['zrz_credit_mission']),//签到奖励
        'zrz_credit_reply'=>trim_value($_POST['zrz_credit_reply']),//回复帖子
        'zrz_credit_topic'=>trim_value($_POST['zrz_credit_topic']),//创建一个帖子
        'zrz_credit_pps'=>trim_value($_POST['zrz_credit_pps']),//发表一个冒泡
        'zrz_credit_labs'=>trim_value($_POST['zrz_credit_labs']),//发表一个研究
        'zrz_credit_rmb'=>trim_value($_POST['zrz_credit_rmb']),//人民币兑换积分比例
        'zrz_credit_name'=>trim_value($_POST['zrz_credit_name']),//积分名称
        'zrz_credit_display'=>$_POST['zrz_credit_display'],//显示类型
        'zrz_credit_invitation'=>trim_value($_POST['zrz_credit_invitation']),//积分名称
        'zrz_credit_be_invitation'=>trim_value($_POST['zrz_credit_be_invitation']),//显示类型
        'zrz_tx_min'=>trim_value($_POST['zrz_tx_min']),
        'zrz_cc'=>trim_value($_POST['zrz_cc']),
        'zrz_tx_allowed'=>$_POST['zrz_tx_allowed'],
        'zrz_tx_admin'=>trim_value($_POST['zrz_tx_admin'])
    );
    
    update_option( 'zrz_credit_setting',$options );

    zrz_settings_error('updated');

}

	$option = new zrzOptionsOutput();

	?>
<div class="wrap">

	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('财富设置','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php zrz_admin_tabs('credit'); ?>
		<?php
        echo '<h2 class="title">提现设置</h2>';
		$option->table( array(
            array(
                'type' => 'select',
                'th' => __('是否允许用户申请提现？','ziranzhi2'),
                'key' => 'zrz_tx_allowed',
                'value' => array(
                    'default' => array(zrz_get_credit_settings('zrz_tx_allowed')),
                    'option' => array(
                        1 => __( '允许', 'ziranzhi2' ),
                        0 => __( '禁止', 'ziranzhi2' ),
                    )
                )
			),
			array(
				'type' => 'input',
				'th' => __('余额超过多少后允许提现？','ziranzhi2'),
				'key' => 'zrz_tx_min',
                'after' => '<p class="description">默认 <code>50</code> 元。</p>',
				'value' => zrz_get_credit_settings('zrz_tx_min')
			),
            array(
				'type' => 'input',
				'th' => __('网站抽成比例','ziranzhi2'),
				'key' => 'zrz_cc',
                'after' => '<p class="description">默认 <code>0.05</code> （5%），如果网站不抽成，请设置为0。</p>',
				'value' => zrz_get_credit_settings('zrz_cc')
			),
            array(
				'type' => 'input',
				'th' => __('提现操作员ID','ziranzhi2'),
				'key' => 'zrz_tx_admin',
                'after' => '<p class="description">请填写审核提现人员的用户ID，一般是管理员，即ID为1。默认为 <code>1</code> ，这个用户将会收到提现的消息通知。</p>',
				'value' => zrz_get_credit_settings('zrz_tx_admin')
			),
		) );
        echo '<h2 class="title">积分名称</h2>';
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('你希望将积分称为？','ziranzhi2'),
				'key' => 'zrz_credit_name',
                'after' => '<p class="description">默认名称 积分。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_name')
			),
            array(
                'type' => 'select',
                'th' => __('积分显示为金、银、铜吗？','ziranzhi2'),
                'after' => '<p>'.__('如果选否，则只显示数值','ziranzhi2').'</p>',
                'key' => 'zrz_credit_display',
                'value' => array(
                    'default' => array(zrz_get_credit_settings('zrz_credit_display')),
                    'option' => array(
                        1 => __( '是', 'ziranzhi2' ),
                        0 => __( '否', 'ziranzhi2' ),
                    )
                )
			),
		) );
        echo '<h2 class="title">积分兑换</h2>';
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('1元人民币兑换多少积分？','ziranzhi2'),
				'key' => 'zrz_credit_rmb',
                'after' => '<p class="description">默认 260 积分。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_rmb')
			),
		) );
        echo '<h2>积分奖励</h2>';
        echo '<p style="color:red">如果不想使用某种奖励，并且关闭其通知，请将这些值设为<code>empty</code></p>';
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('新用户注册奖励','ziranzhi2'),
				'key' => 'zrz_credit_signup',
                'after' =>'<p class="description">奖励对象：注册者。默认260分。首次注册时奖励。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_signup')
			),
			array(
				'type' => 'input',
				'th' => __('发表文章奖励','ziranzhi2'),
				'key' => 'zrz_credit_post',
                'after' => '<p class="description">奖励对象：文章作者。默认200分。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_post')
			),
            array(
				'type' => 'input',
				'th' => __('发布研究奖励','ziranzhi2'),
				'key' => 'zrz_credit_labs',
                'after' => '<p class="description">奖励对象：发布研究的人。默认200分。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_labs')
			),
            // array(
            //     'type' => 'input',
            //     'th' => __('文章被收藏','ziranzhi2'),
            //     'key' => 'zrz_credit_love',
            //     'after' => '<p class="description">奖励对象：文章作者。默认60分。</p>',
            //     'value' => zrz_get_credit_settings('zrz_credit_love')
            // ),
			array(
				'type' => 'input',
				'th' => __('评论一次得分','ziranzhi2'),
				'key' => 'zrz_credit_comment',
                'after' => '<p class="description">奖励对象：发表评论者。默认50分。（此评论积分设置包含文章、冒泡、研究所的评论）</p>',
				'value' => zrz_get_credit_settings('zrz_credit_comment')
			),
            array(
                'type' => 'input',
                'th' => __('评论被喜欢','ziranzhi2'),
                'key' => 'zrz_credit_comment_vote_up',
                'after' => '<p class="description">奖励对象：评论者。默认范围 默认 50 分。</p>',
                'value' => zrz_get_credit_settings('zrz_credit_comment_vote_up')
            ),
            array(
                'type' => 'input',
                'th' => __('点击评论喜欢按钮后自己的减分项','ziranzhi2'),
                'key' => 'zrz_credit_comment_vote_up_deduct',
                'after' => '<p class="description">减分对象：点击评论喜欢按钮的人。默认 <i style="color:red">-40</i> 分（注意：这是扣分项别忘了负号！）。</p>',
                'value' => zrz_get_credit_settings('zrz_credit_comment_vote_up_deduct')
            ),
            array(
                'type' => 'input',
                'th' => __('关注他人','ziranzhi2'),
                'key' => 'zrz_credit_followed',
                'after' => '<p class="description">奖励对象：点击关注的人。默认30分。</p>',
                'value' => zrz_get_credit_settings('zrz_credit_followed')
            ),
            array(
                'type' => 'input',
                'th' => __('被关注奖励积分','ziranzhi2'),
                'key' => 'zrz_credit_follow',
                'after' => '<p class="description">奖励对象：被关注者。默认60分。</p>',
                'value' => zrz_get_credit_settings('zrz_credit_follow')
            ),
            // array(
            //     'type' => 'input',
            //     'th' => __('每天可得积分的次数','ziranzhi2'),
            //     'key' => 'zrz_rec_credit',
            //     'after' => '<p class="description">超过则不再增加积分。</p>',
            //     'value' => zrz_get_credit_settings('zrz_rec_credit')
            // ),
            array(
				'type' => 'input',
				'th' => __('创建一个话题','ziranzhi2'),
				'key' => 'zrz_credit_topic',
                'after' => '<p class="description">奖励对象：话题创建者。默认120分。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_topic')
			),
            array(
                'type' => 'input',
                'th' => __('创建一个话题回复','ziranzhi2'),
                'key' => 'zrz_credit_reply',
                'after' => '<p class="description">奖励对象：话题回复创建者。默认70分。</p>',
                'value' => zrz_get_credit_settings('zrz_credit_reply')
            ),
            array(
                'type' => 'input',
                'th' => __('创建一个冒泡','ziranzhi2'),
                'key' => 'zrz_credit_pps',
                'after' => '<p class="description">奖励对象：发布冒泡者。默认100分。</p>',
                'value' => zrz_get_credit_settings('zrz_credit_pps')
            ),
		) );
		?>
		<h2 class="title"><?php _e('随机奖励','ziranzhi2');?></h2>
		<?php
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('文章被评论或评论被回复或帖子被回复得分','ziranzhi2'),
				'key' => 'zrz_credit_post_commented',
                'after' => '<p class="description">奖励对象：文章（或冒泡）作者或评论者。随机奖励，默认范围 默认 5-20 分（中间横杠不可缺失）。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_post_commented')
			),
            array(
                'type' => 'input',
                'th' => __('每日签到奖励','ziranzhi2'),
                'key' => 'zrz_credit_mission',
                'after' => '<p class="description">登陆用户每日签到将会随机获得积分，如果是固定值请使用 xx-xx 例如 100-100（中间横杠不可缺失）</p>',
                'value' => zrz_get_credit_settings('zrz_credit_mission')
            ),
		) );
		?>
		<h2 class="title"><?php _e('推广奖励','ziranzhi2');?></h2>
		<?php
		$option->table( array(
			array(
				'type' => 'input',
				'th' => __('邀请人获得的积分','ziranzhi2'),
				'key' => 'zrz_credit_invitation',
                'after' => '<p class="description">奖励对象：推广者。</p>',
				'value' => zrz_get_credit_settings('zrz_credit_invitation')
			),
            array(
                'type' => 'input',
                'th' => __('被邀请人获得的积分','ziranzhi2'),
                'key' => 'zrz_credit_be_invitation',
                'after' => '<p class="description">奖励对象：注册人</p>',
                'value' => zrz_get_credit_settings('zrz_credit_be_invitation')
            ),
		) );
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
    <h2 class="title">重置签到时间</h2>
    <p>如果您的签到时间不是凌晨零点钟重置，请点击一下重置签到时间按钮。</p>
    <form method="post">
        <input type="hidden" name="action" value="restart">
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
        <?php
            $_date = wp_next_scheduled( 'clear_zrz_rec_daily_event' );
            if($_date){
                echo '当前签到重置时间是：'.get_date_from_gmt( date( 'Y-m-d H:i:s', $_date ));
            }
        ?>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '重置签到时间', 'ziranzhi2' );?>"></p>
    </form>
</div>
	<?php
}
