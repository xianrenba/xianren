<?php
function zrz_options_info_page(){
	$option = new zrzOptionsOutput();
	$status = apply_filters('zrz_check_theme',false);

	?>
<div class="wrap">
	<h1><?php _e('柒比贰主题设置','zrz');?></h1>
    <h2 class="title"><?php _e('激活及说明','zrz');?></h2>
	<form method="post">
		<input type="hidden" name="action" value="update">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">

		<?php
			zrz_admin_tabs('info');
			echo '<div style="margin-top:20px;">主题当前状态：'.($status ? '<b style="color:green">已激活</b>' : '<b style="color:red">未激活</b>').'</div>';
			$option->table( array(
				array(
					'type' => 'input',
					'th' => __('请输入你的会员号','ziranzhi2'),
					'after' => '<p>'.__('请输入您在 <a href="http://7b2.com">7b2.com</a> 用户会员号，也就是第几号会员。','ziranzhi2').'</p><p>查看方法：进入您的个人主页中查看。</p>',
					'key' => 'zrz_theme_id',
					'value' => get_option('zrz_theme_id')
				)
			));
		?>
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $status ? '手动更新授权' : '激活' ;?>"></p>
</form>
<h2 class="title"><?php _e('字体安装','zrz');?></h2>
<P>这个字体是用来生成字体头像的，请安装，否则可能会报错。</P>
<form style="margin-bottom:100px" method="post">
<?php
	if(zrz_get_theme_fonts(true)){
		echo '<p style="color:green">主题的字体已经成功安装</p>';
	}else{
		echo '
		<input name="setpfonts" type="hidden" value="1">
		<p style="color:red">字体未安装，请点击安装字体</p>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="自动安装依赖字体"></p>
		<p>包含生成验证码和生成头像的字体，根据您当前的网络情况，字体安装可能较慢，请耐心等待！</p>
		';
		if(isset($_POST['setpfonts'])){
			zrz_get_theme_fonts();
		}
	}
?>
</form>
<style>
	li,p{
		font-size: 16px;
		line-height: 1.7
	}
</style>
    <div class="">
	<h3>为了给创业者节省时间和金钱，我们的主题没有限制只能使用一个域名，下面几点请您务必要注意：</h3>

	<ol>
		<li>您最多可以绑定3个域名，除此之外，每增加一个域名需要支付当前主题价格一半的费用，最多可以增加到5个域名。</li>
		<li>购买之后可以终身免费升级。</li>
		<li>一年之内我们可以为您提供免费的咨询服务，超过一年，请再次购买售后服务。价格为100元/年。</li>
		<li>因为源码的可复制性，主题出售以后不支持退换货。</li>
		<li>请不要将主题的压缩包文件（.zip、.rar等）放到服务器中，否则会被不法之人扫描下载。</li>
		<li>每份主题都有一个唯一的识别码，可以追溯到主题的所有者，请不要故意将主题源码泄露。</li>
		<li>以上几点若有违反，我们将取消您的所有域名授权，如果造成经济损失，会依法索赔。</li>
		<h3>为了给创业者节省时间和金钱，我们的主题没有限制域名的使用数量，下面几点请您务必要关注：</h3>
    </ol>
		<h3>使用说明：</h3>
		<ol>
			<li>
				使用本主题必须设置wp的固定链接，链接方式为自定义（比如:/%post_id%.html），主题不支持wp默认带问号的链接方式！
			</li>
			<li>
                <b>使用前主题必须激活，否者将会有若干功能无法使用，比如头像等！</b>
            </li>
            <li>
                初次使用，请在后台安装下载好的主题，如果直接传到服务器，请务必点击了 <span style="color:red">启用主题</span> 的按钮，以使主题初始化成功。
            </li>
            <li>
                如果字母头像没有显示，请查看 wp-content/uploads/ 目录是否存在 avatar 目录，如果没有，说明您没有权限通过程序创建目录，请手动创建 avatar 目录，权限为 755 或 777。
            </li>
			<li>
                幻灯片在个人主页->文章中设置（需要管理员登录）。管理给用户增加积分，增加余额，修改权限，在用户的编辑个人资料页面（需要管理员登录）。
            </li>
            <li>
                推荐使用阿里云OSS图片储存服务，这样可以大幅度减少服务器的压力，也会给用户提供更好的访问体验。<a target="_blank" href="https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=jlp9zsx5">阿里云</a>
            </li>
			<li>
                每次添加、删除、修改<b>链接的分类</b>以后，请务必在主题设置项->导航链接中更新一下排序，否则前台可能无法显示！
            </li>
		</ol>
		<h3>关于个性化：</h3>
		<p>不推荐直接修改主题源码，您可以自己新建css，js，php文件，并引入到主题中，以免升级的时候被覆盖。推荐使用子主题的方式个性化</p>
		<h3>主题排错：</h3>
		<p>如果发现报错，请切换默认主题查看报错是否依然存在，如果仅在本主题中存在，请QQ联系春哥解决。</p>
		<h3>推荐安装插件：</h3>
		<ol>
			<li>
				<p>Smartideo 插件（必装）：主题中的视频功能使用了此插件，请务必安装，否则视频无法打开</p>
			</li>
			<li>
				<p>bbpress 插件（选装）：主题使用了bbpress插件作为后台，如果要使用论坛功能，请在后台安装 bbpress 插件。</p>
				<p>论坛菜单设置：在wp后台的 外观->菜单中选择 <b>顶部菜单-柒比贰</b>，将论坛的链接设置到顶部菜单中。</p>
			</li>
			<li>
				<p>Yet Another Related Posts Plugin 插件（选装）：相关文章插件，主题默认支持，如果您未设置相关文章，此插件将自动生成相关文章。</p>
			</li>
			<li>所有插件可以直接在wp后台插件功能中搜索安装！</li>
		</ol>
		<p style="color:red">本主题QQ群：424186042（仅限付费用户）</p>
		<p>如有特殊情况，请联系春哥解决（QQ:110613846），如果有急事也可以打春哥手机：15586907752。（老铁们，凌晨一两点还是不要打了！囧.jpg）</p>
		<p style="color:green">当前版本：<?php echo zrz_get_theme_version(); ?></p>
</div>
</div>
	<?php
}
