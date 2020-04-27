<?php
/*
* 财富排行，侧边栏
*/
$name = zrz_get_credit_settings('zrz_credit_name');
$display = zrz_get_credit_settings('zrz_credit_display');
?>
<section id="pages-2" class="widget widget_write box mar10-b">
    <h2 class="widget-title l1 pd10 box-header">提示</h2>
    <div class="pd10 b-t">
        <ul>
            <li>
                <b>排名规则</b>
                <p>根据现有<?php echo $name; ?>数据排名，取前15名。</p>
            </li>
            <li>
                <b>如何获取<?php echo $name; ?>？</b>
                <p>1、您可以参与本站的互动获得<?php echo $name; ?>奖励，比如评论，投稿，发帖，或者冒泡等</p>
                <p>2、<b>1元</b>人民币兑换<b><?php echo zrz_get_credit_settings('zrz_credit_rmb'); ?></b><?php echo $name; ?></p>
            </li>
            <li><a class="button empty" href="<?php echo home_url('/gold'); ?>">积分兑换</a></li>
        <ul>
    </div>
</section>
