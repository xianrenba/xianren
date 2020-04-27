<?php
//提现页面
get_header();
$user_id = zrz_get_credit_settings('zrz_tx_admin');
$cuser_id = get_current_user_id();
?>
<div id="primary" class="content-area fd">
	<main id="withdraw" class="site-main withdraw-page box pos-r">
		<div class="pd10 box-header b-b fs12 mar20-b" ref="withdraw" role="main" data-uid="<?php echo $user_id; ?>">提现管理</div>
        <div class="pd10">
            <?php if($cuser_id != $user_id){ ?>
                <div class="loading-dom pos-r" ref="nologin">
    				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">无权访问此页</p></div>
    			</div>
            <?php }else{ ?>
                <div class="loading-dom pos-r" v-if="locked">
                    <div class="lm"><span class="loading"></span></div>
                </div>
                <div class="" v-else v-cloak>
                    <div class="" v-if="data.length > 0">
                        <table cellpadding="5" cellspacing="0" border="0" width="100%" class="gold-tab b-r b-b l1 fs13">
                            <tbody>
                                <tr class="tab-head">
                                    <td width="100" class="mobile-hide">交易ID</td>
                                    <td width="100">提现人</td>
                                    <td width="80">时间</td>
                                    <td width="80">提现金额</td>
                                    <td width="80">状态</td>
                                    <td width="80">操作</td>
                                </tr>
                                <tr v-for="item in data">
                                    <td class="mobile-hide">{{item.id}}</td>
                                    <td v-html="item.users"></td>
                                    <td class="">{{item.date}}</td>
                                    <td class="shu">{{item.credit}}</td>
                                    <td class="yue"><span class="green" v-if="item.status == 1">已支付</span><span class="red" v-else>未支付</span></td>
                                    <td class="yue"><button @click="pay(item.user_id,item.users,item.credit,item.user_code.weixin,item.user_code.alipay,item.id)" v-if="item.status == 0">支付</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <page-nav class="b-b b-l b-r" nav-type="gold" :paged="paged" :pages="pages" :locked-nav="1" v-show="!locked && pages > 1" v-cloak></page-nav>
                    </div>
                    <div v-else>
                        <div class="loading-dom pos-r" ref="nologin">
            				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">没有人申请提现</p></div>
            			</div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div :class="['sign-form','dialog',show ? 'dialog--open' : '']" v-cloak>
    		<div class="dialog__overlay"></div>
    		<div class="dialog__content">
                <div class="withdraw-qcode pd20">
                    <div class="withdraw-weixin fd img-bg user-qrcode qrcode-weixin pos-r" :style="'background-image:url('+payData.weixin+')'">
                        <span class="pos-a">微信支付</span>
                    </div>
                    <div class="withdraw-alipay fd img-bg user-qrcode qrcode-alipay pos-r" :style="'background-image:url('+payData.alipay+')'">
                        <span class="pos-a">支付宝支付</span>
                    </div>
                    <div class="h20 mar20-t "></div>
                    <div class="pay-price mar10-t"><sup>¥</sup>{{payData.price}}</div>
                    <div class="fs12 gray">支付金额</div>
                    <div class="mar20-t"><button class="mar10-r empty" @click="closeForm()">取消支付</button><button @click="payed()">已支付</button></div>
                    <p class="mar20-t fs12">支付完成后，请点击已支付</p>
                </div>
            </div>
        </div>
    </main>
</div><?php
get_sidebar();
get_footer();
