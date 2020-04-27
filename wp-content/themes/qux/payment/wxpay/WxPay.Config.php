<?php
/**
*
* example目录下为简单的支付样例，仅能用于搭建快速体验微信支付使用
* 样例的作用仅限于指导如何使用sdk，在安全上面仅做了简单处理， 复制使用样例代码时请慎重
* 请勿直接直接使用样例对外提供服务
* 
**/
require_once(dirname(__FILE__)."/../../../../../wp-load.php");
require_once "lib/WxPay.Config.Interface.php";

/**
*
* 该类需要业务自己继承， 该类只是作为deamon使用
* 实际部署时，请务必保管自己的商户密钥，证书等
* 
*/

class WxPayConfig extends WxPayConfigInterface{
	
	private $appid = '';
    private $mchid = '';
	private $key = '';
	private $appsecret = ''; 
	
	//构造方法
	public function __construct() {
        $this->appid = _hui('wechat_appid');
        $this->mchid = _hui('wechat_mchid');
        $this->key = _hui('wechat_key');
        //$this->appsecret = _hui('wechat_secret');
    }

	
	//=======【基本信息设置】=====================================
	public function GetAppId(){
		return $this->appid;
	}
	public function GetMerchantId(){
		return $this->mchid;
	}
	
	//=======【支付相关配置：支付成功回调地址/签名方式】===================================
	public function GetNotifyUrl(){
		return "";
	}
	public function GetSignType(){
		return "HMAC-SHA256";
	}

	//=======【curl代理设置】===================================
	public function GetProxy(&$proxyHost, &$proxyPort){
		$proxyHost = "0.0.0.0";
		$proxyPort = 0;
	}
	

	//=======【上报信息配置】===================================
	public function GetReportLevenl(){
		return 1;
	}


	//=======【商户密钥信息-需要业务方继承】===================================
	public function GetKey(){
		return $this->key;
	}
	
	public function GetAppSecret(){
		return $this->appsecret;
	}


	//=======【证书路径设置-需要业务方继承】=====================================
	public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath){
		$sslCertPath = '../cert/apiclient_cert.pem';
		$sslKeyPath = '../cert/apiclient_key.pem';
	}
}