<?php
/*
* 撰写文章，侧边栏
*/
$name = zrz_get_credit_settings('zrz_credit_name');
$display = zrz_get_credit_settings('zrz_credit_display');
$tx = zrz_get_credit_settings('zrz_tx_min');
$cc = (float)zrz_get_credit_settings('zrz_cc');
$tx_allowed = zrz_get_credit_settings('zrz_tx_allowed');
?>
<section id="pages-2" class="widget widget_write mar10-b">
    <h2 class="widget-title l1 pd10 box-header">提示</h2>
    <div class="box">
        <ul>
            <li>
                <b>如何获取<?php echo $name; ?>？</b>
                <p>1、您可以参与本站的互动获得<?php echo $name; ?>奖励，比如评论，投稿，发帖，或者冒泡等</p>
                <p>2、<b>1元</b>人民币可兑换<b><?php echo zrz_get_credit_settings('zrz_credit_rmb'); ?></b><?php echo $name; ?></p>
            </li>
            <?php if($display){ ?>
                <li>
                    <b>金银铜的换算比例如何？</b>
                    <p><?php echo $name; ?>采用100进制的换算方法。</p>
                    <p>1金 = 100银 = 10000铜</p>
                </li>
            <?php } ?>
            <?php if($tx_allowed == 1){ ?>
                <li>
                    <b>余额提现：</b>
                    <p>余额超过<code><?php echo $tx ? : 0; ?></code>元人民币即可申请提现<?php echo $cc > 0 ? '，网站收取<code>'.($cc*100).'%</code>服务费' : ''; ?>。</p>
                </li>
            <?php } ?>
        <ul>
    </div>
</section>
