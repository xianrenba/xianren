<?php
function zrz_options_links_page(){
    $cat_arr = array();

    $cats = get_terms( 'link_category', array( 'hierarchical' => 0,'hide_empty' => false) );

    if( isset($_POST['action']) && sanitize_text_field($_POST['action'])=='update' && wp_verify_nonce( trim($_POST['_wpnonce']), 'check-nonce' ) ) :

        foreach ($cats as $cat) {
            update_term_meta($cat->term_id,'orderby',trim_value($_POST['cat_list'.$cat->term_id]));
        }

        zrz_settings_error('updated');

    endif;

    $i = 0;
    foreach ($cats as $cat) {
        $i ++;
        $j = get_term_meta($cat->term_id,'orderby',true);
        $cat_arr[] = array(
            'type' => 'input',
            'th' => $cat->name,
            'key' => 'cat_list'.$cat->term_id,
            'value' => $j ? $j : $i
        );
    }

	$option = new zrzOptionsOutput();
?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('导航链接设置','ziranzhi2');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
		<?php
		    zrz_admin_tabs('links');
            echo '<h3>链接分类排序</h3><p>此项设置控制导航链接页面的分类排序，请在输入框中填入数字，越小排名越靠前。</p>';
    		$option->table($cat_arr);
		?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( '保存更改', 'ziranzhi2' );?>"></p>
	</form>
</div>
	<?php
}
