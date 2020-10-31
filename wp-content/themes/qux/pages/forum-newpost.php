<?php 
/*
Template Name: 新建问答
*/

get_header();

if ( !is_user_logged_in() ) {
    $login_url = wp_login_url();
    wp_redirect($login_url);
    exit;
} 
?>
<section class="container">
	<div class="content-wrap qa-hentry">
	<?php

    global $wpcomqadb;
    $current_user =  wp_get_current_user();
    
    $category = '';
    $id = 0;
    $title = '';
    $content = '';

    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $id = isset($_GET['id']) ? $_GET['id'] : 0;

    $is_allowed = 1;
    if($type=='edit'){
        $question = $id ? $wpcomqadb->get_question($id) : '';
        if($question && ( $question->post_author==$current_user->ID || $current_user->has_cap( 'edit_others_posts' ) ) ) { // 问题存在，并比对用户权限
            $title = $question->post_title;
            $category = get_the_terms($question->ID, 'forum_cat');
            $category = $category[0]->term_id;
            $content = $question->post_content;
        }else{
            // 无权限
            $is_allowed = 0;
        }
    }

    if($is_allowed){
        ob_start();
        wp_editor( $content, 'editor-question', forum_editor_settings(array('textarea_name'=>'content', 'height'=>450, 'allow_img'=> 1)) );
		//wp_editor(  wpautop($content), 'editor-question', array('media_buttons'=>true, 'quicktags'=>true, 'textarea_name'=>'content', 'textarea_rows' => 20,'editor_height' => 450,'editor_css'=>'<style>.wp-editor-container{border:1px solid #ddd;}.switch-html, .switch-tmce{height:30px !important}</style>') );
        $editor_contents = ob_get_clean();

        $qa_cats = forum_categorys();

        $html = '<div class="q-content">
            <form action="" method="post" id="question-form">';
    if(isset($id) && $id){
        $html .= '<input type="hidden" name="id" value="'.$id.'">';
    }
    $html .= wp_nonce_field( 'forum_add_question', 'add_question_nonce', true, false );
    $html .= '<div class="q-header q-add-header clearfix">
                    <div class="q-add-title">
                        <div class="q-add-label">标题：</div>
                        <div class="q-add-input"><input type="text" name="title" placeholder="请输入标题" value="'.$title.'"></div>
                    </div>
                    <div class="q-add-cat">
                        <div class="q-add-label">分类：</div>
                        <div class="q-add-input">
                            <select name="category" id="category">
                                <option value="">请选择</option>';
    if($qa_cats){ foreach ($qa_cats as $cat) {
        $html .= '<option value="'.$cat->term_id.'"'.($category==$cat->term_id?' selected':'').'>'.$cat->name.'</option>';
    } }
    $html .= '</select></div></div>
                    <div class="q-add-btn"><input class="btn btn-post" type="submit" value="发布"></div>
                </div>
                <div class="q-add-main">'.$editor_contents.'</div>
            </form>
        </div>';
    }else{
        $html = '<div style="text-align:center;padding: 30px 0;font-sisze: 14px;color:#666;">您无权限访问此页面</div>';
    }
    echo $html;
	?>
	</div>
</section>
<?php get_footer();  