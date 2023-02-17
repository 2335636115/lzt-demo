<?php

namespace app\common\library\wechat;

use EasyWeChat\Factory;
use app\task\model\Ordernew as OrderModel;
use app\task\model\Setting as SettingModel;
use app\common\exception\BaseException;
use app\common\model\Wxapp as WxappModel;
use app\common\library\sms\Driver as SmsDriver;
use app\mall\model\dealer\Withdraw as WithdrawModel;

// use app\task\model\sharing\Order as OrderSharingModel;
// use app\task\model\score\Order as OrderScoreModel;

/**
 * 微信支付
 * Class WxPay
 * @package app\common\library\wechat
 */
class WxPay
{
    private $config; // 微信支付配置
    protected $payment;
    /**
     * 构造方法
     * WxPay constructor.
     * @param $config
     */
    public function __construct($config, $is_pay = false)
    {
        if (!empty($config)) {
            $this->config = $config;
            //获取配置
            $arr = [
                'app_id'  => $this->config['appid'], // AppID
                'mch_id'  => $this->config['mchid'],
                'key'     => $this->config['apikey'],
            ];
            //获取证书目录
            !$is_pay && $arr = array_merge($arr, $this->getCertPem());
            $this->payment = Factory::payment($arr);
        }
    }

    /**
     * 统一下单API
     * @param $order_no
     * @param $openid
     * @param $total_fee
     * @return array
     * @throws BaseException
     */
    public function unifiedorder($order_no, $openid, $total_fee, $mall_id, $scene_type = 10)
    {
        $attach = [
            'mall_id' => $mall_id,
            'scene_type' => $scene_type
        ];
        //组织数据
        $attributes = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => $order_no,
            'detail' => '订单付款',
            'out_trade_no' => $order_no,
            'total_fee' => $total_fee * 100, // 价格:单位分
            'notify_url' => base_url() . 'noticenew.php',  // 异步通知地址
            'spbill_create_ip' => \request()->ip(),
            'openid' => $openid,
            // 'product_id' => $order_no, // $message['product_id'] 则为生成二维码时的产品 ID
            'attach' => json_encode($attach),
        ];
        $result = $this->payment->order->unify($attributes);
        // 请求失败
        if ($result['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => $result['return_msg'], 'code' => -10]);
        }
        if ($result['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => $result['err_code_des'], 'code' => -10]);
        }
        $jssdk = $this->payment->jssdk;
        $json = $jssdk->bridgeConfig($result['prepay_id']);
        return $json;
    }

    /**
     * 支付成功异步通知
     * @param \app\task\model\Order $OrderModel
     * @throws BaseException
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    public function notify()
    {

        //        $xml = <<<EOF
        //          <xml>
        //        <appid>wx9f3ffb724944ae27</appid>\n
        //	<attach>{&quot;mall_id&quot;:10009,&quot;scene_type&quot;:&quot;30&quot;}</attach>\n
        //	<bank_type>CFT</bank_type>\n
        //	<cash_fee>1</cash_fee>\n
        //	<fee_type>CNY</fee_type>\n
        //	<is_subscribe>Y</is_subscribe>\n
        //	<mch_id>1311031401</mch_id>\n
        //	<nonce_str>5c9b4c043e3a9</nonce_str>\n
        //	<openid>owR7RjkBx90lWZrrVeU99R6g7qoQ</openid>\n
        //	<out_trade_no>20190327505199575056</out_trade_no>\n
        //	<result_code>SUCCESS</result_code>\n
        //	<return_code>SUCCESS</return_code>\n
        //	<sign>3890E0D0149D11C87EAF5EA16602237F</sign>\n
        //	<time_end>20190327181017</time_end>\n
        //	<total_fee>1</total_fee>\n
        //	<trade_type>JSAPI</trade_type>\n
        //	<transaction_id>4200000260201903271774383859</transaction_id>\n
        //          </xml>
        //EOF;

        //
        //        $data = array (
        //            'appid' => 'wx9f3ffb724944ae27',
        //            'attach' => '{"mall_id":10009,"scene_type":"30"}',
        //            'bank_type' => 'CFT',
        //            'cash_fee' => '1',
        //            'fee_type' => 'CNY',
        //            'is_subscribe' => 'Y',
        //            'mch_id' => '1311031401',
        //            'nonce_str' => '5c9c76622033b',
        //            'openid' => 'owR7RjqFbbtvdDgAA5CiSpbTC_Pc',
        //            'out_trade_no' => '20190328481005555509',
        //            'result_code' => 'SUCCESS',
        //            'return_code' => 'SUCCESS',
        //            'sign' => 'F43F9058D8E4504AB1FD3B1C0789F272',
        //            'time_end' => '20190328152320',
        //            'total_fee' => '1',
        //            'trade_type' => 'JSAPI',
        //            'transaction_id' => '4200000281201903288284283869',
        //            );

        if (!$xml = file_get_contents('php://input')) {
            $this->returnCode(false, 'Not found DATA');
        }
        //        // 将服务器返回的XML数据转化为数组
        $data = $this->fromXml($xml);

        $this->doLogs($xml);
        $this->doLogs($data);

        $attach = json_decode($data['attach'], true);

        $wxapp_id = $attach['mall_id'];
        $config = WxappModel::getWxappCache($wxapp_id);
        $config = $config->toArray();
        $arr = [
            'app_id'  => $config['appid'], // AppID
            'mch_id'  => $config['mchid'],
            'key'     => $config['apikey'],
        ];
        $payment = Factory::payment($arr);

        $response = $payment->handlePaidNotify(function ($message, $fail) {
            if ($message['return_code'] === 'SUCCESS' && $message['result_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状)态
                $attach = json_decode($message['attach'], true);
                // 实例化订单模型
                $OrderModel = $this->instanceOrderModel($attach['scene_type']);
                //使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
                $order = $OrderModel->payDetail($message['out_trade_no']);
                empty($order) && $this->returnCode(true, '订单不存在');
                // 订单支付成功业务处理
                $order->paySuccess($message['transaction_id']);
                // 返回状态
                $this->returnCode(true, 'OK');
            }
        });
        // 返回状态
        $this->returnCode(false, '签名失败');
        exit;
    }



    /**
     * 返回状态给微信服务器
     * @param bool $is_success
     * @param string $msg
     */
    private function returnCode($is_success = true, $msg = null)
    {
        $xml_post = $this->toXml([
            'return_code' => $is_success ? 'SUCCESS' : 'FAIL',
            'return_msg' => $is_success ? 'OK' : $msg,
        ]);
        die($xml_post);
    }


    public function instanceOrderModel($scene_type = 10)
    {
        // switch ($scene_type) {
        //     case 10:
        //         return (new OrderModel);
        //         break;
        //     case 30:
        //         return (new OrderSharingModel);
        //         break;
        //     case 40:
        //         return (new OrderScoreModel);
        //         break;
        // }
        // return null;
        return (new OrderModel);
    }
    /**
     * 写入日志记录
     * @param $values
     * @return bool|int
     */
    private function doLogs($values)
    {
        return write_log($values, __DIR__);
    }

   

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    private function fromXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }



    /**
     * 获取cert证书文件
     * @return array
     * @throws BaseException
     */
    private function getCertPem()
    {
        if (empty($this->config['cert_pem']) || empty($this->config['key_pem'])) {
            throw new BaseException(['msg' => '请先到后台设置填写微信支付证书文件']);
        }
        // cert目录
        $filePath = __DIR__ . '/cert/' . $this->config['wxapp_id'] . '/';
        !is_dir($filePath) && mkdir($filePath, 0755, true);
        $cert_path = $filePath . 'cert.pem';
        !file_exists($cert_path) && file_put_contents($cert_path, $this->config['cert_pem']);
        $key_path = $filePath . 'key.pem';
        !file_exists($key_path) && file_put_contents($key_path, $this->config['key_pem']);
        return compact('cert_path', 'key_path');
    }


    /**
     * 输出xml字符
     * @param $values
     * @return bool|string
     */
    private function toXml($values)
    {
        if (
            !is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}
