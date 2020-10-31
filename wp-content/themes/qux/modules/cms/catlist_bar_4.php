<?php 
	$i=0;
	while (have_posts()&&$i<9) : the_post();
	$r = fmod($i,3)+1;$i++;
?>
<?php if($i<4){ ?>
<div class="col col-large col-s<?php echo $r; ?>">
    <article class="post type-post status-publish format-standard">
            <div class="entry-thumb hover-scale">
                <a href="<?php the_permalink(); ?>"><?php echo _get_post_thumbnail(250,190,'Medium'); ?></a>
            </div>
            <div class="entry-detail">
                <h3 class="entry-title">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                </h3>
                <div class="entry-meta">
                    <span class="datetime text-muted"><i class="fa fa-clock-o"></i><?php echo get_the_time('Y-m-d')?></span>
                    <span class="views-count text-muted"><i class="fa fa-eye"></i><?php echo _get_post_views() ?></span>
                    <span class="comments-count text-muted"><i class="fa fa-comments"></i><?php echo _get_post_comments() ?></span>
                </div>
                <p class="entry-excerpt"><?php echo _get_excerpt('80')//$contents = get_the_content(); echo wp_trim_words($contents,60);  //echo $excerpt.new_excerpt_more('阅读全文');?></p>
            </div>
    </article>
</div>
<?php }else{ ?>
<div class="col col-small col-s<?php echo $r; ?>">
<article class=" post type-post status-publish format-standard">
	<div class="entry-thumb hover-scale">
        <a href="<?php the_permalink(); ?>"><?php echo _get_post_thumbnail(100,68,'Thumbnail'); ?></a>
    </div>
	<div class="entry-detail">
		<h3>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h3>
	</div>
</article>
</div>
<?php } ?>
<?php endwhile;?>