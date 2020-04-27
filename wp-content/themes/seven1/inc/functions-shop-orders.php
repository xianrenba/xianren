<?php
/*
* 商城订单项
* $order_type //订单类型
* c : 抽奖 ，d : 兑换 ，g : 购买 ，w : 文章内购 ，ds : 打赏 ，cz : 充值 ，vip : VIP购买 ,cg : 积分购买
*
* $order_commodity //商品类型
* 0 : 虚拟物品 ，1 : 实物
*
* $order_state //订单状态
* w : 等待付款 ，f : 已付款未发货 ，c : 已发货 ，s : 已删除 ，q : 已签收 ，t : 已退款
*/

function zrz_get_shop_order($key,$val){

    if($key == 'order_type'){
        $arr = array(
            'c'=>__('积分抽奖','ziranzhi2'),
            'd'=>__('商品兑换','ziranzhi2'),
            'g'=>__('商品购买','ziranzhi2'),
			'ds'=>__('打赏','ziranzhi2'),
			'cz'=>__('充值','ziranzhi2'),
			'w'=>__('文章内购','ziranzhi2'),
            'cg'=>__('积分购买','ziranzhi2'),
            'vip'=>__('vip购买','ziranzhi2'),
        );
    }

    if($key == 'order_commodity'){
        $arr = array(
            '0'=>__('虚拟物品','ziranzhi2'),
            '1'=>__('实物','ziranzhi2'),
        );
    }

    if($key == 'order_state'){
        $arr = array(
            'w'=>'<span style="color:#333">'.__('等待付款','ziranzhi2').'</span>',
            'f'=>'<span style="color:red">'.__('已付款未发货','ziranzhi2').'</span>',
            'c'=>'<span style="color:blue">'.__('已发货','ziranzhi2').'</span>',
            's'=>'<span style="color:#999">'.__('已删除','ziranzhi2').'</span>',
            'q'=>'<span style="color:green">'.__('已签收','ziranzhi2').'</span>',
            't'=>'<span style="color:#333">'.__('已退款','ziranzhi2').'</span>',
        );
    }

    return isset($arr[$val]) ? $arr[$val] : '';
}

class Zrz_order_Message{

    private $user_id;//用户ID
    private $post_id;//用户ID
    private $type;//订单类型
    private $order_commodity;//商品类型
    private $date;//时间
    private $order_id;//订单号
    private $order_count;//订单数量
    private $order_price;//订单价格
    private $order_state;//订单状态
    private $key;//自定义的 KEY
    private $value;//订自定义的 value
    private $order_content;//买家留言

    private $wpdb;//数据库全局变量
    private $table_name;//表名


    public function __construct($user_id = 0,$post_id = 0,$order_id = '', $type = '' ,$order_commodity = 0,$order_count = 0,$order_price = 0.00,$order_state = '',$key = '',$value = '',$order_content = ''){

        $this->user_id = esc_sql((int)$user_id);
        $this->post_id = esc_sql((int)$post_id);
        $this->type = esc_sql(esc_attr($type));
        $this->order_commodity = esc_sql((int)$order_commodity);
        $this->order_id = esc_sql(esc_attr($order_id));
        $this->order_count = esc_sql((int)$order_count);
        $this->order_price = esc_sql((float)$order_price);
        $this->order_state = esc_sql(esc_attr($order_state));
        $this->key = esc_sql(esc_attr($key));
        $this->value = esc_sql(esc_attr($value));
        $this->order_content = esc_sql(esc_attr($order_content));


        $this->date = current_time( 'mysql' );

        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'zrz_order';

    }

    //添加数据
    public function add_data(){

    	if( $this->wpdb->insert( $this->table_name, array(
            'user_id'=> $this->user_id,
            'post_id'=> $this->post_id,
            'order_type'=> $this->type,
            'order_commodity'=>$this->order_commodity,
            'order_id'=> $this->order_id,
            'order_date'=> $this->date,
            'order_count'=> $this->order_count,
            'order_price'=> $this->order_price,
            'order_state'=>$this->order_state,
            'order_key'=> $this->key,
            'order_value'=> $this->value,
            'order_content'=> $this->order_content
        ) ) )
    	return $this->wpdb->insert_id;

    	return false;

    }

    //订单筛选
    public function get_orders_data($type,$offset=0,$limit=0,$count = false){

        $where = $this->wpdb->prepare("AND NOT order_state = %s ",'w');
        if(isset($type['m']) && $type['m']){
            $where .= $this->wpdb->prepare("AND order_date LIKE '%%%s%%' ",esc_sql($type['m']));
        }
        if(isset($type['order_type']) && $type['order_type']){
            $where .= $this->wpdb->prepare("AND order_type = %s ",esc_sql($type['order_type']));
        }
        if(isset($type['s']) && $type['s']){
            $where .= $this->wpdb->prepare("AND order_id LIKE '%%%s%%' ", esc_sql($type['s']));
        }
        if(isset($type['state']) && $type['state']){
            $where .= $this->wpdb->prepare( "AND order_state = %s ", esc_sql($type['state']));
        }
        if(isset($type['user_id']) && $type['user_id']){
            $where .= $this->wpdb->prepare( "AND user_id = %s AND NOT order_state = %s", esc_sql($type['user_id']),'s');
        }

        $order = 'ORDER BY order_date DESC';
        $order_count = '';
        if($where){

            if($count){
                $order_count = "SELECT * FROM $this->table_name WHERE $where";
                $order_count = @str_replace('WHERE AND','WHERE',$order_count);
                $this->wpdb->get_results($order_count);
                $order_count = $this->wpdb->num_rows;
                $this->wpdb->flush();
            }

            if($limit && $offset){
                $limit = "LIMIT $limit , $offset";
                $where = "SELECT * FROM $this->table_name WHERE $where $order $limit";
            }elseif(!$limit && $offset){
                $limit = "LIMIT $offset";
                $where = "SELECT * FROM $this->table_name WHERE $where $order $limit";
            }else{
                $where = "SELECT * FROM $this->table_name WHERE $where $order";
            }
            $where = @str_replace('WHERE AND','WHERE',$where);
        }else{
            $where = "SELECT * FROM $this->table_name $order";
        }

        $check = $this->wpdb->get_results($where);
        if($count){
            return array($check,$order_count);
        }else{
            return $check;
        }

    }

    //订单更新
    public function update_orders_data($order_ids,$data,$frontend = false){
        if(empty($order_ids) || empty($data)) return false;

        foreach ($order_ids as $id) {
            if($frontend){
                $arg = array('order_id'=>$id);
            }else{
                $arg = array( 'id' => $id );
            }
            $this->wpdb->update(
                $this->table_name,
                $data,
                $arg
            );
        }

        return true;
    }

    //获取订单数量
    public function get_order_count($type = '',$admin = false){
        if($admin){
            if($type){
                $type = ' WHERE order_type="'.$type.'"';
            }else{
                $type = '';
            }
        }else{
            $user_id = $this->user_id;
            if($type){
                $type = ' WHERE order_type="'.$type.'" AND user_id='.$user_id;
            }else{
                $type = ' WHERE user_id='.$user_id;
            }
        }

        $w = $type ? 'AND NOT order_state="w" AND NOT order_state="s"' : 'WHERE NOT order_state="w" AND NOT order_state="s"';

        $count = $this->wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name $type $w" );
        return $count ? $count : 0;
    }
}

