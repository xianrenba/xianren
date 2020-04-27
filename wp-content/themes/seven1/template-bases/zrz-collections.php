<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi
 */

get_header(); ?>

	<div id="primary-home" class="content-area mar10-b" style="width:100%">
		<main id="collections" class="site-main collections-content" role="main">
			<?php
			$setting = zrz_get_display_settings('collections');
            $taxonomies = get_terms( array(
            'taxonomy' => 'collection',
			'hide_empty' => false,
			'order'=>isset($setting['order']) ? $setting['order'] : 'DESC',
			'orderby'=>isset($setting['orderby']) ? $setting['orderby'] : 'count'
            ) );
			if ( !empty($taxonomies) ) :
            $html = '<ul class="l-8">';
                foreach ($taxonomies as $key => $val) {
                    $html .= '<li class="collections-item fd t-c l0">
					<div class="collections-item-in mar5 box pd10-b">
						<div class="collections-item-img pos-r t-c" style="background-image:url('.zrz_get_category_meta($val->term_id,'image',array(300,300)).')">
							<a href='.esc_url(get_term_link($val->term_id)).' class="pos-a">
								<span class="pos-a shadow">'.$val->name.'</span>
							</a>
						</div>
						<div class="b-t pd10-b pd10-t">
							<p class="fs13 bg-b pd5 mar10-b mobile-hide collections-des">'.($val->description ? $val->description : '没有说明').'</p>
							<p class="fs13 gray">已更新'.$val->count.'篇文章</p>
						</div>
					</div>
					</li>';
                }
            echo $html.'</ul>';
			else :
				echo '<div class="loading-dom pos-r box l0">
					<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">没有专栏，请创建一个！</p></div>
				</div>';
			endif;
            ?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_footer();
