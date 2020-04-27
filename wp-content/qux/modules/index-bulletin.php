<!-- 站内公告 -->
<?php $args=array( 'post_type'=> 'bulletin','posts_per_page' => 2 ); $wp_query = new WP_Query( $args );if ( $wp_query->have_posts() ) { ?>
<div class="speedbar">
	<a class="tpclose" onclick="hidetp()"><i class="fa fa-times"></i></a>
	<div class="toptip" id="callboard">
	    <ul style="font-size:14px;margin-top: 2px;">
            <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
            <li class="bulletin">
                <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                    <?php echo wp_trim_words(get_the_content(),'80'); ?>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
	</div>
</div>
<?php } ?>
<?php wp_reset_query(); ?>