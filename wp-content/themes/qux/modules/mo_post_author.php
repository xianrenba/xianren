<?php  
/**
 * [mo_post_author description]
 * @return [type] [description]
 */
$author_email = get_the_author_email();
$avatar_id =  get_the_author_meta( 'ID' );
$avatar_description =  get_the_author_meta( 'description' );
?>
<div class="article-author">
	<?php /* echo _get_the_avatar($avatar_id, $author_email); */ ?>
	<?php echo um_get_avatar( $avatar_id, '40' , um_get_avatar_type($avatar_id) ); ?>
	<h4> <i class="fa fa-user" aria-hidden="true"></i>
		<a title="查看更多文章" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_the_author(); ?></a>
	</h4>
	<?php echo $avatar_description; ?>
</div>
