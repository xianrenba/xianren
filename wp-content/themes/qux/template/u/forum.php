<?php

global $current_user;
$all_cats = forum_categorys();
//问题页码
$wpcomqadb = new Forum_SQL();
$questions = $wpcomqadb->get_questions_by_user($curauth->ID, 20, 1);
$q_total = $wpcomqadb->get_questions_total_by_user($curauth->ID);
$q_numpages = ceil($q_total/20);
//评论页码
$answers = $wpcomqadb->get_answers_by_user($curauth->ID, 10, 1);
$a_total = $wpcomqadb->get_answers_total_by_user($curauth->ID);
$a_numpages = ceil($a_total/10);
//自己？
$is_user = isset($current_user) && isset($current_user->ID) && $current_user->ID == $curauth->ID;

if($questions){
    $users_id = array();
    foreach($questions as $p){
        if(!in_array($p->user, $users_id)) $users_id[] = $p->user;
        if(!in_array($p->last_answer, $users_id)) $users_id[] = $p->last_answer;
    }

    $user_array = get_users(array('include'=>$users_id));
    $users = array();
    foreach($user_array as $u){
        $users[$u->ID] = $u;
    }
}

?>

    <div class="page-wrapper forum-profile">
        <div class="dashboard-main">
            <div class="page-header">
				<h3 id="info">问答社区</h3>
			</div>
            <div class="dashboard-wrapper select-posts">
    <div class="forum-tab" data-user="<?php echo $curauth->ID;?>">
        <div class="forum-tab-item active">问题</div>
        <div class="forum-tab-item">回答</div>
    </div>
    <div class="forum-content q-content active">
        <?php
        if($questions){
            global $post;
            foreach ($questions as $post) { ?>
                <div class="q-topic-item">
                    <div class="reply-count">
                        <span class="count-of-replies" title="回复数"><?php echo $post->comment_count;?></span>
                        <span class="count-seperator">/</span>
                        <span class="count-of-visits" title="点击数"><?php echo ($post->views?$post->views:0);?></span>
                    </div>
                    <div class="topic-title-wrapper"><span class="topiclist-tab"><?php echo forum_category($post);?></span><a class="topic-title" href="<?php echo get_permalink($post->ID);?>" title="<?php echo esc_attr(get_the_title($post->ID));?>" target="_blank"><?php the_title()?></a>
                    </div>
                    <div class="last-time">
                        <?php if($post->post_mime_type){ ?><a class="last-time-user" href="<?php echo get_permalink($post->ID);?>#answer" target="_blank"><?php echo um_get_avatar( $post->post_mime_type, '60' , um_get_avatar_type($post->post_mime_type));?></a> <?php } ?>
                        <span class="last-active-time"><?php echo forum_format_date(get_post_modified_time());?></span>
                    </div>
                </div>
            <?php } if($q_numpages>1) { ?>
                <div class="load-more-wrap"><a href="javascript:;" class="load-more j-user-questions">点击查看更多</a></div>
            <?php }
        }else{ ?>
            <div class="profile-no-content"><?php echo ($is_user?'你':'该用户');?>还没有发布过问题。</div>
        <?php } ?>
    </div>
    <div class="forum-content profile-comments-list">
    <?php if($answers){ global $post;?>
        <?php foreach($answers as $answer){ $post = $wpcomqadb->get_question($answer->comment_post_ID);?>
            <div class="comment-item">
                <div class="comment-item-link">
                    <a target="_blank" href="<?php echo esc_url(get_permalink($post->ID));?>#answer">
                        <i class="fa fa-comments"></i> <?php $excerpt = wp_trim_words( $answer->comment_content, 100, '...' ); echo $excerpt ? $excerpt : '（过滤内容）' ?>
                    </a>
                </div>
                <div class="comment-item-meta">
                    <span><?php echo forum_format_date(strtotime($answer->comment_date));?> 回答 <a target="_blank" href="<?php echo get_permalink($post->ID);?>"><?php echo get_the_title($post->ID);?></a></span>
                </div>
            </div>
        <?php } if($a_numpages>1) { ?>
            <div class="load-more-wrap"><a href="javascript:;" class="load-more j-user-answers">点击查看更多</a></div>
        <?php } }else{ ?>
            <div class="profile-no-content"><?php echo ($is_user?'你':'该用户');?>还没有回答过问题。</div>
        <?php } ?>
    </div>
            </div>
        </div>
    </div>