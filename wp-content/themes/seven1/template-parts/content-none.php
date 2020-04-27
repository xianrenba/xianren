<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi2
 */

?>

		<?php
		if ( is_home()) { ?>

		<section class="error-404 not-found box pos-r l0" style="min-height:500px">
			<div class="page-content">
				<div class="lm t-c">
					<img class="mar10-b" src="<?php echo ZRZ_THEME_URI.'/images/404.png'; ?>">
					<div class="mar10-b">
						<p class="mar10-b"></p>
						<h1 class="mar20-t mar10-b">没有内容</h1>
						<p class="mar10-b">我一直以为人是慢慢变老的，</p>
						<p class="mar10-b">其实不是，</p>
						<p class="mar10-b">人是瞬间变老的。</p>
					</div>
				</div>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	<?php }elseif(is_search()) { ?>
		<section class="error-404 not-found box pos-r l0" style="min-height:500px">
			<div class="page-content">
				<div class="lm t-c">
					<img class="mar10-b" src="<?php echo ZRZ_THEME_URI.'/images/404.png'; ?>">
					<div class="mar10-b">
						<p class="mar10-b"></p>
						<h1 class="mar20-t mar10-b">没有搜索倒你想要的内容</h1>
						<p class="mar10-b">请试试其它关键词</p>
						<p class="mar10-b">或者向管理员反馈你的需求</p>
					</div>
				</div>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->
	<?php }else{ ?>
		<section class="error-404 not-found box pos-r l0" style="min-height:500px">
			<div class="page-content">
				<div class="lm t-c">
					<img class="mar10-b" src="<?php echo ZRZ_THEME_URI.'/images/404.png'; ?>">
					<div class="mar10-b">
						<p class="mar10-b"></p>
						<h1 class="mar20-t mar10-b">没有找到到任何内容</h1>
						<p class="mar10-b">以前有座山</p>
						<p class="mar10-b">山里有座庙</p>
						<p class="mar10-b">庙里有个页面</p>
						<p class="mar10-b">现在页面找不到</p>
					</div>
				</div>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->
	<?php } ?>
