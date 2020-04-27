<?php

// require settings
define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/settings/' );
require_once THEME_DIR . '/settings/options-framework.php';

require_once THEME_DIR . '/settings/update.php'; 

add_action( 'optionsframework_custom_scripts', 'optionsframework_custom_scripts' );

function optionsframework_custom_scripts() { ?>
	<script type="text/javascript">
	jQuery(document).ready(function() {

		jQuery('#example_showhidden').click(function() {
	  		jQuery('#section-example_text_hidden').fadeToggle(400);
		});

		if (jQuery('#example_showhidden:checked').val() !== undefined) {
			jQuery('#section-example_text_hidden').show();
		}
		

	});
	</script>

	<?php
}

// MD5 FILENAME
if ( _hui('newfilename') && !function_exists('_new_filename') ) :

    function _new_filename($filename) {
        $info = pathinfo($filename);
        $ext = empty($info['extension']) ? '' : '.' . $info['extension'];
        $name = basename($filename, $ext);
        return substr(md5($name), 0, 15) . $ext;
    }
    add_filter('sanitize_file_name', '_new_filename', 10);

endif;

// editor style
add_editor_style( get_locale_stylesheet_uri() . '/css/editor-style.css' );

// 后台Ctrl+Enter提交评论回复
add_action('admin_footer', '_admin_comment_ctrlenter');
function _admin_comment_ctrlenter() {
	echo '<script type="text/javascript">
        jQuery(document).ready(function($){
            $("textarea").keypress(function(e){
                if(e.ctrlKey&&e.which==13||e.which==10){
                    $("#replybtn").click();
                }
            });
        });
    </script>';
};


function _add_editor_buttons($buttons) {
    $buttons[] = 'fontselect';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'cleanup';
    $buttons[] = 'styleselect';
    $buttons[] = 'del';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'copy';
    $buttons[] = 'paste';
    $buttons[] = 'cut';
    $buttons[] = 'image';
    $buttons[] = 'anchor';
    $buttons[] = 'backcolor';
    $buttons[] = 'wp_page';
    $buttons[] = 'charmap';
    return $buttons;
}
add_filter("mce_buttons_2", "_add_editor_buttons");


/* 
 * delete google fonts
 * ====================================================
*/
// Remove Open Sans that WP adds from frontend
if (!function_exists('remove_wp_open_sans')) :
    function remove_wp_open_sans() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
    }
    add_action('wp_enqueue_scripts', 'remove_wp_open_sans');
 
    // Uncomment below to remove from admin
    // add_action('admin_enqueue_scripts', 'remove_wp_open_sans');
endif;

function remove_open_sans() {    
    wp_deregister_style( 'open-sans' );    
    wp_register_style( 'open-sans', false );    
    wp_enqueue_style('open-sans','');    
}    
add_action( 'init', 'remove_open_sans' );



/* 
 * post meta from
 * ====================================================
*/
$postmeta_from = array(
    array(
        "name" => "fromname_value",
        "std" => "",
        "title" => __('来源名', 'haoui').'：'
    ),
    array(
        "name" => "fromurl_value",
        "std" => "",
        "title" => __('来源网址', 'haoui').'：'
    )
);

if( _hui('post_from_s') ){
    add_action('admin_menu', '_postmeta_from_create');
    add_action('save_post', '_postmeta_from_save');
}

function _postmeta_from() {
    global $post, $postmeta_from;
    foreach($postmeta_from as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.$meta_box['title'].'</p>';

        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';

    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function _postmeta_from_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_from_boxes', __('来源', 'haoui'), '_postmeta_from', 'post', 'normal', 'high' );
    }
}

function _postmeta_from_save( $post_id ) {
    global $postmeta_from;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename']) ? $_POST['post_newmetaboxes_noncename'] : '', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_from as $meta_box) {
        $data = $_POST[$meta_box['name']];
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}




/* 
 * post meta keywords
 * ====================================================
*/
$postmeta_keywords_description = array(
    array(
        "name" => "title",
        "std" => "",
        "title" => __('标题', 'haoui').'：'
    ),
    array(
        "name" => "keywords",
        "std" => "",
        "title" => __('关键字', 'haoui').'：'
    ),
    array(
        "name" => "description",
        "std" => "",
        "title" => __('描述', 'haoui').'：'
    )
);

