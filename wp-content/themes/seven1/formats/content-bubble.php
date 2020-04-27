<?php
    $post_id = get_the_id();
    $user_id = get_post_field('post_author',$post_id);
    $content = get_post_field('post_content',$post_id);

    $current_user = get_current_user_id();

    $terms = wp_get_post_terms( $post_id, 'mp',array('fields'=>'all'));
    $terms = isset($terms[0]) ? $terms[0] : false;
    $num_comments = get_comments_number();
    $love = get_post_meta($post_id, 'zrz_favorites', true );
    $love = is_array($love) ? $love : array();

    $loved = 0;
    if(in_array($current_user, $love)){
        $loved = 1;
    }
    $count = count($love);

    $post_status = get_post_status($post_id);
?>
<div class="bubble-list-item box mar16-b <?php if($post_status != 'publish') echo ' status-pending'; ?>" id="bubbleList<?php echo $post_id; ?>">
    <div class="pd10 bubble-list-item-h fs12 gray clearfix">
        <?php echo get_avatar($user_id,'26'); ?><?php echo zrz_get_user_page_link($user_id); ?>
        <?php echo $terms ? '<span class="dot"></span><a href='.get_term_link( $terms ).' class="bubble-cat mouh" data-topic="'.$terms->term_id.'"># '.$terms->name.' #</a>' : ''; ?>
        <?php echo '<span class="dot"></span>'.zrz_time_ago(); ?>
        <div class="fr pos-r">
            <?php if($post_status != 'publish') echo '<span class="red pending-text">待审状态</span>'; ?>
            <?php if(!is_singular('pps')){ ?>
            <button class="text bubble-go"><i class="iconfont zrz-icon-font-more"></i></button>
            <div class="pos-a transform-out t-c bg-w pjt bor">
                <a class="bubble-go-link" href="<?php echo get_permalink(); ?>">前往</a>
                <?php if($post_status != 'publish' && current_user_can('edit_users')){ ?><button class="text pending-bubble" data-id="<?php echo $post_id; ?>">审核</button><?php } ?>
                <?php if(current_user_can('delete_users')){ ?> <button class="text delete-bubble" data-id="<?php echo $post_id; ?>">删除</button> <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="pd10 b-t entry-content pos-r">
        <div class="entry-content-in">
            <?php
                echo wpautop(convert_smilies(strip_tags(strip_shortcodes($content),'<embed> <video> <a>')));
                $_content = $content;
                $pattern = get_shortcode_regex();
                preg_match_all( '/'. $pattern .'/s', $_content, $matches );
                $html = '';
                if(count($matches[0]) > 0 ){
                    foreach ($matches[0] as $key => $val) {
                        $html .= '<p>'.$val.'</p>';
                    }
                }
                echo do_shortcode($html);
            ?>
            <?php echo zrz_get_pp_content_img(); ?>
            <?php echo zrz_get_bubble_video(); ?>
        </div>
    </div>
    <div class="bubble-content-meta pd10 pos-r clearfix">
        <button class="text bubble-comment-button" data-id="<?php echo $post_id; ?>" data-nonce="<?php echo wp_create_nonce( 'unfiltered-html-comment_' . $post_id ); ?>"><i class="iconfont zrz-icon-font-pinglun1 bubble-comment"></i><?php echo $num_comments; ?></button>
        <button class="text bubble-like-button" data-id="<?php echo $post_id; ?>"><i class="iconfont <?php echo $loved == 1 ? 'zrz-icon-font-love' : 'zrz-icon-font-xihuan'; ?>"></i><b><?php echo $count; ?></b></button>
        <div class="t-c bubble-more-content fr"><button class="bubble-show small">阅读全部</button><button class="bubble-hide small">点击收起</button></div>
    </div>
    <div class="bubble-comment-list" id="commentH<?php echo $post_id; ?>">
        <div class="bubble-list-form" id="form<?php echo $post_id; ?>"></div>
        <ul id="list<?php echo $post_id; ?>" class="b-t mar20-t pd10-t bubble-list hide"></ul>
        <div class="t-c hide"><button class="empty load-more-comments">加载更多</button></div>
        <div class="fs12 mar10-t t-c pd10 gray comment-none hide">没有评论。你怎么看？</div>
    </div>
    <div class="bubble-like-box">
        <div class="pd10 bg-b">
            <button class="text bubble-like-button-ac" data-id="<?php echo $post_id; ?>"><i class="iconfont <?php echo $loved == 1 ? 'zrz-icon-font-love' : 'zrz-icon-font-xihuan'; ?>"></i>喜欢</button>
            <span class="mar10-l fs12"><?php echo '<b class="count">'.$count.'</b><b class="love-text">个人喜欢，'.($loved == 1 ? '其中包括你！^_^</b>' : '你呢？</b>'); ?></span>
        </div>
        <?php

                $i = 0;
                $love = array_reverse($love);
                $html = '<div class="like-list clearfix b-t pd10">';
                foreach ($love as $val) {
                    if($i>=21) break;
                    $html .= '<span class="u'.$val.'"><a href="'.zrz_get_user_page_url($val).'">'.get_avatar($val,25).'</a></span>';
                    $i++;
                }
                echo $html.'</div>';
            
        ?>
    </div>
</div>
