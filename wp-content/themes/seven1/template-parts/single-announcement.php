<div id="bubble-home" class="content-area w100">
    <main id="main" class="site-main">
        <div class="pd10 box mar10-b clearfix">公告<span class="fr"><a href="<?php echo home_url('/announcements'); ?>">全部 ❯</a></span></div>
        <?php
            while ( have_posts() ) : the_post();
                get_template_part( 'formats/content','announcement');
            endwhile; // End of the loop.
        ?>
    </main><!-- #main -->
</div><!-- #primary --><?php
get_footer();
