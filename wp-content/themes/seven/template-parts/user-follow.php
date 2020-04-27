<?php
/*关注*/
?>
<div id="user-follow">
    <div class="box-header pd10 b-b" ref="listFollow" data-type="zrz_follow"><span v-text="uName"></span>关注的人</div>
    <loading :ac="ac" :msg="msg" v-if="!ac"></loading>
    <template v-else-if="list.length > 0">
        <div class="follow-box">
            <div class="pos-r clearfix user-follow-list" v-for="item in list">
                <template v-if="item.follow == 1">
                    <div class="follow-avatar"><a :href="item.url" v-html="item.avatar"></a></div>
                    <div class="follow-info">
                        <h2 class="mar5-b"><a :href="item.url" v-html="item.name"></a><template v-html="item.avatar"></template></h2>
                        <p class="mar5-b user-des" v-html="item.des"></p>
                        <p class="user-count"><span>{{item.post_count}} 文章</span><?php echo ZRZ_THEME_DOT; ?><span>{{item.topic_count}} 话题</span><?php echo ZRZ_THEME_DOT; ?><span>{{item.reply_count}} 跟帖</span></p>
                    </div>
                    <button :class="'pos-a list-follow-button '+(item.follow ? '' : 'empty')" @click="cancel(item.id)" v-if="self == 1">
                        <span class="follow-ac" v-if="item.follow"><b class="show-follow">已关注</b><b class="follow-hide">取消关注</b></span>
                        <span v-else>关注</span>
                    </button>
                </template>
            </div>
        </div>
        <page-nav class="b-t" :nav-type="'userFollow'" :paged="paged" :pages="pages" :locked-nav="1"></page-nav>
    </template>
    <div class="loading-dom pos-r" v-else v-cloak><div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有关注任何人</p></div></div>
</div>
