<?php
/*收藏的商品*/
?>
<div id="user-follow">
    <div class="box-header pd10 b-b" ref="listFollow" data-type="shop"><span v-text="uName"></span>收藏的商品</div>
    <loading :ac="ac" :msg="msg" v-if="!ac"></loading>
    <template v-else-if="list.length > 0">
        <div class="collection-box" v-for="item in list">
            <div class="collection-box-in">
                <h2 v-html="item.title"></h2>
                <div class="collection-meta pos-r mar10-t">
                    <span v-html="item.time"></span><?php echo ZRZ_THEME_DOT; ?>
                    <span v-html="item.comment+'条评论'"></span><?php echo ZRZ_THEME_DOT; ?>
                    <span v-html="item.love+'个喜欢'"></span><?php echo ZRZ_THEME_DOT; ?>
                    <span v-html="item.view+'次浏览'"></span>
                </div>
            </div>
        </div>
        <page-nav class="b-t" :nav-type="'sp'" :paged="paged" :pages="pages" :locked-nav="1"></page-nav>
    </template>
    <div class="loading-dom pos-r" v-else v-cloak><div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有收藏商品</p></div></div>
</div>
