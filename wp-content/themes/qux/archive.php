<?php 
get_header(); 

$cat = get_queried_object();

$iscard = (_get_tax_meta($cat->term_id,'style') == 'card') ? true : false;

$style = $cat->description ? '' : 'style="padding:0"';

$pagedtext = '';
if( $paged && $paged > 1 ){
	$pagedtext = ' <small>第'.$paged.'页</small>';
}
$title = '';
if ( is_category() ) {	
	$title = single_cat_title('', false);
}elseif ( is_tax() ) {
    $title = single_term_title( '', false );
}elseif ( is_day() ) {
    $title = get_the_time('Y年m月j日').'的文章';
}elseif ( is_month() ) {  
    $title = get_the_time('Y年m月').'的文章'; 
}elseif ( is_year() ) { 
    $title = get_the_time('Y年').'的文章';
}
?>
<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php if(is_tax('tcat')){ ?> 
		<?php if(empty( z_taxonomy_image_url($cat->term_id) )){ ?>
		<div class="pagetitle"><h1><?php echo $cat->name; ?></h1><div class="catleader-desc"><?php echo wp_trim_words($cat->description, '68'); ?></div></div>
		<?php }else{ ?>
		<div class="zhuanti">
		    <div class="thumbnail-wrap" style="background-image:url(<?php echo z_taxonomy_image_url($cat->term_id) ?>)">
		        <h3 class="zhuanti-title"><?php echo $cat->name; ?></h3>
		    </div>
		    <div class="taxonomy-description" <?php echo $style; ?>><?php echo wp_trim_words($cat->description, '68'); ?></div>
		</div>
		<?php } ?>
		<?php }else{ ?>
		<div class="pagetitle"><h1><?php echo $title; ?></h1><?php echo $pagedtext ?></div>
		<?php } ?>
		<?php  $iscard ? get_template_part( 'modules/card') : get_template_part( 'excerpt' ); ?>
	</div>
	</div>
	<?php get_sidebar(); ?>
</section>
<?php get_footer(); ?>