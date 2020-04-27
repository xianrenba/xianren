 <?php
/*
Template Name: 问答社区
*/ 
get_header();
?>
<div class="main">
	<section class="container">
	<div class="content-wrap">
<div class="q-content content">
<?php 

    if ( _hui('forum_login') && !is_user_logged_in() ) { 
    	
    	$html = '<div class="q-content q-panel"><div class="no-login">该板块需要登录后才能查看。</div></div>';
    
    }else{

    global $wp_query, $current_cat, $wpcomqadb;
    
    //$wpcomqadb = new Forum_SQL();
	
    $page = isset($wp_query->query['forum_page']) && $wp_query->query['forum_page'] ? $wp_query->query['forum_page'] : 1;
   
    $per_page = _hui('forum_list_number',20);
	
	$forum_cats = array();
	if(_hui('forum_top_cat')){
		foreach (_hui('forum_top_cat') as $key => $value) {
			if( $value ) $forum_cats[] = $key;
		}
	}
  
    $cat = isset($wp_query->query['forum_cat']) && $wp_query->query['forum_cat'] ? $wp_query->query['forum_cat'] : '';
    if(!$current_cat) $current_cat = $cat ? get_term_by('slug', $cat, 'forum_cat') : null;

    $list = $wpcomqadb->get_questions($per_page, $page, $current_cat ? $current_cat->term_id : 0);

    if($list){
        $users_id = array();
        foreach($list as $p){
            if(!in_array($p->user, $users_id)) $users_id[] = $p->user;
            if(!in_array($p->last_answer, $users_id)) $users_id[] = $p->last_answer;
        }

        $user_array = get_users(array('include'=>$users_id));
        $users = array();
        foreach($user_array as $u){
            $users[$u->ID] = $u;
        }
    }

    $html = '<div class="q-content q-panel"><div class="q-header"><div class="q-header-tab"><a href="'.forum_category_url('').'" class="topic-tab'.($cat==''?' current-tab':'').'">全部</a>';
    if($forum_cats && $forum_cats[0]){
        foreach ($forum_cats as $cid) {
            $c = get_term(trim($cid), 'forum_cat');
            if($c){
                $html .= '<a href="'.forum_category_url($c->slug).'" class="topic-tab'.($cat==$c->slug?' current-tab':'').'">'.$c->name.'</a>';
            }
        }
    }
    $new_page_id = _hui('forum_new_page');
    $new_url = get_permalink($new_page_id);
    $html .= '</div><div class="q-mobile-ask"><a href="'.esc_url($new_url).'"><img src="' . THEME_URI . '/img/edit.png" alt="提问"> 提问</a></div>';
    $html .= '</div><div class="q-topic-wrap"><div class="q-topic-list">';
    if($list){
        foreach ($list as $question) {
            $html .= '<div class="q-topic-item">
                        <a class="user-avatar" href="javascript:;" data-user="'.$question->post_author.'">
                            '.um_get_avatar($question->post_author , '40' , um_get_avatar_type($question->post_author)).'
                        </a>
                        <div class="reply-count">
                            <span class="count-of-replies" title="回复数">'.$question->comment_count.'</span>
                            <span class="count-seperator">/</span>
                            <span class="count-of-visits" title="点击数">'.($question->views?$question->views:0).'</span>
                        </div>
                        <div class="topic-title-wrapper">'. ($question->menu_order==1 ? '<span class="put-top">置顶</span>' : '<span class="topiclist-tab">'.forum_category($question).'</span>')
                            .(get_post_meta($question->ID, 'qux_type',true) == 2 ? '<span class="put-ok">已解决</span>' : '').' <a class="topic-title" href="'.get_permalink($question->ID).'" title="'.esc_attr(get_the_title($question->ID)).'" target="_blank">'.get_the_title($question->ID).'</a>
                            </div>
                        <div class="last-time">';
            if($question->post_mime_type) $html .= '<a class="last-time-user" href="'.get_permalink($question->ID).'#answer" target="_blank">
                                '.um_get_avatar($question->post_mime_type , '40' , um_get_avatar_type($question->post_mime_type)).'
                            </a>';
            $html .= '<span class="last-active-time">'.forum_format_date(get_post_modified_time('U', false, $question->ID)).'</span>
                        </div></div>';
        }
    }else{
        $html .= '<div class="q-topic-item"><p style="padding: 10px;margin: 0;text-align: center;color:#888;">暂无内容</p></div>';
    }
    $html .= '</div>'.forum_pagination($per_page, $page, $current_cat).'</div></div>';
    }
    
    echo $html;
?>
</div>
</div>
<!-- sidebar  -->	
<?php get_sidebar(); ?>	

    </section>
</div>
<?php  get_footer();  ?>