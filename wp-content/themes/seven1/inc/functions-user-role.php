<?php
/*
* 角色等级和权限
* 等级划分
*/
class ZRZ_USER_LV{

    private $user_id;
    private $lv_setting;
    private $lv;

    public function __construct($user_id){
        $this->user_id = $user_id;
        $this->lv_setting = zrz_get_lv_settings();
        $this->lv = get_user_meta($user_id,'zrz_lv',true);
    }

    //设置等级
    public function set_lv($set_lv = '',$set_vip = false){

        if($set_lv && current_user_can('delete_users')){
            update_user_meta($this->user_id,'zrz_lv',$set_lv);
        }

        if(strpos($this->lv,'vip') !== false && $set_vip == false){
            return;
        }

        //获取积分
        $user_credit = (int)get_user_meta($this->user_id,'zrz_credit_total',true);
        //获取当前用户的权限
        $lv_int = number($this->lv);

        //权限设置
        foreach ($this->lv_setting as $key => $val) {

            $min_nub = (int)number($key) -1;
            $min_nub = $min_nub >= 0 ? $min_nub : 0;
            $min = $this->lv_setting['lv'.$min_nub];
            $min = $min['credit'];
            $max = $this->lv_setting['lv7'];

            if( $min <= $user_credit && $user_credit < $val['credit']){
                $lv = 'lv'.$min_nub;
                break;
            }elseif($user_credit >= $max['credit']){
                $lv = 'lv7';
                break;
            }
        }

        //如果未曾达到这个积分等级才会升级
      	if(number($lv) >= $lv_int || $set_vip){
            update_user_meta($this->user_id,'zrz_lv',$lv);
        }

    }

    public function get_lv(){
        if(!$this->lv){
            $this->set_lv();
        }

        if(isset($this->lv_setting[$this->lv])){
            $lv = $this->lv_setting[$this->lv];
            return array(
                'name'=>$lv['name'],
                'lv'=>$this->lv,
                'open'=>isset($lv['open']) ? $lv['open'] : ''
            );
        }
        return '';
    }
}

function zrz_set_lv($user_id = 0,$set_lv = false){
    if(!$user_id) $user_id = get_current_user_id();
    $lv = new ZRZ_USER_LV($user_id);
	if($set_lv == true){
		return $lv->set_lv(false,true);
	}
    return $lv->set_lv();
}

function zrz_get_lv($user_id = 0,$type = ''){

    if(!$user_id) return '<span class="user-lv guest"><i class="zrz-icon-font-guest iconfont"></i></span>';
    $h = get_user_meta($user_id,'zrz_abled',true);
    
    if(isset($h['abled']) && $h['abled'] == 1){
        return '<span class="xiaoheiwu">小黑屋</span>';
    }

    $lv = new ZRZ_USER_LV($user_id);
    $arr = $lv->get_lv();

    if(!isset($arr['lv'])) return '';
    $is_vip = strpos($arr['lv'],'vip') !== false ? true : false;
    
    if(!$arr) return '';

    if($type === 'name'){
        $html = '<span class="user-lv '.$arr['lv'].'" title="'.($is_vip ? '' : str_replace('lv','LV.',$arr['lv'])).' '.$arr['name'].'"><i class="zrz-icon-font-'.($is_vip ? 'vip' : $arr['lv']).' iconfont mar3-r"></i>'.$arr['name'].'</span>';
    }elseif($type === 'lv'){
        $html = '<span class="user-lv '.$arr['lv'].'" title="'.($is_vip ? '' : str_replace('lv','LV.',$arr['lv'])).' '.$arr['name'].'"><i class="zrz-icon-font-'.($is_vip ? 'vip' : $arr['lv']).' iconfont"></i></span>';
    }else{
        $html = $arr['lv'];
    }

    return apply_filters( 'zrz_get_lv_filters', $html,$is_vip,$arr,$type);
}

//检查用户是否有某个权限
function zrz_user_can($user_id,$can){
    if($user_id == 0) return false;
    $h = get_user_meta($user_id,'zrz_abled',true);

    if(isset($h['abled']) && $h['abled'] == 1) return false;

    //获取当前用户的权限
    $user_lv = zrz_get_lv($user_id);

    //获取权限组
    $capabilities = zrz_get_lv_settings($user_lv);
    $capabilities = isset($capabilities['capabilities']) && is_array($capabilities['capabilities']) ? $capabilities['capabilities'] : array();
    if(in_array($can,$capabilities)){
        return true;
    }else{
        return false;
    }

}

//检查当前用户是否有某个权限
function zrz_current_user_can($can){
    if(!$can) return false;
    if(!is_user_logged_in()) return false;

    return zrz_user_can(get_current_user_id(),$can);
}

function zrz_check_vip($user_id){
    $lv = get_user_meta($user_id,'zrz_lv',true);
    $is_vip = strpos($lv,'vip') !== false ? true : false;
    if($is_vip){
        //检查用户的过期时间
        $user_lv_time = get_user_meta($user_id,'zrz_vip_time',true);
        $lv_setting = zrz_get_lv_settings($lv);

        if(is_array($user_lv_time) && $user_lv_time['end'] != 0){
           
            if(date('Y-m-d H:i:s',time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )) >= $user_lv_time['end']){
              
                if(isset($user_lv_time['oldlv'])){
                    update_user_meta((int)$user_id,'zrz_lv',$user_lv_time['oldlv']);
                }else{
                    zrz_set_lv($user_id,true);
                }
            }
        }
    }
}

//检查小黑屋过期时间
function zrz_check_xiaoheiwu($user_id){
    $h = get_user_meta($user_id,'zrz_abled',true);
    if(!$h) return;
    if(!isset($h['abled']) || !isset($h['end']) || !isset($h['days'])) return;
    if($h['end'] == 0) return;
    if(date("Y-m-d H:i:s", get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + strtotime('+'.$h['days'].' day')) >= $h['end']){
        delete_user_meta($user_id,'zrz_abled');
    }
}