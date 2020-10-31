<?php
/**
 * 附件页面框架
 * @package Tanhaibo.net
 * @subpackage tanhaibonet
 * @since tanhaibonet 4.1
 */
get_header();
?>

<div id="container">
        <div id="content" role="main" >
                <!-- 文章页面的标题上方的导航 -->
                <div id="entry-nav"><strong>当前位置：</strong>首页 &raquo; 附件：<a href="<?php the_permalink(); ?>" target="_self" title="<?php the_title();?>" rel="bookmark"><?php the_title(); ?></a> &raquo; 正文</div><!-- .entry-nav -->
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
        <?php if ( ! empty( $post->post_parent ) ) : ?>
        <?php endif; ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2 class="entry-title"><?php the_title(); ?></h2>

                <div class="entry-content">
                        <div class="entry-attachment">
                                <?php if ( wp_attachment_is_image() ) : $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
                                        foreach ( $attachments as $k => $attachment ) {
                                        if ( $attachment->ID == $post->ID )
                                        break;
                                }
        $k++;
        if ( count( $attachments ) > 1 ) {
                if ( isset( $attachments[ $k ] ) )
                        $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
                else
                        $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
        } else {
                $next_attachment_url = wp_get_attachment_url();
        }
?>

                        <p class="attachment"><a href="<?php echo $next_attachment_url; ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
                        $attachment_width  = apply_filters( 'tanhaibonet_attachment_size', 900 );
                        $attachment_height = apply_filters( 'tanhaibonet_attachment_height', 900 );
                        echo wp_get_attachment_image( $post->ID, array( $attachment_width, $attachment_height ) );
                                                ?></a></p>

                                <div id="nav-below" class="navigation">
                                        <div class="nav-previous"><?php previous_image_link( false ); ?></div>
                                        <div class="nav-next"><?php next_image_link( false ); ?></div>
                                </div><!-- #nav-below -->

                                <?php else : ?>
                                        <a href="<?php echo wp_get_attachment_url(); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
<?php endif; ?>
                </div><!-- .entry-attachment -->
                
        <div class="entry-caption"><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></div>
                        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'tanhaibonet' ) ); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tanhaibonet' ), 'after' => '</div>' ) ); ?>
                </div><!-- .entry-content -->
                
                <div style="text-align:center;"><?php previous_post_link('<strong>相关文章：</strong>%link') ?></div>
                </div><!-- #post-## -->
<?php endwhile; ?>

        </div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>