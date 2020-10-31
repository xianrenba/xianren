<?php  get_header(); ?>
<div class="breadcrumb-box"><div class="crumbs"><a href="<?php echo get_bloginfo('url'); ?>"><?php _e('首页 ','um'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;<a href="<?php echo home_url('/bulletin'); ?>"><?php _e('网站公告 ','um'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;正文</div></div>
<section class="container container-no-sidebar bulletin">
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
            <div class="article-meta">
                <span class="meta-author">发布者: <a class="entry-author" href="<?php echo get_author_posts_url($post->post_author) ?>" target="_blank"><?php echo get_the_author(); ?></a></span>
                <span class="separator" role="separator"> · </span>
                <span class="meta-date">发布于: <time class="entry-date"><?php echo get_the_time('Y-m-d H:i', $post); ?></time></span>
                <span class="separator" role="separator"> · </span>
                <span class="meta-views">浏览: <?php echo absint(get_post_meta( $post->ID, 'views', true )); ?></span>
             </div>
		</header>
		<article class="article-content">
			<?php the_content(); ?>
		</article>
		<div class="action-share bdsharebuttonbox">
			<?php _moloader('mo_share'); ?>
		</div>
		<?php endwhile;  ?>
		<?php //comments_template('', true); ?>
	</div>
</section>
<?php  get_footer(); ?>