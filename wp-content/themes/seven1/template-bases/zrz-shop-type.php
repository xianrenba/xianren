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

get_header();
$type = get_query_var('zrz_shop_type');
if($type == 'buy'){
    $type = 'normal';
}

$query = new WP_Query( array( 'post_type' => 'shop','meta_key' => 'zrz_shop_type', 'meta_value' => $type ) );

$nub = get_option('posts_per_page',10);
$ipaged = get_query_var('paged') ? get_query_var('paged') : 1;
$ipages = ceil( $query->found_posts / $nub);

$shop_img = get_option('zrz_shop_img',array('normal'=>'','lottery'=>'','exchange'=>''));

$text = $type == 'normal' ? __('出售','ziranzhi2') : ($type == 'exchange' ? __('积分兑换','ziranzhi2') : __('积分抽奖','ziranzhi2'));
?>
<div id="primary-home" class="content-area fd mobile-full-width" ref="primaryHome">
    <main id="main" class="site-main grid clearfix pos-r arc shop-type" ref="grid">
        <?php if(zrz_get_display_settings('shop_show')){ ?>
            <div class="shop-title pd10 pos-r l0" :style="'background-image:url('+(normalImg ? normalImg : '<?php echo zrz_get_thumb($shop_img[$type],900,180,'',true); ?>')+')'">
                <div class="shop-title-text shadow"> <?php echo $text ?></div>
                <label class="button empty pos-a" v-if="isAdmin" v-cloak>
                    <?php _e('上传封面图片','ziranzhi2'); ?><b class="" ref="normalLoding"></b>
                    <input id="shop-input" type="file" accept="image/jpg,image/jpeg,image/png" class="hide" ref="input" @change="getFile($event,'normal')">
                </label>
            </div>
            <div class="pd10 shop-title-count box-header bg-w fs12 clearfix l0 b-b">共有<?php echo $query->found_posts; ?>个<?php echo $text; ?>商品</div>
        <?php } ?>
    <?php
    $args = array(
        'post_type' => 'shop',
        'orderby'  => 'date',
        'order'=>'DESC',
        'meta_key' => 'zrz_shop_type',
        'meta_value' => $type
    );
    $the_query = new WP_Query( $args );
    if ( $the_query->have_posts() && zrz_get_display_settings('shop_show')) {
        echo '<div ref="postList" class="'.(zrz_get_theme_style() === 'pinterest' ? 'grid-bor' : '').'">';

        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            get_template_part( 'formats/content','shop');

        };

        echo '</div><page-nav class="box" nav-type="sptype-'.$type.'" :paged="'.$ipaged.'" :pages="'.$ipages.'" :show-type="\'p\'"></page-nav>';
        wp_reset_postdata();
    }else {

        get_template_part( 'template-parts/content', 'none' );

    } ?>
    </main><!-- #main -->
</div><?php
get_sidebar();
get_footer();
