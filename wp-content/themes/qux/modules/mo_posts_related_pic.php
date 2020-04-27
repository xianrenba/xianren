<?php  
/**
 * [mo_posts_related description]
 * @param  integer $limit [description]
 * @return html         [description]
 */
function mo_posts_related_pic($title='热门推荐',$days = '',$limit=4){
	echo '<div class="pads">';
    echo '<div class="title"><h3>'.$title.'</h3></div>';
	global $wpdb,$post;

    $where = '';
    $output = '';
    $datatime = '';
    $mode = 'post';
    if($days){
    	$limit_date = current_time('timestamp') - ($days*86400);
    	$limit_date = date("Y-m-d H:i:s",$limit_date);
    	$datatime = "post_date < '".current_time('mysql')."' AND post_date > '".$limit_date."' AND";
    } 

    if(!empty($mode) && $mode != 'both') {
        $where = "post_type = '$mode'";
    } else {
        $where = '1=1';
    }

    $most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE $datatime $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER  BY views DESC LIMIT $limit");

    if($most_viewed) {
		echo '<ul id="tags_related">';
        foreach ($most_viewed as $post) {
            $title = get_the_title().get_the_subtitle();
            $post_views = intval($post->views); 
          
			$output .= '<li><a href="'.get_permalink($post->ID).'" title="'.$title.'" target="_blank">'._get_post_thumbnail().'</a><h4><a href="'.get_permalink($post->ID).'" target="_blank">'.$title.'</a></h4><time>'.get_the_time("y-m-d").'</time></li>';
            //$output .= '<li><p class="text-muted"><span class="post-comments">'.__('阅读', 'haoui').' ('.$post_views.')</span>'.hui_get_post_like($post->ID).'</p><span class="label label-'.$i.'">'.$i.'</span><a '._post_target_blank().' href="'.get_permalink($post->ID).'" title="'.$title.'">'.$title.'</a></li>';
        }
        echo $output;
		echo '</ul>';
    } else {
        echo '<li></li>';
    }
	echo '</div>';
}
/*
function mo_posts_related_pic($limit=4){
	echo '<div class="pads">';
	global $post;
	$post_tags = wp_get_post_tags($post->ID);
	if ($post_tags) {
		foreach ($post_tags as $tag) {
			// 获取标签列表
			$tag_list[] .= $tag->term_id;
		}
		// 随机获取标签列表中的一个标签
		$post_tag = $tag_list[ mt_rand(0, count($tag_list) - 1) ];
		// 该方法使用 query_posts() 函数来调用相关文章，以下是参数列表
		$args = array(
		    'tag__in' => array($post_tag),
            'category__not_in' => array(NULL),  // 不包括的分类ID
            'post__not_in' => array($post->ID),
            'showposts' => $limit,             // 显示相关文章数量
            'caller_get_posts' => 1
		);
		query_posts($args);
		if (have_posts()) {
			echo '<ul id="tags_related">';
			while (have_posts()) {
				the_post();
				update_post_caches($posts); ?>
				<li>
				   <a href="<?php the_permalink() ?>" title="<?php the_title_attribute()?>" target="_blank"><?php echo _get_post_thumbnail()?></a>
				   <h4><a href="<?php the_permalink() ?>" title="<?php the_title_attribute()?>" target="_blank"><?php the_title() ?></a></h4>
				   <time><?php echo get_the_time("y-m-d")?></time>
				</li>
				<?php
			}
		}else{
			echo '<li></li>';
		}
		wp_reset_query(); 
		echo '</ul>';
	}
	echo '</div>';
} */