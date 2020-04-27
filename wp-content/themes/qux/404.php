<?php get_header(); ?>
<section class="container">
	<div class="f404">
	    <?php if(wp_is_mobile()){ ?>
	    <img src="<?php echo get_stylesheet_directory_uri() ?>/img/404.png">
	    <?php }else{ ?>
		<div class="container">
	        <div class="directions">
		        <p><strong>如何玩：</strong> 使用你的箭头键移动瓷砖。当两个瓦片相互滑动时，它们合并成一个！</p>
	        </div>
	        <div class="scores">
		        <div class="score-container best-score">最佳: <div class="score"><div id="bestScore">0</div></div></div>
		        <div class="score-container">分数: <div class="score"><div id="score">0</div><div class="add" id="add"></div></div></div>
	        </div>
	        <div class="game">
		        <div id="tile-container" class="tile-container"></div>
		        <div class="end" id="end">游戏结束<div class="monkey"></div><button class="btn btn-primary not-recommended__item js-restart-btn" id="try-again">再试一次</button></div>
	       </div>
        </div>
        <?php } ?>
        <!--<script type="text/javascript" src="<?php echo home_url();?>/search_children.js"></script>-->
		<h1>404 . Not Found</h1>
		<h2>沒有找到你要的内容！</h2>
		<p>
			<a class="btn btn-primary" href="<?php echo get_bloginfo('url') ?>">返回 <?php echo get_bloginfo('name') ?> 首页</a>
		</p>
	</div>
</section>
<?php get_footer(); ?>