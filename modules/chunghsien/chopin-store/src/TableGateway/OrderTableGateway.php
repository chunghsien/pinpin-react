<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Where;
use Laminas\Validator\AbstractValidator;
use Chopin\LaminasDb\DB;

class OrderTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = "order";

    /**
     * *訂單正流程
     *
     * @var array
     */
    protected $status = [
        "order_no_status", // 訂單建立
        "credit_account_paid", // 付款完成(信用卡或相關綁定支付)
        "transfer_account_paid", // 轉帳付款完成(ATM轉帳相關)
        "goods_sent_out", // 貨品已寄出
        "goods_sent_out_and_unpaid", // 貨品已寄出(尚未付款)
        "unexpected_situation", // 其他意外狀況
        "delivered_to_store", // 已到店
        "delivered_to_house", // 已到貨，交付管理室或轉至在地物流中心。
        "received_and_paid", // 完成領收且完成付款(超商付款,貨到付款)
        "received", // 完成領收
        "transaction_complete", // 交易完成
    ];

    /**
     * *訂單逆流程(退貨，先不設計換貨，一律先退貨再重新購買)，資料表狀態是顯示負數
     *
     * @var array
     */
    protected $reverse_status = [
        "order_reverse_status_processing",
        "cancel_appication", // 訂單取消申請
        "cancel_agree", // 訂單取消同意
        "cancel_complete", // 訂單取消完成
        "order_reverse_agree", // 退貨同意
        "order_reverse_picup", // 逆物流已取貨
        "order_reverse_delivered", // 退貨店家已收到
        "order_reverse_complete", // 完成退貨
        "third_party_pay_process_fail", // 第三方金流處理錯誤
        'cancel_the_deal', // 取消交易
    ];

    public function insert($values)
    {
        $keys = array_keys($values);
        if (is_int($keys[0])) {
            foreach ($values as &$value) {
                $value['created_at'] = date("Y-m-d H:i:s");
            }
        } else {
            $values['created_at'] = date("Y-m-d H:i:s");
        }
        return parent::insert($values);
    }

    public function getStatusOptions()
    {
        $translator = AbstractValidator::getDefaultTranslator();
        $options = [
            '_order_status_' => [],
            '_order_reverse_status_' => [],
        ];
        foreach ($this->status as $index => $value) {
            $options['_order_status_'][] = [
                'label' => $translator->translate($value, 'chopin-store'),
                'value' => $index,
            ];
        }

        foreach ($this->reverse_status as $index => $value) {
            if ($index > 0) {
                $options['_order_reverse_status_'][] = [
                    'label' => $translator->translate($value, 'chopin-store'),
                    'value' => - ($index),
                ];
            }
        }

        return $options;
    }

    /**
     *
     * @param array $order_row
     * @return array
     */
    public function parseOrder($order_row)
    {
        $_order_row = [];
        if ($order_row || (isset($order_row['order_row']) && $order_row['order_row'])) {
            $_order_row = isset($order_row['order_row']) ? $order_row['order_row'] : $order_row;
            $translator = AbstractValidator::getDefaultTranslator();
            $_order_row['pay_method'] = $translator->translate($_order_row['pay_method'], 'chopin-store');
            $status = $_order_row['status'];
            if ($status < 0) {
                $_order_row['status'] = $translator->translate($this->reverse_status[abs($status)], 'chopin-store');
            } else {
                $_order_row['status'] = $translator->translate($this->status[$status], 'chopin-store');
            }
            if (isset($_order_row['third_party_pay_response']) && $_order_row['third_party_pay_response']) {
                $response = $_order_row['third_party_pay_response']['response'];
                $_order_row['third_party_pay_response']['response'] = json_decode($response, true);
            }
        }
        return $_order_row;
    }

    public function getOrderList($users_id)
    {
        $translator = AbstractValidator::getDefaultTranslator();

        $resultset = DB::selectFactory([
            'table' => $this->table,
            'where' => [
                [
                    'equalTo',
                    'and',
                    [
                        'users_id',
                        $users_id
                    ]
                ],
                [
                    'isNull',
                    'and',
                    [
                        'deleted_at'
                    ]
                ],
            ],
            'order' => [
                'id desc',
            ],
        ])->toArray();
        foreach ($resultset as &$order) {
            $status = $order['status'];
            if ($status < 0) {
                $order['status'] = $translator->translate($this->reverse_status[abs($status)], 'chopin-store');
            } else {
                $order['status'] = $translator->translate($this->status[$status], 'chopin-store');
            }
            $pay_method = $order['pay_method'];
            $order['pay_method'] = $translator->translate($pay_method, 'chopin-store');
            // chopin-store
        }
        return $resultset;
    }

    public function buildOrderSerial($prefix = 'PP')
    {
        if (APP_ENV != 'production') {
            $prefix = "LL";
        }
        $serial = date("ymd");
        $where = new Where();
        $where->between('created_at', date("Y-m-d") . ' 00:00:00', date("Y-m-d") . ' 23:59:59');
        // $where->isNull('deleted_at');
        $num = ($this->select($where)->count() + 1);
        $tail = str_pad($num, 10, '0', STR_PAD_LEFT);
        return $prefix . $serial . $tail;
    }
}
