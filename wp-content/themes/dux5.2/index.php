<?php 
	get_header(); 
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>
<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php 
			if( $paged==1 && _hui('focusslide_s') ){ 
				_moloader('mo_slider', false);
				mo_slider('focusslide');
			} 
		?>
		<?php 
			$pagedtext = ''; 
			if( $paged > 1 ){
				$pagedtext = ' <small>第'.$paged.'页</small>';
			}
		?>
		<?php  
			if( _hui('minicat_home_s') ){
				_moloader('mo_minicat');
			}
		?>
		
		<!--自定义文章列表--> 
		<?php 
			if( _hui('qgg_diy_postlist_open') ){
				_moloader('qgg_diy_post_list');
			}
		?>
		
		<!--热门文章排行-->  
		<?php 
			if( _hui('most_list_s') ){
				_moloader('qgg_recent_posts_most');
			}
		?>
		
		<?php _the_ads($name='ads_index_01', $class='asb-index asb-index-01') ?>
		<div class="title">
			<h3>
				<?php echo _hui('index_list_title') ? _hui('index_list_title') : '最新发布' ?>
				<?php echo $pagedtext ?>
			</h3>
			<?php 
				if( _hui('index_list_title_r') ){
					echo '<div class="more">'._hui('index_list_title_r').'</div>';
				} 
			?>
		</div>
		<?php 
			$pagenums = get_option( 'posts_per_page', 10 );
			$offsetnums = 0;
			$stickyout = 0;
			$sticky_ids = get_option('sticky_posts');
			if( _hui('home_sticky_s') && in_array(_hui('home_sticky_n'), array('1','2','3','4','5')) && $pagenums>_hui('home_sticky_n') && count($sticky_ids)>0 ){
				if( $paged <= 1 ){
					$pagenums -= count($sticky_ids);
					rsort( $sticky_ids );
					$args = array(
						'post__in'            => $sticky_ids,
						'showposts'           => _hui('home_sticky_n'),
						'ignore_sticky_posts' => 1
					);
					query_posts($args);
					get_template_part( 'excerpt' );
					wp_reset_query();
				}else{
					$offsetnums = _hui('home_sticky_n');
				}
				$stickyout = get_option('sticky_posts');
			}


			$args = array(
				'post__not_in'        => array(),
				'ignore_sticky_posts' => 1,
				'showposts'           => $pagenums,
				'paged'               => $paged
			);
			if( $offsetnums ){
				$args['offset'] = $pagenums*($paged-1) - $offsetnums;
			}
			if( _hui('notinhome') ){
				$pool = array();
				foreach (_hui('notinhome') as $key => $value) {
					if( $value ) $pool[] = $key;
				}
				if( $pool ) $args['cat'] = '-'.implode($pool, ',-');
			}
			if( _hui('notinhome_post') ){
				$pool = _hui('notinhome_post');
				$args['post__not_in'] = explode("\n", $pool);
			}
			if( $stickyout ){
				$args['post__not_in'] = array_merge($stickyout, $args['post__not_in']);
			}
			query_posts($args);
			get_template_part( 'excerpt' ); 
			_moloader('mo_paging');
			wp_reset_query();
		?>
		<?php _the_ads($name='ads_index_02', $class='asb-index asb-index-02') ?>
	</div>
	</div>
	<?php get_sidebar(); ?>
</section>
<?php get_footer();