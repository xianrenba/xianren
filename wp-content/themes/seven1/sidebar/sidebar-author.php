<?php
$user_id =  get_query_var('author');
$user_favorites = get_user_meta($user_id, 'zrz_user_favorites', true );
$view = get_user_meta($user_id,'views',true);
$view = $view ? $view : 0;

$user_data = get_user_meta($user_id,'zrz_user_custom_data',true);

$name = zrz_get_credit_settings('zrz_credit_name');
$credit = zrz_get_credit_settings('zrz_credit_be_invitation');
$c_credit = zrz_get_credit_settings('zrz_credit_invitation');

$init = new Zrz_Invitation_Reg();
$code = $init->set_invitation_code($user_id);
echo '<section id="user_achievement-2" class="widget widget_user_achievement mar10-b">
			<h2 class="widget-title l1 pd10 box-header"><span v-text="uName">他</span>的推广网址</h2>
			<div class="pd10 box">
				<p class="mar10-b gray">通过推广网址注册，<span v-text="uName">他</span>和注册人都将获得'.$name.'奖励。</p>
				<p class="mar10-b">注册人将获得：<b class="green">'.$credit.'</b>'.$name.'。</p>
				<p class="mar10-b"><span v-text="uName">他</span>也将获得：<b class="green">'.$c_credit.'</b>'.$name.'</p>
				<p class="mar10-b"><span v-text="uName">他</span>的推广网址：</p>
				<p class="mar10-b"><input type="text" class="pd10 w100" value="'.home_url('/?i='.$code).'" ref="urlInput" readonly></p>
				<button class="empty" @click="copyUrl">复制网址</button>
			</div>
		</section>';

dynamic_sidebar( 'sidebar-6' );
echo '
    <div class="user-sidebar-bottom fs14 mar10-b box">
        <a href="'.zrz_get_user_page_url($user_id).'/collection-shop'.'" class="clearfix"><span v-text="uName">他</span>收藏的商品<span class="fr">'.(isset($user_favorites['shop']) ? count($user_favorites['shop']) : 0).'</span></a>
        <a href="'.zrz_get_user_page_url($user_id).'/collection-post'.'" class="clearfix"><span v-text="uName">他</span>收藏的文章<span class="fr">'.(isset($user_favorites['post']) ? count($user_favorites['post']) : 0).'</span></a>
        <a href="'.zrz_get_user_page_url($user_id).'/collection-topic'.'" class="clearfix"><span v-text="uName">他</span>收藏的帖子<span class="fr">'.(isset($user_favorites['topic']) ? count($user_favorites['topic']) : 0).'</span></a>
        <a href="'.zrz_get_user_page_url($user_id).'/collection-bubble'.'" class="clearfix"><span v-text="uName">他</span>收藏的'.zrz_custom_name('bubble_name').'<span class="fr">'.(isset($user_favorites['bubble']) ? count($user_favorites['bubble']) : 0).'</span></a>
    </div>
    <div class="fs14 gray">个人主页被浏览 '.$view.' 次</div>
';