if( _hui('post_keywords_description_s') ){
    add_action('admin_menu', '_postmeta_keywords_description_create');
    add_action('save_post', '_postmeta_keywords_description_save');
}

function _postmeta_keywords_description() {
    global $post, $postmeta_keywords_description;
    foreach($postmeta_keywords_description as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.$meta_box['title'].'</p>';
        if( $meta_box['name'] == 'description' ){
            echo '<p><textarea style="width:98%" name="'.$meta_box['name'].'">'.$meta_box_value.'</textarea></p>';
        }else{
            echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
        }
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function _postmeta_keywords_description_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO设置', 'haoui'), '_postmeta_keywords_description', 'post', 'normal', 'high' );
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO设置', 'haoui'), '_postmeta_keywords_description', 'page', 'normal', 'high' );
        add_meta_box( 'postmeta_keywords_description_boxes', __('SEO设置', 'haoui'), '_postmeta_keywords_description', 'store', 'normal', 'high' );
    }
}

function _postmeta_keywords_description_save( $post_id ) {
    global $postmeta_keywords_description;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_keywords_description as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}


/* 
 * post meta link
 * ====================================================
*/
$postmeta_link = array(
    array(
        "name" => "link",
        "std" => ""/*,
        "title" => __('直达链接', 'haoui').'：'*/
    )
);

if( _hui('post_link_excerpt_s') || _hui('post_link_single_s') ){
    add_action('admin_menu', '_postmeta_link_create');
    add_action('save_post', '_postmeta_link_save');
}

function _postmeta_link() {
    global $post, $postmeta_link;
    foreach($postmeta_link as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.$meta_box['title'].'</p>';
        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function _postmeta_link_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_link_boxes', __('直达链接', 'haoui'), '_postmeta_link', 'post', 'normal', 'high' );
    }
}

