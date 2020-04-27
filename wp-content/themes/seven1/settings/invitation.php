<?php
function zrz_options_invitation_list_page(){
    global $wpdb;
    $order = 'ORDER BY id ASC';
    $table_name = $wpdb->prefix . 'zrz_invitation';
    $limit = 20;
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
    $request = http_build_query($_REQUEST);
    $request = $request ? '?'.$request : '';
    global $wp;
    $current_url = admin_url( '/admin.php'.$wp->request );
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
    <h1><?php _e('柒比贰主题邀请码设置','ziranzhi2');?></h1>
    <h2 class="title"><?php _e('邀请码管理','ziranzhi2');?></h2>
    <form method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'check-nonce' );?>">
        <?php
            zrz_admin_invitation_tabs('list');
            if(count($cards) > 0){
        ?>
        <p>&nbsp;</p>
        <table class="wp-list-table widefat fixed striped shop_page_order_option">
            <thead>
                <tr><td>编号</td><td>邀请码</td><td>奖励的积分</td><td>生成人</td><td>使用状态</td><td>使用者</td><td>操作</td></tr>
            </thead>
            <tbody>
                <?php
                    foreach ($cards as $val) {
                        if($val['invitation_user']){
                            $user = zrz_get_user_page_link($val['invitation_user']);
                        }else{
                            $user = '无';
                        }
                        $owner = zrz_get_user_page_link($val['invitation_owner']);
                        echo '<tr>
                        <td>'.$val['id'].'</td>
                        <td>'.$val['invitation_nub'].'</td>
                        <td>'.$val['invitation_credit'].'</td>
                        <td>'.$owner.'</td>
                        <td>'.($val['invitation_status'] ? '<span style="color:green">已使用</span>' : '<span style="color:red">未使用</span>').'</td>
                        <td>'.$user.'</td>
                        <td><a href="'.add_query_arg('del',$val['id'],$current_url.$request).'">删除</a></td>
                        </tr>';
                    }
                ?>
            </tbody>
        </table>
        <?php
            }else{
                echo '<p>暂无邀请码</p>';
            }
        ?>
    </form>
    <?php echo '<div class="pagenav clearfix">'.zrz_pagenavi(5,$data).'</div>' ?>
</div>
<?php
}