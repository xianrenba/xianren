<?php
get_header();

?>
<div id="primary" class="content-area w100">
	<main id="gg" class="site-main top-page pos-r" role="main" ref="gg">
		<div class="pd10 box mar10-b">所有公告</div>
        <article class="page-top">
			<?php
				$args = array(
					'post_type' => 'announcement',
					'orderby'  => 'date',
					'order'=>'DESC',
				);
				$the_query = new WP_Query($args);
				if ( $the_query->have_posts()) {

					while ($the_query->have_posts()){
						$the_query->the_post();
						get_template_part( 'formats/content','announcement');
					};

					wp_reset_postdata();
				}else{
					get_template_part( 'template-parts/content', 'none' );
				}
			?>
        </article>
    </main>
</div><?php
get_footer();
