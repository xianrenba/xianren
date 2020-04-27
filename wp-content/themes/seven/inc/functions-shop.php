<?php
//添加商品的文章类型
function zrz_create_shop_type() {
	$labels = array(
 		'name' => sprintf( '%1$s',zrz_custom_name('shop_name')),
    	'singular_name' => sprintf( '%1$s',zrz_custom_name('shop_name')),
    	'add_new' => __('添加一个商品','ziranzhi2'),
    	'add_new_item' => __('添加一个商品','ziranzhi2'),
    	'edit_item' => __('编辑商品','ziranzhi2'),
    	'new_item' => __('新的商品','ziranzhi2'),
    	'all_items' => __('所有商品','ziranzhi2'),
    	'view_item' => __('查看商品','ziranzhi2'),
    	'search_items' => __('搜索商品','ziranzhi2'),
    	'not_found' =>  __('没有商品','ziranzhi2'),
    	'not_found_in_trash' =>__('回收站为空','ziranzhi2'),
    	'parent_item_colon' => '',
    	'menu_name' => sprintf( '%1$s',zrz_custom_name('shop_name')),
    );
	register_post_type( 'shop', array(
		'labels' => $labels,
		'has_archive' => true,
 		'public' => true,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail','comments'),
		'taxonomies' => array('shoptype','post_tag'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'shop' ),
		)
	);
}
add_action( 'init', 'zrz_create_shop_type' );

//添加商品的类型
add_action( 'init', 'zrz_create_shop_taxonomies', 0 );
function zrz_create_shop_taxonomies() {

	$labels = array(
		'name'              => __( '商品分类', 'ziranzhi2' ),
		'singular_name'     => __( '商品分类', 'ziranzhi2' ),
		'search_items'      => __( '搜索商品分类', 'ziranzhi2' ),
		'all_items'         => __( '所有商品分类', 'ziranzhi2' ),
		'parent_item'       => __( '父级商品分类', 'ziranzhi2' ),
		'parent_item_colon' => __( '父级商品分类：', 'ziranzhi2' ),
		'edit_item'         => __( '编辑商品分类', 'ziranzhi2' ),
		'update_item'       => __( '更新商品分类', 'ziranzhi2' ),
		'add_new_item'      => __( '添加商品分类', 'ziranzhi2' ),
		'new_item_name'     => __( '商品分类名称', 'ziranzhi2' ),
		'menu_name'         => __( '商品分类', 'ziranzhi2' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'shop' ),
	);

	register_taxonomy( 'shoptype', array( 'shoptype' ), $args );
}

/**
 * 修改链接类型为post_id.html
 */
 add_filter('post_type_link', 'zrz_custom_shop_link', 1, 3);
 function zrz_custom_shop_link( $link, $post = 0 ){
     if ( $post->post_type == 'shop' ){
         return home_url( 'shop/' . $post->ID .'.html' );
     } else {
         return $link;
     }
 }

 add_action( 'init', 'zrz_custom_shop_rewrites_init' );
 function zrz_custom_shop_rewrites_init(){
     add_rewrite_rule(
         'shop/([0-9]+)?.html$',
         'index.php?post_type=shop&p=$matches[1]',
         'top' );
     add_rewrite_rule(
         'shop/([0-9]+)?.html/comment-page-([0-9]{1,})$',
         'index.php?post_type=shop&p=$matches[1]&cpage=$matches[2]',
         'top'
         );
 }

 //商品管理页面添加栏目
 add_filter( 'manage_shop_posts_columns', 'zrz_set_edit_shop_columns' );
 function zrz_set_edit_shop_columns($columns) {

     $columns['zrz_shop_type'] = __( '商品类型', 'ziranzhi2' );
	 $columns['zrz_shop_commodity'] = __( '是虚拟物品吗', 'ziranzhi2' );

     return $columns;
 }

//商品管理页面添加栏目属性
add_action( 'manage_shop_posts_custom_column' , 'zrz_shop_column', 10, 4 );
function zrz_shop_column( $column, $post_id ) {
 switch ( $column ) {

     case 'zrz_shop_type' :
	    $terms = get_post_meta($post_id, 'zrz_shop_type', true);
        if($terms == 'normal'){
			echo '出售';
		}elseif($terms == 'lottery'){
			echo '抽奖';
		}else{
			echo '兑换';
		}
        break;
	case 'zrz_shop_commodity' :
	    $terms = get_post_meta($post_id, 'zrz_shop_commodity', true);
       if($terms == 1){
			echo '实物';
		}else{
			echo '虚拟物品';
		}
       break;
 }
}

