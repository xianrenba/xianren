<?php 
	$i=0;
	while (have_posts()&&$i<5) : the_post();
    $r = fmod($i-1, 2) + 1;
    $i++;
?>
<?php if($i==1){ ?>
	        <div class="col col-full clearfix">
              <article id="post-<?php echo $post->ID; ?>" class="post type-post status-publish format-standard">
                    <div class="entry-thumb hover-scale">
                         <a href="<?php the_permalink(); ?>"><?php echo _get_post_thumbnail(400,300,'Medium'); ?></a>
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
                        <p class="entry-excerpt"><?php echo _get_excerpt(); //$contents = get_the_content(); echo wp_trim_words($contents,120);  //echo $excerpt.new_excerpt_more('阅读全文');?></p>
                    </div>
             </article>
            </div>
<?php }else{ ?> 
	         <div class="col col-small col-1_2 col-s<?php echo $r; ?>">
                <article id="post-<?php echo $post->ID; ?>" class="post type-post status-publish format-standard">
                    <div class="entry-thumb hover-scale">
                          <a href="<?php the_permalink(); ?>"><?php echo _get_post_thumbnail(150,100,'Thumbnail'); ?></a>
					</div>
                    <div class="entry-detail">
                        <h3 class="entry-title">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="entry-meta">
                            <span class="datetime text-muted"><i class="fa fa-clock-o"></i><?php echo get_the_time('Y-m-d')?></span>
                            <span class="views-count text-muted"><i class="fa fa-eye"></i><?php echo _get_post_views() ?></span>
                            <?php if ( comments_open()) {?>
                            <span class="comments-count text-muted"><i class="fa fa-comments"></i><?php echo get_comments_number('0', '1', '%') ?> (评论)</span>
					        <?php }?>
                        </div>
                    </div>
                </article>
            </div> 
<?php } ?>
<?php endwhile;?>