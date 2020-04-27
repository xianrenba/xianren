<?php
/*
*活动内页
*/
    get_header();
    
?>
<?php 
    while ( have_posts() ) : the_post();
    do_action( 'seven_activity_single_top' );
?>
<div class="content-in activity-content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
        <?php
            do_action( 'seven_activity_single_content' );

            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;            
        ?>
        </main>
    </div>
</div>
<?php endwhile; ?>
<?php
    get_footer();
?>