// //排序
// add_filter( 'manage_edit-shop_sortable_columns', 'zrz_set_shop_sortable_columns' );
// function zrz_set_shop_sortable_columns( $columns ) {
// 	$columns['zrz_shop_type'] = 'zrz_shop_type';
// 	$columns['zrz_shop_commodity'] = 'zrz_shop_commodity';
//
// 	return $columns;
// }
//
// //排序事件
// add_action( 'pre_get_posts', 'zrz_shop_orderby' );
// function zrz_shop_orderby( $query ) {
// 	if ( ! is_admin() )
// 	return;
//
// 	$orderby = $query->get( 'orderby');
//
// 	if ($orderby == 'zrz_shop_type') {
// 		$query->set( 'meta_key', 'zrz_shop_type');
// 	}
// 	$query->set( 'orderby', 'name' );
// }

// 添加商品信息
add_action('add_meta_boxes','zrz_shop_metas_box_init');
function zrz_shop_metas_box_init(){
	add_meta_box('shop-type-metas',__('商品类型','ziranzhi2'),'zrz_shop_type_box','shop','side','high');
	add_meta_box('shop-count-metas',__('商品数量','ziranzhi2'),'zrz_shop_count_box','shop','side','high');

	add_meta_box('shop-commodity-metas',__('是虚拟物品吗？','ziranzhi2'),'zrz_shop_commodity_box','shop','side','high');

	//出售
	add_meta_box('shop-price-metas',__('商品价格','ziranzhi2'),'zrz_shop_price_box','shop','side','high');

	//抽奖
	add_meta_box('shop-lottery-metas',__('抽奖设置','ziranzhi2'),'zrz_shop_lottery_box','shop','side','high');

	//兑换
	add_meta_box('shop-credit-metas',__('多少积分可兑换','ziranzhi2'),'zrz_shop_credit_box','shop','side','high');

	//添加更多图片
	add_meta_box( 'listingimagediv', __( '更多展示图片', 'ziranzhi2' ), 'zrz_more_images_metabox', 'shop', 'normal', 'high');

	//虚拟物品信息
	add_meta_box( 'shop-virtual-metas', __( '虚拟物品信息', 'ziranzhi2' ), 'zrz_shop_virtual_metabox', 'shop', 'normal', 'high');

	add_meta_box('shop-attributes-metas',__('商品属性','ziranzhi2'),'zrz_shop_attributes_box','shop','normal','high');
}

