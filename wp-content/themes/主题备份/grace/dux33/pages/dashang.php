<?php if (_hui('qrimg')) { ?>
   <style type="text/css">
   <!--
.mod-article__reward {
	margin-top:20px
}
.mod-article__reward .mod-article__reward-title {
	display:block;
	margin-bottom:10px;
	line-height:24px;
	font-size:14px;
	color:#999;
	text-align:center
}
.mod-article__reward .mod-article__reward-btn {
	display:block;
	width:88px;
	height:26px;
	line-height:26px;
	margin:0 auto;
	text-align:center;
	background-color:#ff6a55;
	border:1px solid #ff6a55;
	color:#fff;
	font-size:15px;
	border-radius:4px;
	-webkit-transition:all .2s ease;
	transition:all .2s ease
}
.mod-article__reward .mod-article__reward-btn:hover {
	border:1px solid #ff6a55;
	color:#ff6a55;
	background-color:#fff;
	text-decoration:none
}
.mod-article__reward .mod-article__reward-mask {
	display:none;
	position:fixed;
	top:0;
	left:0;
	right:0;
	bottom:0;
	width:100%;
	height:100%;
	background-color:rgba(0,0,0,.4);
	z-index:10000
}
.mod-article__reward .mod-article__reward-content {
	position:absolute;
	top:50%;
	left:50%;
	margin-left:-200px;
	margin-top:-200px;
	width:320px;
	height:auto;
	background-color:#fff;
	border-radius:4px
}
.mod-article__reward .mod-article__reward-content i {
	width:30px;
	height:30px;
	position:absolute;
	right:-15px;
	top:-15px;
	background-color:#eee;
	border-radius:50%;
	cursor:pointer;
	-webkit-transition:all .25s linear;
	transition:all .25s linear
}
.mod-article__reward .mod-article__reward-content i:after,.mod-article__reward .mod-article__reward-content i:before {
	content:"";
	position:absolute;
	width:2px;
	height:20px;
	left:14px;
	bottom:5px;
	background:#ff6a55;
	-webkit-transform:rotate(45deg);
	transform:rotate(45deg);
	-webkit-transform:rotate(rotate(45deg)) scale(1) skew(1deg) translate(10px);
	transform:rotate(rotate(45deg)) scale(1) skew(1deg) translate(10px)
}
.mod-article__reward .mod-article__reward-content i:after {
	-webkit-transform:rotate(-45deg);
	transform:rotate(-45deg);
	-webkit-transform:rotate(rotate(-45deg)) scale(1) skew(1deg) translate(10px);
	transform:rotate(rotate(-45deg)) scale(1) skew(1deg) translate(10px)
}
.mod-article__reward .mod-article__reward-content i:hover {
	-webkit-transform:rotate(180deg);
	transform:rotate(180deg);
	-webkit-transform:rotate(rotate(180deg)) scale(1) skew(1deg) translate(10px);
	transform:rotate(rotate(180deg)) scale(1) skew(1deg) translate(10px);
	background-color:#fff
}
.mod-article__reward .mod-article__reward-header {
	padding:10px 0;
	background-color:#ff6a55;
	border-top-left-radius:4px;
	border-top-right-radius:4px
}
.mod-article__reward .mod-article__reward-header span {
	display:block;
	color:#fff;
	font-size:14px;
	line-height:24px;
	text-align:center
}
.mod-article__reward .mod-article__reward-header img {
	display:block;
	width:80px;
	height:80px;
	margin:5px auto;
	border-radius:50%;
	border:2px solid hsla(0,0%,100%,.6);
	box-shadow:0 0 5px #fff
}
.mod-article__reward .mod-article__reward-main {
	padding:10px 0;
	text-align:center
}
.mod-article__reward .mod-article__reward-main h5 {
	color:#000;
	font-size:18px
}
.mod-article__reward .mod-article__reward-main h5 span {
	color:#000;
	font-size:24px
}
.mod-article__reward .mod-article__reward-main img {
	width:200px;
	height:200px;
	margin:0 auto;
	display:block
}
.mod-article__reward .mod-article__reward-main a {
	width:64px;
	display:block;
	margin:5px auto;
	color:#39c;
	text-decoration:none
}
.mod-article__reward .mod-article__reward-main span {
	display:inline-block;
	font-size:14px;
	color:#999
}


@-webkit-keyframes flipInY {
	0% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,90deg);
		transform:perspective(400px) rotate3d(0,1,0,90deg);
		opacity:0
	}
	0%,40% {
		-webkit-animation-timing-function:ease-in;
		animation-timing-function:ease-in
	}
	40% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,-20deg);
		transform:perspective(400px) rotate3d(0,1,0,-20deg)
	}
	60% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,10deg);
		transform:perspective(400px) rotate3d(0,1,0,10deg);
		opacity:1
	}
	80% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,-5deg);
		transform:perspective(400px) rotate3d(0,1,0,-5deg)
	}
	to {
		-webkit-transform:perspective(400px);
		transform:perspective(400px)
	}
}
@keyframes flipInY {
	0% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,90deg);
		transform:perspective(400px) rotate3d(0,1,0,90deg);
		opacity:0
	}
	0%,40% {
		-webkit-animation-timing-function:ease-in;
		animation-timing-function:ease-in
	}
	40% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,-20deg);
		transform:perspective(400px) rotate3d(0,1,0,-20deg)
	}
	60% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,10deg);
		transform:perspective(400px) rotate3d(0,1,0,10deg);
		opacity:1
	}
	80% {
		-webkit-transform:perspective(400px) rotate3d(0,1,0,-5deg);
		transform:perspective(400px) rotate3d(0,1,0,-5deg)
	}
	to {
		-webkit-transform:perspective(400px);
		transform:perspective(400px)
	}
}
.flipInY {
	-webkit-backface-visibility:visible!important;
	backface-visibility:visible!important;
	-webkit-animation-name:flipInY;
	animation-name:flipInY
}