function _postmeta_link_save( $post_id ) {
    global $postmeta_link;
   
    if ( !wp_verify_nonce( $_POST['post_newmetaboxes_noncename'], plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_link as $meta_box) {
        $data = $_POST[$meta_box['name']];
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}



add_action('admin_init', '_init');
function _init() {
    $z_taxonomies = get_taxonomies();
    if (is_array($z_taxonomies)) {

        foreach ($z_taxonomies as $z_taxonomy) {
        	add_action($z_taxonomy.'_add_form_fields', '_add_tax_field');
        	add_action($z_taxonomy.'_edit_form_fields', '_edit_texonomy_field');
            add_action($z_taxonomy.'_edit_form_fields', '_edit_tax_field');
        }
    }
}

function _edit_texonomy_field( $tag ){

    $widthfull = get_term_meta( $tag->term_id, '_widthfull', true );
    ?>

    <tr class='form-field'>
        <th scope='row'><label for='cat_page_title'><?php _e('布局方式', 'qux'); ?></label></th>
        <td>
                
            <input class="checkbox" type="checkbox" name='widthfull' id='widthfull' <?php if (!empty($widthfull)) { echo "checked"; }?> />
            <label for="widthfull">全宽分类</label>
            
        </td>
    </tr> <?php
}


add_action('edit_term','_save_taxonomy_image');
add_action('create_term','_save_taxonomy_image');
function _save_taxonomy_image($term_id) {
	
    if ( isset( $_POST['widthfull'] )  ) {
        update_term_meta( $term_id, '_widthfull', $_POST['widthfull'] );
    } else {
        update_term_meta( $term_id, '_widthfull', null );
    }
}

function _add_tax_field(){
    echo '
        <div class="form-field">
            <label for="term_meta[style]">展示样式</label>
            <select name="term_meta[style]" id="term_meta[style]" class="postform">
                <option value="default">列表模式</option>
                <option value="card">卡片模式</option>
            </select>
            <p class="description">选择后前台展示样式将有所不同</p>
        </div>
        <div class="form-field">
            <label for="term_meta[title]">SEO 标题</label>
            <input type="text" name="term_meta[title]" id="term_meta[title]" />
        </div>
        <div class="form-field">
            <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
            <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" />
        </div>
        <div class="form-field">
            <label for="term_meta[keywords]">SEO 描述（description）</label>
            <textarea name="term_meta[description]" id="term_meta[description]" rows="4" cols="40"></textarea>
        </div>
    ';
}
	
function _edit_tax_field( $term ){

    $term_id = $term->term_id;
    $term_meta = get_option( "_taxonomy_meta_$term_id" );

    $meta_style = isset($term_meta['style']) ? $term_meta['style'] : '';

    $meta_title = isset($term_meta['title']) ? $term_meta['title'] : '';
    $meta_keywords = isset($term_meta['keywords']) ? $term_meta['keywords'] : '';
    $meta_description = isset($term_meta['description']) ? $term_meta['description'] : '';
        
    echo '
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[style]">展示样式</label>
                <td>
                    <select name="term_meta[style]" id="term_meta[style]" class="postform">
                        <option value="default" '. ('default'==$meta_style?'selected="selected"':'') .'>列表模式</option>
                        <option value="card" '. ('card'==$meta_style?'selected="selected"':'') .'>卡片模式</option>
                    </select>
                    <p class="description">选择后前台展示样式将有所不同</p>
                </td>
            </th>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[title]">SEO 标题</label>
                <td>
                    <input type="text" name="term_meta[title]" id="term_meta[title]" value="'. $meta_title .'" />
                </td>
            </th>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
                <td>
                    <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" value="'. $meta_keywords .'" />
                </td>
            </th>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[description]">SEO 描述（description）</label>
                <td>
                    <textarea name="term_meta[description]" id="term_meta[description]" rows="4">'. $meta_description .'</textarea>
                </td>
            </th>
        </tr>
    ';
}

add_action( 'edit_term', '_save_tax_meta' , 10, 2 );
add_action( 'create_term', '_save_tax_meta' , 10, 2 ); 
function _save_tax_meta( $term_id ){
 
    if ( isset( $_POST['term_meta'] ) ) {
            
        $term_meta = array();

        $term_meta['style'] = isset ( $_POST['term_meta']['style'] ) ? esc_sql( $_POST['term_meta']['style'] ) : '';
        $term_meta['title'] = isset ( $_POST['term_meta']['title'] ) ? esc_sql( $_POST['term_meta']['title'] ) : '';
        $term_meta['keywords'] = isset ( $_POST['term_meta']['keywords'] ) ? esc_sql( $_POST['term_meta']['keywords'] ) : '';
        $term_meta['description'] = isset ( $_POST['term_meta']['description'] ) ? esc_sql( $_POST['term_meta']['description'] ) : '';

        update_option( "_taxonomy_meta_$term_id", $term_meta );
 
    }
}


$postmeta_subtitle = array(
    array(
        "name" => "subtitle",
        "std" => ""
    )
);

add_action('admin_menu', 'hui_postmeta_subtitle_create');
add_action('save_post', 'hui_postmeta_subtitle_save');


function hui_postmeta_subtitle() {
    global $post, $postmeta_subtitle;
    foreach($postmeta_subtitle as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.(isset($meta_box['title']) ? $meta_box['title'] : '').'</p>';
        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_subtitle_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_subtitle_boxes', __('副标题', 'haoui'), 'hui_postmeta_subtitle', 'post', 'normal', 'high' );
    }
}

function hui_postmeta_subtitle_save( $post_id ) {
    global $postmeta_subtitle;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_subtitle as $meta_box) {
        $data = $_POST[$meta_box['name']];
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}

$postmeta_thumblink = array(
    array(
        "name" => "thumblink",
        "std" => ""
    )
);

if( _hui('thumblink_s') ){
    add_action('admin_menu', 'hui_postmeta_thumblink_create');
    add_action('save_post', 'hui_postmeta_thumblink_save');
}


function hui_postmeta_thumblink() {
    global $post, $postmeta_thumblink;
    foreach($postmeta_thumblink as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo'<p>'.(isset($meta_box['title']) ? $meta_box['title'] : '').'</p>';
        echo '<p><input type="text" style="width:98%" value="'.$meta_box_value.'" name="'.$meta_box['name'].'"></p>';
    }
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_thumblink_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_thumblink_boxes', __('外链缩略图地址', 'haoui'), 'hui_postmeta_thumblink', 'post', 'normal', 'high' );
    }
}

function hui_postmeta_thumblink_save( $post_id ) {
    global $postmeta_thumblink;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_thumblink as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}

$postmeta_xzh = array(
    array(
        "title" => "原创文章",
        "name" => "is_original",
        "std" => ""
    )
);

if( _hui('xzh_on') ){
    add_action('admin_menu', 'hui_postmeta_xzh_create');
    add_action('save_post', 'hui_postmeta_xzh_save');
}

function hui_postmeta_xzh() {
    global $post, $postmeta_xzh;
    foreach($postmeta_xzh as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
        if($meta_box_value == "")
            $meta_box_value = $meta_box['std'];
        echo '<p><label><input '.($meta_box_value?'checked':'').' type="checkbox" value="1" name="'.$meta_box['name'].'"> '.(isset($meta_box['title']) ? $meta_box['title'] : '').'</label></p>';
    }
    $tui = get_post_meta($post->ID, 'xzh_tui_back', true);
    if( $tui ) echo '<p>实时推送结果：'.$tui.'</p>';
   
    echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function hui_postmeta_xzh_create() {
    global $theme_name;
    if ( function_exists('add_meta_box') ) {
        add_meta_box( 'postmeta_xzh_boxes', __('百度熊掌号设置', 'haoui'), 'hui_postmeta_xzh', 'post', 'normal', 'high' );
    }
}

function hui_postmeta_xzh_save( $post_id ) {
    global $postmeta_xzh;
   
    if ( !wp_verify_nonce( isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'', plugin_basename(__FILE__) ))
        return;
   
    if ( !current_user_can( 'edit_posts', $post_id ))
        return;
                   
    foreach($postmeta_xzh as $meta_box) {
        $data = isset($_POST[$meta_box['name']]) ? $_POST[$meta_box['name']] : '';
        if(get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}

add_action('admin_init','custom_meta_init');
function custom_meta_init(){

    wp_enqueue_style('custom_meta_css', THEME_URI . '/css/shortcodes.css');

    foreach (array('post','page','store') as $type){
        add_meta_box('shortcode_meta', __('短代码','um'), 'custom_meta_setup', $type, 'side', 'low');
    }

    // add a callback function to save any data a user enters in
    add_action('save_post','custom_meta_save');
}

function custom_meta_setup(){
    global $post;

    $meta = get_post_meta($post->ID,'_custom_meta',TRUE);

    // instead of writing HTML here, lets do an include
    include(THEME_DIR . '/func/meta-shortcode.php');

    // create a custom nonce for submit verification later
    echo '<input type="hidden" name="custom_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function custom_meta_save($post_id){
    // authentication checks

    if (!wp_verify_nonce($_POST['custom_meta_noncename'],__FILE__)) return $post_id;

    // check user permissions
    if ($_POST['post_type'] == 'page'){
        if (!current_user_can('edit_page', $post_id)) return $post_id;
    }else{
        if (!current_user_can('edit_post', $post_id)) return $post_id;
    }


    $current_data = get_post_meta($post_id, '_custom_meta', TRUE);

    $new_data = $_POST['_custom_meta'];

    custom_meta_clean($new_data);

    if ($current_data)
    {
        if (is_null($new_data)) delete_post_meta($post_id,'_custom_meta');
        else update_post_meta($post_id,'_custom_meta',$new_data);
    }
    elseif (!is_null($new_data))
    {
        add_post_meta($post_id,'_custom_meta',$new_data,TRUE);
    }

    return $post_id;
}

function custom_meta_clean(&$arr){
    if (is_array($arr)){
        foreach ($arr as $i => $v){
            if (is_array($arr[$i])){
                custom_meta_clean($arr[$i]);

                if (!count($arr[$i])){
                    unset($arr[$i]);
                }
            }else{
                if (trim($arr[$i]) == ''){
                    unset($arr[$i]);
                }
            }
        }

        if (!count($arr)){
            $arr = NULL;
        }
    }
}