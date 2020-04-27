<?php 
//注册问答社区
add_action( 'init', 'forum_register_category' );
function forum_register_category() {
	
	$forum_slug = _hui('forum_archive_slug','forum');
	$cat_pre = _hui('forum_cat_pre') ? '/'._hui('forum_cat_pre') : '';
	
    $labels = array(
        'name' => '问题',
        'singular_name' => '问题',
        'add_new' => '添加',
        'add_new_item' => '添加',
        'edit_item' => '编辑',
        'new_item' => '添加',
        'view_item' => '查看',
        'search_items' => '查找',
        'not_found' => '没有内容',
        'not_found_in_trash' => '回收站为空',
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'capability_type' => 'post',
		'menu_icon' => 'dashicons-flag',
        'hierarchical' => true,
        'rewrite' => array('slug' => $forum_slug, 'with_front' => 0),
        'show_in_rest' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', 'author', 'comments')
    );
    register_post_type('forum', $args);
	
	register_taxonomy( 'forum_cat', 'forum', array(
			'hierarchical'  => true,
			'labels' => array(
                'add_new_item' => '添加分类',
                'edit_item' => '编辑分类',
                'update_item' => '更新分类'
            ),
			'show_ui'      => true,
			'query_var'    => true,
			'label'       => '问答分类',
			'show_in_rest' => true,
			'rewrite'      => array( 
				'slug'          => $forum_slug.$cat_pre,
				'with_front'    => false,
			),
	) );

    register_taxonomy_for_object_type( 'forum_cat', 'forum' );
}

//载入问答模板
function include_forum_template_function( $template_path ) {
    if ( get_post_type() == 'forum' ) {
        if ( is_single() ) {
            $template_path = UM_DIR.'/template/forum-single.php';
        }elseif(is_archive()){
        	$template_path = UM_DIR.'/template/forum.php';
		}
    }
    return $template_path;
}
add_filter( 'template_include', 'include_forum_template_function' );

//问答模板路由
function custom_forum_link( $link, $post = 0 ){
	$forum_slug = _hui('forum_archive_slug','forum');
	$qa_slug = _hui('forum_link_mode')=='post_name' ? $post->post_name : $post->ID;
	if ( $post->post_type == 'forum' ){
		return home_url( $forum_slug.'/' . $qa_slug .'.html' );
	} else {
		return $link;
	}
}
add_filter('post_type_link', 'custom_forum_link', 1, 3);
function custom_forum_rewrites_init(){
	$forum_slug = _hui('forum_archive_slug','forum');
	if(_hui('forum_link_mode')=='post_name'):
	add_rewrite_rule(
		$forum_slug.'/([一-龥a-zA-Z0-9_-]+)?.html([\s\S]*)?$',
		'index.php?post_type=forum&name=$matches[1]',
		'top' );
	else:
	add_rewrite_rule(
		$forum_slug.'/([0-9]+)?.html([\s\S]*)?$',
		'index.php?post_type=forum&p=$matches[1]',
		'top' );
	endif;
}
add_action( 'init', 'custom_forum_rewrites_init' );

add_filter( 'rewrite_rules_array','forum_rewrite' );
function forum_rewrite( $rules ){
    global $qa_slug, $permalink_structure;
   
    $qa_page_id = _hui('list_page');

    if($qa_slug==''){
        $qa_page = get_post($qa_page_id);
        $qa_slug = isset($qa_page->ID) ? $qa_page->post_name : '';
    }

    $newrules = array();
    if($qa_slug){
        if(!isset($permalink_structure)) $permalink_structure = get_option('permalink_structure');
        $pre = preg_match( '/^\/index\.php\//i', $permalink_structure) ? 'index.php/' : '';

        $newrules[$pre . $qa_slug.'/(\d+)\.html$'] = 'index.php?post_type=forum&p=$matches[1]';
        $newrules[$pre . $qa_slug.'/(\d+)?$'] = 'index.php?page_id='.$qa_page_id.'&forum_page=$matches[1]';
        $newrules[$pre . $qa_slug.'/([^/]+)?$'] = 'index.php?page_id='.$qa_page_id.'&forum_cat=$matches[1]';
        $newrules[$pre . $qa_slug.'/([^/]+)/(\d+)?$'] = 'index.php?page_id='.$qa_page_id.'&forum_cat=$matches[1]&forum_page=$matches[2]';
    }

    return $newrules + $rules;
}

add_filter('query_vars', 'forum_query_vars', 10, 1 );
function forum_query_vars($public_query_vars) {
    $public_query_vars[] = 'forum_page';
    $public_query_vars[] = 'forum_cat';

    return $public_query_vars;
}

