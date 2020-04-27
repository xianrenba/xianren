<?php

/**
 * Password Protected
 *
 * @package bbPress
 * @subpackage Theme
 */
$id = get_the_id();
$role = get_option('zrz_bbp_capabilities',true);

if(!isset($role[$id]) || !is_array($role[$id])) return;

$cap_str = '';
foreach ($role[$id] as $value) {
	$cap_str .= '<span class="user-lv '.$value.' mar10-l"><i class="zrz-icon-font-'.(strpos($value,'vip') !== false ? 'vip' : $value).' iconfont"></i></span>';
}
?>

<div id="bbpress-forums" class="bbpress-wrapper pos-r no-role" style="min-height:300px">
	<div class="lm">
		<p class="fs14"><span class="red">权限不足</span>，您需要以下权限方可查看</p>
		<p class="mar10-t"><?php echo $cap_str; ?></p>
	</div>
</div>
