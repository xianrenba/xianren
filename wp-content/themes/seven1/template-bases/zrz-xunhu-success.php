<?php
//支付同步回调
get_header();
?>
<div id="primary" class="content-area mar10-b" style="width:100%">
    <main id="pay" class="site-main pay-page box pos-r" style="min-height:400px">
        <div class="lm t-c entry-content">
            <i class="iconfont zrz-icon-font-29" style="font-size:30px;display:block;color:green"></i><h1 class="mar10-t">付款成功</h1><p class="mar10-t"><a href="<?php echo zrz_get_user_page_url(get_current_user_id()).'/orders'; ?>">查看我的订单</a></p>
        </div>
    </main>
</div>
<?php
get_footer();
