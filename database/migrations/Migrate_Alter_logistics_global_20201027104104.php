<?php
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\Store\TableGateway\LogisticsGlobalTableGateway;

class Migrate_Alter_logistics_global_20201027104104 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'alter';

    /**
     *
     * @var string
     */
    protected $table = 'logistics_global';
    
    protected $priority = 3;
    
    public function up()
    {
        $tableGateway = new LogisticsGlobalTableGateway($this->adapter);
        $sets = [
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'shipping',
                'code' => 'CSVCOM',
                'name' => '超商取貨付款',
                'param' => 1,
                'price' => 60,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'shipping',
                'code' => 'CSVCOM',
                'name' => '超商取貨不付款',
                'param' => 2,
                'price' => 60,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'other',
                'code' => 'EmailModify',
                'name' => '付款人電子信箱開放修改',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'other',
                'code' => 'LoginType',
                'name' => '需要登入藍新金流會員',
                'param' =>1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'CREDIT',
                'name' => '信用卡一次付清',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'ANDROIDPAY',
                'name' => 'Google Pay ',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'SAMSUNGPAY',
                'name' => 'Samsung Pay ',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'LINEPAY',
                'name' => 'LINE Pay',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'InstFlag',
                'name' => '信用卡分期付款',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'CreditRed',
                'name' => '信用卡紅利',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'CREDITAE',
                'name' => '信用卡美國運通卡',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'UNIONPAY',
                'name' => '信用卡銀聯卡',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'WEBATM',
                'name' => 'WEBATM',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'VACC',
                'name' => 'ATM轉帳',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                //當該筆訂單金額小於30元或超過2萬元時，即使此參數設定為，MPG付款頁面仍不會顯示此支付方式選項。
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'CVS',
                'name' => '超商代碼繳費',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'ALIPAY',
                'name' => '支付寶',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'manufacturer' => '藍新',
                'method' => 'payment',
                'code' => 'P2G',
                'name' => 'ezPay電子錢包',
                'param' => 1,
                'price' => 0,
                'is_use' => 0,
            ],
            
        ];
        foreach ($sets as $set) {
            if($tableGateway->select($set)->count() == 0) {
                $tableGateway->insert($set);
            }
        }
    }

    public function down()
    {
        //
    }
}
