<?php get_header(); ?>
<?php if( _hui('breadcrumbs_single_s') ){ ?>
<div class="breadcrumb-box"><div class="crumbs"><?php echo get_breadcrumb(); ?></div></div>
<?php } ?>
<section class="container">
    <?php while (have_posts()) : the_post(); ?>
	<?php if(has_post_format( 'video' )){ ?> 
    <div class="wall full-width-video-layout">
        <div id="video" class="wrap cf">	
	        <div id="videoBox" class="box screen fluid-width-video-wrapper"><?php dp_video($post->ID,true); ?></div>
		</div><!-- end #video -->
	</div><!-- end #wall -->
	<?php } ?>
	<div class="content-wrap">
	<div class="content">
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?><?php echo get_the_subtitle() ?></a></h1>
			<div class="article-meta">
                <span class="item"><?php echo tb_xzh_is_original() ? get_the_time('Y-m-d H:i:s') : get_the_time('Y-m-d'); ?></span>
				<?php _moloader('mo_get_post_from', false); ?>
				<?php if( mo_get_post_from() ){ ?><span class="item"><?php echo mo_get_post_from(); ?></span><?php } ?>
				<span class="item"><?php echo '分类：';the_category(' / '); ?></span>
				<?php if( _hui('post_plugin_view') ){ ?><span class="item post-views"><?php echo _get_post_views() ?></span><?php } ?>
				<span class="item"><?php echo _get_post_comments() ?></span>
				<?php /*print baidu_record()*/ ?>
				<?php
                if ( _hui('erweima_s')) { ?>
				<span class="item muted"><i class="fa fa-qrcode"></i> <a style="cursor : pointer;" onMouseOver="document.all.qr.style.visibility=''" onMouseOut="document.all.qr.style.visibility='hidden'">扫描二维码</a>
				<span id="qr" style="visibility: hidden;"><img style="position:absolute;z-index:99999;" src="https://sapi.k780.com/?app=qr.get&data=<?php the_permalink(); ?>&level=L&size=3"/></span></span>
		        <span class="bianlan" style="color: red; cursor: pointer;" ><span class="close-sidebar" ><i class="fa fa-chevron-circle-right"> 隐藏侧边</i></span><span class="show-sidebar" style="display:none;"><i class="fa fa-chevron-circle-left"> 显示侧边</i></span></span>
                <?php } ?>
				<span class="item"><?php edit_post_link('[编辑]'); ?></span>
			</div>
		</header>
        <?php tb_xzh_render_body() ?>
		<article class="article-content" <?php if(!_hui('p_readmore')) echo 'style="max-height:100%"'; ?>>
		<?php _the_ads($name='ads_post_01', $class='asb-post asb-post-01') ?>
		<?php the_content(); ?>
       		
        <?php 
		    if(_hui('p_readmore')){ 
                echo '<div class="mask"></div></article><div class="hidetitle"><button id="read-more">阅读全文</button></div>';
            }else{ echo '</article>';} 
		?>
        <?php _post_activity_button(); ?>
		<?php wp_link_pages('link_before=<span>&link_after=</span>&before=<div class="article-paging">&after=</div>&next_or_number=number'); ?>
		<?php  
			if( _hui('post_link_single_s') ){
				_moloader('mo_post_link');
			}
		?>
        <?php tb_xzh_render_tail() ?>
		<?php 
			if( _hui('post_copyright_s') ){
				echo '<p class="post-copyright">' . _hui('post_copyright') . '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a> &raquo; <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
			}
		?>
		<div class="action-share bdsharebuttonbox">
			<?php _moloader('mo_share'); ?>
		</div>
        <div class="article-tags"><?php the_tags('标签：','',''); ?></div>
		<?php 
			if (_hui('post_author')) {
				_moloader('mo_post_author', false);
			}
		?>
		<?php _the_ads($name='ads_post_02', $class='asb-post asb-post-02') ?>
		<nav class="pager" role="navigation">
			<li class="previous">
				<?php $prev_post = get_previous_post(); get_previous_post() ? print '<a title="上一篇：'.$prev_post->post_title.'" href="'.get_permalink( $prev_post->ID ).'">上一篇：'.$prev_post->post_title.'</a>' : print '<a>很抱歉，这已经是最后的一篇文章了！</a>';?>
			</li>
			<li class="next">
				<?php $next_post = get_next_post(); get_next_post() ? print '<a title="下一篇：'.$next_post->post_title.'" href="'.get_permalink( $next_post->ID ).'">下一篇：'.$next_post->post_title.'</a>' : print '<a>很抱歉，这已经是最新的一篇文章了！</a>';?>
			</li>
		</nav>
		
		<nav class="nav-single-c">
            <nav class="navigation post-navigation" role="navigation">
                <div class="nav-links">
                    <div class="nav-previous">
                        <?php $prev_post = get_previous_post(); get_previous_post() ? print '<a href="'.get_permalink( $prev_post->ID ).'"><span class="meta-nav-r" aria-hidden="true"><i class="fa fa-angle-left"></i></span></a>' : print '<a></a>'; ?>
                    </div>
                    <div class="nav-next">
                        <?php $next_post = get_next_post(); get_next_post() ? print '<a href="'.get_permalink( $next_post->ID ).'"><span class="meta-nav-l" aria-hidden="true"><i class="fa fa-angle-right"></i></span></a>'  : print '<a></a>'; ?>
                    </div>
                </div>
            </nav>
        </nav>
        <?php if(_hui('post_hot_s')){ 
                   _moloader('mo_posts_related_pic',false);	
                   mo_posts_related_pic(_hui('post_hot_title'),_hui('post_hot_day'),4);
              } 
        ?>
		<?php 
			if( _hui('post_related_s') ){
				_moloader('mo_posts_related', false); 
				mo_posts_related(_hui('related_title'), _hui('post_related_n'));
			}
		?>
		<?php _the_ads($name='ads_post_03', $class='asb-post asb-post-03') ?>
		<?php comments_template('', true); ?>	
	</div>
	</div>
	<?php endwhile; ?>
	<?php 
		if( has_post_format( 'aside' )){

		}else{
			get_sidebar();
		} 
	?>
</section>

<?php get_footer(); 