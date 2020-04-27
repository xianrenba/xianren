<?php
//财富页面
get_header();
$lvs = zrz_get_lv_settings();
$user_id = get_current_user_id();
$clv = zrz_get_lv($user_id);
?>
<div id="primary" class="content-area fd">
	<main id="vips" class="site-main gold-page box pos-r" role="main" ref="vips">
		<div class="pd10 box-header b-b fs12 mar20-b"><span class="fl"></span>成为会员</div>
		<div class="t-c">
			<?php
				$html = '';
				foreach($lvs as $key => $lv){
					if(strpos($key,'vip') !== false && $lv['open'] == 1){
						if($lv['price'] == 0) continue;
						$open = false;
						if($clv != $key){
							$open = true;
						}
						$html .= '<div class="be-vip fd pd20">
							<div class=""><span class="user-lv '.$key.'"><i class="zrz-icon-font-vip iconfont"></i></span></div>
							<div class="vip-name">'.$lv['name'].'</div>
							<div class="vip-rmb">¥'.$lv['price'].'</div>
							<div class="vip-time">'.($lv['time'] == 0 ? '终身' : ''.$lv['time'].'天').'</div>
							<div class="mar20-t">
							'.($open ? '<button class="'.($lv['time'] != 0 ? 'empty' : '').'" @click="showForm(\''.$lv['price'].'\',\''.$key.'\')">开通</button>' : '<button class="disabled">已开通</button>').'	
							</div>
						</div>';
					}
				}
				echo $html;
			?>
		</div>
	<div class="pd20 clearfix b-t mar20-t" ref="vipsInfo">
		<button class="tab-title mar20-r text picked mar10-l">您的会员信息</button>
		<div class="bor pd20">
			<?php
				$user_role = get_user_meta($user_id,'zrz_lv',true);
				$user_role = strpos($user_role,'vip') !== false ? $user_role : '';
				if($user_role){
				$lv = zrz_get_lv_settings($user_role);
				$time = get_user_meta($user_id,'zrz_vip_time',true);
				$start = '';
				$end = '';
				if(is_array($time)){
					$start = $time['start'];
					$end = $time['end'];
				}
			?>
					<p class="fs14 mar10-b">您当前的等级：<span class="user-lv <?php echo $user_role; ?>"><i class="zrz-icon-font-vip iconfont"></i><?php echo $lv['name'];?></span></p>
					<p class="fs14 mar10-b"><?php echo $start ? '开通时间：'.$start : '';?></p>
					<p class="fs14"><?php echo $end === 0 ? '结束时间：<span class="green">终身有效</span>' : '结束时间：'.$end;?></p>


			<?php }else{ ?>
					<p class="fs14 mar10-b">您当前的等级：<?php echo zrz_get_lv($user_id,'name'); ?></p>
					<p class="fs14 mar10-b">暂无付费会员特权</p>
			<?php } ?>
		</div>
		<payment :show="show" :type-text="'购买会员'" :type="'vip'" :price="price" :data="data" @close-form="closeForm"></payment>
	</div>
    </main>
</div><?php
get_sidebar();
get_footer();
