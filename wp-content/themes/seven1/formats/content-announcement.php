<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ziranzhi2
 */
?>

<article id="post-single" class="box mar10-b">
	<header class="entry-header pos-r">
		<?php
			if(zrz_is_page(false,'announcements')){
				the_title( '<h2 class="pd20" ref="postTitle"><a href="'.get_permalink().'">', '</a></h2>' );
			}else{
				the_title( '<h1 class="entry-title pd20" ref="postTitle">', '</h1>' );
			}
		?>
	</header>
    <div class="post-r-meta"><?php echo zrz_time_ago().ZRZ_THEME_DOT; ?></div>
	<div id="entry-content" class="entry-content pd20">
		<?php
			the_content();
		?>
	</div>
</article>
