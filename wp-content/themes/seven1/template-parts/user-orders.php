<div id="user-order" class="user-order-content" ref="userOrders">
    <loading :ac="ac" :msg="msg" v-if="!ac"></loading>
    <div v-cloak v-else>
        <div class="box-header pd10 b-b"><?php _e('我的订单','ziranzhi2'); ?></div>
        <div class="user-order-flickr pd20 b-b clearfix">
            <button :class="['text',orderType == '' ? 'selected' : '']" @click.stop.prevent="forderType('')"><?php _e('全部','ziranzhi2'); ?><span>（{{count.total}}）</span></button>
            <button :class="['text',orderType == 'g' ? 'selected' : '']" @click.stop.prevent="forderType('g')"><?php _e('购买','ziranzhi2'); ?><span>（{{count.g}}）</span></button>
            <button :class="['text',orderType == 'd' ? 'selected' : '']" @click.stop.prevent="forderType('d')"><?php _e('兑换','ziranzhi2'); ?><span>（{{count.d}}）</span></button>
            <button :class="['text',orderType == 'c' ? 'selected' : '']" @click.stop.prevent="forderType('c')"><?php _e('抽奖','ziranzhi2'); ?><span>（{{count.c}}）</span></button>
            <button :class="['text',orderType == 'w' ? 'selected' : '']" @click.stop.prevent="forderType('w')"><?php _e('付费内容','ziranzhi2'); ?><span>（{{count.w}}）</span></button>
            <button :class="['text',orderType == 'ds' ? 'selected' : '']" @click.stop.prevent="forderType('ds')"><?php _e('打赏支出','ziranzhi2'); ?><span>（{{count.ds}}）</span></button>
            <button :class="['text',orderType == 'cz' ? 'selected' : '']" @click.stop.prevent="forderType('cz')"><?php _e('充值','ziranzhi2'); ?><span>（{{count.cz}}）</span></button>
            <div class="fr">
                <select name="order_state" v-model="orderState">
                    <option :selected="orderState == '' ? 'true' : false" value=""><?php _e('所有订单状态','ziranzhi2'); ?></option>
                    <option :selected="orderState == 'w' ? 'true' : false" value="w"><?php _e('等待付款','ziranzhi2'); ?></option>
                    <option :selected="orderState == 'f' ? 'true' : false" value="f"><?php _e('已付款未发货','ziranzhi2'); ?></option>
                    <option :selected="orderState == 'c' ? 'true' : false" value="c"><?php _e('已发货','ziranzhi2'); ?></option>
                    <option :selected="orderState == 'q' ? 'true' : false" value="q"><?php _e('已签收','ziranzhi2'); ?></option>
                    <option :selected="orderState == 't' ? 'true' : false" value="t"><?php _e('已退款','ziranzhi2'); ?></option>
                </select>
            </div>
        </div>
        <div class="user-order">
            <div class="l-8 user-order-header">
                <div class="fd order-name l0 t-c">
                    <?php _e('商品信息','ziranzhi2'); ?>
                </div>
                <div class="order-id l0 fd t-c">
                    <?php _e('订单号','ziranzhi2'); ?>
                </div>
                <div class="fd order-price l0 t-c">
                    <?php _e('单价','ziranzhi2'); ?>
                </div>
                <div class="fd order-count l0 t-c">
                    <?php _e('数量','ziranzhi2'); ?>
                </div>
                <div class="fd order-date l0 t-c">
                    <?php _e('下单日期','ziranzhi2'); ?>
                </div>
                <div class="fd order-state l0 t-c">
                    <?php _e('订单状态','ziranzhi2'); ?>
                </div>
                <div class="fd order-key l0 t-c">
                    <?php _e('运单号','ziranzhi2'); ?>
                </div>
            </div>

            <ul class="l-8" v-if="list.length > 0">
                <li :class="['mouh',{'order-light' : moreIndex == index}]" v-for="(item ,index) in list" @click.stop="showMore(index)" ref="order-item">
                    <div class="fd order-name pos-r l0" v-html="item.order_name" @click.stop></div>
                    <div class="order-id l0 fd t-c"><span class="lm" v-text="item.order_id"></span></div>
                    <div class="fd order-price l0 t-c"><span class="lm" v-html="item.order_price"></span></div>
                    <div class="fd order-count l0 t-c"><span class="lm" v-text="item.order_count"></span></div>
                    <div class="fd order-date l0 t-c">
                        <span class="lm" v-html="item.order_date"></span>
                    </div>
                    <div class="fd order-state l0 t-c"><span class="lm" v-html="item.order_state"></span></div>
                    <div class="fd order-key l0 t-c"><span class="lm" v-html="item.order_key"></span></div>
                    <div class="order-more b-t t-r pd10 clearfix pos-r" v-show="moreIndex == index">
                        <span class="pjt pos-a"></span>
                        <span class="order-total-price l0 fl" v-html="item.order_total"></span>
                        <span class="fr"><button class="empty" @click.stop="deleteOrder(index,item.order_id)"><?php _e('删除','ziranzhi2'); ?></button></span>
                    </div>
                </li>
                <page-nav nav-type="userOrder" :paged="paged" :pages="pages" :locked-nav="1"></page-nav>
            </ul>
            <div class="loading-dom pos-r" v-else>
                <div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有订单</p></div>
            </div>
        </div>
    </div>
</div>