.animated {
	-webkit-animation-duration:1s;
	animation-duration:1s;
	-webkit-animation-fill-mode:both;
	animation-fill-mode:both
}
.animated.infinite {
	-webkit-animation-iteration-count:infinite;
	animation-iteration-count:infinite
}
.short {
	-webkit-animation-duration:.6s;
	animation-duration:.6s
}
.animated.hinge {
	-webkit-animation-duration:2s;
	animation-duration:2s
}
@-webkit-keyframes fadeIn {
	0% {
		opacity:0
	}
	to {
		opacity:1
	}
}
@keyframes fadeIn {
	0% {
		opacity:0
	}
	to {
		opacity:1
	}
}
.fadeIn {
	-webkit-animation-name:fadeIn;
	animation-name:fadeIn
}
@-webkit-keyframes fadeInDown {
	0% {
		opacity:0;
		-webkit-transform:translate3d(0,-100%,0);
		transform:translate3d(0,-100%,0)
	}
	to {
		opacity:1;
		-webkit-transform:none;
		transform:none
	}
}
@keyframes fadeInDown {
	0% {
		opacity:0;
		-webkit-transform:translate3d(0,-100%,0);
		transform:translate3d(0,-100%,0)
	}
	to {
		opacity:1;
		-webkit-transform:none;
		transform:none
	}
}
.fadeInDown {
	-webkit-animation-name:fadeInDown;
	animation-name:fadeInDown
}
@-webkit-keyframes fadeInUp {
	0% {
		opacity:0;
		-webkit-transform:translate3d(0,100%,0);
		transform:translate3d(0,100%,0)
	}
	to {
		opacity:1;
		-webkit-transform:none;
		transform:none
	}
}
@keyframes fadeInUp {
	0% {
		opacity:0;
		-webkit-transform:translate3d(0,100%,0);
		transform:translate3d(0,100%,0)
	}
	to {
		opacity:1;
		-webkit-transform:none;
		transform:none
	}
}
.fadeInUp {
	-webkit-animation-name:fadeInUp;
	animation-name:fadeInUp
}
@-webkit-keyframes fadeOut {
	0% {
		opacity:1
	}
	to {
		opacity:0
	}
}
@keyframes fadeOut {
	0% {
		opacity:1
	}
	to {
		opacity:0
	}
}
.fadeOut {
	-webkit-animation-name:fadeOut;
	animation-name:fadeOut
}
@-webkit-keyframes fadeOutDown {
	0% {
		opacity:1
	}
	to {
		opacity:0;
		-webkit-transform:translate3d(0,100%,0);
		transform:translate3d(0,100%,0)
	}
}
@keyframes fadeOutDown {
	0% {
		opacity:1
	}
	to {
		opacity:0;
		-webkit-transform:translate3d(0,100%,0);
		transform:translate3d(0,100%,0)
	}
}
.fadeOutDown {
	-webkit-animation-name:fadeOutDown;
	animation-name:fadeOutDown
}
@-webkit-keyframes fadeOutUp {
	0% {
		opacity:1
	}
	to {
		opacity:0;
		-webkit-transform:translate3d(0,-100%,0);
		transform:translate3d(0,-100%,0)
	}
}
@keyframes fadeOutUp {
	0% {
		opacity:1
	}
	to {
		opacity:0;
		-webkit-transform:translate3d(0,-100%,0);
		transform:translate3d(0,-100%,0)
	}
}
.fadeOutUp {
	-webkit-animation-name:fadeOutUp;
	animation-name:fadeOutUp
}
@-webkit-keyframes zoomIn {
	0% {
		opacity:0;
		-webkit-transform:scale3d(.3,.3,.3);
		transform:scale3d(.3,.3,.3)
	}
	50% {
		opacity:1
	}
}
@keyframes zoomIn {
	0% {
		opacity:0;
		-webkit-transform:scale3d(.3,.3,.3);
		transform:scale3d(.3,.3,.3)
	}
	50% {
		opacity:1
	}
}
.zoomIn {
	-webkit-animation-name:zoomIn;
	animation-name:zoomIn
}
@-webkit-keyframes zoomOut {
	0% {
		opacity:1
	}
	50% {
		-webkit-transform:scale3d(.3,.3,.3);
		transform:scale3d(.3,.3,.3)
	}
	50%,to {
		opacity:0
	}
}
@keyframes zoomOut {
	0% {
		opacity:1
	}
	50% {
		-webkit-transform:scale3d(.3,.3,.3);
		transform:scale3d(.3,.3,.3)
	}
	50%,to {
		opacity:0
	}
}
.zoomOut {
	-webkit-animation-name:zoomOut;
	animation-name:zoomOut
}
@-webkit-keyframes fadeInUpBig {
	0% {
		opacity:0;
		-webkit-transform:translate3d(0,2000px,0);
		transform:translate3d(0,2000px,0)
	}
	to {
		opacity:1;
		-webkit-transform:none;
		transform:none
	}
}
@keyframes fadeInUpBig {
	0% {
		opacity:0;
		-webkit-transform:translate3d(0,2000px,0);
		transform:translate3d(0,2000px,0)
	}
	to {
		opacity:1;
		-webkit-transform:none;
		transform:none
	}
}
.fadeInUpBig {
	-webkit-animation-name:fadeInUpBig;
	animation-name:fadeInUpBig
}
    -->
   </style>
    <div class="modal fade" id="pay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" style="margin-top:250px;">
              <div class="modal-body">
