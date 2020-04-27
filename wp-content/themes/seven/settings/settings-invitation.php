<?php
function zrz_options_invitation_create_page(){
    $card = '';
    $credit = '';
    if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
        $count = $_POST['nub'];
        $credit = $_POST['credit'];
        $current_user = get_current_user_id();
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_invitation';

        for ($i=0; $i < $count; $i++) {
            $nub = zrz_create_guid(null,true);

            $wpdb->insert($table_name, array(
                'invitation_nub'=> $nub,
                'invitation_owner'=> $current_user,
                'invitation_credit'=> $credit,
                'invitation_status'=> 0,
                'invitation_user'=> 0
            ) );

            $card .= $nub.'-'.$credit.'<br>';
        }
    }

    $option = new zrzOptionsOutput();
?>
<div class="wrap">
	<h1><?php _e('柒比贰主题邀请码设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('邀请码生成','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		    zrz_admin_invitation_tabs('create');
            echo '<p>请填写生成的数量，及邀请注册以后奖励的积分。建议不要一次生成太多，以免服务器开销过大。建议一次生成50组以内。</p>';
            if($card){
                echo '<div style="background-color:#ddd;padding:10px">
                <p>邀请码经生成，当前邀请码奖励积分：'.$credit.'。</p>
                '.$card.'
                </div>';
            }

            $option->table(array(
                array(
                    'type' => 'input',
                    'th' => __('生成的数量','ziranzhi2'),
                    'after' => '<p>'.__('请填写生成邀请码的数量，默认20组','ziranzhi2').'</p>',
                    'key' => 'nub',
                    'value' => 20
                ),
                array(
                    'type' => 'input',
                    'th' => __('生成邀请码奖励的积分','ziranzhi2'),
                    'after' => '<p>'.__('请填写邀请码对应奖励积分的数额，默认100积分','ziranzhi2').'</p>',
                    'key' => 'credit',
                    'value' => 100
                ),
            ));
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '立刻生成', 'ziranzhi2' );?>"></p>
	</form>
</div>
<?php
}