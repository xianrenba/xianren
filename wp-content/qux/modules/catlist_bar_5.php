<?php 
	$i=0;
	while (have_posts()&&$i<8) : the_post();
	$r = fmod($i,2)+1;$i++;
	if($r==1)$cls='left';else $cls='right';
?>
<div class="col col-<?php echo $cls; ?>">
	<article id="post-<?php echo $post->ID; ?>" class="post type-post status-publish format-standard">
        <div class="entry-thumb hover-scale">
            <a href="<?php the_permalink(); ?>"><?php echo _get_post_thumbnail(150,100,'Thumbnail'); ?></a>
        </div>
        <div class="entry-detail">
            <h3 class="entry-title">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
            </h3>
            <p class="entry-excerpt"><?php echo _get_excerpt(); ?></p>
        </div>
    </article> 
</div>
<?php endwhile;?>
