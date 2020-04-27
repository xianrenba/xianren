<?php
add_action( 'admin_menu', 'zrz_admin_menu_page' );
function zrz_admin_menu_page(){

	add_menu_page( '柒比贰主题设置', '柒比贰主题设置', 'manage_options', 'zrz_options', 'zrz_options_page','' );

	$zrz_submenus = array(
		''=>__('常规设置','ziranzhi2'),
		'_card' =>__('卡密设置','ziranzhi2'),
		'_invitation' =>__('邀请码生成','ziranzhi2'),
	);
	foreach( $zrz_submenus as $key=>$title ){
		add_submenu_page( 'zrz_options', $title, $title, 'manage_options', 'zrz_options'.$key , 'zrz_options'.$key.'_page' );
	}
}

function zrz_admin_tabs($tab='media'){
	$zrz_tabs = array(
		'info' => __('说明', 'ziranzhi2'),
		'home' => __('基本设置', 'ziranzhi2'),
		'display'=>__('显示设置', 'ziranzhi2'),
		'media' => __('媒体', 'ziranzhi2'),
		'social' => __('登录与注册', 'ziranzhi2'),
		'writing' =>__('投稿','ziranzhi2'),
		'reading' =>__('阅读','ziranzhi2'),
		'links' =>__('导航链接','ziranzhi2'),
		'credit' =>__('财富设置','ziranzhi2'),
		'pay'=>__('支付设置','ziranzhi2'),
		'task'=>__('任务设置','ziranzhi2'),
		'lv'=>__('等级制度','ziranzhi2'),
		'mail'=>__('邮件发送设置','ziranzhi2'),
		'ads' =>__('广告设置','ziranzhi2'),
	);

	$tab_output = '<h2 class="nav-tab-wrapper">';
	foreach( $zrz_tabs as $tab_key=>$tab_name ){
		$tab_output .= sprintf('<a href="%s" class="nav-tab%s">%s</a>', add_query_arg('tab', $tab_key), $tab_key==$tab ? ' nav-tab-active' : '', $tab_name);
	}
	$tab_output .= '</h2>';
	echo $tab_output;
	echo '<style>
		h2{border-bottom:1px solid #666;padding-bottom:10px}
		.regular-text {width: 31em;}.form-table td label{margin-right:10px}
	</style>';
}

$zrz_tabs_array = array(
	'info',
	'home',
	'display',
	'media',
	'social',
	'writing',
	'links',
	'reading',
	'credit',
	'lv',
	'task',
	'mail',
	'pay',
	'ads',
);

foreach( $zrz_tabs_array as $zrz_tab_slug ){
	require_once( get_template_directory() . '/settings/'.$zrz_tab_slug.'.php' );
}

function zrz_options_page(){
	global $zrz_tabs_array;
	$tab = 'info';
	if( isset($_GET['tab']) ){
		$tab = in_array($_GET['tab'], $zrz_tabs_array) ? $_GET['tab'] : 'info';
	}
	$tab = 'zrz_options_'.$tab.'_page';
	$tab();
}

//卡密设置页面
require_once( get_template_directory() .'/settings/settings-card.php' );
require_once( get_template_directory() .'/settings/list.php' );
function zrz_admin_card_tabs($tab='media'){
	$zrz_tabs = array(
		'create' => __('卡密生成', 'ziranzhi2'),
		'list' => __('卡密管理', 'ziranzhi2'),
	);

	$tab_output = '<h2 class="nav-tab-wrapper">';
	foreach( $zrz_tabs as $tab_key=>$tab_name ){
		$tab_output .= sprintf('<a href="%s" class="nav-tab%s">%s</a>', add_query_arg('tab', $tab_key), $tab_key==$tab ? ' nav-tab-active' : '', $tab_name);
	}
	$tab_output .= '</h2>';
	echo $tab_output;
	echo '<style>
		h2{border-bottom:1px solid #666;padding-bottom:10px}
		.regular-text {width: 31em;}.form-table td label{margin-right:10px}
	</style>';
}

function zrz_options_card_page(){
	$zrz_card_tabs_array = array(
		'create',
		'list'
	);
	$tab = 'create';
	if( isset($_GET['tab']) && isset($_GET['page']) && $_GET['page'] == 'zrz_options_card'){
		$tab = in_array($_GET['tab'], $zrz_card_tabs_array) ? $_GET['tab'] : 'create';
	}
	$tab = 'zrz_options_card_'.$tab.'_page';
	$tab();
}

//邀请码设置
require_once( get_template_directory() .'/settings/settings-invitation.php' );
require_once( get_template_directory() .'/settings/invitation.php' );
function zrz_admin_invitation_tabs($tab='media'){
	$zrz_tabs = array(
		'create' => __('邀请码生成', 'ziranzhi2'),
		'list' => __('邀请码管理', 'ziranzhi2'),
	);

	$tab_output = '<h2 class="nav-tab-wrapper">';
	foreach( $zrz_tabs as $tab_key=>$tab_name ){
		$tab_output .= sprintf('<a href="%s" class="nav-tab%s">%s</a>', add_query_arg('tab', $tab_key), $tab_key==$tab ? ' nav-tab-active' : '', $tab_name);
	}
	$tab_output .= '</h2>';
	echo $tab_output;
	echo '<style>
		h2{border-bottom:1px solid #666;padding-bottom:10px}
		.regular-text {width: 31em;}.form-table td label{margin-right:10px}
	</style>';
}

function zrz_options_invitation_page(){
	$zrz_invitation_tabs_array = array(
		'create',
		'list'
	);
	$tab = 'create';
	if( isset($_GET['tab']) && isset($_GET['page']) && $_GET['page'] == 'zrz_options_invitation'){
		$tab = in_array($_GET['tab'], $zrz_invitation_tabs_array) ? $_GET['tab'] : 'create';
	}
	$tab = 'zrz_options_invitation_'.$tab.'_page';
	$tab();
}

class zrzOptionsOutput {

	public function table($items){

		if( empty($items[0]['type']) ) return;

		echo '<table class="form-table"><tbody>';

		foreach( $items as $item){

			$item = wp_parse_args( $item, array(
								'type' => '',
								'th' => '',
								'before' => '',
								'after' => '',
								'key' => '',
								'value' => ''
							));

			echo '<tr>';
			$this->tableTH($item['key'], $item['th']);
			$this->tableTD($item['type'], $item['key'], $item['value'], $item['before'], $item['after']);
			echo '</tr>';
		}

		echo '</tbody></table>';
	}

	public function tableTH($key, $title){
		echo sprintf('<th scope="row"><label for="%s">%s</label></th>', $key, $title);
	}

	public function tableTD($type, $key, $value, $before, $after){

		echo '<td>'.$before;

		if( $type=='input' ){
			echo sprintf('<input name="%1$s" type="text" id="%1$s" value="%2$s" class="regular-text ltr">', $key, $value);
		}

		if( $type=='input-password' ){
			echo sprintf('<input name="%1$s" type="password" id="%1$s" value="%2$s" class="regular-text ltr">', $key, $value);
		}

		if( $type=='textarea' ){
			echo sprintf('<textarea name="%1$s" rows="8" cols="50" id="%1$s" class="large-text code">%2$s</textarea>', $key, $value);
		}

		if( $type=='select' ){
			echo sprintf('<select name="%1$s" id="%1$s">', $key);
			foreach( $value['option'] as $option_key=>$option_value ){
				echo sprintf('<option value="%1$s"%2$s>%3$s</option>', $option_key, (in_array($option_key, $value['default']) ? ' selected="selected"' : ''), $option_value);
			}
			echo '</select>';
		}

		if( $type=='checkbox' ){
			foreach( $value['option'] as $option_key=>$option_value ){
				echo sprintf('<label><input name="%1$s[]" type="checkbox" value="%2$s"%3$s> %4$s </label>', $key, $option_key, (in_array($option_key, $value['default']) ? ' checked' : ''), $option_value);
			}
		}

		if( $type=='editor' ){
			wp_editor( $value, $key, array( 'media_buttons' => false, 'textarea_rows' => 5 ) );
		}

		echo $after.'</td>';
	}

}
