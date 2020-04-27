<?php
/**
 * 购物车，订单列表
 */

get_header();

$id = isset($_GET['id']) ? (int)$_GET['id'] : '';
$count = isset($_GET['count']) && $_GET['count'] != 0 ? (int)$_GET['count'] : 1;
$user_id = get_current_user_id();
$user_custom_data = get_user_meta($user_id,'zrz_user_custom_data',true);
$defaultAddress = get_user_meta($user_id,'zrz_default_address',true);

if($id && $count && get_post_type($id) == 'shop'){
	//商品缩略图
	$thumb = get_the_post_thumbnail_url($id,'full');

	//虚拟物品还是实物？
	$commodity = get_post_meta($id, 'zrz_shop_commodity', true);
    $commodity = $commodity ? 1 : 0;

	//商品类型
	$type = get_post_meta($id,'zrz_shop_type',true);
	$type = $type == 'normal' ? 'g' : 'd';

	wp_localize_script( 'ziranzhi2-shop', 'zrz_cart_data',array(
		'postid'=>'p'.$id,
		'title'=>'<a href="'.get_permalink($id).'" target="_blank"><img src="'.zrz_get_thumb($thumb,100,100).'" /><span>'.get_the_title($id).'</span></a>',
		'type'=>$type,
		'count'=>$count,
		'commodity'=>$commodity,
		'm'=>get_user_meta($user_id,'zrz_rmb',true),
		'address'=>isset($user_custom_data['address']) && !empty($user_custom_data['address']) ? $user_custom_data['address'] : 0,
		'defaultAddress'=>$defaultAddress,
	));
}else{
	wp_localize_script( 'ziranzhi2-shop', 'zrz_cart_ban',array(
		'm'=>get_user_meta($user_id,'zrz_rmb',true),
		'address'=>isset($user_custom_data['address']) && !empty($user_custom_data['address']) ? $user_custom_data['address'] : 0,
		'defaultAddress'=>$defaultAddress,
	));
}