add_action( 'wp_ajax_forum_user_questions', 'forum_user_questions' );
add_action( 'wp_ajax_nopriv_forum_user_questions', 'forum_user_questions' );
function forum_user_questions(){
    if( isset($_POST['user']) && is_numeric($_POST['user']) && $user = get_user_by('ID', $_POST['user'] ) ){
        global $wpcomqadb;
        $page = $_POST['page'];
        $page = $page ? $page : 1;
        $all_cats = QAPress_categorys();
        $questions = $wpcomqadb->get_questions_by_user($user->ID, 20, $page);
        if($questions){
            global $post;
            foreach($questions as $post){ ?>
                <div class="q-topic-item">
                    <div class="reply-count">
                        <span class="count-of-replies" title="回复数"><?php echo $post->comment_count;?></span>
                        <span class="count-seperator">/</span>
                        <span class="count-of-visits" title="点击数"><?php echo ($post->views?$post->views:0);?></span>
                    </div>
                    <div class="topic-title-wrapper"><span class="topiclist-tab"><?php echo QAPress_category($post);?></span><a class="topic-title" href="<?php echo get_permalink($post->ID);?>" title="<?php echo esc_attr(get_the_title());?>" target="_blank"><?php the_title();?></a>
                    </div>
                    <div class="last-time">
                        <?php if($post->post_mime_type){ ?><a class="last-time-user" href="<?php echo get_permalink($post->ID);?>#answer" target="_blank"><?php echo um_get_avatar($post->post_mime_type, '60' ,um_get_avatar_type($post->post_mime_type) ,true);?></a> <?php } ?>
                        <span class="last-active-time"><?php echo forum_format_date(get_post_modified_time());?></span>
                    </div>
                </div>
            <?php }
        }else{ echo 0; }
    }
    exit;
}

add_action( 'wp_ajax_forum_user_answers', 'forum_user_answers' );
add_action( 'wp_ajax_nopriv_forum_user_answers', 'forum_user_answers' );
function forum_user_answers(){
    if( isset($_POST['user']) && is_numeric($_POST['user']) && $user = get_user_by('ID', $_POST['user'] ) ){
        global $wpcomqadb;
        $page = $_POST['page'];
        $page = $page ? $page : 1;
        $answers = $wpcomqadb->get_answers_by_user($user->ID, 10, $page);

        if($answers){
            global $post;
            foreach($answers as $answer){ $post = $wpcomqadb->get_question($answer->comment_post_ID);?>
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
            <?php }
        }else{ echo 0; }
    }
    exit;
}

