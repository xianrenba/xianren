<?php

function zrz_seo_quota_encode($value) {
	$value = str_replace('"','&#34;',$value);
	$value = str_replace("'",'&#39;',$value);
	return $value;
}

function zrz_seo_get_post_meta($post_id,$key) {
	$value = get_post_meta($post_id,$key,true);
	$value = stripslashes($value);
	return $value;
}
function zrz_seo_get_term_meta($term_id,$key) {
	$value = get_term_meta($term_id,$key,true);
	$value = stripslashes($value);
	return $value;
}

// 将关键词和描述输出在wp_head区域
add_action('wp_head','zrz_seo_head_meta',10);
function zrz_seo_head_meta(){
    echo '<meta property="og:locale" content="zh_CN" />'."\n";
    echo zrz_seo_head_meta_keywords()."\n";
	echo zrz_get_html_code(zrz_get_theme_settings('meta'));
}

add_action('wp_footer','zrz_seo_footer_meta',10);
function zrz_seo_footer_meta(){
	echo zrz_get_html_code(zrz_get_theme_settings('statistics'));
}

//seo 标题
add_filter("document_title_parts", "zrz_seo_document_title");
function zrz_seo_document_title($title){
    global $post;
    if(is_singular()){
        $custom_title = zrz_seo_get_post_meta($post->ID, 'zrz_seo_title');
        if($custom_title){ $title["title"] = $custom_title; }
    }
	if(is_tax() || is_category()){
		$cat = get_queried_object();
		$cat = $cat->term_id;
		$custom_title = zrz_seo_get_term_meta($cat,'seo_title');
        if($custom_title){ $title["title"] = $custom_title; }
	}
    return $title;
}

