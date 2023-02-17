<?php
namespace app\mobile\controller;
use app\common\library\wechat\WxPay as WxPayclass;
use app\mobile\model\Wxapp as WxappModel;
use app\task\model\Ordernew as OrderModel;
use think\Db;

class Pays extends \think\Controller
{
    //创建订单
    public function goToPay(){
        $form_data = $this->request->param();//获取所有参数
        $member_id = $form_data['people_id'];
        $result = Db::name('member')->where('article_id', $member_id)->find();
        $data['order_no'] = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
        $data['total_price']    = $result['cost'];
        $data['pay_status']     = 10;
        $data['pay_time']       = '';
        $data['transaction_id'] = '';
        $data['user_id']        = $member_id;
        $data['wxapp_id']       = 10024;
        $data['create_time']    = time();
        
        $res = Db::name('order')->insert($data);  //将订单插入数据库
        $url = url('getpay') . '&order_no=' . $data['order_no'];  //跳转下一个方法，带上参数（订单编号）

        $this->success("操作成功",$url);
    }

    /**
     * 下单支付
     */
    public function getpay()
    {
        //获取订单编号
        $order_no = input('order_no');
        //场景类型
        $scene_type = 10;  // @10普通订单  @30拼团
        // 实例化订单模型
        $OrderModel = new OrderModel;
        //获取用户信息
        $open_id = session('user_new')['openid'];
        //订单明细
        $Order = $OrderModel->payDetail($order_no);
        empty($Order) && $this->error('订单不存在');
        // 获取支付成功/支付失败的返回地址
        $url = $this->getOrderUrl(10,$Order->order_id);
        //获取支付配置
        $wxConfig = WxappModel::getWxappCache(10024);  //10024是指这个商城的id
        $WxPay = new WxPayclass($wxConfig, true);
        $pay_price = $Order->total_price;
        //发起统一下单
        $json = $WxPay->unifiedorder($order_no, $open_id, $pay_price, 10024, $scene_type);
        $html = <<<EOF
    <script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',$json,
            function(res){
                //WeixinJSBridge.log(res.err_msg);
                if(res.err_msg == "get_brand_wcpay_request:ok") {
                    layer.open({content: '支付成功',time:2,end:function () {
                        location.replace("$url[0]");
                    }});
                }else{
                    //alert(res.err_code+res.err_desc+res.err_msg);
                    location.replace("$url[1]");
                }
            }
        );
    }

    function callpay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
    callpay();
    </script>
EOF;
        return $this->fetch('getpay', compact('html', 'url'));
    }


    public function getOrderUrl($scene_type = 10, $order_id)
    {
        switch ($scene_type) {
            case 10:
                $go = url('Mobile/Pays/pay_end', array('order_id' => $order_id));
                $back = url('Mobile/User/order_list', array('dataType' => 'payment'));
                return [$go, $back];
                break;
            case 30:
                $go = url('Mobile/User/Sharing_order_detail', array('order_id' => $order_id));
                $back = url('Mobile/User/Sharing_order_list', array('dataType' => 'payment'));
                return [$go, $back];
                break;
        }
    }
}