add_action('wp_ajax_zrz_edit_order', 'zrz_edit_order');
function zrz_edit_order(){
    if(!current_user_can('manage_options')){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
		exit;
    }
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $state = isset($_POST['state']) ? esc_attr($_POST['state']) : '';
    $key = isset($_POST['key']) ? $_POST['key'] : '';
    $value = isset($_POST['value']) ? esc_attr($_POST['value']) : '';
    $count = isset($_POST['count']) ? (int)$_POST['count'] : '';

    $init = new Zrz_order_Message();
    $resout = $init->update_orders_data(array($order_id),array('order_price'=>$price,'order_count'=>$count,'order_key'=>$key,'order_value'=>$value,'order_state'=>$state));

    print json_encode(array('status'=>200,'msg'=>__('成功','ziranzhi2')));
    exit;
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Zrz_Order_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;

        parent::__construct( array(
            'singular'  => 'id',
            'ajax'      => false
        ) );

    }

    //默认的项目
    function column_default($item, $column_name){
        switch($column_name){
            case 'id':
            case 'order_id':
            case 'post_id':
            case 'order_name':
            case 'user_id':
            case 'order_date':
            case 'order_type':
            case 'order_commodity':
            case 'order_count':
            case 'order_price':
            case 'order_key':
            case 'order_value':
            case 'order_state':
            case 'order_content':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }

    //编辑按钮
    function column_id($item){

        $actions = array(
            'edit'      => sprintf('<a class="order-edit" href="?post_type=shop&page=%s&action=%s&id=%s">编辑</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf('<a href="?post_type=shop&page=%s&action=%s&id=%s">删除</a>',$_REQUEST['page'],'delete',$item['id']),
        );

        return sprintf('%1$s %2$s',
            $item['id'],
            $this->row_actions($actions)
        );
    }

    //批量操作回调
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'id'          => 'ID',
            'order_name'=> __('商品名称','ziranzhi'),
            'order_id'     => __('订单号','ziranzhi'),
            'user_id'     => __('买家','ziranzhi'),
            'order_date'        => __('下单时间','ziranzhi'),
            'order_type'        => __('订单形式','ziranzhi'),
            'order_commodity' => __('商品类型','ziranzhi'),
            'order_count' => __('订单数量','ziranzhi'),
            'order_price'    => __('订单价格','ziranzhi'),
            'order_state'      => __('订单状态','ziranzhi'),
            'order_key'    => __('运单号','ziranzhi'),
            'order_value'    => __('发货地址','ziranzhi'),
            'order_content'    => __('买家留言','ziranzhi'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id'          => array('id',false),
            'order_id'     => array('order_id',false),
            'order_name'     => array('order_name',true),
            'user_id'     => array('user_id',false),
            'order_date'        => array('order_date',false),
            'order_type'        => array('order_type',false),
            'order_commodity' => array('order_commodity',false),
            'order_count' => array('order_count',false),
            'order_price'    => array('order_price',false),
            'order_key'    => array('order_key',false),
            'order_value'    => array('order_value',false),
            'order_state'      => array('order_state',false),
            'order_content'      => array('order_content',false),
        );
        return $sortable_columns;
    }

    function ormonths_dropdown(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_order';
        $months = $wpdb->get_results("
            SELECT DISTINCT YEAR( order_date ) AS year, MONTH( order_date ) AS month
            FROM $table_name
            ORDER BY order_date DESC
            ",ARRAY_A);
             $m = isset( $_GET['m'] ) ? $_GET['m'] : 0;
             $state = isset( $_GET['state'] ) ? $_GET['state'] : 0;
        ?>

            <label for="filter-date" class="screen-reader-text">按日期筛选</label>
            <select name="m" id="filter-date">
                <option <?php echo selected($m,0,true); ?> value="0">全部日期</option>
                <?php
                    foreach ($months as $val) {
                        echo '<option '.selected($m,$val['year'].'-'.$val['month'],true).' value="'.$val['year'].'-'.$val['month'].'">'.$val['year'].'年'.$val['month'].'月</option>';
                    }
                ?>
            </select>
            <label class="screen-reader-text" for="state">订单状态</label>
            <select name="state" id="state" class="postform">
                <option <?php echo selected($state,0,true); ?> value="0">所有订单状态</option>
                <option <?php echo selected($state,'w',true); ?> value="w">等待付款</option>
                <option <?php echo selected($state,'f',true); ?> value="f">已付款未发货</option>
                <option <?php echo selected($state,'c',true); ?> value="c">已发货</option>
                <option <?php echo selected($state,'s',true); ?> value="s">已删除</option>
                <option <?php echo selected($state,'q',true); ?> value="q">已签收</option>
                <option <?php echo selected($state,'t',true); ?> value="t">已退款</option>
            </select>
            <input type="submit" class="button" name="filter_action" value="<?php _e('筛选','ziranzhi2'); ?>">
        <?php
    }

    function display_tablenav( $which ) {

        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php if ( $this->has_items() ): ?>
                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
                <div class="alignleft actions date-f">
                    <?php $this->ormonths_dropdown(); ?>
                </div>
            <?php endif;
                $this->extra_tablenav( $which );
                $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
        <?php
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('删除','ziranzhi2')
        );
        return $actions;
    }

    //列表数据
    function prepare_items($val = '') {
        //每页显示多少条
        $per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $arr = array();
        $data = new Zrz_order_Message();
        $data = $data->get_orders_data($val);

        if($data){
            foreach ($data as $_val) {

                $post_name = explode('-',$_val->order_id);

                if($post_name && count($post_name) > 2){
                    $credit = $post_name[0];
                    $post_name = $post_name[1];
    				if($credit == 'pay_credit'){
                        $post_name = '积分购买';
                    }elseif($post_name == 0){
    					$post_name = '会员购买';
    				}elseif($_val->order_type == 'cz'){
                        $post_name = '充值';
                    }else{
    					$post_name = '<a target="_blank" href="' . esc_url( get_permalink($post_name) ) . '">' . get_the_title($post_name) . '</a>';
    				}
                }else{
                    switch ($_val->order_type) {
                        case 'vip':
                            $post_name = '会员购买';
                            break;
                        case 'w':
                        case 'c':
                        case 'ds':
                        case 'g':
                        case 'd':
                            $post_id = $_val->post_id;
                            $post_name = '<a target="_blank" href="' . esc_url( get_permalink($post_id) ) . '">' . get_the_title($post_id) . '</a>';
                            break;
                        case 'cz':
                            $post_name = '充值';
                            break;
                        case 'cg':
                            $post_name = '积分购买';
                            break;
                        default:
                            # code...
                            break;
                    }
                }


                $arr[] = array(
                    'id'=>$_val->id,
                    'order_name'=>$post_name,
                    'order_id'=>$_val->order_id,
                    'user_id'=>zrz_get_user_page_link($_val->user_id).'<span style="color:#ccc;display:block">（ID:'.$_val->user_id.'）</span>',
                    'order_date'=>$_val->order_date,
                    'order_type'=>zrz_get_shop_order('order_type',$_val->order_type),
                    'order_commodity'=>zrz_get_shop_order('order_commodity',$_val->order_commodity),
                    'order_count'=>$_val->order_count,
                    'order_price'=>$_val->order_price,
                    'order_key'    => $_val->order_key,
                    'order_value'    => $_val->order_value,
                    'order_state'=>zrz_get_shop_order('order_state',$_val->order_state),
                    'order_content'    => $_val->order_content,
                );
            }
        }

        $data = $arr;

        //排序方式
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order_date';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='asc') ? $result : -$result;
        }
        usort($data, 'usort_reorder');

        //当前页面
        $current_page = $this->get_pagenum();

        //总数
        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }
}

function order_add_menu_items(){
    add_submenu_page( 'edit.php?post_type=shop',__('订单管理','ziranzhi2'), __('订单管理','ziranzhi2'), 'manage_options', 'order_option', 'order_render_list_page');
}
add_action('admin_menu', 'order_add_menu_items');

