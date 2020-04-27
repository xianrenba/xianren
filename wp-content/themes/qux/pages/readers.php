<?php 
/**
 * Template name: Readers
 * Description:   A readers page
 */

get_header();

?>

<?php 
function readers_wall( $outer='1',$timer='3',$limit='100' ){
	global $wpdb;
	$counts = $wpdb->get_results("SELECT count(comment_author) AS cnt, user_id, comment_author, comment_author_url, comment_author_email FROM $wpdb->comments WHERE comment_date > date_sub( now(), interval $timer month ) AND user_id!='1' AND comment_author!=$outer AND comment_approved='1' AND comment_type='' GROUP BY comment_author ORDER BY cnt DESC LIMIT $limit");

	$i = 0;
	$type = '';
	foreach ($counts as $comment) {
		$id = (int)$user_id=$comment->user_id;
		if($id===0){
			$user_avatar = get_avatar( $user_author=$comment->comment_author, '40' );
		}else{
			$user_avatar = um_get_avatar( $user_id=$comment->user_id , '40' , um_get_avatar_type($user_id=$comment->user_id) ); 
		}
		$i++;
		$c_url = um_get_user_url('index',$id);
        if ($id===0) $c_url = $comment->comment_author_url;
		if ($c_url == '') 	$c_url = 'javascript:;';
		$tt = $i;
		if( $i == 1 ){
			$tt = '金牌读者';
		}else if( $i == 2 ){
			$tt = '银牌读者';
		}else if( $i == 3 ){
			$tt = '铜牌读者';
		}else{
			$tt = '第'.$i.'名';
		}
		if( $i < 4 ){
			$type .= '<a class="item-top item-'.$i.'" target="_blank" href="'. $c_url . '"><h4>【'.$tt.'】<small>评论：'. $comment->cnt . '</small></h4>'.$user_avatar .'<strong>'.$comment->comment_author.'</strong>'.$c_url.'</a>';
		}else{
			$type .= '<a target="_blank" href="'. $c_url . '" title="【'.$tt.'】评论：'. $comment->cnt . '">'.$user_avatar .$comment->comment_author.'</a>';
		}
		
	}
	echo $type;
};
?>

<div class="container container-page">
	<?php _moloader('mo_pagemenu', false) ?>
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
		</header>
		<article class="article-content">
			<?php the_content(); ?>
		</article>
		<?php endwhile;  ?>

		<div class="readers">
			<?php //readers_wall(1, 6, 100); ?>
			<?php readers_wall(1, _hui('readwall_limit_time'), _hui('readwall_limit_number')); ?>
		</div>

		<?php comments_template('', true); ?>
	</div>
</div>

<?php

get_footer();