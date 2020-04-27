<?php
$user_id =  get_query_var('author');
$sign_type = zrz_get_social_settings('type');
?>
<div class="user-info pd20 user-setting">
    <div class="gray">基本设置</div>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            昵称
        </div><div class="setting-info fd">
            <div v-show="!nicknameShow">
                <span v-text="userData.nickname" v-show="userData.nickname" class="mar10-r"></span>
                <button class="mar20-l fs14 text" @click="nicknameShow = true" v-if="userData.nickname"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
                <button class="mar20-l fs14 text setting-emp" @click="nicknameShow = true" v-else><i class="iconfont zrz-icon-font-xiugai"></i> 填写</button>
            </div>
            <div class="edit" v-show="nicknameShow" v-cloak>
                <input type="text" v-model="userData.nickname">
                <div class="mar10-t">
                    <button @click="save('nickname')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('nickname')">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            性别
        </div><div class="setting-info fd">
            <div v-show="!genderShow">
                <span v-text="userData.gender == 1 ? '男' : '女'"></span><span class="dot"></span>
                <button class="mar20-l fs14 text" @click="genderShow = true" v-if="userData.gender"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
                <button class="mar20-l fs14 text" @click="genderShow = true" v-else><i class="iconfont zrz-icon-font-xiugai"></i> 填写</button>
            </div>
            <div class="edit" v-show="genderShow" v-cloak>
                <label><input type="radio" class="radio" value="1" v-model="userData.gender"> 男</label>
                <span class="dot"></span><span class="dot"></span>
                <label><input type="radio" class="radio" value="0" v-model="userData.gender"> 女</label>
                <div class="mar10-t">
                    <button @click="save('gender')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('gender')">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            网址
        </div><div class="setting-info fd">
            <div v-show="!siteShow">
                <span v-text="userData.site" v-show="userData.site" class="mar10-r"></span>
                <button class="mar20-l fs14 text" @click="siteShow = true" v-if="userData.site"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
                <button class="mar20-l fs14 text setting-emp" @click="siteShow = true" v-else><i class="iconfont zrz-icon-font-xiugai"></i> 填写</button>
            </div>
            <div class="edit" v-show="siteShow" v-cloak>
                <input type="text" v-model="userData.site">
                <div class="mar10-t">
                    <button @click="save('site')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('site')">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            一句话介绍自己
        </div><div class="setting-info fd">
            <div v-show="!bioShow">
                <span v-text="userData.bio" v-show="userData.bio" class="mar10-r"></span>
                <button class="mar20-l fs14 text" @click="bioShow = true" v-if="userData.bio"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
                <button class="mar20-l fs14 text setting-emp" @click="bioShow = true" v-else><i class="iconfont zrz-icon-font-xiugai"></i> 填写</button>
            </div>
            <div class="edit" v-show="bioShow" v-cloak>
                <input type="text" v-model="userData.bio">
                <div class="mar10-t">
                    <button @click="save('bio')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('bio')">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            收货地址
        </div><div class="setting-info fd setting-address">
            <div>
                <div v-for="(address,key,index) in userData.address" :class="['fs13','address-list','pos-r','mar10-b',{'picked':defaultAddress == key}]">
                    <span v-text="address.address"></span>
                    <span v-text="address.name" class="mar10-l red"></span>
                    <span v-text="address.phone" class="mar10-l"></span>
                    <div class="pos-a">
                        <button class="text" @click="setDefault(key)" v-if="defaultAddress != key">设为默认</button> <button class="text" @click="deleteAddress(key)">删除</button>
                    </div>
                </div>
                <div class="fs13 mar10-b add-address" v-show="addressShow == true" v-cloak>
                    <div class="mar10-b"><div id="address" ref="address"></div><p><span>地址：</span><input type="text" v-model="add.address" @focus="addressPicked"/></p></div>
                    <p class="mar10-b"><span>联系人：</span><input type="text" v-model="add.name"/></p>
                    <p class="mar10-b"><span>电话：</span><input type="text" v-model="add.phone"/></p>
                    <p><span></span><button class="mar10-r" @click="addAddress">添加</button> <button class="empty" @click="addressShow = false">取消</button><b class="red mar10-l" v-text="addError"></b></p>
                </div>
                <button class="mar20-l fs14 text" @click="addressShow = true" v-show="!addressShow"><i class="iconfont zrz-icon-font-jia1"></i> 添加收货地址</button>
            </div>
            <p class="setting-des">如果您在本站购物，请务必填写此项，以便发货！</p>
        </div>
    </div>
    <?php if($sign_type == 2 || $sign_type == 3){ ?>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            绑定手机
        </div><div class="setting-info fd edit-phone">
            <div v-show="!phoneShow">
                <span v-text="userData.phone" v-show="userData.phone" class="mar10-r"></span>
                <button class="mar20-l fs14 text" @click="phoneShow = true" v-if="userData.phone"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
                <button class="mar20-l fs14 text setting-emp" @click="phoneShow = true" v-else><i class="iconfont zrz-icon-font-xiugai"></i> 填写</button>
            </div>
            <div class="edit" v-show="phoneShow" v-cloak>
                <label><input type="text" v-model="userData.phone" placeholder="电话号码"><button :class="['empty','mar10-l',{'disabled':sendCodeLocked.phone}]" @click="sendCode('phone')"><span v-text="(sendCodeLocked.phone ? nub+'后可' : '')+sendText.phone"></span></button></lable>
                <p class="mar10-t"><input type="text" placeholder="手机验证码" v-model="code"/></p>
                <p class="mar10-t"><input type="password" placeholder="重新设置登陆密码" v-model="pass"/></p>
                <div class="mar10-t">
                    <button @click="save('phone')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('phone')">取消</button>
                </div>
            </div>
            <p class="red setting-des">可用作登陆</p>
        </div>
    </div>
    <?php } ?>
    <!-- <div class="setting-row b-b">
        <div class="setting-title fd">
            用户名
        </div><div class="setting-info fd">
            <span><?php echo $user_data->user_login; ?></span><span class="dot"></span><span class="fs12 gray">（不可修改）</span>
            <p class="setting-des">可用作登陆！</p>
        </div>
    </div> -->
    <?php if($sign_type == 1 || $sign_type == 3 || $sign_type == 4){ ?>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            绑定邮箱
        </div><div class="setting-info fd edit-phone">
            <div v-show="!mailShow">
                <span v-text="userData.mail" v-show="userData.mail" class="mar10-r"></span>
                <button class="mar20-l fs14 text" @click="mailShow = true" v-if="userData.mail"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
                <button class="mar20-l fs14 text setting-emp" @click="mailShow = true" v-else><i class="iconfont zrz-icon-font-xiugai"></i> 填写</button>
            </div>
            <div class="edit" v-show="mailShow" v-cloak>
                <label><input type="text" v-model="userData.mail" placeholder="邮箱地址"><button :class="['empty','mar10-l',{'disabled':sendCodeLocked.mail}]" @click="sendCode('mail')"><span v-text="(sendCodeLocked.mail ? nub+'后可' : '')+sendText.mail"></span></button></lable>
                <p class="mar10-t"><input type="text" placeholder="邮箱验证码" v-model="code"/></p>
                <p class="mar10-t"><input type="password" placeholder="重新设置登陆密码" v-model="pass"/></p>
                <div class="mar10-t">
                    <button @click="save('mail')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('mail')">取消</button>
                    <span class="mar10-l red fs12" v-show="mailMsg" v-text="mailMsg"></span>
                </div>
            </div>
            <p class="setting-des"><span class="red">可用作登陆</span></p>
        </div>
    </div>
    <?php } ?>
    <div class="setting-row b-b">
        <div class="setting-title fd">
            修改密码
        </div><div class="setting-info fd">
            <div v-show="!passShow">
                <button class="mar20-l fs14 text setting-emp" @click="passShow = true"><i class="iconfont zrz-icon-font-xiugai"></i> 修改</button>
            </div>
            <div class="edit" v-show="passShow" v-cloak>
                <input type="password" v-model="pass" placeholder="请输入新密码" class="mar10-b">
                <input type="password" v-model="rePass" placeholder="请再次输入新密码">
                <div class="mar10-t">
                    <button @click="save('pass')" :class="{'disabled':saveLocked}">保存</button><span class="dot"></span><button class="empty"  @click="cancelSave('pass')">取消</button><span class="mar10-l green fs12" v-show="passMsg">修改成功，页面将刷新，请重新登陆</span>
                </div>
            </div>
            <p class="setting-des">请确保两次密码一致，并且密码大于6个字符！</p>
            <p class="setting-des red">修改成功后，需要使用新密码重新登陆！</p>
        </div>
    </div>
    <p class="h20"></p>
    <div class="mar20-t gray mar20-b">收款码设置<span class="dot"></span><span class="fs12" v-html="qcodeError"><span></div>
    <div class="get-qrcode">
        <label class="user-qrcode qrcode-weixin mar10-r pos-r img-bg" :style="'background-image:url('+qcode.weixin+')'"><span v-if="!qcode.weixin" class="lm fs12">微信收款码</span>
            <input id="avatar-input" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="hide" @change="getCodeFile($event,'weixin')">
        </label>
        <label class="user-qrcode qrcode-alipay pos-r img-bg" :style="'background-image:url('+qcode.alipay+')'"><span v-if="!qcode.alipay" class="lm fs12">支付宝收钱码</span>
            <input id="avatar-input" type="file" accept="image/jpg,image/jpeg,image/png,image/gif" class="hide" @change="getCodeFile($event,'alipay')">
        </label>
        <p class="setting-des">请上传微信或者支付宝的收款二维码，否则将无法提现。(直接上传手机端微信和支付宝的收款码即可)</p>
    </div>
    <p class="h20"></p>
    <div class="mar20-t gray mar20-b">社交登陆设置</div>
    <div class="social-setting">
        <div class="fd pos-r" v-if="qq == 1">
            <div class="savatar pos-a" :style="'background-image:url('+social.qq.avatar+')'"></div>
            <div class="social-bund" v-cloak>
                <p>QQ</p>
                <div class="green" v-if="social.qq.bind">
                    <p class="fs12">已绑定</p>
                    <button @click="unbind($event,'qq')" class="empty">解除绑定</button>
                </div>
                <div class="red" v-else="social.qq.bind">
                    <p class="fs12">未绑定</p>
                    <button @click="openWin(qqUrl,'qq')">添加绑定</button>
                </div>
            </div>
        </div><div class="fd pos-r" v-if="weibo == 1">
            <div class="savatar pos-a" :style="'background-image:url('+social.weibo.avatar+')'"></div>
            <div class="social-bund" v-cloak>
                <p>微博</p>
                <div class="green" v-if="social.weibo.bind">
                    <p class="fs12">已绑定</p>
                    <button @click="unbind($event,'weibo')" class="empty">解除绑定</button>
                </div>
                <div class="red" v-else="social.weibo.bind">
                    <p class="fs12">未绑定</p>
                    <button @click="openWin(weiboUrl,'weibo')">添加绑定</button>
                </div>
            </div>
        </div><div class="fd pos-r" v-if="weixin == 1">
            <div class="savatar pos-a" :style="'background-image:url('+social.weixin.avatar+')'"></div>
            <div class="social-bund" v-cloak>
                <p>微信</p>
                <div class="green" v-if="social.weixin.bind">
                    <p class="fs12">已绑定</p>
                    <button class="empty" @click="unbind($event,'weixin')">解除绑定</button>
                </div>
                <div class="red" v-else="social.weixin.bind">
                    <p class="fs12">未绑定</p>
                    <button @click="openWin(weixinUrl,'weixin')">添加绑定</button>
                </div>
            </div>
        </div>
        <p class="setting-des">请先设置邮箱再解除绑定，否则无法解除绑定！</p>
    </div>
    <p class="h20"></p>
    <div class="mar20-t gray mar20-b">头像选择</div>
    <div class="avatar-picked" v-cloak>
        <div class="fd pos-r mouh click" @click="setAvatar('default')"><img style="background-color:<?php echo zrz_get_avatar_background_by_id($user_id); ?>" :src="social.default" /><span class="pos-a" v-show="avatarPicked == 'default'"><i class="iconfont zrz-icon-font-29"></i></span></div>
        <div class="fd pos-r mouh click" v-if="social.qq.avatar" @click="setAvatar('qq')"><img :src="social.qq.avatar" /><span class="pos-a" v-show="avatarPicked == 'qq'"><i class="iconfont zrz-icon-font-29"></i></span></div>
        <div class="fd pos-r mouh click" v-if="social.weixin.avatar" @click="setAvatar('weixin')"><img :src="social.weixin.avatar" /><span class="pos-a" v-show="avatarPicked == 'weixin'"><i class="iconfont zrz-icon-font-29"></i></span></div>
        <div class="fd pos-r mouh click" v-if="social.weibo.avatar" @click="setAvatar('weibo')"><img :src="social.weibo.avatar" /><span class="pos-a" v-show="avatarPicked == 'weibo'"><i class="iconfont zrz-icon-font-29"></i></span></div>
    </div>
    <p class="h20"></p>
    <?php if(current_user_can('delete_users')){ ?>
        <div class="mar20-t gray mar20-b">财富设置</div>
        <div class="setting-credit">
            <p class="fs12 mar10-b">积分设置：</p>
            <p class="fs14 mar10-b">用户当前积分：<?php echo zrz_coin($user_id,'nub'); ?></p>
            <input type="number" v-model="cfSetting.credit.val" class="mar10-b" placeholder="请输要增加（减少）的积分"/>
            <p class="setting-des mar10-b">如果要减少积分，请输入负数！</p>
            <textarea placeholder="修改原因" v-model="cfSetting.credit.why" class="mar10-b"></textarea>
            <button @click="nubChange('credit')">提交</button> <span class="mar10-l green fs12" v-show="nubMsg.credit" v-text="nubMsg.credit"></span>
        </div>
        <div class="h20"></div>
        <div class="setting-rmb">
            <p class="fs12 mar10-b">余额设置：</p>
            <p class="fs14 mar10-b">用户当前余额：<?php echo get_user_meta($user_id,'zrz_rmb',true) ? : 0; ?>元</p>
            <input type="number" v-model="cfSetting.rmb.val" class="mar10-b" placeholder="请输要增加（减少）的金额"/>
            <p class="setting-des mar10-b">如果要减少金额，请输入负数！</p>
            <textarea placeholder="修改原因" v-model="cfSetting.rmb.why" class="mar10-b"></textarea>
            <button @click="nubChange('rmb')">提交</button> <span class="mar10-l green fs12" v-show="nubMsg.rmb" v-text="nubMsg.rmb"></span>
        </div>
        <div class="mar20-t gray mar20-b">权限设置</div>
        <select v-model="LvSelected" style="width:400px;max-width:100%">
            <?php
                $lv = zrz_get_lv_settings();
                foreach ($lv as $key => $val) {
					if(isset($val['open']) && $val['open'] == 0) continue;
                    echo '<option value="'.$key.'">'.$val['name'].'</option>';
                }
            ?>
        </select>
        <div class="mar20-t"><button @click="saveLv">提交</button><span class="fs12 red mar10-l" v-text="LvMsg"></span></div>
    <?php } ?>
    <div class="h20"></div>
    <div class="h20"></div>
    <div class="h20"></div>
</div>
