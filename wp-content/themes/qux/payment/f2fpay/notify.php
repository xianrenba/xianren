<?php
header('Content-type:text/html; Charset=utf-8');
require_once 'config.php'; 

if (empty($_POST)) {
    echo '非法请求';exit();
}

// Debug 
function _debug_log($text){
	$file = THEME_DIR.'/log.log';
    file_put_contents($file, $text.PHP_EOL, FILE_APPEND);
}

//$aliPay = new AlipayService();
//$aliPay->alipayPublicKey($alipayPublicKey);

$aliPay = new AlipayServiceCheck($alipayPublicKey);
//验证签名
$result = $aliPay->rsaCheck($_POST,$_POST['sign_type']);
if($result){
	
	if($_POST['trade_status'] == 'TRADE_SUCCESS'){
		
		//商户订单号
		$out_trade_no = $_POST['out_trade_no'];
		//支付宝交易号
		$trade_no = $_POST['trade_no'];
        
        $success_time = $_POST['gmt_payment'];
        
        $alipay_id = $_POST['buyer_id'];
        
        //_debug_log('订单号：'.$_POST['out_trade_no'].'已付款。  日期：'.$success_time);
        
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'um_orders';
        $row = $wpdb->get_row("select * from ".$table." where order_id=".$out_trade_no);
        
        if($row){
        	if($row->order_status<=3){
        		$wpdb->query( "UPDATE $table SET order_status=4, trade_no='$trade_no', order_success_time='$success_time', user_alipay='$alipay_id' WHERE order_id='$out_trade_no'" );
        		update_success_order_product($row->product_id,$row->order_quantity);
        		if(!empty($row->user_email)){$email = $row->user_email;}
        		//发送订单状态变更email
        		store_email_template($out_trade_no,'',$email);
        		//发送购买可见内容或下载链接或会员状态变更
        		send_goods_by_order($out_trade_no,'',$email);
        		echo 'success';exit();
        	}else{
        		echo 'success';exit();
        	}
        }
		
	}
	//_debug_log('订单号：'.$_POST['out_trade_no'].'付款中。  日期：'.date("Y-m-d h:i:s",time()));
	echo 'success';exit();
 
}else{
	//_debug_log('订单号：'.$_POST['out_trade_no'].'验证错误。  日期：'.date("Y-m-d h:i:s",time()));
    echo 'error';exit();
}
exit();

class AlipayServiceCheck
{
    //支付宝公钥
    protected $alipayPublicKey;
    protected $charset;

    public function __construct($alipayPublicKey)
    {
        $this->charset         = 'utf8';
        $this->alipayPublicKey = $alipayPublicKey;
    }

    public function rsaCheck($params)
    {
        $sign     = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    public function verify($data, $sign, $signType = 'RSA')
    {
        $pubKey = $this->alipayPublicKey;
        $res    = "-----BEGIN PUBLIC KEY-----\n" .
        wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool) openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION, '5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool) openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }

    protected function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }

        if ($value === null) {
            return true;
        }

        if (trim($value) === "") {
            return true;
        }

        return false;
    }

    public function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i                = 0;
        foreach ($params as $k => $v) {

            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 修复转义导致签名失败
                $v = stripslashes($v);

                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {

                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }

                $i++;

            }
        }

        unset($k, $v);
        return $stringToBeSigned;
    }
    public function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }
}
