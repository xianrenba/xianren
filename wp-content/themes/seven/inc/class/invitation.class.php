<?php
/*
* 邀请注册
*/
class Zrz_Invitation_Reg{

	//获得邀请人的ID
	public function get_invitation_user_id($code){
		require ZRZ_THEME_DIR. '/inc/class/decode.class.php';

		$obj = new XDeode(9);
		$id = $obj->decode($code);
		if(is_numeric($id)){
			if (!isset($_SESSION)) {
				session_start();
			}
			$_SESSION['zrz_inv_id'] = $id;
			return $id;
		}

		return false;
	}

	//生成邀请人的code
	public function set_invitation_code($user_id){

		require ZRZ_THEME_DIR. '/inc/class/decode.class.php';
		$obj = new XDeode(9);
		$code = $obj->encode($user_id);
		if($code){
			return $code;
		}

		return false;
	}

	//给邀请人和自己添加积分
	public function do_credit($user_id){
		if (!isset($_SESSION)) {
				session_start();
			}

		if(!isset($_SESSION['zrz_inv_id'])) return;

		$_user_id = $_SESSION['zrz_inv_id'];

		//成功邀请获得的积分
		$credit = zrz_get_credit_settings('zrz_credit_invitation');

		//被邀请人获得的积分
		$be_credit = zrz_get_credit_settings('zrz_credit_be_invitation');

		//邀请人添加积分和通知
		$init = new Zrz_Credit_Message($_user_id,39);
		$add_msg = $init->add_message($user_id, $credit,0,'');

		//被邀请人添加积分和通知
		$_init = new Zrz_Credit_Message($user_id ,40);
		$_add_msg = $_init->add_message($_user_id, $be_credit,0,'');

		unset($_SESSION['zrz_inv_id']);

		if($add_msg && $_add_msg){
			return true;
		}
		return false;
	}
}
