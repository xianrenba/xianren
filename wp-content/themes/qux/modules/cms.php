<?php 
/*
* use for index
*/
?>
<?php
	$args=array(  
		'orderby' => 'id',  
		'order' => 'ASC',
		'exclude' => _hui('cmsundisplaycats')
	);	
	$customcats = _hui('example_text'); 
	if(!empty($customcats)){
		$catids = explode(',',$customcats);
		foreach($catids as $catid){
			$categories[] = get_category($catid);
		}
	}else{
      $categories = get_categories($args);
    }
    $cats_count = count($categories);
    $start_wrap = true;
    $end_wrap = false;
	$index = 0;
	$tps = array();
	foreach ($categories as $category) {
        $tps[] = _get_cms_cat_template($category->cat_ID);
    }
	foreach ($categories as $cat) {
      	$index++;
		$catid = $cat->cat_ID;
		$catname = $cat->cat_name;
		query_posts(array('cat'=>$catid,'post__not_in'=>get_option('sticky_posts'),'posts_per_page'=>-1));
		$catlink = get_category_link($catid);
        $tp = $tps[$index-1];
        if ($index == 1 || $end_wrap) {
            $start_wrap = true;
        } else {
            $start_wrap = false;
        }
        if ($index == $cats_count || $tp != 'catlist_bar_0' || !$start_wrap || $tps[$index] != 'catlist_bar_0') {
            $end_wrap = true;
        } else {
            $end_wrap = false;
        }
        if($index <= $cats_count && $tp != 'catlist_bar_0'){ ?>
            <div class="catlist-<?php echo $catid;?> cat-container clearfix">
                <h2 class="home-heading clearfix">
                   <span class="heading-text"><?php echo $catname;?></span><a href="<?php echo $catlink;?>">更多<i class="fa fa-angle-right"></i></a>
                </h2>
                <div class="cms-cat cms-cat-s<?php echo substr($tp,-1);?>">
                    <?php get_template_part('modules/cms/'.$tp);?>	
                </div>                            
            </div>	
        <?php  
        }else{ 
            if($start_wrap) { ?>
            <div class="catlist clr cat-container clearfix ">
            <?php } ?>
            <div class="catlist-<?php echo $catid;?> cat-col-1_2">
             <div class="cat-container clearfix">
               <h2 class="home-heading clearfix">
                 <span class="heading-text"><?php echo $catname;?></span><a href="<?php echo $catlink;?>">更多<i class="fa fa-angle-right"></i></a>
               </h2>
               <div class="cms-cat cms-cat-s<?php echo substr($tp,-1);?>">
                  <?php get_template_part('modules/cms/catlist_bar_0');?>	
               </div>  
             </div>		
            </div>
            <?php if($end_wrap) { ?>
            </div>
            <?php }
        }	
    }
    wp_reset_query(); 
?>