function order_render_list_page(){

    $testListTable = new Zrz_Order_List_Table();
    $order_db = new Zrz_order_Message();

    $m = isset($_GET['m']) && $_GET['m'] ? $_GET['m'] : 0;
    $state = isset($_GET['state']) && $_GET['state'] ? $_GET['state'] : 0;
    $order_type = isset($_GET['order_type']) && $_GET['order_type'] ? $_GET['order_type'] : 0;
    $s = isset($_GET['s']) && $_GET['s'] ? $_GET['s'] : 0;

    $testListTable->prepare_items(array('s'=>$s,'state'=>$state,'order_type'=>$order_type,'m'=>$m));

    $doaction = $testListTable->current_action();
    if( $doaction && isset( $_REQUEST['SOMEVAR'] ) ) {
    } elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
        wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
        exit;
    }

    ?>
    <style>
        .tablenav.bottom .date-f{display: none}
        .colspanchange{
            border-bottom:1px solid #e1e1e1;
        }
        @media screen and (max-width: 782px){
            .colspanchange{display:table-cell!important;}
        }
        #wpbody-content .inline-edit-row fieldset{
            display: inline-block;
            margin-left:10px;
            float: inherit;
            width: auto;
        }
    </style>
    <div class="wrap">
        <h2>订单管理</h2>
        <ul class="subsubsub">
        	<li class="all"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option'); ?>" class="<?php echo $order_type === 0 ? 'current' : ''; ?>">全部<span class="count">（<?php echo $order_db->get_order_count('',true); ?>）</span></a> |</li>
        	<li class="mine"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=g'); ?>" class="<?php echo $order_type === 'g' ? 'current' : ''; ?>">购买<span class="count">（<?php echo $order_db->get_order_count('g',true); ?>）</span></a> |</li>
        	<li class="publish"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=c'); ?>" class="<?php echo $order_type === 'c' ? 'current' : ''; ?>">抽奖<span class="count">（<?php echo $order_db->get_order_count('c',true); ?>）</span></a> |</li>
        	<li class="sticky"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=d'); ?>" class="<?php echo $order_type === 'd' ? 'current' : ''; ?>">商品兑换<span class="count">（<?php echo $order_db->get_order_count('d',true); ?>）</span></a></li>
            <li class="sticky"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=w'); ?>" class="<?php echo $order_type === 'w' ? 'current' : ''; ?>">付费阅读<span class="count">（<?php echo $order_db->get_order_count('w',true); ?>）</span></a></li>
            <li class="sticky"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=ds'); ?>" class="<?php echo $order_type === 'ds' ? 'current' : ''; ?>">打赏<span class="count">（<?php echo $order_db->get_order_count('ds',true); ?>）</span></a></li>
            <li class="sticky"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=cz'); ?>" class="<?php echo $order_type === 'cz' ? 'current' : ''; ?>">充值<span class="count">（<?php echo $order_db->get_order_count('cz',true); ?>）</span></a></li>
            <li class="sticky"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=cg'); ?>" class="<?php echo $order_type === 'cg' ? 'current' : ''; ?>">积分购买<span class="count">（<?php echo $order_db->get_order_count('cg',true); ?>）</span></a></li>
            <li class="sticky"><a href="<?php echo home_url('/wp-admin/edit.php?post_type=shop&page=order_option&order_type=vip'); ?>" class="<?php echo $order_type === 'vip' ? 'current' : ''; ?>">VIP购买<span class="count">（<?php echo $order_db->get_order_count('vip',true); ?>）</span></a></li>
        </ul>
        <?php
            if(isset($_GET['action'])){
                if($_GET['action'] == 'delete'){
                    $order_ids = isset($_GET['id']) ? (array)$_GET['id'] : '';
                    if($order_ids){
                        $data = new Zrz_order_Message();
                        $resout = $data->update_orders_data($order_ids,array('order_state'=>'s'));
                        header("Location:".home_url('/wp-admin/edit.php?post_type=shop&page=order_option'));
                    }
                }
            }
        ?>

        <form id="order-filter" method="get" action="">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <input type="hidden" name="post_type" value="shop" />
            <?php
                if($order_type){
                    echo '<input type="hidden" name="order_type" value="'.$order_type.'">';
                }
            ?>

            <?php
                $testListTable->search_box( '搜索订单', 'search_id' );
                $testListTable->display();
             ?>

        </form>

    </div>

    <script>
        var $ =jQuery.noConflict();
        $('.tablenav.bottom').find('.date-f').remove();
        $('.order-edit').click(function(event){
            event.preventDefault();

            var tr = $(this).parent().parent().parent().parent();

            var count = tr.find('.column-order_count').text();
                price = tr.find('.column-order_price').text(),
                state = tr.find('.column-order_state').text(),
                key = tr.find('.column-order_key').text(),
                value = tr.find('.column-order_value').text(),
                order_id = getQueryString(event.currentTarget.href,'id');

            tr.hide();
            //添加编辑框
            var editform = '<tr><td colspan="11" class="colspanchange inline-edit-row edit-row">'+
                '<fieldset class="inline-edit-col-left">'+
                '<legend class="inline-edit-legend">订单数量</legend>'+
                '<input type="number" value="'+count+'" id="order_count_input">'+
                '</fieldset>'+
                '<fieldset class="inline-edit-col-center">'+
                '<legend class="inline-edit-legend">订单价格</legend>'+
                '<input type="number" value="'+price+'" id="order_price_input">'+
                '</fieldset>'+
                '<fieldset class="inline-edit-col-left">'+
                '<legend class="inline-edit-legend">运单号</legend>'+
                '<input type="text" value="'+key+'" id="order_key_input">'+
                '</fieldset>'+
                '<fieldset class="inline-edit-col-left">'+
                '<legend class="inline-edit-legend">收货地址</legend>'+
                '<input type="text" value="'+value+'" id="order_value_input">'+
                '</fieldset>'+
                '<fieldset class="inline-edit-col-right">'+
                '<legend class="inline-edit-legend">订单状态</legend>'+
                '<select id="order_state_input">'+
                '<option value="w" '+(state == '等待付款' ? 'selected' : '')+'>等待付款</option>'+
                '<option value="f" '+(state == '已付款未发货' ? 'selected' : '')+'>已付款未发货</option>'+
                '<option value="c" '+(state == '已发货' ? 'selected' : '')+'>已发货</option>'+
                '<option value="s" '+(state == '已删除' ? 'selected' : '')+'>已删除</option>'+
                '<option value="q" '+(state == '已签收' ? 'selected' : '')+'>已签收</option>'+
                '<option value="t" '+(state == '已退款' ? 'selected' : '')+'>已退款</option>'+
                '</select>'+
                '</fieldset>'+
                '<p class="submit"><button type="button" class="button cancel alignleft">取消</button><button type="button" class="button button-primary save alignright">更新</button></p>'+
            '</td></tr>';

            $('#the-list').find('tr').show();
            $('.edit-row').remove();

            tr.after(editform);
            $('.cancel').click(function(event){
                event.preventDefault();
                $('.edit-row').remove();
                tr.show();
            })

            $('.save').click(function(event){
                event.preventDefault();
                var count = $('#order_count_input').val(),
                    price = $('#order_price_input').val(),
                    key = $('#order_key_input').val(),
                    value = $('#order_value_input').val(),
                    state = $('#order_state_input').val();
                    jQuery.post(
                           '<?php echo admin_url("admin-ajax.php")."?action=zrz_edit_order"; ?>',
                           {
                                'count' : count,
                                'price' : price,
                                'state' : state,
                                'key':key,
                                'value':value,
                                'order_id':order_id
                           },
                           function( response ) {
                                var resout = jQuery.parseJSON(response );
                                if(resout.status == 200){
                                    window.location.reload();
                                }
                           }
                    );
            })
        })

        function getQueryString(url, name) {
            var reg = new RegExp("(^|&|\\?)" + name + "=([^&]*)(&|$)", "i");
            var r = url.substr(1).match(reg);
            if (r != null) return unescape(r[2]); return null;
        }
    </script>
    <?php
}