// 网页关键字与og标签
function zrz_seo_head_meta_keywords(){
    if(is_paged())
    {
        return;
    }

    $keywords = '';
    $og = '';
    $title = esc_html(get_bloginfo('name').' &#8211; '.get_bloginfo( 'description', 'display' ));
	$logo = zrz_get_theme_settings('logo');
    if(is_home() || is_front_page()){
        $keywords = zrz_get_theme_settings('keywords');
        $og = '
<meta property="og:site_name" content="'.$title.'" />
<meta property="og:type" content="website" />
<meta property="og:title" content="'.$title.'" />
<meta property="og:url" content="'.home_url().'" />
<meta property="og:image" content="'.$logo.'" />';
    }elseif(is_category() || zrz_is_custom_tax('collection')){
	    $cat = get_queried_object()->term_id;
	    $cat_name = single_cat_title('',false);
	    $keywords = zrz_seo_get_term_meta($cat,'seo_keywords');
        $keywords = $keywords ? $keywords : $cat_name;
        $og = '
<meta property="og:type" content="website" />
<meta property="og:site_name" content="'.$title.'" />
<meta property="og:title" content="'.$cat_name.'" />
<meta property="og:url" content="'.get_category_link( $cat ).'" />
<meta property="og:image" content="'.$logo.'" />';
    }elseif(is_tag()){
        global $wp_query;
        $tag_name = single_cat_title('',false);
        $tag_id = $wp_query->queried_object->term_id;
        $keywords = zrz_seo_get_term_meta($tag_id,'seo_keywords');
        $keywords = $keywords ? $keywords : $tag_name;
        $og = '
<meta property="og:type" content="website" />
<meta property="og:site_name" content="'.$title.'" />
<meta property="og:title" content="'.$tag_name.'" />
<meta property="og:url" content="'.get_tag_link( $tag_id ).'" />
<meta property="og:image" content="'.$logo.'" />';
    }elseif(is_single()){
        global $post;
	    $post_id = $post->ID;
        $post_cats = strip_tags(get_the_category_list( ',', 'multiple', $post_id ));
        $post_tags = strip_tags(get_the_tag_list('',',',''));
        $post_meta = zrz_seo_get_post_meta($post_id, 'zrz_seo_keywords');
        $keywords = $post_meta ? $post_meta : $post_tags.($post_tags ? ',' : '').$post_cats;
        $author_id = get_post_field( 'post_author', $post_id );
        $thumb = get_the_post_thumbnail_url($post_id) ? get_the_post_thumbnail_url($post_id) : zrz_get_first_img(get_post_field('post_content', $post_id));
        $og = '
<meta property="og:site_name" content="'.$title.'" />
<meta property="og:type" content="article" />
<meta property="og:url" content="'.get_permalink($post_id).'" />
<meta property="og:title" content="'.get_the_title($post_id).'" />
<meta property="og:updated_time" content="'.get_the_modified_date('c',$post_id).'" />
<meta property="og:image" content="'.$thumb.'" />
<meta property="article:published_time" content="'.get_the_time('c',$post_id).'" />
<meta property="article:modified_time" content="'.get_the_modified_date('c',$post_id).'" />
<meta property="article:author" content="'.zrz_get_user_page_url($author_id).'" />
<meta property="article:publisher" content="'.home_url().'" />';
    }elseif(is_singular()){
        global $post;
        $post_id = $post->ID;
        $keywords = zrz_seo_get_post_meta($post->ID, 'zrz_seo_keywords');
        $author_id = get_post_field( 'post_author', $post_id );
        $og = '
<meta property="og:site_name" content="'.$title.'" />
<meta property="og:type" content="article" />
<meta property="og:url" content="'.get_permalink($post_id).'" />
<meta property="og:title" content="'.get_the_title($post_id).'" />
<meta property="og:updated_time" content="'.get_the_modified_date('c',$post_id).'" />
<meta property="og:image" content="'.(get_the_post_thumbnail_url($post_id) ? get_the_post_thumbnail_url($post_id) : zrz_get_first_img(get_post_field('post_content', $post_id))).'" />
<meta property="article:published_time" content="'.get_the_time('c',$post_id).'" />
<meta property="article:modified_time" content="'.get_the_modified_date('c',$post_id).'" />
<meta property="article:author" content="'.zrz_get_user_page_url($author_id).'" />
<meta property="article:publisher" content="'.home_url().'" />';
    }

    $keywords = trim(strip_tags($keywords));
    $og = trim($og);
    if($keywords)
    {
        $keywords = '<meta name="keywords" content="'.$keywords.'" />'."\n";
    }
    return $keywords.zrz_seo_head_meta_description().$og;
}
// 网页描述
function zrz_seo_head_meta_description(){
    if(is_paged())
    {
        return;
    }
    $description = '';
    $og = '';
    if(is_home() || is_front_page()){
        $description =  zrz_get_theme_settings('description');
    }elseif(is_category() || zrz_is_custom_tax('collection')){
        $description = category_description();
    }elseif(is_tag()){
        $description = tag_description();
    }elseif(is_single() || is_singular()){
        global $post;
        $description = zrz_get_post_des($post->ID);
    }

    $description = strip_tags($description);
    $description = trim($description);

    if($description)
    {
        return '<meta name="description" content="'.$description.'" />'."\n".'<meta property="og:description" content="'.$description.'" />'."\n";
    }
    return '';
}

// 添加后台界面meta_box
add_action('add_meta_boxes','zrz_zrz_seo_post_metas_box_init');
function zrz_zrz_seo_post_metas_box_init(){
	add_meta_box('seo-metas','SEO','zrz_seo_post_metas_box',array('post','page','labs','shop','collection'),'side','high');
}
function zrz_seo_post_metas_box($post){
	if($post->ID) {
		$post_id = $post->ID;
        $seo_title = zrz_seo_get_post_meta($post_id,'zrz_seo_title');
		$seo_keywords = zrz_seo_get_post_meta($post_id,'zrz_seo_keywords');
		$seo_description = zrz_seo_get_post_meta($post_id,'zrz_seo_description');
	}
	else {
        $seo_title = '';
		$seo_keywords = '';
		$seo_description = '';
	}
	?>
	<div class="seo-metas">
        <p>SEO标题：<input type="text" class="regular-text" name="seo_title" value="<?php echo zrz_seo_quota_encode($seo_title); ?>" style="max-width: 98%;"></p>
		<p>SEO关键词：<input type="text" class="regular-text" name="seo_keywords" value="<?php echo zrz_seo_quota_encode($seo_keywords); ?>" style="max-width: 98%;"></p>
		<p>SEO描述：<br><textarea class="large-text" name="seo_description"><?php echo $seo_description; ?></textarea></p>
		<p>若不指定，则自动使用文章标签作为关键词，文章前20个字符作为描述，若要取消，请直接设置成空格然后保存。</p>
	</div>
<?php
}

