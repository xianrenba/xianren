<?php
/*
Template Name: 作者墙
*/

get_header();


function user_role($user_id){
   if(user_can($user_id,'install_plugins')) {
        $authorcan =  '管理员';
     }elseif(user_can($user_id,'edit_others_posts')) {
        $authorcan =  '编辑';
     }elseif(user_can($user_id,'publish_posts')) {
        $authorcan =  '作者';
     }elseif(user_can($user_id,'delete_posts')) {
        $authorcan = '投稿者';
     }elseif(user_can($user_id,'read')) {
        $authorcan = '订阅者';
   }
  return $authorcan;
}


function _user_main($user,$class,$key){
    global $salong,$wp_query,$post;
    $user_id          = $user->ID;
    $user_name        = get_the_author_meta('display_name',$user_id);    
    $user_description = get_the_author_meta('user_description',$user_id);
    
    $items  = '';
    $items .= '<li class="layout_li'.$class.'">';
    $items .= '<article class="user_main">';
    if($key!=null){
        $items .= '<span class="num">'.$key.'</span>';
    }
    $items .= '<div class="img" title="'.$user_description.'">'.um_get_avatar( $user_id , '40' , um_get_avatar_type($user_id) ).'</div>';
    $items .= '<a href="' .get_author_posts_url($user_id). '" title="' . $user_description . '" class="title" title="' . $user_description . '"><h3>' . $user_name . '</h3><span>' . user_role($user_id) . '</span></a>';
    $items .= '<div class="follow-links">'.um_follow_button($user_id).'<a class="message" href="'.add_query_arg('tab', 'message', get_author_posts_url($user_id)).'" title="发送私信"><i class="fa fa-envelope"></i> 私信</a></div>';
    $items .= '<div class="post">';
    $items .= '<span title="文章数"><i class="fa fa-file-text-o"></i><b>' . count_user_posts($user_id) . '</b></span>';
    $items .= '<span title="浏览量"><i class="fa fa-eye"></i><b>' . author_post_field_count('post',$user_id,'views'). '</b></span>';
    $items .= '<span title="喜欢"><i class="fa fa-heart-o"></i><b>' . author_post_field_count('post',$user_id,'um_post_likes') . '</b></span>';
    $items .= '</div>';
    $items .= '</article>';
    $items .= '</li>';
    return $items;
}

function _all_user(){
    global $salong;
    $number       = 10;
    $blog_id      = get_current_blog_id();
    
    $user_r = _hui('recommend_user');
    $paged        = ( get_query_var( 'paged')) ? get_query_var( 'paged') : 1;
    $offset       = ( $paged - 1) * $number;
    $current_page = max(1, get_query_var('paged'));
    $users        = get_users( 'blog_id='.$blog_id.'&exclude='.$user_r);
    $query        = get_users( 'offset='.$offset. '&number='.$number.'&exclude='.$user_r.'&orderby=post_count&order=DESC');
    $total_users  = count($users);
    $total_pages  = ceil($total_users / $number);
    $items = '';
    $items .= '<section class="all_user_list">';
    $items .= '<ul class="layout_ul">';
    
        if(!empty($user_r)){
        $query_r = get_users( 'include='.$user_r.'&orderby=post_count&order=DESC');
        foreach ($query_r as $value=>$user) {
            $class = ' recommend';
            $key = $value+1;
            $items .= _user_main($user,$class,$key);
        }
        $items .= '<hr>';
    }
    
    foreach ($query as $user) {
        $class = ' other';
        $key = '';
        $items .= _user_main($user,$class,$key);
    }
    $items .= '</ul>';
    $items .= '</section>';
    if($total_pages >= 2){
    $items .= '<div class="pagination">';
    $paginate = paginate_links(array(
        'base'  => get_pagenum_link(1) . '%_%',
		'format' => '?paged=%#%',
        'current'   => $current_page,
        'total'     => $total_pages,
		'type' => 'array',
        'prev_next' => true,
        'prev_text' => __('上一页', 'um'),
        'next_text' => __('下一页', 'um')
    ));
    foreach ($paginate as $value) {
		$items .= '<span class="pg-item">'.$value.'</span>';
	}
    }
    
    $items .= '</div>';
    return $items;
}

?>
<div class="container container-no-sidebar">
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
		</header>
		<article class="users-content">
			<?php 
			//_list_authors(); 
            echo _all_user();
			//echo um_pager($paged, $pages); 
			?>
		</article>
		<?php endwhile;  ?>
		<?php comments_template('', true); ?>
	</div>
</div>
<?php get_footer(); ?>