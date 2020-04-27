<?php
/*
* 专题页面
*/

//创建一个专题文章类型
function zrz_create_series_taxonomies() {
	$labels = array(
		'name'              => __( '专题', 'ziranzhi2' ),
		'singular_name'     => __( '专题', 'ziranzhi2' ),
		'search_items'      => __( '搜索专题', 'ziranzhi2' ),
		'all_items'         => __( '所有专题', 'ziranzhi2' ),
		'parent_item'       => __( '父级专题', 'ziranzhi2' ),
		'parent_item_colon' => __( '父级专题', 'ziranzhi2' ),
		'edit_item'         => __( '编辑专题', 'ziranzhi2' ),
		'update_item'       => __( '更新专题', 'ziranzhi2' ),
		'add_new_item'      => __( '添加专题', 'ziranzhi2' ),
		'new_item_name'     => __( '专题名称', 'ziranzhi2' ),
		'menu_name'         => __( '专题', 'ziranzhi2' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'collection' ),
	);

	register_taxonomy( 'collection', array( 'post' ), $args );
}
add_action( 'init', 'zrz_create_series_taxonomies' );

function DeleteHtml($str){
	$str = str_replace("<br/>","",$str);
	$str = str_replace("\t","",$str);
	$str = str_replace("\r\n","",$str);
	$str = str_replace("\r","",$str);
	$str = str_replace("\n","",$str);
	return trim($str);
}