//添加更多图片
function zrz_more_images_metabox($post){
	$image_ids = get_post_meta( $post->ID, 'zrz_shop_images', true );
	$html = '';
	if ( $image_ids && !empty($image_ids) ) {
		$html .= '<div id="image-list">';
		foreach ($image_ids as $image_id) {
			$html .= '<div id="'.$image_id.'" class="item"><div class="pd5"><div class="max-h">'.wp_get_attachment_image( $image_id, 'post-thumbnail' ).'</div>';
			$html .= '<input type="hidden" class="upload_listing_image" name="zrz_shop_images[]" value="' . esc_attr( $image_id ) . '" />';
			$html .= '<p><a href="javascript:;" class="remove_listing_image_button" >' . esc_html__( '删除此图', 'ziranzhi2' ) . '</a></p></div></div>';
		}
		$html .='</div>';
	}else{
		$html .='<div id="image-list"></div>';
	}
	$html .= '<div><a id="upload_listing_image_button" class="button" href="javascript:;">'
		.__('添加一个图片','ziranzhi2').
	'</a></div>';
	$html .='
	<style>
		#image-list{
			letter-spacing: -.8em;
		}
		.pd5{
			padding:5px;
		}
		.item{
			width:25%;
			display: inline-block;
			vertical-align: top;
			letter-spacing: 0
		}
		.item img{
			width:100%;
			height: auto;
		}
		.max-h{
			max-height: 150px;
			overflow: hidden;
		}
		@media screen and (max-width:873px){
			.item{
				width:50%
			}
		}
	</style>
	<script>
	var $ =jQuery.noConflict();
	$(document).ready(function($) {

	var file_frame;

	$.fn.upload_listing_image = function( button ) {

		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: $( this ).data( \'uploader_title\' ),
		  button: {
		    text: $( this ).data( \'uploader_button_text\' ),
		  },
		  multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( \'select\', function() {
		  var attachment = file_frame.state().get(\'selection\').first().toJSON();
		  var dom = \'<div id="\'+attachment.id+\'" class="item"><div class="pd5"><div class="max-h"><img src="\'+attachment.url+\'" /><input type="hidden" class="upload_listing_image" name="zrz_shop_images[]" value="\'+attachment.id+\'" /></div><p><a href="javascript:;" class="remove_listing_image_button" >删除此图</a></p></div></div>\';
		  $(\'#image-list\').append(dom);
		});

		// Finally, open the modal
		file_frame.open();
	};

	$(\'#listingimagediv\').on( \'click\', \'#upload_listing_image_button\', function( event ) {
		event.preventDefault();
		$.fn.upload_listing_image( $(this) );
	});

	$(\'#listingimagediv\').on( \'click\', \'.remove_listing_image_button\', function( event ) {
		event.preventDefault();
		$(event.target).parents(\'.item\').remove();
	});

});
</script>';
	echo $html;
}

//订单类型选择回调
function zrz_shop_type_box($post){
    $meta_element_class = get_post_meta($post->ID, 'zrz_shop_type', true);
	?>
	<select name="zrz_shop_type" id="zrz_shop_type">
      <option value="normal" <?php echo selected( $meta_element_class, 'normal' ); ?>>出售</option>
      <option value="lottery" <?php echo selected( $meta_element_class, 'lottery'); ?>>抽奖</option>
      <option value="exchange" <?php echo selected( $meta_element_class, 'exchange'); ?>>兑换</option>
    </select>
	<?php
}

//虚拟物品，还是实物
function zrz_shop_commodity_box($post){
    $meta_element_class = get_post_meta($post->ID, 'zrz_shop_commodity', true);
	$meta_element_class = $meta_element_class ? 1 : 0 ;
	?>
	<select name="zrz_shop_commodity" id="zrz_shop_commodity">
      <option value="1" <?php echo selected( $meta_element_class, '1' ); ?>>实物</option>
	  <option value="0" <?php echo selected( $meta_element_class, '0' ); ?>>虚拟物品</option>
    </select>
	<?php
}

//商品数量回调
function zrz_shop_count_box($post){
	$total = zrz_get_shop_count($post->ID,'total');
	$sell = zrz_get_shop_count($post->ID,'sell');
	echo '
	<p>商品总数：
		<input type="text" class="regular-text" name="total" value="'.$total.'" style="max-width: 98%;">
	</p>
	<p>已经出售或兑换数量：
		<input type="text" class="regular-text" name="sell" value="'.$sell.'" style="max-width: 98%;">
	</p>
	<p>剩余：'.((int)$total - (int)$sell).'</p>';
}

//商品价格栏目回调
function zrz_shop_price_box($post){
	$post_id = isset($post->ID) ? $post->ID : 0;
	$price = zrz_get_shop_price($post_id,'price');
	$d_price = zrz_get_shop_price($post_id,'d_price');
	$u_price = zrz_get_shop_price($post_id,'u_price');
	$credit = zrz_get_shop_price($post_id,'credit');
	echo '
		<p>正常价格：<input type="text" class="regular-text" name="price" value="'.$price.'" style="max-width: 98%;"></p>
		<p>折后价格：<input type="text" class="regular-text" name="d_price" value="'.$d_price.'" style="max-width: 98%;"></p>
		<p>会员价格：<input type="text" class="regular-text" name="u_price" value="'.$u_price.'" style="max-width: 98%;"></p>

		<p>购买商品奖励的积分：</p>
		<p><input type="text" class="regular-text" name="g_credit" value="'.number($credit).'" style="max-width: 98%;"></p>';
}

//抽奖栏目回调
function zrz_shop_lottery_box($post){
	$probability = zrz_get_shop_lottery($post->ID,'probability');
	$credit = zrz_get_shop_lottery($post->ID,'credit');
	$lv = zrz_get_lv_settings();
	$lv_meta = zrz_get_shop_lottery($post->ID,'capabilities');
	$html = '
		<p>命中概率（默认0.01 即1%的命中率）：
			<input type="text" class="regular-text" name="probability" value="'.$probability.'" style="max-width: 98%;"></p>
		<p>所需积分：
			<input type="text" class="regular-text" name="credit" value="'.$credit.'" style="max-width: 98%;"></p>
		<p>
		<p>允许抽奖的用户组：</p>';
		foreach ($lv as $key => $val) {
			if(isset($val['open']) && $val['open'] == 0) continue;
			$checked = '';
			if(in_array($key,$lv_meta)){
				$checked = 'checked';
			}
			$html .= '<label><input type="checkbox" name="lottery_capabilities[]" '.$checked.' value="'.$key.'">'.$val['name'].'</label><br/>';
 		}
		$html .= '</p>';
		echo $html;
}

//虚拟物品
function zrz_shop_virtual_metabox($post){
		$type = zrz_get_shop_virtual($post->ID,'type');
		$title = zrz_get_shop_virtual($post->ID,'title');
		$content = zrz_get_shop_virtual($post->ID,'content');
	?>
		<select name="zrz_shop_virtual_type">
			<option value="1" <?php echo selected( $type, '1' ); ?>>下载链接</option>
			<option value="0" <?php echo selected( $type, '0' ); ?>>其他内容</option>
	    </select>
		<?php
		echo '
	<p></p>
	<div>
		<label>付费内容标题</label><br>
		<input type="text" class="regular-text" name="zrz_shop_virtual_title" value="'.$title.'" style="widht:100%">
	</div>
	<p></p>
	<div>
		<label>付费内容</label><br>
		<textarea rows="4" cols="40" name="zrz_shop_virtual_content" style="width:100%">'.$content.'</textarea>
		<br>
		<span>如果是文字内容，请直接输入，如果是下载链接，复制链接到此处，每个链接占一行</span>
	</div>';
}

//积分兑换回调
function zrz_shop_credit_box($post){
	$credit = get_post_meta($post->ID,'zrz_shop_need_credit',true);
	$credit = $credit ? $credit : '';
	echo '
	兑换所需积分：
	<input type="text" class="regular-text" name="zrz_shop_need_credit" value="'.$credit.'" style="max-width: 98%;"></p>';
}

//商品属性栏目回调
function zrz_shop_attributes_box($post){

	    echo '<div id="attributes">';

		    $attributes = get_post_meta($post->ID,'zrz_shop_attributes',true);
			$attributes = is_array($attributes) ? $attributes : array();
		    $c = 0;
		    if ( count( $attributes ) > 0 ) {
		        foreach( $attributes as $track ) {
		            if ( isset( $track['title'] ) || isset( $track['track'] ) ) {
		                printf( '<p>%5$s<input type="text" name="zrz_shop_attributes[%1$s][title]" value="%2$s" class="regular-text"/> %6$s<input type="text" name="zrz_shop_attributes[%1$s][track]" value="%3$s" class="regular-text"/> <span class="remove button">%4$s</span></p>', $c, $track['title'], $track['track'], __( '删除','ziranzhi2' ),__('属性名称：','ziranzhi2'),__('值：','ziranzhi2') );
		                $c = $c +1;
		            }
		        }
		    }

		echo '<span id="here"></span>
		<span class="add button">添加属性</span>
	</div>
	<style>
    #shop-price-metas,#shop-lottery-metas,#shop-credit-metas,#shop-virtual-metas{display:none}
    </style>
	<script>
	    var $ =jQuery.noConflict();
	    $(document).ready(function() {
	        var count = '.$c.';
	        $(".add").click(function() {
	            count = count + 1;

	            $(\'#here\').append(\'<p> 属性名称：<input type="text" name="zrz_shop_attributes[\'+count+\'][title]" value="" class="regular-text"/> 值： <input type="text" name="zrz_shop_attributes[\'+count+\'][track]" value="" class="regular-text"/> <span class="remove button">删除</span></p>\' );
	            return false;
	        });
	        $(".remove").live(\'click\', function() {
	            $(this).parent().remove();
	        });
	    });
		var zrzyun = $(\'#zrz_shop_type\').find(\':selected\').val();
	    if(zrzyun == \'normal\'){
	        $(\'#shop-price-metas\').show();
	    }else if(zrzyun == \'lottery\'){
	        $(\'#shop-lottery-metas\').show();
	    }else if(zrzyun == \'exchange\'){
	        $(\'#shop-credit-metas\').show();
	    }
		if($(\'#zrz_shop_type\'))
		$(\'#zrz_shop_type\').on(\'change\', function() {
		    var val = $(this).find(\':selected\').val();
		if(val === \'normal\'){
		    $(\'#shop-price-metas\').show();
		    $(\'#shop-lottery-metas\').hide();
		    $(\'#shop-credit-metas\').hide();
		}else if(val === \'lottery\'){
			$(\'#shop-price-metas\').hide();
		    $(\'#shop-lottery-metas\').show();
		    $(\'#shop-credit-metas\').hide();
		}else if(val === \'exchange\'){
			$(\'#shop-price-metas\').hide();
		    $(\'#shop-lottery-metas\').hide();
		    $(\'#shop-credit-metas\').show();
		}
		})
		var zrzcommodity = $(\'#zrz_shop_commodity\').find(\':selected\').val();
			if(zrzcommodity == \'0\'){
				$(\'#shop-virtual-metas\').show();
			}else{
				$(\'#shop-virtual-metas\').hide();
			}
		if($(\'#zrz_shop_commodity\'))
		$(\'#zrz_shop_commodity\').on(\'change\', function() {
			var val = $(this).find(\':selected\').val();
			if(val == \'0\'){
				$(\'#shop-virtual-metas\').show();
			}else{
				$(\'#shop-virtual-metas\').hide();
			}
		})
	</script>';

}

// 保存填写的meta信息
add_action('save_post','zrz_shop_metas_box_save');
function zrz_shop_metas_box_save($post_id){
	$post_type = get_post_type($post_id);

	if($post_type != 'shop') return;

	//添加更多图片
	if( isset($_POST['zrz_shop_images'])) {
		update_post_meta( $post_id, 'zrz_shop_images', $_POST['zrz_shop_images']);
	}else{
		delete_post_meta( $post_id, 'zrz_shop_images');
	}

	//保存商品价格
	if(isset($_POST['price']) && $_POST['price']){
		$price = strip_tags($_POST['price']);
		$d_price = isset($_POST['d_price']) ? esc_attr($_POST['d_price']) : '';
		$u_price = isset($_POST['u_price']) ? esc_attr($_POST['u_price']) : '';
		$credit = isset($_POST['g_credit']) ? esc_attr($_POST['g_credit']) : '';
		$price_arr = array(
			'price'=>$price,
			'd_price'=>$d_price,
			'u_price'=>$u_price,
			'credit'=>$credit
		);
		update_post_meta($post_id,'zrz_shop_price',$price_arr);
	}

	//商品属性
    if(isset($_POST['zrz_shop_attributes'])){
		update_post_meta($post_id,'zrz_shop_attributes',$_POST['zrz_shop_attributes']);
	}else{
		delete_post_meta( $post_id, 'zrz_shop_attributes');
	}

	//是虚拟物品吗
	if(isset($_POST['zrz_shop_commodity'])){
		update_post_meta($post_id,'zrz_shop_commodity',esc_attr($_POST['zrz_shop_commodity']));
	}

	//商品类型
	if(isset($_POST['zrz_shop_type']) && $_POST['zrz_shop_type']){
		update_post_meta($post_id,'zrz_shop_type',esc_attr($_POST['zrz_shop_type']));
	}

	//抽奖
	if(isset($_POST['credit']) && $_POST['credit'] !== ''){
		$lottery = array(
			'probability'=>$_POST['probability'],
			'credit'=>$_POST['credit'],
			'capabilities'=>$_POST['lottery_capabilities']
		);
		update_post_meta($post_id,'zrz_shop_lottery',$lottery);
	}

	//兑换所需积分
	if(isset($_POST['zrz_shop_need_credit']) && $_POST['zrz_shop_need_credit']){
		update_post_meta($post_id,'zrz_shop_need_credit',esc_attr($_POST['zrz_shop_need_credit']));
	}

	//保存虚拟物品信息
	if(isset($_POST['zrz_shop_virtual_content']) && $_POST['zrz_shop_virtual_content']){
		$virtual = array(
			'title'=>isset($_POST['zrz_shop_virtual_title']) ? esc_attr($_POST['zrz_shop_virtual_title']) : '',//虚拟物品标题
			'type'=>isset($_POST['zrz_shop_virtual_type']) ? (int)$_POST['zrz_shop_virtual_type'] : '',//虚拟物品类型
			'content'=>isset($_POST['zrz_shop_virtual_content']) ? esc_attr($_POST['zrz_shop_virtual_content']) : ''//虚拟物品内容
		);

		update_post_meta($post_id,'zrz_shop_virtual',$virtual);
	}

	//商品数量
	if(isset($_POST['total']) && $_POST['total']){
		$count = array(
			'total'=>$_POST['total'],
			'sell'=>$_POST['sell'],
			'sold_out'=>(int)$_POST['total'] - (int)$_POST['sell'] > 0 ? 1 : 0,
		);
		update_post_meta($post_id,'zrz_shop_count',$count);
	}
}

//获取商品价格
function zrz_get_shop_price($post_id,$type){
	$price = get_post_meta($post_id,'zrz_shop_price',true);
	if(!$price){
		$price = array(
			'price'=>'',//正常价格
			'd_price'=>'',//折后价格
			'u_price'=>'',//会员价格
			'credit'=>''//奖励的积分
		);
	}

	return isset($price[$type]) ? $price[$type] : '';
}

//获取商品数量
function zrz_get_shop_count($post_id,$type){
	$count = get_post_meta($post_id,'zrz_shop_count',true);
	if(!$count){
		$count = array(
			'total'=>0,//总数量
			'sell'=>0,//已经出售或兑换的数量
			'sold_out'=>0
		);
	}

	return isset($count[$type]) ? $count[$type] : '';
}

//获取抽奖数据
function zrz_get_shop_lottery($post_id,$type){
	$lottery = get_post_meta($post_id,'zrz_shop_lottery',true);
	if(!$lottery){
		$lottery = array(
			'probability'=>0.01,//中奖概率
			'credit'=>'',//所需积分
			'capabilities'=>array()
		);
	}

	return isset($lottery[$type]) ? $lottery[$type] : '';
}

function zrz_get_shop_virtual($post_id,$type){
	$virtual = get_post_meta($post_id,'zrz_shop_virtual',true);
	if(!$virtual){
		$virtual = array(
			'title'=>'',//虚拟物品标题
			'type'=>'',//虚拟物品类型
			'content'=>''//虚拟物品内容
		);
	}

	return isset($virtual[$type]) ? $virtual[$type] : '';
}

//商品剩余
function zrz_shop_count_remaining($post_id){
	$total = (int)zrz_get_shop_count($post_id,'total');
	$sell = (int)zrz_get_shop_count($post_id,'sell');
	return $total - $sell;
}

//商品价格
function zrz_get_shop_price_dom($post_id = 0,$user_id = 0){
	if(!$post_id) {
		global $post;
		$post_id = $post->ID;
	};
	$d_price = zrz_get_shop_price($post_id,'d_price');
	$u_price = zrz_get_shop_price($post_id,'u_price');
	$price = zrz_get_shop_price($post_id,'price');

	if($user_id){
		$current_user = $user_id;
	}else{
		$current_user = get_current_user_id();
	}

	$lv = get_user_meta($current_user,'zrz_lv',true);

	$is_vip = strpos($lv,'vip') !== false ? true : false;

	$q = '';
	if($u_price != 0 && $u_price != '' && $is_vip){
		return array(
			'dom'=>'<div class="shop-price fd">¥<b>'.$u_price.'</b></div>',
			'msg'=>'<span class="shop-u fs12 pos-a">会员价</span>',
			'price'=>$u_price
		);
	}elseif($d_price){
		return array(
			'dom'=>'<div class="shop-price fd">¥<b>'.$d_price.'</b></div>',
			'msg'=>'<span class="shop-u fs12 pos-a">'.(ceil($d_price / $price * 100)).'折</span>',
			'price'=>$d_price
		);
	}else{
		return array(
			'dom'=>'<div class="shop-price fd">¥<b>'.$price.'</b></div>',
			'msg'=>'',
			'price'=>$price
		);
	}
}

add_action('wp_ajax_zrz_update_shop_home_img', 'zrz_update_shop_home_img');
function zrz_update_shop_home_img(){
	if(!current_user_can('manage_options')){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
    }
	$type = isset($_POST['type']) ? esc_attr($_POST['type']) : '';

	$upload = new zrz_media($_FILES['file'],'vote',get_current_user_id(),'','');
    $resout = $upload->media_upload();

    if($resout){
        $img = json_decode($resout);
    }else{
		print $resout;
		exit;
	}

	$imgs = get_option('zrz_shop_img',array('normal'=>'','lottery'=>'','exchange'=>''));
	$imgs[$type] = isset($img->Turl) ? $img->Turl : '';
	update_option('zrz_shop_img',$imgs);

	print json_encode(array('status'=>200,'url'=>zrz_get_thumb($img->Turl,900,180,'',true)));
	exit;

}
