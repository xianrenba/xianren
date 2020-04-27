<?php
/*
* 财富排行，侧边栏
*/
$setting = zrz_get_display_settings('delete_msg');
?>
<section id="pages-2" class="widget widget_write box mar10-b">
    <h2 class="widget-title l1 pd10 box-header">提示</h2>
    <div class="pd10 b-t">
        <ul>
            <li>
                <b>说明</b>
                <p>这里会显示您在本站的互动消息。</p>
                <p>参与互动，结交朋友，这是一件很棒的事情。</p>
            </li>
            <?php if($setting['msg_open']) {?>
                <li>
                    <b>过期消息清理时间</b>
                    <p>本站已经开启了过期消息清理机制</p>
                    <p>超过<?php echo $setting['msg_time']; ?>天的已读消息会自动清理。</p>
                </li>
            <?php } ?>
        </ul>
    </div>
</section>
