<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi2
 */

get_header();
global $wp_query;
$nub = get_option('posts_per_page',10);
$ipaged = get_query_var('paged') ? get_query_var('paged') : 1;
$ipages = ceil( $wp_query->found_posts / $nub);
$des = get_the_archive_description();
$tax_shop = is_tax( 'shoptype' ) && zrz_get_display_settings('shop_show') ? 1 : 0;
$tax_labs = is_tax( 'labtype' ) && zrz_get_display_settings('labs_show') ? 1 : 0;
$cat_id = get_queried_object_id();
$ar_type = get_queried_object();
$ar_type = isset($ar_type->taxonomy) ? $ar_type->taxonomy : '';
$nav_type = '';
if($ar_type == 'shoptype'){
	$nav_type = 'shop'.$cat_id;
}elseif($ar_type == 'post_tag'){
	$nav_type = 'tag'.$cat_id;
}
?>
<?php if($tax_shop){ ?>
	<div id="cat-header" class="cat-header pos-r box mar10-b">
		<div class="cat-header-in pos-r">
			<label class="label-switch" v-cloak v-if="isAdmin"><input class="zrz-switch zrz-switch-anim mouh" v-model="blur" type="checkbox" @click.stop="blurChange"></label>
			<div :class="['cat-bg','pos-a','img-bg',{'blur':blur}]" :style="{ backgroundImage: 'url(\'' + bgimage + '\')',margin:!blur ? 0 : '-10px' }"></div>
			<div class="cat-title pos-a lm">
				<h1 class="mar10-b shadow"><?php echo get_the_archive_title('',false); ?></h1>
				<p class="shadow"><?php echo $des ? $des : __('没有描述','ziranzhi2'); ?></p>
			</div>
			<span class="pos-a cat-button mobile-hide" v-cloak v-if="isAdmin">
				<label for="cat-input" class="button empty"><b :class="{'loading':locked}"></b>上传分类封面图片</button>
				<input id="cat-input" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="hide" @change="getFile($event)">
			</span>
		</div>
	</div>
<?php }else{ ?>
<div class="box pd10 mar10-b fs13">
	<i class="iconfont zrz-icon-font-da"></i> <?php echo get_the_archive_title('',false); ?>
</div>
<?php } ?>
<div id="primary-home" class="content-area fd mobile-full-width" ref="primaryHome">
	<main id="main" class="site-main grid clearfix pos-r arc" ref="grid">
	<?php
	if ( have_posts() ) :
		echo '<div ref="postList" class="'.(zrz_get_theme_style() === 'pinterest' ? 'grid-bor' : 'box').'">';

		while ( have_posts() ) : the_post();

			 if($tax_shop){
				 get_template_part( 'formats/content','shop');
			 }elseif($tax_labs){
				 get_template_part( 'formats/content','labs');
			 }else{
				 get_template_part( 'formats/content');
			 }

		endwhile;

		echo '</div><page-nav class="box" nav-type="'.$nav_type.'" :paged="'.$ipaged.'" :pages="'.$ipages.'" :show-type="\'p\'"></page-nav>';

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;?>
	</main><!-- #main -->
</div><?php
get_sidebar();
get_footer();