//时间
function forum_format_date($time){
    $t = time() - $time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    if($t<=0){
        return '1秒前';
    }
    foreach ($f as $k=>$v){
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}


function forum_category( $post ){
    $cats = get_the_terms($post->ID, 'forum_cat');

    if($cats){
        return $cats[0]->name;
    }
}

function forum_categorys(){
    // WP 4.5+
    $terms = get_terms( array(
            'taxonomy' => 'forum_cat',
            'hide_empty' => false
        )
    );

    return $terms;
}

add_filter( 'wp_title_parts', 'forum_title_parts', 5 );
function forum_title_parts( $parts ){
    global $qa_options, $current_cat;
    if(!isset($qa_options)) $qa_options = get_option('qa_options');
    if(is_page($qa_options['list_page'])){
        global $wp_query, $post, $wpcomqadb;
        if( is_singular('forum_post') ){
            $parts[] = $post->post_title;
        }else if(isset($wp_query->query['forum_cat']) && $wp_query->query['forum_cat']){
            if(!$current_cat) $current_cat = get_term_by('slug', $wp_query->query['forum_cat'], 'forum_cat');
            $parts[] = $current_cat ? $current_cat->name : '';
        }

        if(isset($wp_query->query['forum_page']) && $wp_query->query['forum_page']){
            array_unshift($parts, '第'.$wp_query->query['forum_page'].'页');
        }
    }
    return $parts;
}

// 后台按时间排序
add_action('pre_get_posts', 'forum_admin_order');
function forum_admin_order( $q ) {
    if(is_admin() && function_exists('get_current_screen')){
        $s = get_current_screen();
        if ( isset($s->base) && $s->base === 'edit' && isset($s->post_type) && $s->post_type === 'forum' && $q->is_main_query() ) {
            if( !isset($_GET[ 'orderby' ]) ) {
                $q->set('orderby', 'date');
                $q->set('order', 'desc');
            }
        }
    }
}

// 用于head结束后将wp_query设置为问答页面，主要用于面包屑导航、边栏等的获取与问答列表页面一致
add_action( 'wp_head', 'forum_single_use_page_tpl', 99999 );
function forum_single_use_page_tpl(){
    global $post, $wp_query;
    if(!isset($qa_options)) $qa_options = get_option('qa_options');
    if( $wp_query->is_main_query() && is_singular('qa_post') ) {
        $post = get_post(_hui('list_page'));
        $wp_query->is_page = 1;
        $wp_query->is_single = 0;
        $wp_query->query['qa_id'] = $wp_query->queried_object_id;
        $wp_query->queried_object_id = _hui('list_page');
        $wp_query->queried_object = $post;
        $wp_query->posts[0] = $post;
    }
}


//分页
function forum_pagination($per_page=20, $page=1, $cat=null){
    global $wpcomqadb;
    $total_q = $wpcomqadb->get_questions_total($cat?$cat->term_id:0);
    $numpages = ceil($total_q/$per_page);
    $range = 9;

    if($numpages>1){
        $cat_slug = $cat ? $cat->slug : '';

        $html = '<div class="q-pagination clearfix">';
            $prev = $page - 1;
            if ( $prev > 0 ) {
                $html .= '<a class="prev" href="'.forum_category_url($cat_slug, $prev).'">' . __('上一页', 'qux') . '</a>';
            }

            if($numpages > $range){
                if($page < $range){
                    for($i = 1; $i <= ($range + 1); $i++){
                        if($i==$page){
                            $html .= '<a class="current" href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                        } else {
                            $html .= '<a href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                        }
                    }
                } elseif($page >= ($numpages - ceil(($range/2)))){
                    for($i = $numpages - $range; $i <= $numpages; $i++){
                        if($i==$page){
                            $html .= '<a class="current" href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                        } else {
                            $html .= '<a href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                        }
                    }
                } elseif($page >= $range && $page < ($numpages - ceil(($range/2)))){
                    for($i = ($page - ceil($range/2)); $i <= ($page + ceil(($range/2))); $i++){
                        if($i==$page){
                            $html .= '<a class="current" href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                        } else {
                            $html .= '<a href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                        }
                    }
                }
            }else{
                for ( $i = 1; $i <= $numpages; $i++ ) {
                    if($i==$page){
                        $html .= '<a class="current" href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                    } else {
                        $html .= '<a href="'.forum_category_url($cat_slug, $i).'">' . $i . "</a>";
                    }
                }
            }

            $next = $page + 1;
            if ( $next <= $numpages ) {
                $html .= '<a class="next" href="'.forum_category_url($cat_slug, $next).'">' . __('下一页', 'qux') . '</a>';
            }
            $html .= '</div>';
        return $html;
    }
}

function forum_category_url($cat, $page=1){
    global $permalink_structure, $wp_rewrite;
    if(!isset($permalink_structure)) $permalink_structure = get_option('permalink_structure');
    
    $qa_page_id = _hui('list_page');//$qa_options['list_page'];

    $page_url = get_permalink($qa_page_id);

    if($permalink_structure){
        $url = trailingslashit($page_url).$cat;
        if($page>1){
            $url = trailingslashit($url).$page;
        }
    }else{
        $url =  $cat ? add_query_arg('forum_cat', $cat, $page_url) : $page_url;
        if($page>1){
            $url = add_query_arg('forum_page', $page, $url);
        }
    }

    if ( $wp_rewrite->use_trailing_slashes )
        $url = trailingslashit($url);
    else
        $url = untrailingslashit($url);

    return $url;
}

function forum_edit_url( $qid ){

    $new_page_id = _hui('forum_new_page');

    $edit_url = get_permalink($new_page_id);

    $edit_url =  add_query_arg('type', 'edit', $edit_url);
    $edit_url =  add_query_arg('id', $qid, $edit_url);

    return $edit_url;
}

function forum_editor_settings($args = array()){
    return array(
        'textarea_name' => $args['textarea_name'],
        'media_buttons' => false,
        'quicktags' => false,
        'tinymce' => array(
            'statusbar' => false,
            'height'        => isset($args['height']) ? $args['height'] : 120,
            'toolbar1' => 'bold,italic,underline,blockquote,bullist,numlist,QAImg',
            'toolbar2' => '',
            'toolbar3' => ''
        )
    );
}

add_filter( 'mce_external_plugins', '_mce_plugin');
function _mce_plugin($plugin_array){
    $plugin_array['QAImg'] = THEME_URI . '/js/libs/QAImg.min.js';
    return $plugin_array;
}

add_action( 'pre_get_comments', 'forum_pre_get_comments', 10 );
function forum_pre_get_comments( $q ) {
    if( !(is_admin() && ! wp_doing_ajax()) && !$q->query_vars['type'] && !$q->query_vars['parent'] ){
        $q->query_vars['type__not_in'] = array('answer', 'forum_comment');
    }
    return $q;
}

add_filter('the_comments', 'forum_admin_comments' );
function forum_admin_comments($comments){
    global $pagenow;
    if( is_admin() && $pagenow=='index.php' ){
        if($comments){
            foreach ($comments as $k => $comment) {
                if( $comment->comment_type=='answer' || $comment->comment_type=='forum_comment' ){
                    $comments[$k]->comment_type = '';
                }
            }
        }
    }
    return $comments;
}
// 2.0 数据迁移
add_action( 'admin_menu', 'forum_post_2_0' );
function forum_post_2_0(){
    global $wpdb, $QAPress;
    $table_q = $wpdb->prefix.'qux_questions';
    $table_a = $wpdb->prefix.'qux_answers';
    $table_c = $wpdb->prefix.'qux_comments';

    if( $wpdb->get_var("SHOW TABLES LIKE '$table_q'") != $table_q ) return false;

    $sql = "SELECT * FROM `$table_q` WHERE `flag` > -1 OR `flag` is null";
    $questions = $wpdb->get_results($sql);

    if($questions){
        foreach ($questions as $question) {
            $post = array(
                'post_author' => $question->user,
                'post_date' => $question->date,
                'post_modified' => $question->modified,
                'post_content' => $question->content,
                'post_title' => $question->title,
                'menu_order' => $question->flag ? $question->flag : 0,
                'comment_count' => $question->answers,
                'post_mime_type' => $question->last_answer,
                'post_status' => 'publish',
                'post_type' => 'forum',
                'comment_status' => 'open',
            );
            // 插入文章
            $pid = wp_insert_post($post);
            // 插入文章信息
            if($pid){
                update_post_meta($pid, 'views', $question->views);
                wp_set_object_terms( $pid, array( (int)$question->category ), 'forum_cat' );

                // 插入回答信息
                $answers = $wpdb->get_results("SELECT * FROM `$table_a` WHERE `question` = '$question->ID'");
                if($answers){
                    foreach ($answers as $answer) {
                        $user = get_user_by('ID', $answer->user);
                        $data = array(
                            'comment_post_ID' => $pid,
                            'comment_content' => $answer->content,
                            'comment_type' => 'answer',
                            'comment_parent' => 0,
                            'user_id' => $answer->user,
                            'comment_author_email' => $user->user_email,
                            'comment_author' => $user->display_name,
                            'comment_date' => $answer->date,
                            'comment_approved' => 1,
                            'comment_karma' => $answer->comments
                        );

                        $answer_id = wp_insert_comment($data);

                        // 插入评论信息
                        if($answer_id){
                            $comments = $wpdb->get_results("SELECT * FROM `$table_c` WHERE `answer` = '$answer->ID'");
                            if($comments){
                                foreach ($comments as $comment) {
                                    $cuser = get_user_by('ID', $comment->user);
                                    $data = array(
                                        'comment_post_ID' => $pid,
                                        'comment_content' => $comment->content,
                                        'comment_type' => 'forum_comment',
                                        'comment_parent' => $answer_id,
                                        'user_id' => $comment->user,
                                        'comment_author_email' => $cuser->user_email,
                                        'comment_author' => $cuser->display_name,
                                        'comment_date' => $comment->date,
                                        'comment_approved' => 1
                                    );

                                    wp_insert_comment($data);
                                }
                            }
                        }
                    }
                }
                $wpdb->update($table_q, array('flag' => -($pid)), array('ID' => $question->ID));
            }
        }
    }
}

// 2.3 评论字段修改
add_action( 'admin_menu', 'forum_comment_2_3' );
function forum_comment_2_3(){
    global $wpdb;
    if(get_option('_forum_2_3')) return false;

    $table_c = $wpdb->prefix.'comments';
    $sql = "SELECT * FROM `$table_c` WHERE `comment_type`='comment' AND `comment_parent`>0 AND `comment_approved`=1";
    $comments = $wpdb->get_results($sql);
    if($comments){
        foreach ($comments as $comment) {
            if($comment->comment_post_ID && $post = get_post($comment->comment_post_ID)){
                if($post->post_type=='forum'){
                    $wpdb->update($table_c, array('comment_type' => 'forum_comment'), array('comment_ID' => $comment->comment_ID));
                }
            }
        }
        update_option('_forum_2_3', '1');
    }
}