<?php
class Payjs
{
    private $url = 'https://payjs.cn/api/native';
    private $key;            // 填写通信密钥
    private $mchid;          // 特写商户号

    public function __construct($data=null,$key,$mchid) {
        $this->data = $data;
        $this->key = $key;
        $this->mchid = $mchid;
    }

    public function pay(){
        $data = $this->data;

        $data['mchid'] = $this->mchid;
        $data['sign'] = $this->sign($data);

        return $this->post($data, $this->url);
    }

    public function post($data, $url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $rst = curl_exec($ch);
        curl_close($ch);

        return $rst;
    }

    public function sign(array $attributes) {
        ksort($attributes);
        $sign = strtoupper(md5(urldecode(http_build_query($attributes)) . '&key=' . $this->key));
        return $sign;
    }
}
