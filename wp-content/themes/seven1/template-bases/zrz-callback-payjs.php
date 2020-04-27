<?php
/**
 * 购物车，订单列表
 */

get_header();

if(isset($_GET['callback'])){
	$callback = esc_url($_GET['callback']);
  	$type = esc_attr($_GET['paytype']);
  	wp_localize_script( 'ziranzhi2-main', 'payjs_data',array(
		'address'=>$callback,
      	'type'=>$type
	));
}

?>

	<div id="payjs-check" class="content-area mar10-b" style="width:100%">
		<div class="loading-dom pos-r box">
			<div class="lm" v-html="text"></div>
		</div>
	</div>
<?php
get_footer();
