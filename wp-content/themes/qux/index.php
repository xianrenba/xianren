<?php 
	get_header();
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $thelayout = the_layout();
?>
<section class="container">
    <?php if (_hui ("dux_tui_s")) { get_template_part('modules/index','bulletin'); } ?>	
	<div class="content-wrap">
	<div class="content">
	    <?php 
            
            if( $paged==1 && _hui('focusslide_s') ){ _moloader('mo_slider', false); mo_slider('focusslide');}
            
            if( $paged==1 && _hui('focus_s') )  _moloader('mo_posts_focus');
            
            if( $paged==1 && _hui('home_topic') )  _moloader('mo_home_topic');
            
            if( $paged==1 && _hui('most_list_s') ) _moloader('mo_recent_posts_most');
            $pagedtext = ''; 
            if( $paged > 1 ){
				 $pagedtext = ' <small>第'.$paged.'页</small>';
            }
            if( _hui('minicat_home_s') ) _moloader('mo_minicat');
            
            _the_ads($name='ads_index_01', $class='asb-index asb-index-01');
            
            if($thelayout == 'index-cms' && $paged==1){
               get_template_part( 'modules/cms');
            }
			
			if(_hui('cms_blog') || $thelayout !== 'index-cms'){
			?>
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
		    <?php }
				$args = array(
			       'ignore_sticky_posts' => 1,
			       'paged' => $paged
			    );
			    
			    if( _hui('notinhome') ){
				    $pool = array();
				    foreach (_hui('notinhome') as $key => $value) {
					    if( $value ) $pool[] = $key;
				    }
				    $args['cat'] = '-'.implode($pool, ',-');
			    }
			    
			    if( _hui('notinhome_post') ){
				    $pool = _hui('notinhome_post');
				    $args['post__not_in'] = explode("\n", $pool);
			    }
			    if($thelayout == 'index-card' && _hui('card-num')){
			    	$args['posts_per_page'] = intval(_hui('card-num'));
			    }
			    
			    query_posts($args);
				if($thelayout == 'index-card'){
					get_template_part( 'modules/card');
				}elseif($thelayout == 'index-blog' || _hui('cms_blog')){
					get_template_part( 'excerpt');
				}                   
        ?>      
	    <?php _the_ads($name='ads_index_02', $class='asb-index asb-index-02') ?>
	</div>
	<?php dynamic_sidebar( 'homepage' ); ?>
	</div>
	<?php get_sidebar(); ?>
</section>
<?php get_footer();  