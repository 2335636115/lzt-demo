<?php

namespace app\task\model;

use app\common\service\Message;
use app\common\service\order\Printer;
use app\common\enum\OrderStatus as OrderStatusEnum;
use app\common\model\Ordernew as OrderModel;
use app\task\model\dealer\Apply as DealerApplyModel;
use app\task\model\WxappPrepayId as WxappPrepayIdModel;

/**
 * 订单模型
 * Class Order
 * @package app\common\model
 */
class Ordernew extends OrderModel
{
    /**
     * 待支付订单详情
     * @param $order_no
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function payDetail($order_no)
    {
        return self::get(['order_no' => $order_no, 'pay_status' => 10]);
    }

    /**
     * 订单支付成功业务处理
     * @param $transaction_id
     * @throws \Exception
     * @throws \think\Exception
     */
    public function paySuccess($transaction_id)
    {
        // 更新付款状态
        $this->updatePayStatus($transaction_id);
    }

    /**
     * 更新付款状态
     * @param $transaction_id
     * @return false|int
     * @throws \Exception
     */
    public function updatePayStatus($transaction_id)
    {
        $this->startTrans();
        try {
            // 更新商品库存、销量
            (new Goods)->updateStockSales($this['goods']);
            // 更新订单状态
            $this->save([
                'pay_status' => 20,
                'pay_time' => time(),
                'transaction_id' => $transaction_id
            ]);
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 购买指定商品成为分销商
     * @param $user_id
     * @param $goodsList
     * @param $wxapp_id
     * @return bool
     * @throws \think\exception\DbException
     */
    private function becomeDealerUser($user_id, $goodsList, $wxapp_id)
    {
        // 整理商品id集
        $goodsIds = [];
        foreach ($goodsList as $item) {
            $goodsIds[] = $item['goods_id'];
        }
        $model = new DealerApplyModel;
        return $model->becomeDealerUser($user_id, $goodsIds, $wxapp_id);
    }

    /**
     * 获取订单列表
     * @param array $filter
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($filter = [])
    {
        return $this->with(['goods' => ['refund']])->where($filter)->select();
    }
}
