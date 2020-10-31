<?php 
get_header(); 

// paging
$pagedtext = '';
if( $paged && $paged > 1 ){
	$pagedtext = ' <small>第'.$paged.'页</small>';
}

_moloader('mo_is_minicat', false);

$catid = get_queried_object_id();

$widthfull = get_term_meta( $catid , '_widthfull', true);
if(_get_tax_meta($catid,'style') == 'card'){
	$layout = ' is_card';
	$iscard = false;
}else{
	$layout = '';
	$iscard = true;
}


$description = trim(strip_tags(category_description()));
?>

<?php if( mo_is_minicat() || $widthfull ){ ?>
<div class="pageheader">
	<div class="container">
		<div class="share bdsharebuttonbox">
			<?php _moloader('mo_share', false); mo_share('renren'); ?>
		</div>
	  	<h1><?php echo single_cat_title();$pagedtext; ?></h1>
	  	<div class="note"><?php echo $description ? $description : '去分类设置中添加分类描述吧' ?></div>
	</div>
</div>
<?php } ?>
<section class="container">
	<div class="content-wrap">
		<div class="content">
			<?php _the_ads($name='ads_cat_01', $class='asb-cat asb-cat-01') ?>
			<?php 
				if( mo_is_minicat() ){
					while ( have_posts() ) : the_post(); 
					    echo '<article class="excerpt-minic">';
					        echo '<h2><a'._post_target_blank().' href="'.get_permalink().'" title="'.get_the_title()._get_delimiter().get_bloginfo('name').'">'.get_the_title().'</a></h2>';
					        echo '<p class="meta">';

					        if( _hui('post_plugin_date') ){
					            echo '<time><i class="fa fa-clock-o"></i>'.get_the_time('Y-m-d').'</time>';
					        }

					        if( _hui('post_plugin_author') ){
					            $author = get_the_author();
					            if( _hui('author_link') ){
					                $author = '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.$author.'</a>';
					            }
					            echo '<span class="author"><i class="fa fa-user"></i>'.$author.'</span>';
					        }

					        if( _hui('post_plugin_view') ){
					            echo '<span class="pv"><i class="fa fa-eye"></i>'._get_post_views().'</span>';
					        }

					        if ( comments_open() && _hui('post_plugin_view') ) {
					            echo '<a class="pc" href="'.get_comments_link().'"><i class="fa fa-comments-o"></i>评论('.get_comments_number('0', '1', '%').')</a>';
					        }

					        echo '</p>';

					        echo '<div class="article-content">'; the_content(); echo '</div>';
					    echo '</article>';

					endwhile; 

					_moloader('mo_paging');

					wp_reset_query();

				}else{
					if(!$widthfull) echo '<div class="pagetitle'.$layout.'"><h1>', single_cat_title(), $pagedtext.'</h1>'.'<div class="catleader-desc">'.$description.'</div></div>';
					if(have_posts()){
						$iscard ? get_template_part( 'excerpt' ) : get_template_part( 'modules/card');
					}else{
						echo '<div class="no-post"><h2>哎呀！什么都没有！</h2></div>';
					}
				}
			?>
		</div>
	</div>
	<?php get_sidebar() ?>
</section>

<?php get_footer(); ?>