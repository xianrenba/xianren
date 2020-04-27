<?php
/*
Template Name: 视屏页
*/
get_header(); ?>
<script>
var num=0
function Clo(){
	document.getElementById("video_iframe").src ="https://www.qyblog.cn/wp-content/uploads/2019/01/tvshow.jpg";
}
function Tip(){
	var link = document.getElementById('link').value;
	var jiexi = document.getElementById('jiexi').value;
	document.getElementById("video_iframe").src = jiexi + link;
	num=0;
}
function Keytest(event){
	if(event.keyCode==13){
		alert("提交视频地址后，按Alt+数字1-6选择解析接口")
	};
	if(event.keyCode==18){
		num=1
	};
	if (num==1){
		if(event.keyCode==49|event.keyCode==97){
			var link = document.getElementById('link').value;
			document.getElementById("video_iframe").src ='https://api.bbbbbb.me/jx/?url=' + link;
			num=0;
		}
		if(event.keyCode==50|event.keyCode==98){
			var link = document.getElementById('link').value;
			document.getElementById("video_iframe").src ='https://api.bbbbbb.me/yunjx/?url=' + link;
			num=0;
		}
		if(event.keyCode==51|event.keyCode==99){
			var link = document.getElementById('link').value;
			document.getElementById("video_iframe").src ='https://api.bbbbbb.me/playm3u8/?url=' + link;
			num=0;
		}
		if(event.keyCode==52|event.keyCode==100){
			var link = document.getElementById('link').value;
			document.getElementById("video_iframe").src ='https://api.bbbbbb.me/vip/?url=' + link;
			num=0;
		}
		if(event.keyCode==53|event.keyCode==101){
			var link = document.getElementById('link').value;
			document.getElementById("video_iframe").src ='https://api.bbbbbb.me/yun/?url=' + link;
			num=0;
		}
		if(event.keyCode==54|event.keyCode==102){
			var link = document.getElementById('link').value;
			document.getElementById("video_iframe").src ='https://api.47ks.com/webcloud/?v=' + link;
			num=0;
		}
	}
}
</script>
<body onkeydown="Keytest(event)"& onkeyup="keyup()">
<style>
.video{overflow:hidden; margin: 20px auto; width:1200px;}
#video_iframe{height:650px;width:100%;frameborder:0;marginheight:0px;marginwidth:0px;background:#000000;border:0}
#video_button{margin:0 auto;width:1200px;display: block;overflow: hidden;}
#jiexi{height:44px;line-height:30px;border:1px solid #00a0e9;margin-right:10px;width:190px;display:block;float:left;border-radius:0}
#link{border:1px solid #00a0e9; width:780px; line-height:30px; padding:6px; display:block; float:left;}
#beginbtn{width:100px; line-height:40px; background-color:#00a0e9; border:1px solid #00a0e9; display:block; float:right; color:#fff;}
#closebtn{width:100px; line-height:40px; background-color:#00a0e9; border:1px solid #00a0e9; display:block; float:right; color:#fff;margin:0 10px;}
#tips{width:1200px; line-height:30px; background-color:#63b3ff; border:0; display:block; float:right; color:#fff;margin:20px auto;padding:2px 8px;}
@media (max-width:720px){
	.video{width:100%;margin:0;padding:5px}
	#video_iframe{height:222px;}
	#video_button{width:100%;padding:0 5px;}
	#jiexi{width:28%;margin:0 2% 5px 0;}
	#link{width:70%;margin:0 0 5px 0}
	#beginbtn{width:49%; margin:5px 0 5px 1%; padding:0; border:0;}
	#closebtn{width:49%; margin:5px 1% 5px 0; padding:0; border:0;}
	#tips{width:100%;margin:5px auto;padding:5px;}
}
</style>
<div class="video">
   <iframe id="video_iframe" src="https://www.myxin.top/jx/api/?url=http://v.youku.com/v_show/id_XMzE2MTg2MjE2OA==.html?spm=a2hmv.20009921.yk-slide-107667.5~5!2~5~5!2~A"></iframe>
</div>
<div id="video_button">
	<select class="form-control input-lg" id="jiexi" >  
	<option value="https://www.myxin.top/jx/api/?url=" selected="">一号解析接口</option>  
	<option value="https://api.bbbbbb.me/jx/?url=">二号解析接口</option>   
	<option value="https://api.bbbbbb.me/playm3u8/?url=">三号解析接口</option>
	<option value="https://api.bbbbbb.me/vip/?url=">四号解析接口</option>
	<option value="https://api.bbbbbb.me/yun/?url=">五号解析接口</option> 
    <option value="https://api.bbbbbb.me/jx/?url=">六号解析接口</option>
	</select> 
    <input type="text"  id="link"  placeholder="在此粘贴视频地址！" />
    <input type="submit" id="beginbtn" value="关闭视频"  onclick="Clo()" />
    <input type="submit" id="closebtn" value="提交视频地址"  onclick="Tip()" />
    <p id="tips">在上方输入视频地址后点击提交视频或者按Alt+数字1-6选择解析接口后即可观看。    VIP视频支持爱奇艺，芒果TV，优酷，腾讯，乐视等"</p>
</div>
</body>
<?php get_footer(); ?>