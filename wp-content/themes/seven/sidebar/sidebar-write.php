<?php
/*
* 撰写文章，侧边栏
*/
?>
<section id="pages-2" class="widget widget_write box mar10-b">
    <h2 class="widget-title l1 pd10 box-header">提示</h2>
    <div class="pd10 b-t">
        <ul>
            <li>
                <b>尊重原创</b>
                <p>请不要在 <?php echo get_bloginfo('name'); ?> 发布任何盗版下载链接，包括软件、音乐、电影等等。我们尊重原创。</p>
            </li>
            <li>
                <b>友好互助</b>
                <p>您的文章将会有成千上万人阅读，保持对陌生人的友善，用知识去帮助别人也是一种快乐。</p>
            </li>
            <li>
                <b>阅读权限设置</b>
                <p>如果您使用了权限阅读的功能，请确保隐藏的内容被 <code>[content_hide]</code> 和 <code>[/content_hide]</code> 两个短代码包裹其中。权限阅读功能对一篇文章所有隐藏内容有效。</p>
            </li>
            <li>
                <p class="bg-blue-light pd10">您的文章发布之后，可以在 <span class="red"><?php echo zrz_get_writing_settings('edit_time'); ?>小时</span> 之内重新编辑</p>
            </li>
            <li>
                <p class="bg-blue-light pd10">正文字数限制在 <span class="red"><?php echo zrz_get_writing_settings('min_strlen'); ?></span> 和 <span class="red"><?php echo zrz_get_writing_settings('max_strlen'); ?></span> 之间</p>
            </li>
        <ul>
    </div>
</section>
