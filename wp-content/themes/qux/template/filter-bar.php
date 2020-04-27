<?php

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
    $cat_ID =  get_queried_object_id();
    $order = isset($_GET['order']) ? $_GET['order'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $args = array('post_type'=> 'store', 'paged' => $paged, 'posts_per_page' => 12);
    
    if ($order) {
        if ($order == 'hot') {
            $args['meta_key'] = 'um_post_views';
            $args['orderby']  = 'meta_value_num';
            $args['order']  = 'DESC';
        }else{
            $args['orderby'] =  $order;
        }
    }
    
    if ($cat_ID) {
        $tax_query = array(
            array(
                'taxonomy' => 'products_category', //可换为自定义分类法
                'field'    => 'term_id',
                'terms'    => array($cat_ID),
            ),
        );
        $args['tax_query'] = $tax_query;
    }
    
    if ($type) {
        switch ($type) {
            case 'free':
                $_type_meta_key = 'product_price';
                $_type_value = array('0', '0.00');
                $_type_compare = 'IN';
                break;
            case 'credit':
                $_type_meta_key = 'pay_currency';
                $_type_value = '0';
                $_type_compare = '=';
                break;
            case 'cash':
                $_type_meta_key = 'pay_currency';
                $_type_value = '1';
                $_type_compare = '=';
                break;
            default:
                break;
        }
            
        $type_meta_query =  array(
        	array(
            	'key'     => $_type_meta_key,
            	'value'   => $_type_value,
            	'compare' => $_type_compare,
            )
        );
        
        $args['meta_query'] = $type_meta_query;
    }
    
    query_posts($args);
    
    $categories = get_terms('products_category', array('hide_empty' => 0,'parent' => 0));//获取所有主分类
    
    function term_parent($term_slug, $taxonomy){
    	
    	$parent  = get_term_by( 'id', $term_slug, $taxonomy);
    	while ($parent->parent){
    		$term_id = $parent->parent;
    		$parent = get_term_by('id', $term_id,$taxonomy);
    	}
    	return $parent;
    	
    }
?>

<div class="filter--content">
    <div class="form-box">
        <div class="filter-item">
            <?php
            $content = '<ul class="filter-tag"><span><i class="fa fa-folder-open-o"></i> 分类</span>';
            foreach ($categories as $category) {
                // 排除二级分类
                $_oncss = ($category->term_id == $cat_ID) ? 'on' : '' ;
                $content .= '<li><a href="'.get_category_link($category->term_id).'" class="'.$_oncss.'">'.$category->name.'</a></li>';
            }
            $content .= "</ul>";
            echo $content;
            ?>
        </div>
        <?php 
        $child_categories = get_terms( 'products_category', array('hide_empty' => 0,'parent'=>$cat_ID) );//获取所有分类
        if (empty($child_categories)) {
            $root_cat = term_parent($cat_ID ,'products_category');
            $child_categories = get_terms( 'products_category', array('hide_empty' => 0,'parent'=>$root_cat->term_id ));//获取所有分类
        }
        if ($cat_ID && !empty($child_categories)) : ?>
        <!-- 二级分类 -->
		<div class="filter-item">
            <?php
            $content = '<ul class="filter-tag"><span><i class="fa fa-long-arrow-right"></i> 更多</span>';
            foreach ($child_categories as $category) {
                $_oncss = ($category->term_id == $cat_ID) ? 'on' : '' ;
                $content .= '<li><a href="'.get_category_link($category->term_id).'" class="'.$_oncss.'">'.$category->name.'</a></li>';
            }
            $content .= "</ul>";
            echo $content;
            ?>
        </div>
        <?php endif; ?>
        <div class="filter-tab">
            <div class="row">
                <div class="col-12 col-sm-6">
                <?php 
				$is_on = !empty($_GET['type']) ? $_GET['type'] : 'all';
                $content = '<ul class="filter-tag"><span><i class="fa fa-filter"></i> 价格</span>';
                $caotype_arr = array('all' => '全部','free' => '免费','credit' => '积分' ,'cash' => '现金');
                foreach ($caotype_arr as $key => $item) {
                    $_oncss = ($is_on == $key) ? 'on' : '' ;
                    $content .= '<li><a href="'.add_query_arg("type",$key).'" class="tab '.$_oncss.'"><i></i><em>'.$item.'</em></a></li>';
                }
                $content .= "</ul>";
                echo $content; 
				?>
                </div>
                <div class="col-12 col-sm-6">
                <!-- 排序 -->
                <?php 
				$is_on = !empty($_GET['order']) ? $_GET['order'] : 'date';
                $content = '<ul class="filter-tag" style="width: 100%;"><div class="right">';
                $order_arr = array('date' => '发布日期','modified' => '修改时间','comment_count' => '评论数量','rand' => '随机','hot' => '热度');
                foreach ($order_arr as $key => $item) {
                    $_oncss = ($is_on == $key) ? 'on' : '' ;
                    $content .= '<li class="rightss"><i class="fa fa-caret-down"></i> <a href="'.add_query_arg("order",$key).'" class="'.$_oncss.'">'.$item.'</a></li>';
                }
                $content .= "</div></ul>";
                echo $content;
				?>  
                </div>
            </div>
        </div>
    </div>
</div>