//获取用户订单
add_action( 'wp_ajax_zrz_get_user_orders', 'zrz_get_user_orders' );
function zrz_get_user_orders(){
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : '';
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : '';
    $filter = isset($_POST['filter']) ? $_POST['filter'] : '';

    $current_user = (int)get_current_user_id();

    if((!$user_id || $user_id !== $current_user) && !current_user_can('manage_options')){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
    	exit;
    }

    $number = (int)get_option('posts_per_page',true);
    $offset = ($paged-1)*$number;

    $count = array(
        'total'=>0,
        'g'=>0,
        'ds'=>0,
        'cz'=>0,
        'w'=>0,
        'c'=>0,
        'd'=>0,
        'vip'=>0
    );

    $order = new Zrz_order_Message($user_id);
    $order_list = $order->get_orders_data(array('user_id'=>$user_id,'order_type'=>$filter['order_type'],'state'=>$filter['order_state']),$number,(int)$offset,true);
    $post_count = ceil($order_list[1]/$number);
    $order_list = $order_list[0];
    if($order_list){
        $arg = array();
        foreach ($order_list as $value) {
            $post_name = explode('-',$value->order_id);
            $date = explode(' ',$value->order_date);
            if($post_name && count($post_name) > 2){
                $credit = $post_name[0];
                $post_name = $post_name[1];
                if($credit == 'pay_credit'){
                    $post_name = '<a href="'.home_url('/gold').'"><i class="iconfont iconfont zrz-icon-font-credit"></i><span>积分购买</span></a>';
                }elseif($post_name == 0){
                    $post_name = '<a href="'.home_url('/vips').'"><i class="iconfont zrz-icon-font-denglu"></i><span>会员购买</span></a>';
                }elseif($value->order_type == 'cz'){
                    $post_name = '<a href="'.home_url('/gold').'" target="_blank"><i class="iconfont zrz-icon-font-fufeilianjie"></i><span>充值</span></a>';
                }else{
                    $thumb = zrz_get_post_thumb($post_name);
                    $thumb = $thumb ? '<img src="'.zrz_get_thumb($thumb,60,60).'" />' : '';
                    $post_name = '<a target="_blank" href="'.esc_url(get_permalink($post_name)).'">'.$thumb.'<span>'. esc_attr(get_the_title($post_name)) . '</span></a>';
                }
            }else{
                switch ($value->order_type) {
                    case 'vip':
                        $post_name = '<a href="'.home_url('/vips').'"><i class="iconfont zrz-icon-font-denglu"></i><span>会员购买</span></a>';
                        break;
                    case 'w':
                    case 'c':
                    case 'ds':
                    case 'g':
                    case 'd':
                            $post_id = $value->post_id;
                            $thumb = zrz_get_post_thumb($post_id);
                            $thumb = $thumb ? '<img src="'.zrz_get_thumb($thumb,60,60).'" />' : '';
                            $post_name = '<a target="_blank" href="'.esc_url(get_permalink($post_id)).'">'.$thumb.'<span>'. esc_attr(get_the_title($post_id)) . '</span></a>';
                        break;
                    case 'cz':
                        $post_name = '<a href="'.home_url('/gold').'" target="_blank"><i class="iconfont zrz-icon-font-fufeilianjie"></i><span>充值</span></a>';
                        break;
                    case 'cg':
                        $post_name = '<a href="'.home_url('/gold').'"><i class="iconfont iconfont zrz-icon-font-credit"></i><span>积分购买</span></a>';
                        break;
                    default:
                        # code...
                        break;
                }
            }

            $arg[] = array(
                'order_id'=>$value->order_id,
                'order_name'=>$post_name,
                'order_type'=> $value->order_type == 'g' || $value->order_type == 'w' || $value->order_type == 'ds' || $value->order_type == 'cz' || $value->order_type == 'cg' || $value->order_type == 'vip' ? '¥'.$value->order_price.'元' : zrz_coin(0,0,$value->order_price),
                'order_date'=>'<b>'.$date[0].'</b> <b>'.$date[1].'</b>',
                'order_state'=>$value->order_type == 'ds' ? '<span class="green">打赏成功</span>' : ($value->order_type == 'cz' ? '<span class="green">充值成功</span>' : ($value->order_type == 'cg' ? '<span class="green">购买成功</span>' : zrz_get_shop_order('order_state',$value->order_state))),
                'order_total'=>__('总金额：','ziranzhi2').($value->order_type == 'g' || $value->order_type == 'w' || $value->order_type == 'ds' || $value->order_type == 'cz' || $value->order_type == 'cg' || $value->order_type == 'vip' ? '¥'.$value->order_price*$value->order_count.'元' : zrz_coin(0,0,$value->order_price*$value->order_count)),
                'order_price'=>$value->order_type == 'g' || $value->order_type == 'w' || $value->order_type == 'ds' || $value->order_type == 'cz' || $value->order_type == 'cg' || $value->order_type == 'vip' ? '¥'.$value->order_price.'元' : zrz_coin(0,0,$value->order_price),
                'order_count'=>$value->order_count,
                'order_key'=>$value->order_key ? $value->order_key : '暂无'
            );
        }

        if($paged == 1){
            $count['total'] = $order->get_order_count();
            $count['g'] = $order->get_order_count('g');
            $count['w'] = $order->get_order_count('w');
            $count['ds'] = $order->get_order_count('ds');
            $count['cz'] = $order->get_order_count('cz');
            $count['c'] = $order->get_order_count('c');
            $count['d'] = $order->get_order_count('d');
            $count['vip'] = $order->get_order_count('vip');
        }
        print json_encode(array('status'=>200,'msg'=>$arg,'count'=>$count,'postCount'=>$post_count));
    	exit;
    }
    print json_encode(array('status'=>401,'msg'=>__('没有订单','ziranzhi2')));
    exit;
}

//用户删除订单
add_action( 'wp_ajax_zrz_delete_user_orders', 'zrz_delete_user_orders' );
add_action( 'wp_ajax_nopriv_zrz_delete_user_orders', 'zrz_delete_user_orders' );
function zrz_delete_user_orders(){
    $order_id = isset($_POST['order_id']) ? esc_attr($_POST['order_id']) : '';

    $current_user = (int)get_current_user_id();

    $order_user = explode('-',$order_id);

    if((!$order_id || $current_user !== (int)$order_user[3]) && !current_user_can('manage_options')){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
    	exit;
    }

    $data = new Zrz_order_Message();
    $resout = $data->update_orders_data(array($order_id),array('order_state'=>'s'),true);
    print json_encode(array('status'=>200,'msg'=>__('删除成功','ziranzhi2')));
    exit;
}

//订单确认
add_action('wp_ajax_nopriv_zrz_shop_confirm', 'zrz_shop_confirm');
add_action('wp_ajax_zrz_shop_confirm', 'zrz_shop_confirm');
function zrz_shop_confirm(){
	if(!is_user_logged_in()) exit;
	$data = isset($_POST['data']) && is_array($_POST['data']) && !empty($_POST['data']) ? $_POST['data'] : '';
	if(!$data) exit;

    //检查价格和数量
    $resout = array();
    foreach ($data as $val) {
        $val = number($val);

        $post_type = get_post_meta($val,'zrz_shop_type',true);
        $remaining = zrz_shop_count_remaining($val);

        if($post_type == 'exchange'){
            array_push($resout,array(
                'price'=>get_post_meta($val,'zrz_shop_need_credit',true),
                'remaining'=>$remaining,
                'postid'=>$val,
                'count'=>1
            ));
        }else{
            $price = zrz_get_shop_price_dom($val);
            array_push($resout,array(
                'price'=>$price['price'],
                'remaining'=>$remaining,
                'postid'=>$val
            ));
        }
    }

    //当前用户的积分
    $credit = get_user_meta(get_current_user_id(),'zrz_credit_total',true);
	print json_encode(array('status'=>200,'msg'=>$resout,'credit'=>$credit));
	exit;
}

//积分购买
add_action('wp_ajax_zrz_shop_exchange_buy', 'zrz_shop_exchange_buy');
function zrz_shop_exchange_buy(){
    if(!is_user_logged_in()) exit;
	$data = isset($_POST['data']) && is_array($_POST['data']) && !empty($_POST['data']) ? $_POST['data'] : '';
	if(!$data) exit;

    $order_content = isset($_POST['orderContent']) ? $_POST['orderContent'] : '';
    
    $resout = array();
    foreach ($data as $val) {
        $resout[] = zrz_shop_exchange(number($val['postid']),$val['count'],$order_content);
    }
    print json_encode(array('status'=>200,'msg'=>$resout));
	exit;

}