<div class="mod-article__reward js_article_content mod-article__content">

                                        <div class="mod-article__reward-content animated flipInY">
                                <button type="button" class="close js_reward_close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <div class="mod-article__reward-header">
                                                <span>赞赏作者</span>
                                                <img class="animated fadeIn" src="https://secure.gravatar.com/avatar/9e1b4dad23ca273150d0a8b584704cb3?s=160&d=mm" alt="">
                                                <span>Biebb-技术博客</span>
                                            </div>
                                            <div class="mod-article__reward-main">
                                                <h5>¥ <span class="js_reward_count" id="qrcode_text">支付宝</span></h5>
                                                <img src="https://blog.biebb.online/wp-content/themes/dux/img/zhifubao.png" class="js_reward_qrcode animated zoomIn" id="qrcode_pic">
						<a type="button" class="js_change_reward" onClick="change_pic()">换个方式支付</a>
                                                <span>扫一扫赞赏</span>
                                                <ul class="js_reward_data ui-d-n">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>			  
			  
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
<script>
function change_pic(){
var imgObj = document.getElementById("qrcode_pic");
var label = document.getElementById("qrcode_text").innerHTML;
if(imgObj.getAttribute("src",2)=="https://blog.biebb.online/wp-content/themes/dux/img/zhifubao.png"){
imgObj.src="https://blog.biebb.online/wp-content/themes/dux/img/weixinzhifu.png";
}else{
imgObj.src="https://blog.biebb.online/wp-content/themes/dux/img/zhifubao.png";
}
if(label == ("支付宝")){
    document.getElementById("qrcode_text").innerHTML="微信支付";
}
else{
    document.getElementById("qrcode_text").innerHTML="支付宝";
}
}
</script>
<?php } ?>