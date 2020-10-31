<?php 

    //创建专题分类法
    add_action( 'init', 'create_redvine_topic_taxonomy', 0 );
    function create_redvine_topic_taxonomy() {
        //添加分类分类法
        $labels = array(
            'name'              => __( '专题', 'qux' ),
            'singular_name'     => __( '专题', 'qux' ),
            'search_items'      => __( '搜索专题','qux' ),
            'all_items'         => __( '所有专题','qux' ),
            'parent_item'       => __( '父级专题','qux' ),
            'parent_item_colon' => __( '父级专题：','qux' ),
            'edit_item'         => __( '编辑专题','qux' ), 
            'update_item'       => __( '更新专题','qux' ),
            'add_new_item'      => __( '添加专题','qux' ),
            'new_item_name'     => __( '新的专题','qux' ),
            'menu_name'         => __( '专题','qux' ),
        );     

        //注册分类法
        register_taxonomy('tcat',array('post'), array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'tcat' )
        ));

    }