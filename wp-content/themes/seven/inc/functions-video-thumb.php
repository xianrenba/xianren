<?php
//视频网站截图获取
//版本：V1.0
//开发者：顾问
//官方网站：https://www.dounai0.com
//502网站无法访问

function zrz_get_url_content($gurl){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$gurl);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    return $result;
}
function g($uc,$data,$datam){
	preg_match($data,$uc,$m);
	return $m[$datam];
}

function zrz_base64_file_upload($_url){

    $data = zrz_get_video_thumb($_url);

    if(!$data) return;
    $url = $data['img'];
    $title = $data['title'];

    $file_contents = wp_remote_post($url, array(
        'method' => 'GET',
        'timeout' => 300,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array( 'Accept-Encoding' => '' ),
        'sslverify' => false,
        )
    );

    //上传图片
    if (isset($file_contents['body']) && $file_contents['body'] != ''){

        add_filter('upload_dir', 'zrz_upload_dir', 100, 1);
        $img_base64 = base64_encode($file_contents['body']);

        $url = str_shuffle(uniqid()).'.jpg';

        $img             = str_replace( 'data:image/jpeg;base64,', '', $img_base64 );
        $img             = str_replace( ' ', '+', $img );
        $decoded         = base64_decode( $img );

        return array(
            'url'=>wp_upload_bits($url, null, $decoded),
            'title'=>$title
        );
    }else{
        return false;
    }
}

function zrz_get_video_thumb($_url){

    if(!$_url){
        return false;
    }
    $url = parse_url($_url);
    $bigThumbnail = '';
    //优酷
    if($url['host']=="v.youku.com"){
    	$youkuid = g($url['path'],'|/v_show/id_(.*).html|i',1);
    	$youkupic = json_decode(zrz_get_url_content("https://api.youku.com/videos/show.json?client_id=58263aed0903a6d8&video_id=$youkuid"),true);
        $title = $youkupic['title'];
    	$bigThumbnail = $youkupic['bigThumbnail'];
    }
    //土豆
    elseif($url['host']=="new-play.tudou.com" || $url['host']=="video.tudou.com"){
    	$tudouid = g($url['path'],'|/v/(.*).html|i',1);
    	$tudoupic = json_decode(zrz_get_url_content("https://api.youku.com/videos/show.json?client_id=58263aed0903a6d8&video_id=$tudouid"),true);
        $title = $youkupic['title'];
        $bigThumbnail = $tudoupic['bigThumbnail'];
    }
    //qq
    elseif($url['host']=="v.qq.com"){
    	$qqidurl = zrz_get_url_content($_url);
    	preg_match_all("|\"pic_640_360\"\:\"(.*)\",\"c_title_segment|U", $qqidurl, $regs2);//获取网站大图
        preg_match("/<title>(.+)<\/title>/i", $qqidurl, $matches);
    	$bigThumbnail = $regs2[1][0];
        $title = $matches[1];
    }
    //奇艺
    elseif($url['host']=="www.iqiyi.com"){
    	$qiyiidurl = zrz_get_url_content($_url);
    	preg_match_all("|<meta itemprop=\"image\" content=\"(.*)\"\/>|U", $qiyiidurl, $regs2);//获取网站大图
        preg_match("/<title>(.+)<\/title>/i", $qiyiidurl, $matches);
    	$bigThumbnail = $regs2[1][0];
        $title = $matches[1];
    }
    //bili
    elseif($url['host']=="www.bilibili.com"){
    	$biliidurl = zrz_get_url_content($_url);
    	preg_match_all("|<meta data-vue-meta=\"true\" itemprop=\"image\" content=\"(.*)\"\/>|U", $biliidurl, $regs2);//获取网站大图
        preg_match("/<title>(.+)<\/title>/i", $biliidurl, $matches);
    	$bigThumbnail = $regs2[1][0];
        $title = $matches[1];
    }
    //acfun
    elseif($url['host']=="www.acfun.cn" || $url['host']=="v.hapame.com"){
    	$acfunidurl = zrz_get_url_content($_url);
    	preg_match_all("|coverImage\":\"(.*)\",\"|U", $acfunidurl, $regs2);//获取网站大图
        preg_match("/<title>(.+)<\/title>/i", $acfunidurl, $matches);
    	$bigThumbnail = $regs2[1][0];
        $title = $matches[1];
    }
    //秒拍
    elseif($url['host']=="www.miaopai.com"){
    	$miaopaiidurl = zrz_get_url_content($_url);
    	preg_match_all("|\"poster\":\"(.*)\"|U", $miaopaiidurl, $regs2);//获取网站大图
        preg_match("/<title>(.+)<\/title>/i", $miaopaiidurl, $matches);
    	$bigThumbnail = $regs2[1][0];
        $title = $matches[1];
    }
    //无效地址返回
    else{
    	return false;
    }

    if($bigThumbnail){
        return array(
            'img'=>$bigThumbnail,
            'title'=>$title
        );
    }
    return false;
}