// 保存填写的meta信息
add_action('save_post','zrz_seo_post_metas_box_save');
function zrz_seo_post_metas_box_save($post_id){
    if(!isset($_POST['seo_title']) || !isset($_POST['seo_keywords']) || !isset($_POST['seo_description'])) return $post_id;
    $seo_title = strip_tags($_POST['seo_title']);
	$seo_keywords = strip_tags($_POST['seo_keywords']);
	$seo_description = stripslashes(strip_tags($_POST['seo_description']));
    if($seo_title == ' '){
        delete_post_meta($post_id,'zrz_seo_title');
    }elseif($seo_title){
        update_post_meta($post_id,'zrz_seo_title',$seo_title);
    }
	if($seo_keywords == ' '){
		delete_post_meta($post_id,'zrz_seo_keywords');
	}elseif($seo_keywords){
		update_post_meta($post_id,'zrz_seo_keywords',$seo_keywords);
	}

	if($seo_description == ' '){
		delete_post_meta($post_id,'zrz_seo_description');
	}elseif($seo_description){
		update_post_meta($post_id,'zrz_seo_description',$seo_description);
	}
}

add_action('category_add_form_fields','zrz_seo_extra_term_fields');
add_action('created_category','zrz_seo_extra_term_fileds_save');
add_action('edit_category_form_fields','zrz_seo_extra_term_fields');
add_action('edited_category','zrz_seo_extra_term_fileds_save');

//商品
add_action('shoptype_add_form_fields','zrz_seo_extra_term_fields');
add_action('created_shoptype','zrz_seo_extra_term_fileds_save');
add_action('edit_shoptype_form_fields','zrz_seo_extra_term_fields');
add_action('edited_shoptype','zrz_seo_extra_term_fileds_save');

//专题
add_action('collection_add_form_fields','zrz_seo_extra_term_fields');
add_action('created_collection','zrz_seo_extra_term_fileds_save');
add_action('edit_collection_form_fields','zrz_seo_extra_term_fields');
add_action('edited_collection','zrz_seo_extra_term_fileds_save');

add_action('add_tag_form_fields','zrz_seo_extra_term_fields');
add_action('created_post_tag','zrz_seo_extra_term_fileds_save');
add_action('edit_tag_form_fields','zrz_seo_extra_term_fields');
add_action('edited_post_tag','zrz_seo_extra_term_fileds_save');

function zrz_seo_extra_term_fields($term){
    $metas = array(
        array('meta_name' => 'SEO关键词','meta_key' => 'seo_keywords'),
		array('meta_name' => 'SEO标题','meta_key' => 'seo_title')
    );
    if(isset($term->term_id))
        $term_id = $term->term_id;
    foreach($metas as $meta) {
        $meta_name = $meta['meta_name'];
        $meta_key = $meta['meta_key'];
        if(isset($term_id)) $meta_value = zrz_seo_get_term_meta($term_id,$meta_key);
        else $meta_value = '';
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_<?php echo $meta_key; ?>"><?php echo $meta_name; ?></label></th>
            <td><input type="text" name="term_meta_<?php echo $meta_key; ?>" id="term_<?php echo $meta_key; ?>" class="regular-text" value="<?php echo $meta_value; ?>"></td>
        </tr>
    <?php
    }
}

function zrz_seo_extra_term_fileds_save($term_id){
    if(!empty($_POST)) foreach($_POST as $key => $value){
        if(strpos($key,'term_meta_') === 0 && trim($value) != '') {
            $meta_key = str_replace('term_meta_','',$key);
            $meta_value = trim($value);
            update_term_meta($term_id,$meta_key,$meta_value) OR add_term_meta($term_id,$meta_key,$meta_value,true);
        }
    }
}
