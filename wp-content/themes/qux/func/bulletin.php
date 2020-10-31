<?php 
//添加站内公告
if(_hui('dux_tui_s')){
add_action('init', 'create_redvine_bulletin');
function create_redvine_bulletin(){
    $labels = array(
        'name'                => _x('公告', 'qux'),
        'singular_name'       => _x('网站公告', 'qux'),
        'add_new'             => _x('添加公告', 'qux'),
        'add_new_item'        => __('添加公告', 'qux'),
        'edit_item'           => __('编辑公告', 'qux'),
        'new_item'            => __('新的公告', 'qux'),
        'view_item'           => __('预览公告', 'qux'),
        'search_items'        => __('搜索公告', 'qux'),
        'not_found'           => __('您还没有发布公告', 'qux'),
        'not_found_in_trash'  => __('回收站中没有公告', 'qux'), 
        'parent_item_colon'   => ''
    );
    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_ui'           => true, 
        'query_var'         => true,
        'rewrite'           => array('slug'=> 'bulletin'),
        'has_archive'       => true,
        'capability_type'   => 'post',
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'menu_position'     => 5,
        'menu_icon'         => 'dashicons-megaphone',
        'supports'          => array('title','editor','author'),
        'show_in_nav_menus'	=> true
    ); 
    register_post_type('bulletin',$args);

}

/* Load bulletin template */
function include_bulletin_template_function( $template_path ) {
    if ( get_post_type() == 'bulletin' ) {
        if ( is_single() ) {
            $template_path = UM_DIR.'/template/bulletin.php';
        }elseif(is_archive()){
        	$template_path = UM_DIR.'/template/bulletin-archive.php';
        }else{
        	$template_path = UM_DIR.'/template/bulletin-archive.php';
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'include_bulletin_template_function' );

add_filter('post_type_link', 'custom_book_link', 1, 3);
function custom_book_link( $link, $post = 0 ){
    if ( $post->post_type == 'bulletin' ){
        return home_url( 'bulletin/' . $post->ID .'.html' );
    } else {
        return $link;
    }
}

add_action( 'init', 'custom_book_rewrites_init' );
function custom_book_rewrites_init(){
    add_rewrite_rule('bulletin/([0-9]+)?.html$','index.php?post_type=bulletin&p=$matches[1]','top' );
    add_rewrite_rule('bulletin/([0-9]+)?.html/comment-page-([0-9]{1,})$','index.php?post_type=bulletin&p=$matches[1]&cpage=$matches[2]','top');
}
}
?>