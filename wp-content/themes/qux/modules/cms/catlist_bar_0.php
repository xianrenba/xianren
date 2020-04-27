<div class="cms-cat cms-cat-s0">
<?php 
	$i=0;
	while (have_posts()&&$i<9) : the_post();
	$r = fmod($i,3)+1;$i++;
?>
<?php if($i<2){ ?>
    <div class="row-up">
        <article id="post-<?php echo $post->ID; ?>" class="post type-post status-publish format-standard">
		    <div class="entry-thumb hover-scale">
              <a href="<?php the_permalink(); ?>"><?php echo _get_post_thumbnail(120,100,'Thumbnail'); ?></a>
            </div>
            <div class="entry-detail">
             <h3 class="entry-title">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
             </h3>
             <p class="entry-excerpt"><?php echo _get_excerpt(); ?></p>
            </div>
        </article>
    </div>
<?php }else{ ?>
    <div class="row-small">
        <article id="post-3830" class="post type-post status-publish format-standard">
            <div class="entry-detail">
                <h3 class="entry-title">
                    <i class=""></i>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                </h3>
            </div>
        </article>
    </div>
<?php } ?>
<?php endwhile;?>
</div>