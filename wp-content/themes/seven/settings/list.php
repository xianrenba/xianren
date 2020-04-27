<?php
function zrz_options_card_list_page(){
    global $wpdb;
    $order = 'ORDER BY id ASC';
    $table_name = $wpdb->prefix . 'zrz_card';
    $limit = 10;
    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
    $offset = ($paged-1)*$limit;
    //删除订单
    if(isset($_GET['del'])){
        $wpdb->delete( $table_name, array( 'id' => $_GET['del'] ) );
    }
    $pages = $wpdb->get_var( "SELECT count(*) FROM $table_name");
    $cards = $wpdb->get_results( "SELECT * FROM $table_name $order LIMIT $offset,$limit" ,ARRAY_A );
    $data = array(
        'pages' => ceil($pages/$limit),
        'paged'=>$paged,
    );
    $current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<div class="wrap">
    <style>
        .clearfix:after,.nav-links:after {
          content: ".";
          display: block;
          height: 0;
          clear: both;
          visibility: hidden;
        }
        .clearfix,.nav-links {
          display: inline-block;
        }
        * html .clearfix,* html .nav-links {
          height: 1%;
        }
        .clearfix,.nav-links {
          display: block;
        }
        .pagenav{
            margin-top:20px
        }
        .btn-group{
            float:left
        }
        .btn-pager{
            float:right
        }
        .wp-core-ui .btn-group a{
            margin-right:10px
        }
        .wp-core-ui .btn-pager button{
            margin-left:10px
        }
        .bordernone{
            background: none;
            border:0;
            margin-right:10px
        }
    </style>
    <h1><?php _e('柒比贰主题卡密设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('卡密管理','ziranzhi2');?></h2>
    <form method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
        <?php
            zrz_admin_card_tabs('list');
            if(count($cards) > 0){
        ?>
        <p>&nbsp;</p>
        <table class="wp-list-table widefat fixed striped shop_page_order_option">
            <thead>
                <tr><td>编号</td><td>卡号</td><td>密码</td><td>面值</td><td>是否使用</td><td>使用者</td><td>操作</td></tr>
            </thead>
            <tbody>
                <?php
                    foreach ($cards as $val) {
                        if($val['card_user']){
                            $user = zrz_get_user_page_link($val['card_user']);
                        }else{
                            $user = '无';
                        }
                        echo '<tr>
                        <td>'.$val['id'].'</td>
                        <td>'.$val['card_key'].'</td>
                        <td>'.$val['card_value'].'</td>
                        <td>'.$val['card_rmb'].'</td>
                        <td>'.($val['card_status'] ? '<span style="color:green">已使用</span>' : '<span style="color:red">未使用</span>').'</td>
                        <td>'.$user.'</td>
                        <td><a href="'.$current_url.'&del='.$val['id'].'">删除</a></td>
                        </tr>';
                    }
                ?>
            </tbody>
        </table>
        <?php
            }else{
                echo '<p>暂无卡密</p>';
            }
        ?>
    </form>
    <?php echo '<div class="pagenav clearfix">'.zrz_pagenavi(5,$data).'</div>' ?>
</div>
<?php
}
