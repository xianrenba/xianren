<?php
function zrz_options_card_create_page(){
    $card = '';
    $rmb = '';
    if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) {
        $count = $_POST['nub'];
        $rmb = $_POST['rmb'];
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_card';

        for ($i=0; $i < $count; $i++) {
            $key = zrz_create_guid();
            $value = wp_create_nonce(zrz_build_order_no());

            $wpdb->insert($table_name, array(
                'card_key'=> $key,
                'card_value'=> $value,
                'card_rmb'=> $rmb,
                'card_status'=> 0,
                'card_user'=> 0
            ) );

            $card .= $key.' '.$value.'<br>';
        }
    }

    $option = new zrzOptionsOutput();
?>
<div class="wrap">
	<h1><?php _e('柒比贰主题卡密设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('卡密生成','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		    zrz_admin_card_tabs('create');
            echo '<p>请填写生成的数量，及面值。在售卡平台注意设置出售价格和在此设置的价格一致，建议不要一次生成太多，以免服务器开销过大。建议一次生成50组以内。</p>';
            if($card){
                echo '<div style="background-color:#ddd;padding:10px">
                <p>卡密已经生成，当前面值'.$rmb.'元，请直接复制到卡密销售平台进行销售。</p>
                '.$card.'
                </div>';
            }

            $option->table(array(
                array(
                    'type' => 'input',
                    'th' => __('生成的数量','ziranzhi2'),
                    'after' => '<p>'.__('请填写生成卡密的数量，默认20组','ziranzhi2').'</p>',
                    'key' => 'nub',
                    'value' => 20
                ),
                array(
                    'type' => 'input',
                    'th' => __('生成卡密的面值','ziranzhi2'),
                    'after' => '<p>'.__('请填写生成卡密的面值，默认100元','ziranzhi2').'</p>',
                    'key' => 'rmb',
                    'value' => 100
                )
            ));
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '立刻生成', 'ziranzhi2' );?>"></p>
	</form>
</div>
<?php
}