?>

	<div id="primary" class="content-area mar10-b" style="width:100%">
		<?php if(is_user_logged_in()){ ?>
			<main id="cart" class="site-main cart-list" ref="cartListPage">
				<div class="loading-dom pos-r" ref="loading">
					<div class="lm"><div class="loading"></div></div>
				</div>
				<div class="box mar10-b list-address" v-cloak v-if="step">
					<div class="pd10 b-b">请选择收货地址</div>
					<div v-for="(add,key,index) in address" :class="['pd10','fs14','list-address','pos-r b-b',{'address-picked':defaultAddress == key}]">
						<i class="iconfont zrz-icon-font-29 mar10-r"></i>
						<span v-text="add.address"></span>
						<span v-text="add.name" class="red"></span>
						<span v-text="add.phone"></span>
						<button class="pos-a text" @click="setDefault(key)" v-if="defaultAddress != key">选定</button>
						<span class="pos-a current-add" @click="setDefault(key)" v-else>当前地址</span>
					</div>
					<div class="pd10 bg-blue-light">
						<div class="fs13 mar10-b add-address" v-show="addressShow == true" v-cloak>
							<div id="address" ref="address"></div>
		                    <p class="mar10-b"><span>地址：</span><input type="text" v-model="add.address" @focus="addressPicked"/></p>
		                    <p class="mar10-b"><span>联系人：</span><input type="text" v-model="add.name"/></p>
		                    <p class="mar10-b"><span>电话：</span><input type="text" v-model="add.phone"/></p>
		                    <p><span></span><button class="mar10-r" @click="addAddress">添加</button> <button class="empty" @click="addressShow = false">取消</button><b class="red mar10-l" v-text="addError"></b></p>
		                </div>
						<div class="clearfix">
			                <button class="mar20-l fs14 text fl" @click="pickeAdd()" v-show="!addressShow"><i class="iconfont zrz-icon-font-jia1"></i> 添加收货地址</button>
							<a class="fr" href="<?php echo zrz_get_user_page_url($user_id).'/setting';?>">地址管理</a>
						</div>
					</div>
				</div>
				<div class="box pos-r" v-cloak v-if="step == 1">
					<div class="bubble-login pos-a" v-if="Object.keys(address).length == 0"><span class="lm red fs14"><i class="iconfont zrz-icon-font-error"></i>请填写收货地址</span></div>
					<div class="cart-step" style="margin-bottom:-1px" v-cloak>
						<div :class="['fd','cart-step-c',{'picked':step >= 1}]"><i class="iconfont zrz-icon-font-wodegouwuche"></i><span>我的购物车</span></div><div class="fd cart-jt">
							<span class="lm"><i class="iconfont zrz-icon-font-jiantou-copy-copy"></i></span></div><div :class="['fd','cart-step-c',{'picked':step >= 2}]">
							<i class="iconfont zrz-icon-font-jiesuan"></i><span>结算</span></div><div class="fd cart-jt">
							<span class="lm"><i class="iconfont zrz-icon-font-jiantou-copy-copy"></i></span></div><div :class="['fd','cart-step-c',{'picked':step >= 3}]">
							<i class="iconfont zrz-icon-font-fukuanchenggongzhangdan"></i><span>支付结果</span></div>
					</div>
					<div class="user-order-header fs14 l-8 b-b">
						<div class="cart-order-index fd l0 t-c mouh" @click="pickedAll"><?php _e('操作','ziranzhi2'); ?></div>
						<div class="cart-order-name fd l0 t-l"><?php _e('名称','ziranzhi2'); ?></div>
						<div class="cart-order-price fd l0 t-c"><?php _e('价格','ziranzhi2'); ?></div>
						<div class="cart-order-count fd l0 t-c"><?php _e('数量','ziranzhi2'); ?></div>
						<div class="cart-order-total-price fd l0 t-c"><?php _e('总价','ziranzhi2'); ?></div>
						<div class="cart-order-delete fd l0 pd10 pos-r t-c"><?php _e('操作','ziranzhi2'); ?></div>
					</div>
					<div v-show="Object.keys(list).length > 0">
						<ul class="l-8 fs14 b-b">
							<li class="t-c" v-for="(item ,key,index) in list" v-if="item.buyed == 0">
								<div class="cart-order-index fd l0 pd10 pos-r"><button class="pos-a text" @click="deleteIndex(key)">删除</button><label class="lm mouh"><input type="checkbox" :checked="list[key].picked == 1 ? 'checked' : ''" class="mouh" @click="pickedc(key)"></label></div>
								<div class="cart-order-name fd l0 t-l pd10 pos-r"><div class="lm" v-html="item.title"></div></div>
								<div class="cart-order-price fd l0 pd10 pos-r fs12"><div class="lm" v-html="item.type == 'd' ? '<i>单价：</i>'+coin(item.price) : '<span><i>单价：</i>¥ '+item.price+'</span>'"></div></div>
								<div class="cart-order-count fd l0 pd10 pos-r" v-if="item.commodity == 0 || step >=2">
									<span class="lm" v-text="item.count"></span>
								</div>
								<div class="cart-order-count fd l0 pd10 pos-r" v-else>
									<div class="shop-buy-count lm"><button class="mouh empty" @click="count(key,'del')"><i class="iconfont zrz-icon-font-jiajianchengchu-"></i></button> <span class="shop-buy-count-input"><input type="number" v-model="list[key].count" @blur.stop.lazy="countAc(key)" @focus.stop.lazy="countAc(key)"></span> <button class="mouh empty" @click="count(key,'add')"><i class="iconfont zrz-icon-font-jiajianchengchu-1"></i></button></div>
								</div>
								<div class="cart-order-total-price fd l0 pd10 pos-r fs12"><div class="lm" v-html="item.type == 'd' ? '<i>总价：</i>'+coin(item.price * item.count) : '<span><i>总价：</i>¥ '+item.price * item.count+'</span>'"></div></div>
								<div class="cart-order-delete fd l0 pd10 pos-r"><span class="lm"><button class="empty" @click="deleteIndex(key)">删除</button></span></div>
							</li>
						</ul>
						<div class="b-b pd20 bg-blue-light"><textarea v-model="orderContent" placeholder="给卖家留言"></textarea></div>
						<div class="clearfix fs14 cart-sub pos-r" ref="postFooter">
							<div :class="[{'footer-fixed':footerBarFixed},'pos-r']">
								<div class="clearfix pd10">
									<div class="fl">
										<button class="text" @click="pickedAll"><input type="checkbox" @click="pickedAll" v-model="all" value="1"> <span class="mar5-l">全选</span></button>
										<button class="cart-delete text" @click="deleteOrders">删除选中商品</button>
										<span class="cart-picked-count">已选 <b v-text="pickedLength"></b> 件</span>
									</div>
									<div class="fr">
										<span class="cart-total fs12 mar20-r"  v-show="totalPrice || totalCoin">总价：<b v-text="'¥'+totalPrice" v-show="totalPrice" :class="{'mar20-r':totalPrice && totalCoin}"></b><div class="cart-total-coin" v-html="coin(totalCoin)" v-show="totalCoin"></div></span><button :class="['cart-sub-1',{'disabled':(!totalPrice && !totalCoin) || totalCoin > userCredit}]" @click.stop="confirmOrder">结算</button>
									</div>
								</div>
								<div class="tips-msg fs12 b-t" v-if="totalCoin > userCredit">
									<span class="red">您的积分（<div class="coin" v-html="coin(userCredit)"></div>）不足以支付积分商品，请修改购物车。</span>
								</div>
							</div>
						</div>
					</div>
					<div class="loading-dom pos-r box" v-show="Object.keys(list).length == 0">
						<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t">没有订单</p></div>
					</div>
				</div>

				<div class='step2' v-if="step == 2" v-cloak>
					<div class="box  mar10-b" v-if="totalCoin">
						<div class="pd10 fs12 box-header b-b">积分兑换</div>
						<div class="cart-step b-b" style="margin-bottom:-1px" v-cloak>
							<div :class="['fd','cart-step-c',{'picked':step >= 1}]"><i class="iconfont zrz-icon-font-wodegouwuche"></i><span>我的购物车</span></div><div class="fd cart-jt">
								<span class="lm"><i class="iconfont zrz-icon-font-jiantou-copy-copy"></i></span></div><div :class="['fd','cart-step-c',{'picked':step >= 2}]">
								<i class="iconfont zrz-icon-font-jiesuan"></i><span>结算</span></div><div class="fd cart-jt">
								<span class="lm"><i class="iconfont zrz-icon-font-jiantou-copy-copy"></i></span></div><div :class="['fd','cart-step-c',{'picked':Dstep3.success || Dstep3.fail}]">
								<i class="iconfont zrz-icon-font-fukuanchenggongzhangdan"></i><span>兑换结果</span>
							</div>
						</div>
						<ul>
							<li v-for="(item ,key,index) in list" v-if="item.picked == 1 && item.type=='d'">
								<div class="step2-title fd pd10" v-html="item.title"></div><div class="fd step2-ac pos-r">
									<div class="lm fs12">
										<span class="dot-cart"></span>
										<span class='red' v-html="'x '+item.count"></span><span class="dot-cart"></span>
										<span class="cart-total-coin" v-html="coin(item.price * item.count)"></span><span class="dot-cart"></span>
										<span class="green" v-if="item.buyed == 1" v-text="item.msg"></span>
										<span class="red" v-else-if="item.buyed == 0" v-text="item.msg"></span>
									</div>
								</div>
							</li>
							<div class="b-t pd20 bg-blue-light"><textarea v-model="orderContent" placeholder="给卖家留言"></textarea></div>
							<div class="t-c pd20 fs14 dstep3" v-if="Dstep3.success || Dstep3.fail">
								<span v-if="Dstep3.success">您已成功兑换<b v-text="Dstep3.success"></b>个商品。</span><span v-if="Dstep3.fail">兑换失败<b v-text="Dstep3.fail"></b>个商品</span>
								<div class="mar10-t"><a href="<?php echo zrz_get_user_page_url($user_id); ?>/orders" target="_blank">查看我的订单</a></div>
							</div>
							<div class="clearfix fs12 pd10 b-t" v-else><div class="fl step2-total" v-html="'总计：<div class=\'cart-total-coin\'>'+coin(totalCoin)+'</div>'"></div><div class="fr"><button :class="['mar10-r', 'empty',{'disabled':payLocked}]" @click="back">返回修改</button><button @click="coinPay" :class="{'disabled':payLocked}"><b class="loading" v-show="payLocked"></b>立刻兑换</button></div></div>
						</ul>
					</div>

					<div class="box" v-if="totalPrice">
						<div class="pd10 fs12 box-header b-b">现金购买</div>
						<div class="cart-step b-b" style="margin-bottom:-1px" v-cloak>
							<div :class="['fd','cart-step-c',{'picked':step >= 1}]"><i class="iconfont zrz-icon-font-wodegouwuche"></i><span>我的购物车</span></div><div class="fd cart-jt">
								<span class="lm"><i class="iconfont zrz-icon-font-jiantou-copy-copy"></i></span></div><div :class="['fd','cart-step-c',{'picked':step >= 2}]">
								<i class="iconfont zrz-icon-font-jiesuan"></i><span>结算</span></div><div class="fd cart-jt">
								<span class="lm"><i class="iconfont zrz-icon-font-jiantou-copy-copy"></i></span></div><div :class="['fd','cart-step-c',{'picked':Gstep3.success || Gstep3.fail}]">
								<i class="iconfont zrz-icon-font-fukuanchenggongzhangdan"></i><span>支付结果</span>
							</div>
						</div>
						<ul>
							<li v-for="(item ,key,index) in list" v-if="item.picked == 1 && item.type=='g'">
								<div class="step2-title fd pd10" v-html="item.title"></div><div class="fd step2-ac pos-r">
									<div class="lm fs12">
										<span class="dot-cart"></span>
										<span class='red' v-html="'x '+item.count"></span><span class="dot-cart"></span>
										<span v-text="'¥'+(item.price * item.count)"></span>
										<span class="green" v-if="item.buyed == 1" v-html="item.msg"></span>
										<span class="red" v-else-if="item.buyed == 0" v-html="item.msg"></span>
									</div>
								</div>
							</li>
						</ul>
						<div class="b-t pd20 bg-blue-light" v-show="!Gstep3.success">
							<p class="mar10-b fs14 hide">是否开具发票？</p>
							<div class="redio-fapiao hide">
								<input type="radio" id="one" value="0" v-model="fapiao.open">
								<label for="one">不开发票</label>

								<input type="radio" id="two" value="1" v-model="fapiao.open">
								<label for="two">开专票</label>

								<input type="radio" id="tree" value="2" v-model="fapiao.open">
								<label for="tree">开普票</label>

								<div class="fapiao-info" v-show="fapiao.open == 1 || fapiao.open == 2">
									<label>
										<p>公司名称</p>
										<input type="text" v-model="fapiao.gongsi">
									</label>
									<label>
										<p>纳税人识别号</p>
										<input type="text" v-model="fapiao.shibiehao">
									</label>
									<label>
										<p>公司地址</p>
										<input type="text" v-model="fapiao.dizhi">
									</label>
									<label>
										<p>公司电话</p>
										<input type="text" v-model="fapiao.dianhua">
									</label>
									<label>
										<p>开户银行</p>
										<input type="text" v-model="fapiao.yinhang">
									</label>
									<label>
										<p>银行账号</p>
										<input type="text" v-model="fapiao.zhanghao">
									</label>
								</div>
							</div>
							<textarea v-model="orderContent" placeholder="给卖家留言"></textarea>
						</div>
						<div class="t-c pd20 fs14 dstep3 b-t" v-if="Gstep3.success || Gstep3.fail">
							<span v-if="Gstep3.success">您已成功购买<b v-text="Gstep3.success"></b>个商品。</span><span v-if="Gstep3.fail">购买失败<b v-text="Gstep3.fail"></b>个商品</span>
							<div class="mar10-t"><a href="<?php echo zrz_get_user_page_url($user_id).'/orders'; ?>" target="_blank">查看我的订单</a></div>
						</div>
						<div class="clearfix fs12 pd10 b-t" v-if="!Gstep3.success"><span class="fl step2-total" v-html="'总计：¥'+totalPrice"></span><div class="fr"><button class="mar10-r empty" @click="back" v-show="!GpayLocked">返回修改</button><button @click="pay" :class="['payG',{'disabled':GpayLocked}]">支付</button></div></div>
					</div>
				</div>
				<payment :show="show" :type-text="'商品购买'" :type="'shop'" :price="totalPrice" :data="data" @close-form="closeForm"></payment>
			</main><!-- #main -->
		<?php }else{ ?>
			<div class="loading-dom pos-r box">
				<div class="lm"><i class="iconfont zrz-icon-font-wuneirong"></i><p class="mar10-t fs13 gray">请先登录</p></div>
			</div>
		<?php } ?>
	</div>
	<style>
		.redio-fapiao{
			margin-bottom:20px;
		}
		.redio-fapiao label{
			vertical-align: 2px;
			margin-right:10px;
			cursor: pointer;
		}
		.redio-fapiao input{
			cursor: pointer;
		}
		.fapiao-info input{
			width:300px;
			padding:5px;
		}
		.fapiao-info label{
			margin-top: 10px;
    		display: block;
		}
		.fapiao-info label p{
			margin-bottom:5px
		}
	</style>
<?php
get_footer();
