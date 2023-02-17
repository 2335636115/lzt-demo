<?php
namespace Home\Controller;

header("Content-type: text/html; charset=utf-8");

class IndexController
{
    protected function _initialize()
    {
        parent::_initialize();
    }
   
    public function wxlogin()
    {
		//获取前端发送的code
        $code = I('code');  
		//拼接API，用curl请求
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $result = $this->curl($url, [
                    'appid' => 'wxf49810e84ee7825b',
                    'secret' => 'd9919b67f9b16254638c136f110ebc66',
                    'grant_type' => 'authorization_code',
                    'js_code' => $code
                ]);
        //curl请求接口后，会返回openid和access_token，然后可以进行数据库的修改（省略）...
        $this->ajaxReturn($result);
    }
    
    //请求接口的方法
    public function curl($url, $data = [])
    {
        if (!empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