//抽奖
add_action('wp_ajax_zrz_shop_lottery', 'zrz_shop_lottery');
function zrz_shop_lottery(){
    $post_id = isset($_POST['pid']) ? $_POST['pid'] : '';

    //检查文章id以及用户的登陆情况
    if(!$post_id || !is_user_logged_in()){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_id = get_current_user_id();

    //检查文章类型
    $type = get_post_meta($post_id,'zrz_shop_type',true);
    if($type != 'lottery'){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    //检查抽奖的权限
    $capabilities = zrz_get_shop_lottery($post_id,'capabilities');
    $user_lv = zrz_get_lv($user_id,'');
    if(!in_array($user_lv,$capabilities)){
        print json_encode(array('status'=>402,'msg'=>__('权限不足','ziranzhi2')));
        exit;
    }

    //检查商品剩余量
    if(zrz_shop_count_remaining($post_id) < 1 ){
        print json_encode(array('status'=>402,'msg'=>__('商品数量不足','ziranzhi2')));
        exit;
	}

    //获取用户的积分以及抽奖所需的积分
    $credit = (int)zrz_coin($user_id,'nub');
	$lottery_credit = (int)zrz_get_shop_lottery($post_id,'credit');

    //如果积分不足，则禁止抽奖
	if($credit < $lottery_credit) {
        print json_encode(array('status'=>402,'msg'=>__('积分不足','ziranzhi2')));
        exit;
	};

    //中奖概率
    $probability = zrz_get_shop_lottery($post_id,'probability');

    //取整数
    $sr = str_replace('.','',$probability);
    $sr = preg_replace('/^0*/', '', $sr);

    //取小数点后面的位数
    $m = zrz_getFloatLength($probability);

    //计算最大值
    $m = intval(pow(10,$m)/$sr);

    //生成随机数
    $rand1 = rand(1,$m);
    $rand2 = rand(1,$m);

    $picked = false;

    //如果两次随机数相等，则代表中奖
    if($rand1 == $rand2){
        $picked = true;

        //商品销售数量增加
        zrz_update_shop_count($post_id,1);

        //记录已经中奖的用户
        zrz_update_shop_buy_user($user_id,$post_id);

        //生成订单号
        $order_id = zrz_build_order_no();
        //地址
	    $add = zrz_get_default_address($user_id);

        //是虚拟物品，还是实物？
        $commodity = get_post_meta($post_id, 'zrz_shop_commodity', true);
        $state = $commodity ? 'f' : 'q';
        $ordre = new Zrz_order_Message($user_id,$post_id,$order_id,'c',$commodity,1,$lottery_credit,$state,'',$add);
        $resout = $ordre->add_data();
    }

    //添加消息
    $init = new Zrz_Credit_Message($user_id,29);
    $add_msg = $init->add_message(1,-$lottery_credit,$post_id,$picked ? 'z' : 'm');

    if($picked && $add_msg){
        print json_encode(array('status'=>200,'msg'=>__('中奖啦','ziranzhi2'),'rand1'=>$rand1,'rand2'=>$rand2,'m'=>$m));
        exit;
    }

    print json_encode(array('status'=>403,'msg'=>__('没有中奖','ziranzhi2'),'rand1'=>$rand1,'rand2'=>$rand2,'m'=>$m));
    exit;
}

//记录已经购买或者兑换或者抽奖的用户
function zrz_update_shop_buy_user($user_id,$post_id){

    $buy_user = get_post_meta($post_id,'zrz_buy_user',true);
    $buy_user = is_array($buy_user) ? $buy_user : array();
    if(!in_array($user_id,$buy_user)){
        array_push($buy_user,$user_id);
        update_post_meta($post_id,'zrz_buy_user',$buy_user);
    }
}
//delete_post_meta(1028,'zrz_buy_user');
function zrz_update_shop_count($post_id,$count){
    $count_all = get_post_meta($post_id,'zrz_shop_count',true);

    //检查剩余数量
    $remaining = $count_all['total'] - $count_all['sell'];
    if($remaining >= $count){
        $count_all['sell'] = (int)$count_all['sell'] + $count;

        if($count_all['sell'] >= $count_all['total']){
            $count_all['sold_out'] = 0;
        }else{
            $count_all['sold_out'] = 1;
        }
        update_post_meta($post_id,'zrz_shop_count',$count_all);
        return 1;
    }else{
        return 0;
    }
}

//获取小数点后面的位数
function zrz_getFloatLength($num) {
    $count = 0;
    $temp = explode ( '.', $num );
    if (sizeof ( $temp ) > 1) {
        $decimal = end ( $temp );
        $count = strlen ( $decimal );
    }

    return $count;
}

//商品支付成功，回调函数。 $pay_order_id : 支付订单号，$total_amount : 支付的金额
function zrz_notify_data_update($pay_order_id,$total_amount){

    $order = zrz_check_order_state($pay_order_id);

    if($order){
        $order_id = $order->id;
        $new_order_id = $pay_order_id;
        $pay_order_id = $order->order_key;
        $order_content = $order->order_content;
    }else{
        return false;
    }

    $order = explode("-",$pay_order_id);

    //活动付费
    if(strpos($pay_order_id,'pay_activity-') !== false){
        $user_id = $order[3];

        $paydata = get_user_meta($user_id,'zrz_ds_resout',true);
        if(!isset($paydata['more'])) return false;
        $post_id = $order[1];

        if(get_post_type($post_id) != 'activity') return false;
        
        //更新订单
		$ordre = new Zrz_order_Message();
        $resout = $ordre->update_orders_data(array($order_id),array(
            'user_id'=>$user_id,
            'post_id'=>$post_id,
            'order_id'=>$new_order_id,
            'order_type'=>'w',
            'order_commodity'=>0,
            'order_count'=>1,
            'order_price'=>$total_amount,
            'order_state'=>'q',
            'order_key'=>'',
            'order_value'=>$paydata['name'].'-'.$paydata['number'].'-'.($paydata['sex'] == 1 ? '男' : '女').'-'.$paydata['more'],
        ));

        if($resout){
            //记录活动用户
            $users = get_post_meta($post_id,'zrz_activity_users',true);
            $users = is_array($users) ? $users : array();
            $users[$user_id] = array(
                'name'=>$paydata['name'],
                'number'=>$paydata['number'],
                'sex'=>$paydata['sex'],
                'more'=>$paydata['more'],
                'time'=>current_time( 'mysql' )
            );

            update_post_meta($post_id,'zrz_activity_users',$users);

            //给活动发起人通知
            $post_author = get_post_field('post_author',$post_id);
            $init = new Zrz_Credit_Message((int)$post_author,44);
            $add_msg = $init->add_message((int)$user_id, 'empty',$post_id,'');

            //给活动发起人增加余额
            $rmb = get_user_meta($post_author,'zrz_rmb',true);
            $rmb = $rmb ? $rmb : 0;
            update_user_meta($post_author,'zrz_rmb',$rmb + $total_amount);

            //报名人数加1
            $count = get_post_meta($post_id, 'zrz_activity_applicants_count', true);
            $count = is_numeric($count) ? $count : 0;
            update_post_meta($post_id, 'zrz_activity_applicants_count', $count+1);

            //回调成功
            update_user_meta($user_id,'zrz_ds_resout','success');

            do_action('zrz_shop_buy_action', 'activity',$user_id,$post_id);

            return true;
        }
        return false;
        

    //购买会员回调
    }elseif(strpos($pay_order_id,'pay_vip-') !== false){
		$user_id = $order[3];

		$lv = get_user_meta($user_id,'zrz_ds_resout',true);
        if(!isset($lv['vip'])) return false;

		$lv = $lv['vip'];
        
		$lv_setting = zrz_get_lv_settings($lv);

        //更新订单
		$ordre = new Zrz_order_Message();
        $resout = $ordre->update_orders_data(array($order_id),array(
            'user_id'=>$user_id,
            'order_id'=>$new_order_id,
            'order_type'=>'vip',
            'order_commodity'=>0,
            'order_count'=>1,
            'order_price'=>$total_amount,
            'order_state'=>'q',
            'order_key'=>'',
            'order_value'=>'',
        ));

		if($resout){

            $c_lv = get_user_meta($user_id,'zrz_lv',true);

			update_user_meta($user_id,'zrz_lv',$lv);
            
			//回调成功
			update_user_meta($user_id,'zrz_ds_resout','success');

			//记录购买时间和到期时间
			update_user_meta($user_id,'zrz_vip_time',array(
				'start'=>date('Y-m-d H:i:s',time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )),
                'end'=>$lv_setting['time'] == 0 ? 0 : date("Y-m-d H:i:s", get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + strtotime('+'.$lv_setting['time'].' day')),
                'lv'=>$lv,
                'oldlv'=>$c_lv
            ));

            do_action('zrz_shop_buy_action', 'vip',array('user_id'=>$user_id,'lv'=>$lv),$total_amount);

            return true;
		}
		return false;
	    //积分购买回调
        }elseif(strpos($pay_order_id,'pay_credit-') !== false){
            $user_id = $order[3];

    		$rmb = get_user_meta($user_id,'zrz_ds_resout',true);
            if(!isset($rmb['rmb'])) return false;

            //更新订单
            $ordre = new Zrz_order_Message();
            $resout = $ordre->update_orders_data(array($order_id),array(
                'user_id'=>$user_id,
                'order_id'=>$new_order_id,
                'order_type'=>'cg',
                'order_commodity'=>0,
                'order_count'=>1,
                'order_price'=>$total_amount,
                'order_state'=>'q',
                'order_key'=>'',
                'order_value'=>0
            ));

    		if($resout){

                $exchange = zrz_get_credit_settings('zrz_credit_rmb');

                //添加消息
                $init = new Zrz_Credit_Message($user_id,38);
                $add_msg = $init->add_message($user_id, $total_amount*$exchange,0,'');

    			//回调成功
    			update_user_meta($user_id,'zrz_ds_resout','success');

                do_action('zrz_shop_buy_action', 'credit',$user_id,$total_amount);

                return true;
    		}
    		return false;

        //充值回调
        }elseif(strpos($pay_order_id,'pay_cz-') !== false){
            
        $user_id = $order[3];
        $res = get_user_meta($user_id,'zrz_ds_resout',true);
        if(!isset($res['text'])) return false;

        //获取当前用户余额
        $rmb = get_user_meta($user_id,'zrz_rmb',true);
        $rmb = $rmb ? $rmb : 0;
        $rmb = $rmb + $total_amount;
        update_user_meta($user_id,'zrz_rmb',$rmb);

        //标记支付成功
        update_user_meta($user_id,'zrz_ds_resout','success');

        //更新订单
        $ordre = new Zrz_order_Message();
        $resout = $ordre->update_orders_data(array($order_id),array(
            'user_id'=>$user_id,
            'order_id'=>$new_order_id,
            'order_type'=>'cz',
            'order_commodity'=>0,
            'order_count'=>1,
            'order_price'=>$total_amount,
            'order_state'=>'q',
            'order_key'=>'',
            'order_value'=>'',
        ));

        do_action('zrz_shop_buy_action', 'chongzhi',$user_id,$total_amount);

        return true;
    }elseif(strpos($pay_order_id,'pay_ds-') !== false){
        //打赏回调
        $post_id = $order[1];
        $post_author = get_post_field('post_author',$post_id);
        $user_id = (int)$order[3];

        //校验支付数据
        $text = get_user_meta($user_id,'zrz_ds_resout',true);
        if(!isset($text['text'])) return false;

        //更新作者打赏金额
        $author_rmb = get_user_meta($post_author,'zrz_rmb',true);
        $author_rmb = $author_rmb ? $author_rmb : 0;
        $author_rmb = $author_rmb + $total_amount;
        update_user_meta($post_author,'zrz_rmb',$author_rmb);

        //记录打赏的人
        $shang = get_post_meta($post_id,'zrz_shang',true);
        $shang = is_array($shang) ? $shang : array();
        $shang[] = array('user'=>$user_id,'rmb'=>$total_amount);

        update_post_meta($post_id,'zrz_shang',$shang);

        //更新订单
        $ordre = new Zrz_order_Message();
        $resout = $ordre->update_orders_data(array($order_id),array(
            'user_id'=>$user_id,
            'post_id'=>$post_id,
            'order_id'=>$new_order_id,
            'order_type'=>'ds',
            'order_commodity'=>0,
            'order_count'=>1,
            'order_price'=>$total_amount,
            'order_state'=>'q',
            'order_key'=>'',
            'order_value'=>'',
        ));

        $msg = array(
            'user'=>$user_id,
            'price'=>$total_amount,
            'text'=>$text['text']
        );

        //给作者通知
        $init = new Zrz_Credit_Message($post_author,35);
        $add_msg = $init->add_message($user_id, 0,$post_id,serialize($msg));

        update_user_meta($user_id,'zrz_ds_resout','success');

        do_action('zrz_shop_buy_action', 'dashang',array('author'=>$post_author,'user_id'=>$user_id,'rmb'=>$total_amount),$post_id);

        return 'success';
    }

    $user_id = (int)$order[3];

    $res = get_user_meta($user_id,'zrz_ds_resout',true);
    if($res != 'begin') return false;

    //获取临时订单数据
    $order_data_total = get_user_meta($user_id,'zrz_orders',true);

    if(isset($order_data_total['order']) && $order_data_total['order'] == $pay_order_id){

        $order_data = $order_data_total;
        $ids = $order_data['ids'];
        $toatl_price = 0;

        //如果是文章支付
        if($order_data['type'] == 'post'){
            foreach ($ids as $val) {
                $cap = get_post_meta($val['id'],'capabilities',true);
                if(isset($cap['key']) && isset($cap['val']) && $cap['key'] == 'rmb'){
                    $price = $cap['val'];
                    $toatl_price += $price;

                    //更新订单
                    $ordre = new Zrz_order_Message();
                    $resout = $ordre->update_orders_data(array($order_id),array(
                        'user_id'=>$user_id,
                        'post_id'=>$val['id'],
                        'order_id'=>$new_order_id,
                        'order_type'=>'w',
                        'order_commodity'=>0,
                        'order_count'=>1,
                        'order_price'=>$price,
                        'order_state'=>'q',
                        'order_key'=>'',
                        'order_value'=>''
                    ));

                    if($resout){

                        //记录已经购买的用户
                        zrz_update_shop_buy_user($user_id,$val['id']);

                        $post_author = get_post_field('post_author',$val['id']);

                        //给购买者添加消息
                        $init = new Zrz_Credit_Message($user_id,31);
                        $add_msg = $init->add_message($post_author, 0,$val['id'],$cap['val']);

                        //给文章作者支付金额
                        $post_author_rmb = get_user_meta($post_author,'zrz_rmb',true);
                        $post_author_rmb = $post_author_rmb ? $post_author_rmb : 0;
                        $post_author_rmb = $post_author_rmb + $cap['val'];
                        update_user_meta($post_author,'zrz_rmb',$post_author_rmb);

                        //给文章作者添加消息
                        $init = new Zrz_Credit_Message($post_author,32);
                        $add_msg = $init->add_message($user_id, 0,$val['id'],$cap['val']);

                        //增加钩子
                        do_action('zrz_shop_buy_action','post',$user_id,$val['id']);
                    }
                }
            }

        }elseif($order_data['type'] == 'shop'){
            //如果是商品购买
            //获取邮寄地址
            $add = zrz_get_default_address($user_id);
            $i = 0;
            foreach ($ids as $val) {
                $i++;
                $price = zrz_get_shop_price_dom($val['id'],$user_id);

                $toatl_price += $price['price'];

                //是虚拟物品，还是实物？
                $commodity = get_post_meta($val['id'], 'zrz_shop_commodity', true);
                $state = $commodity ? 'f' : 'q';

                //写入订单数据
                $new_pay_order_id = $new_order_id.'-'.$i;
                $ordre = new Zrz_order_Message($user_id,$val['id'],$new_pay_order_id,'g',$commodity,$val['count'],$price['price'],$state,'',$add,$order_content);

                $resout = $ordre->add_data();

                //删除临时订单数据
                global $wpdb;
                $table_name = $wpdb->prefix . 'zrz_order';
                $wpdb->delete( $table_name, array( 'order_id' => $new_order_id ));

                if($resout){
                    //商品销售数量增加
                    zrz_update_shop_count($val['id'],$val['count']);

                    //记录已经购买的用户
                    zrz_update_shop_buy_user($user_id,$val['id']);

                    //添加消息
                    $init = new Zrz_Credit_Message($user_id,30);
                    $add_msg = $init->add_message('', zrz_get_shop_price($val['id'],'credit'),$val['id'],$val['count']);

                    //增加钩子
                    do_action('zrz_shop_buy_action','shop',$user_id,$val['id']);
                }
            }

        }

        $order_data_total['payed'] = 1;
        update_user_meta($user_id,'zrz_orders',$order_data_total);
        //标记已经回调成功
        update_user_meta($user_id,'zrz_ds_resout','success');
        return true;
    }
    return false;
}

//积分兑换
function zrz_shop_exchange($post_id,$count,$order_content = ''){

	//检查商品类型
	$shop_type = get_post_meta($post_id,'zrz_shop_type',true);
	if($shop_type != 'exchange') {
		return array(
			'postid'=>$post_id,
			'status'=>401,
			'msg'=>__('商品类型错误','ziranzhi2')
		);
	};

	//检查商品数量
	if(zrz_shop_count_remaining($post_id) < $count ){
		return array(
			'postid'=>$post_id,
			'status'=>401,
			'msg'=>__('商品数量不足','ziranzhi2')
		);
	}

	//检查当前用户的积分
	$user_id = get_current_user_id();
	$credit = (int)zrz_coin($user_id,'nub');
	$change_credit = (int)get_post_meta($post_id,'zrz_shop_need_credit',true);

	//如果积分不足，则禁止兑换
	if($credit < ($change_credit * $count)) {
		return array(
			'postid'=>$post_id,
			'status'=>401,
			'msg'=>__('积分不足','ziranzhi2')
		);
	};

	//生成订单号
	$order_id = zrz_build_order_no();

	//地址
	$add = zrz_get_default_address($user_id);

	//是虚拟物品，还是实物？
	$commodity = get_post_meta($post_id, 'zrz_shop_commodity', true);
	$state = $commodity ? 'f' : 'q';

	$ordre = new Zrz_order_Message($user_id,$post_id,$order_id,'d',$commodity,$count,$change_credit,$state,'',$add,$order_content);

	$resout = $ordre->add_data();

	if($resout){
		//商品销售数量增加
		zrz_update_shop_count($post_id,$count);

		//记录已经购买的用户
		zrz_update_shop_buy_user($user_id,$post_id);

		//添加消息
		$init = new Zrz_Credit_Message($user_id,28);
		$add_msg = $init->add_message(1,-$change_credit*$count,$post_id,$count);
		if($add_msg){

            do_action('zrz_shop_buy_action','duihuan',$user_id,$post_id);

			return array(
				'postid'=>$post_id,
				'status'=>200,
				'msg'=>__('兑换成功','ziranzhi2'),
			);
		}
	}

	return array(
		'postid'=>$post_id,
		'status'=>401,
		'msg'=>__('兑换失败','ziranzhi2')
	);
}

//使用帐号余额支付付费文章
add_action('wp_ajax_zrz_post_pay_with_balance', 'zrz_post_pay_with_balance');
function zrz_post_pay_with_balance(){
    if(!is_user_logged_in()){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $post_id = isset($_POST['pid']) ? $_POST['pid'] : 0;

    if(!$post_id){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $user_id = get_current_user_id();

    //获取用户余额
    $balance = get_user_meta($user_id,'zrz_rmb',true);
    $balance = $balance ? $balance : 0;
    if(!$balance){
        print json_encode(array('status'=>401,'msg'=>__('余额不足','ziranzhi2')));
        exit;
    }

    //生成订单号
    $id_arr = array();
    $order_id = 'pay_post-'.$post_id.'-'.str_shuffle(uniqid()).'-'.$user_id;

    //检查文章所需的金额
    $cap = get_post_meta($post_id,'capabilities',true);

    //检查文章类型
    if(isset($cap['key']) && isset($cap['val']) && $cap['key'] == 'rmb'){
        $toatl_price = $cap['val'];
        //支付订单号
        $id = array(
            'id'=>$post_id,
            'count'=>1
        );
        array_push($id_arr,$id);
    }else{
        print json_encode(array('status'=>401,'msg'=>__('商品类型错误','ziranzhi2')));
        exit;
    }

    if($balance < $toatl_price){
        print json_encode(array('status'=>401,'msg'=>__('余额不足','ziranzhi2')));
        exit;
    }

    //删除订单信息
    delete_user_meta($user_id,'zrz_orders');

    //删除订单状态
    delete_user_meta($user_id,'zrz_ds_resout');

    $user_order = array(
        'order'=>$order_id,
        'ids'=>$id_arr,//商品ID
        'total_price'=>$toatl_price,//支付金额
        'balance'=>0,//是否使用余额
        'payed'=>0,
        'type'=>'post'
    );

    //设置一个临时数据，回调的时候检查
    update_user_meta($user_id,'zrz_orders',$user_order);

    //先付费阅读信息打赏信息标记
    update_user_meta($user_id,'zrz_ds_resout','begin');

    $c_order_id = zrz_build_order_no();

    //生成一个临时的订单
    $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
    $resout = $ordre->add_data();

    $resout = zrz_notify_data_update($c_order_id,$toatl_price);

    if($resout){
        //当前用户减去积分
        update_user_meta($user_id,'zrz_rmb',$balance-$toatl_price);
        print json_encode(array('status'=>200,'msg'=>__('支付成功','ziranzhi2')));
        exit;
    }

    print json_encode(array('status'=>401,'msg'=>__('余额不足','ziranzhi2')));
    exit;
}

//账户余额购买积分
add_action('wp_ajax_zrz_rmb_to_credit','zrz_rmb_to_credit');
function zrz_rmb_to_credit(){
    $rmb = isset($_POST['rmb']) ? $_POST['rmb'] : 0;
    if(!$rmb){
        print json_encode(array('status'=>401,'msg'=>__('请输入金额','ziranzhi2')));
        exit;
    }

    $user_id = get_current_user_id();
    $c_rmb = get_user_meta($user_id,'zrz_rmb',true);
    $c_rmb = $c_rmb ? $c_rmb : 0;

    if($c_rmb < $rmb){
        print json_encode(array('status'=>401,'msg'=>__('余额不足，请先充值','ziranzhi2')));
        exit;
    }

    $order_id = 'pay_credit-0-'.str_shuffle(uniqid()).'-'.$user_id;

	//先清除支付信息
    delete_user_meta($user_id,'zrz_ds_resout');
	update_user_meta($user_id,'zrz_ds_resout',array('rmb'=>$rmb));

    $c_order_id = zrz_build_order_no();

    //生成一个临时的订单
    $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
    $resout = $ordre->add_data();

    $resout = zrz_notify_data_update($c_order_id,$rmb);

    if($resout){
        update_user_meta($user_id,'zrz_rmb',$c_rmb - $rmb);
        print json_encode(array('status'=>200,'msg'=>__('购买成功','ziranzhi2')));
        exit;
    }

    print json_encode(array('status'=>401,'msg'=>__('购买失败','ziranzhi2')));
    exit;
}

//使用帐号余额支付商品
add_action('wp_ajax_zrz_pay_with_balance', 'zrz_pay_with_balance');
function zrz_pay_with_balance(){

    if(!is_user_logged_in()){
        print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
    }

    $order_content = isset($_POST['orderContent']) ? esc_sql(sanitize_text_field($_POST['orderContent'])) : '';
   
    //计算总价
    $data = isset($_POST['data']) ? $_POST['data'] : '';

    $user_id = get_current_user_id();

    $toatl_price = 0;
    $ye = get_user_meta($user_id,'zrz_rmb',true);
    $ye = $ye ? $ye : 0;

    $id_arr = array();
    $ids = array();
    $msg = '';

    foreach ($data as $val) {
        $post_id = number($val['pid']);

        //检查商品类型
        $type = get_post_meta($post_id, 'zrz_shop_type', true);
        if($type != 'normal'){
            $msg .= '<p><a href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a> 不是要出售的商品。</p>';
            break;
        }

        //检查商品剩余的数量
        $remaining = (int)zrz_shop_count_remaining($post_id);
        if($remaining - (int)$value['count'] < 0){
            $msg .= '<p><a href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a> 剩余数量不足，请修改订单。</p>';
            break;
        }

        $price = zrz_get_shop_price_dom($post_id);
        $price = $price['price']*$val['count'];
        $toatl_price += $price;
        $id = array(
            'id'=>$post_id,
            'count'=>$val['count']
        );
        array_push($id_arr,$id);
        $ids[] = $post_id;
    }

    if($msg){
        print json_encode(array('status'=>401,'msg'=>$msg));
        exit;
    }

    if($ye < $toatl_price){
        print json_encode(array('status'=>401,'msg'=>__('金额不足','ziranzhi2')));
        exit;
    }

    $order_id = 'pay_shop-1-'.str_shuffle(uniqid()).'-'.$user_id;

    //保存订单信息
    delete_user_meta($user_id,'zrz_orders');
    $user_order = array(
        'order'=>$order_id,
        'ids'=>$id_arr,//商品ID
        'total_price'=>$toatl_price,//支付金额
        'balance'=>0,//是否使用余额
        'payed'=>0,
        'type'=>'shop'
    );

    //添加一个标记
    update_user_meta($user_id,'zrz_ds_resout','begin');

    //设置一个临时数据，回调的时候检查
    update_user_meta($user_id,'zrz_orders',$user_order);

    $c_order_id = zrz_build_order_no();

    //生成一个临时的订单
    $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id,'',$order_content);
    $resout = $ordre->add_data();

    $resout = zrz_notify_data_update($c_order_id,$toatl_price);

    if($resout){
        update_user_meta($user_id,'zrz_rmb',$ye-$toatl_price);
        print json_encode(array('status'=>200,'msg'=>$ids));
        exit;
    }
    print json_encode(array('status'=>401,'msg'=>'支付失败'));
    exit;
}

//使用余额打赏
add_action('wp_ajax_zrz_ds_with_balance', 'zrz_ds_with_balance');
function zrz_ds_with_balance(){

    $data =  isset($_POST['data']) ? $_POST['data'] : 0;

    $post_id = isset($data['post_id']) && !empty($data['post_id']) ? $data['post_id'] : '';
    $total_price = isset($data['price']) && !empty($data['price']) ? $data['price'] : '';
    $text = isset($data['text']) ? $data['text'] : 0;

    if(!$total_price || !$post_id){
        print json_encode(array('status'=>401,'msg'=>'非法参数'));
        exit;
    }

    $user_id = get_current_user_id();
    $author_id = get_post_field('post_author',$post_id);

    $balance = get_user_meta($user_id,'zrz_rmb',true);
    $balance = $balance ? $balance : 0;

    if($total_price <= 0 || (float)$total_price > (float)$balance){
        print json_encode(array('status'=>401,'msg'=>'余额不足'));
        exit;
    }

    //先清除打赏信息
    delete_user_meta($user_id,'zrz_ds_resout');

    //记录留言信息
    update_user_meta($user_id,'zrz_ds_resout',array('text'=>$text));

    $order_id = 'pay_ds-'.$post_id.'-'.str_shuffle(uniqid()).'-'.$user_id;

    $c_order_id = zrz_build_order_no();

    //生成一个临时的订单
    $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
    $resout = $ordre->add_data();

    $resout = zrz_notify_data_update($c_order_id,$total_price);

    if($resout == 'success'){
        update_user_meta($user_id,'zrz_rmb',$balance-$total_price);
        print json_encode(array('status'=>200,'msg'=>'打赏成功'));
        exit;
    }
}

//使用余额购买会员
add_action('wp_ajax_zrz_buy_vip_by_balance', 'zrz_buy_vip_by_balance');
function zrz_buy_vip_by_balance(){
	$lv = isset($_POST['lv']) ? $_POST['lv'] : '';
	$user_id = get_current_user_id();

	if($lv != 'vip' && $lv != 'vip1' && $lv != 'vip2' && $lv != 'vip3'){
		print json_encode(array('status'=>401,'msg'=>__('非法操作','ziranzhi2')));
        exit;
	}

    $lv_setting = zrz_get_lv_settings($lv);
	$price = $lv_setting['price'];

    //检查余额是否充足
    $rmb = get_user_meta($user_id,'zrz_rmb',true);
    $rmb = $rmb ? $rmb : 0;

    //如果余额不足，结束支付
    if($rmb < $price){
        print json_encode(array('status'=>401,'msg'=>__('余额不足','ziranzhi2')));
        exit;
    }

	//先清除支付信息
    delete_user_meta($user_id,'zrz_ds_resout');
	update_user_meta($user_id,'zrz_ds_resout',array('vip'=>$lv));
    
	$order_id = 'pay_vip-0-'.str_shuffle(uniqid()).'-'.$user_id;

    $c_order_id = zrz_build_order_no();

    //生成一个临时的订单
    $ordre = new Zrz_order_Message($user_id,'0',$c_order_id,'',0,1,0,'w',$order_id);
    $resout = $ordre->add_data();

    $resout = zrz_notify_data_update($c_order_id,$price);

    if($resout){
        update_user_meta($user_id,'zrz_rmb',$rmb-$price);
        print json_encode(array('status'=>200,'msg'=>'购买成功','user_id'=>$user_id));
        exit;
    }

    print json_encode(array('status'=>401,'msg'=>__('购买失败','ziranzhi2')));
    exit;
}

//提现申请
add_action('wp_ajax_zrz_tx_application', 'zrz_tx_application');
function zrz_tx_application(){
    $price = isset($_POST['price']) ? (float)$_POST['price'] : '';
    $cprice = isset($_POST['cprice']) ? (float)$_POST['cprice'] : '';
    $user_id = get_current_user_id();

    //检查提现额度是否允许
    $rmb = (float)get_user_meta($user_id,'zrz_rmb',true);
    $rmb = $rmb ? $rmb : 0;
    if($price > $rmb || $cprice > $rmb){
        print json_encode(array('status'=>401,'msg'=>__('非法参数','ziranzhi2')));
        exit;
    }

    //检查是否有未完成的提现
    $tx_allowed = zrz_get_credit_settings('zrz_tx_allowed');

    $all_sql = "msg_key=0 AND msg_users=$user_id AND msg_type=41";
    $init = new Zrz_Credit_Message($tx_allowed);
    $count = $init->get_message(true,$all_sql);

    if($count > 0){
        print json_encode(array('status'=>401,'msg'=>__('还有未完成的提现申请，完成以后方可再申请。','ziranzhi2')));
        exit;
    }

    //审核人
    $admin = zrz_get_credit_settings('zrz_tx_admin');
    $admin = is_numeric($admin) ? $admin : 1;

    //添加消息
    $init = new Zrz_Credit_Message($admin,41);
    $add_msg = $init->add_message($user_id, 0,0,$cprice);

    if($add_msg){
        update_user_meta($user_id,'zrz_rmb',round($rmb-$price,2));
        update_user_meta($user_id,'zrz_tx_rmb',$cprice);
        print json_encode(array('status'=>200,'msg'=>__('申请成功','ziranzhi2')));
        exit;
    }else{
        print json_encode(array('status'=>401,'msg'=>__('申请失败','ziranzhi2')));
        exit;
    }
}

//提现完成
add_action('wp_ajax_zrz_withdraw_payed', 'zrz_withdraw_payed');
function zrz_withdraw_payed(){
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if(!$id){
        print json_encode(array('status'=>401,'msg'=>__('参数无效','ziranzhi2')));
        exit;
    }

    $current_user_id = get_current_user_id();
    $admin = zrz_get_credit_settings('zrz_tx_admin');

    //检查权限
    if($current_user_id != $admin){
        print json_encode(array('status'=>401,'msg'=>__('你没有权限这么做','ziranzhi2')));
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'zrz_message';

    $resout = $wpdb->update(
        $table_name,
        array('msg_key'=>1),
        array('msg_id'=>$id)
    );
    if($resout){
        print json_encode(array('status'=>200));
        exit;
    }
    print json_encode(array('status'=>401));
    exit;
}

//生成订单号
function zrz_build_order_no() {
    $year_code = array('A','B','C','D','E','F','G','H','I','J');
    $order_sn = $year_code[intval(date('Y'))-2010].
    strtoupper(dechex(date('m'))).date('d').
    substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
    return $order_sn;
}
