<?php
namespace Common\Controller;

use Think\Controller;
use Common\Org\OffiAccount;

class IndexController extends Controller
{
    protected function _initialize()
    {
        $wx_config = M('wx_user')->find();     //此处从数据表获取openid和secret	
		$data = (new OffiAccount($wx_config))->getOpenid('snsapi_userinfo'); //微信登录，将openid、secret配置调入OffiAccount，再执行里面的getOpenid()
		$openid = $data['openid']; //获取openid
		//然后可以进行数据库的修改（省略）...
    }
}
