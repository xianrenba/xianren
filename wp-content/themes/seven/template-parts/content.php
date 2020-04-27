<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi2
 */
	$thumb = zrz_get_post_thumb($post->ID,true);
	$loveNub = get_post_meta($post->ID, 'zrz_zan_nub', true );
	$loveNub = $loveNub ? $loveNub : 0;
	$id = get_the_id();
	$excerpt = get_post_field('post_excerpt', $id);
	$user_id = get_the_author_meta( 'ID' );
	$user_name = get_the_author_meta('display_name');
	$lv = zrz_get_lv($user_id,'lv');
	//显示的设置项
	$display = zrz_get_display_settings('single');
	$view = '';
	$video = get_post_meta($post->ID,'zrz_thumb_video',true);
	$video_dom = apply_filters('the_content', $video);
	$vclass = "";
	if(strpos($video_dom,'smartideo') !== false){
		$video_dom = $video_dom;
	}else{
		$video_dom = '<video src="'.$video.'" controls="controls"></video>';
		$vclass = "nav-video";
	}
	$strlen = mb_strlen( strip_shortcodes(strip_tags(get_post_field('post_content', $id))) ,'UTF-8');
	$html = $class = '';
	if($strlen <= 511){
		$html = '<span class="play-audio post-meta-read" v-cloak>
			<button class="text clearfix t-l" @click="playAudio()" v-cloak><i :class="(play ? \'zrz-icon-font-pause\' : \'zrz-icon-font-play1\')+\' iconfont\'"></i><span v-if="play">暂停朗读</span><span v-else>为您朗读</span></button>
			<audio ref="audio" class="hide" :src="\'//fanyi.sogou.com/reventondc/microsoftGetSpeakFile?from=translateweb&spokenDialect=zh-CHS&text=\'+text" preload="none"></audio>
		</span>';
	}
	$shang = zrz_get_post_shang($id);
 ?>
