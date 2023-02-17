<?php

namespace Common\Org;

class OffiAccount
{
    private $wx_config;

    /**
     * 构造函数
     * @param $config
     */
    public function __construct($config)
    {
        $this->wx_config = $config;
    }

    /**
     * 网页授权登录获取 openid
     */
    public function getOpenid($scope = 'snsapi_base')
    {
        if ($_SESSION['wxuserinfo']['openid']) {
            return $_SESSION['wxuserinfo'];
        }

        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $url = $this->createOauthUrlForCode($scope); // 获取 code地址
            Header("Location: $url");  // 跳转到微信授权页面 需要用户确认登录的页面
            exit();
        } else {
            // 上面跳转, 这里跳了回来，再获取code码，然后获取openid
            $code = $_GET['code'];   //获取code码
            $data = $this->getOpenidFromCode($code);  //获取openid
            if ($data['scope'] == 'snsapi_userinfo') {
                $data2 = $this->getUserInfo($data['access_token'], $data['openid']);  //获取用户信息
                $data = array_merge($data, $data2);  //将数组合并，相同字符串键的值覆盖前一个值
            }
            return $data;
        }
    }
    
    /**
     * 获取当前的url地址，以获取code
     */
    private function getUrl()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }
    
    /**
     * 构造获取code的url连接
     */
    private function createOauthUrlForCode($scope)
    {
        $options = [
            'https://open.weixin.qq.com/connect/oauth2/authorize',
            '?appid=' . $this->wx_config['appid'],
            '&redirect_uri=' . urlencode($this->getUrl()),
            '&response_type=code',
            '&scope=' . $scope,
            '&state=STATE',
            '#wechat_redirect',
        ];
        return implode('', $options);
    }

    /**
     * 通过appid、appsecret、code来获取openid
     */
    public function getOpenidFromCode($code)
    {
        $apiUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->wx_config['appid']}&secret={$this->wx_config['appsecret']}&code={$code}&grant_type=authorization_code";
        $return = httpRequest($apiUrl, 'GET');
        $return = json_decode($return, true);

        return $return;
    }

    /**
     * 通过access_token和openid来获取用户信息
     */
    public function getUserInfo($access_token, $openid)
    {
        $apiUrl = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $return = httpRequest($apiUrl, 'GET');
        $return = json_decode($return, true);

        return $return;
    }
}
