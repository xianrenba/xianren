<?php
require_once ZRZ_THEME_DIR.'/inc/SDK/Vaptcha/Vaptcha.php';
use Vaptcha\Vaptcha;
add_action( 'wp_ajax_zrz_vaptcha', 'zrz_vaptcha' );
add_action( 'wp_ajax_nopriv_zrz_vaptcha', 'zrz_vaptcha' );
function zrz_vaptcha(){
    $ac = isset($_POST['ac']) ? $_POST['ac'] : '';

    $vid = '';
    $key = '';
    
    $vaptcha = new Vaptcha($vid, $key);

    if($ac == 'get'){
        $res = $vaptcha->getChallenge();
        print json_encode(array('status'=>200,'msg'=>$res));
        exit;
    }

    if($ac = 'check'){
        $request = $_POST; 
        $validatePass = $vaptcha->validate($request['challenge'], $request['token']);
        print json_encode(array('status'=>200,'msg'=>$validatePass));
        exit;
    }
    
    if($ac = 'getCallback'){
        $data = $_GET['data']; 
        $callback = $_GET['callback'];
        $jsonResult = json_encode($vaptcha->downTime($data));
        print json_encode(array('status'=>200,'msg'=>"$callback($jsonResult)"));
        exit;
    }
   
}