<article id="post-single" class="box" data-date="<?php echo get_the_date('Y/n/d'); ?>">

	<?php if($video){ ?>
		<header class="entry-header pos-r entry-header-none">
			<div class="write-video <?php echo $vclass; ?>"><?php echo $video_dom; $class = 'no-thumb'; ?></div>
			<div id="post-views" class="post-views pos-a l1 shadow"><i class="iconfont zrz-icon-font-fire1"></i><span ref="postViews">0</span>°</div>
		</header><!-- .entry-header -->
	<?php }elseif($thumb){ ?>
		<header class="entry-header pos-r">
			<div class="img-bg" style="background-image:url('<?php echo zrz_get_thumb($thumb,ceil(zrz_get_theme_settings('page_width')*0.74751),300); ?>')"></div>
			<div id="post-views" class="post-views pos-a l1 shadow"><i class="iconfont zrz-icon-font-fire1"></i><span ref="postViews">0</span>°</div>
		</header><!-- .entry-header -->
	<?php }else{ ?>
		<header class="entry-header pos-r entry-header-none">
			<?php
				$view = '<span class="post-views l1"><span ref="postViews">0</span>次点击</span>';
				$class = 'no-thumb';
			?>
		</header><!-- .entry-header -->
	<?php } ?>
	<div class="post-meta">
		<div class="single-post-meta pos-r fs13 <?php echo $class; ?>">
			<a href="<?php echo zrz_get_user_page_url(); ?>"><?php echo get_avatar($user_id,'70'); ?></a>
			<div class="single-user-meta">
				<a href="<?php echo zrz_get_user_page_url(); ?>">
					<span class="author-name" ref="uname"><?php echo $user_name; ?></span>
					<span><?php echo $lv; ?></span>
				</a>
				<?php
					echo '<div class="post-r-meta">发表于：'.zrz_time_ago().ZRZ_THEME_DOT.$view.'</div>';
				?>
				<div class="pos-a single-add-follow" v-if="cuser != author" v-cloak>
					<button :class="follow == 1 ? 'single-follow' : 'single-follow-none'" @click="followAc()">
						<span v-if="follow == 1" class="gz-p"><b class="gz-p-y">已关注</b><b class="gz-p-q">取消关注</b></span>
						<span v-else><i class="iconfont zrz-icon-font-lnicon34"></i>关注</span>
					</button>
				<span class="dot"></span><button class="empty" @click="dMsgBox">私信</button>
				</div>
			</div>
			<!-- 给作者发私信 -->
			<msg-box :show="showBox" :tid="tid" :tname="uname" :mtype="mtype" title="发送私信" @close-form="closeForm"></msg-box>
		</div>
	</div>
	<?php
		the_title( '<h1 class="entry-title" ref="postTitle">', $html.'</h1>' );
	?>
	<div class="clearfix">
		<?php echo zrz_get_post_tags(); ?>
	</div>
	<div id="entry-content" class="entry-content pos-r pd20">

		<div id="content-innerText">
			<?php if($excerpt){ ?>
				<div class="post-excerpt mar20-b pos-r mar20-t"><?php echo $excerpt; ?></div>
			<?php } ?>
			<?php
				the_content();
			?>
		</div>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ziranzhi2' ),
				'after'  => '</div>',
			) );
		?>
		<footer class="entry-footer mar20-t pos-a" data-margin-top="70">
			<div class="pos-r clearfix">
				<?php echo zrz_get_share(); ?>
				<div class="footer-author fs14 mar20-t">
					<button class="text" @click.stop="favorites()"><i :class="['iconfont' ,loved ? 'zrz-icon-font-collect' : 'zrz-icon-font-shoucang2']"></i> <span v-text="count"></span> 收藏</button>
				</div>
			</div>
		</footer><!-- .entry-footer -->
	</div><!-- .entry-content -->
	<div id="post-ac" class="post-ac">
		<div class="pd20">
			<?php if(isset($display['ds']) && $display['ds'] == 1){ ?>
				<div class="shang pos-r t-c mar20-b" ref="shang">
					<div class="fs15 mar20-b gray">「点点赞赏，手留余香」</div>
					<div class="mar20-b">
						<button class="pos-r" @click="dsShowAc">赞赏</button>
					</div>
					<p class="fs13 mar20-b gray"><?php echo $shang['count'] ? $shang['count'].'人已赞赏' : ''; ?></p>
					<div class="shang-user">
						<?php echo $shang['html']; ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="clearfix post-vote b-t">
			<?php if(isset($display['long_weibo']) && $display['long_weibo'] == 1){ ?>
				<div class="longwb t-r fl">
					<button @click="showLongWeiBo"><span>海报</span></button>
				</div>
			<?php } ?>
			<?php if(isset($display['reaction']) && $display['reaction'] == 1){ ?>
				<div class="reaction pos-r fr">
					<face></face>
					<!-- 投票组件，在文件 main.js 中修改 -->
				</div>
			<?php } ?>
		</div>
		<long-weibo :show="longShow" :post-title="'<?php echo get_the_title(); ?>'" @close-long="closeLong"></long-weibo>
		<?php echo zrz_post_edit_button($id); ?>
		<!-- 打赏组件，在文件 main.js 中修改 -->
		<payment :show="dsShow" :type-text="'给'+authorData.name+'打赏'" :type="'ds'" :price="price" :data="authorData" @close-form="closeForm"></payment>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
<?php
	$open_ads = zrz_get_ads_settings('single_footer');
	$open_ads = $open_ads['open'];
	if($open_ads){
		get_template_part( 'template-parts/ads','single-footer');
	}
?>
<div id="pay-form">
	<payment :show="show" :type-text="'付费内容'" :type="'post'" :price="price" :data="data" @close-form="closeForm"></payment>
	<!-- 支付组件，在文件 main.js 中修改 -->
</div>
<?php if(isset($display['navigation']) && $display['navigation'] == 1){ ?>
	<div class="post-navigation clearfix mar10-t"><?php echo zrz_get_pre_next_post(); ?></div>
<?php } ?>
