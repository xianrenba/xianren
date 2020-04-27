<div class="shortcodes_control">
    <div>
        <label>
            <span><?php _e( '选择短代码', 'um'); ?></span></label>
        <select name="items" class="shortcode_sel" size="1" onchange="document.getElementById('items_accumulated').value = this.options[selectedIndex].value;">
            <option class="parentscat">
                <?php _e( '1.提示框短代码', 'um'); ?>
            </option>
            <option value="[v_notice]绿色通知[/v_notice]">
                <?php _e( '绿色通知', 'um'); ?>
            </option>
            <option value="[v_error]红色警告[/v_error]">
                <?php _e( '红色警告', 'um'); ?>
            </option>
            <option value="[v_warn]黄色错误[/v_warn]">
                <?php _e( '黄色错误', 'um'); ?>
            </option>
            <option value="[v_tips]灰色提示[/v_tips]">
                <?php _e( '灰色提示', 'um'); ?>
            </option>
            <option value="[v_blue]蓝色提示[/v_blue]">
                <?php _e( '蓝色提示', 'um'); ?>
            </option>
            <option value="[v_act]蓝边文本[/v_act]">
                <?php _e( '蓝边文本', 'um'); ?>
            </option>
            <option value="[v_organge]橙色文本[/v_organge]">
                <?php _e( '橙色文本', 'um'); ?>
            </option>
            <option value="[v_qing]青色文本[/v_qing]">
                <?php _e( '青色文本', 'um'); ?>
            </option>
            <option value="[v_pink]粉色文本[/v_pink]">
                <?php _e( '粉色文本', 'um'); ?>
            </option>
            <option class="parentscat">
                <?php _e( '2.按钮短代码', 'um'); ?>
            </option>
            <option value="[dm href='链接']链接按钮[/dm]">
                <?php _e( '链接按钮', 'um'); ?>
            </option>
            <option value="[dl href='链接']下载按钮[/dl]">
                <?php _e( '下载按钮', 'um'); ?>
            </option>
            <option value="[gt href='链接']开源地址[/gt]">
                <?php _e( '开源按钮', 'um'); ?>
            </option>
            <option value="[gt href='链接']音乐按钮[/gt]">
                <?php _e( '音乐按钮', 'um'); ?>
            </option>
            <option value="[video href='链接']视频按钮[/video]">
                <?php _e( '视频按钮', 'um'); ?>
            </option>
            <option class="parentscat">
                <?php _e( '5.其它短代码', 'um'); ?>
            </option>
            <option value="[reply]回复可见内容[/reply]">
                <?php _e( '回复可见', 'um'); ?>
            </option>
            <option value="[buy product_id='产品ID']购买产品才能查看的内容[/buy]">
                <?php _e( '购买商品可见', 'um'); ?>
            </option>
            <option value="[collapse title='标题']折叠内容[/collapse]">
                <?php _e( '隐藏收缩', 'um'); ?>
            </option>
            <option value="[dltable file='文件名称' size='文件大小']文件下载A标签链接，可以放多个链接[/dltable]">
                <?php _e( '下载面板', 'um'); ?>
            </option>
        </select>
        <label>
            <?php _e( '简码预览', 'um'); ?><br><span><?php _e('注：复制短代码到编辑器中，修改成自己的内容。','um'); ?></span>
        </label>
        <p>
            <textarea id="items_accumulated" name="items_accumulated" rows="4"></textarea>
        </p>
    </div>
</div>