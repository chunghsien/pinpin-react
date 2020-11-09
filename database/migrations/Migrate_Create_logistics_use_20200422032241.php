<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_logistics_use_20200422032241 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    /**
     *
     * @var string
     */
    protected $table = 'logistics_use';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', [
            'unsigned' => true,
            'auto_increment' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('order_id', 'int', [
            'unsigned' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('manufacturer', 'varchar', [
            'length' => 45,
            'comment' => "服務商:綠界,藍新etc.",
        ]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('merchant_trade_no', 'varchar', [
            'length' => 20,
            'comment' => "廠商交易編號(有可能是我們的訂單編號)",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('rtn_code', 'smallint', [
            'unsigned' => true,
            'comment' => "目前物流狀態",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('rtn_msg', 'varchar', [
            'length' => 255,
            'comment' => "物流狀態說明",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('all_pay_logistics_id', 'varchar', [
            'length' => 20,
            'comment' => "綠界科技的物流交易編號",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('logistics_type', 'varchar', [
            'length' => 20,
            'comment' => "物流類型",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('logistics_sub_type', 'varchar', [
            'length' => 20,
            'comment' => "物流子類型",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('goods_amount', 'int', [
            'unsigned' => true,
            'comment' => "商品遺失賠償依據，僅可使用數字",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('receiver_namel', 'varchar', [
            'length' => 60,
            'comment' => "收件人姓名",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('receiver_phone', 'varchar', [
            'length' => 20,
            'comment' => "收件人電話",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('receiver_cell_phone', 'varchar', [
            'length' => 20,
            'comment' => "收件人手機",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('receiver_email', 'varchar', [
            'length' => 200,
            'comment' => "收件人Email",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('receiver_address', 'varchar', [
            'length' => 45,
            'comment' => "收件人地址",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('cvs_payment_no', 'varchar', [
            'length' => 15,
            'comment' => "寄貨編號\n若超商取貨7-ELEVEN超商、全家超商為店到店，則會回傳。",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('cvs_validation_no', 'varchar', [
            'length' => 10,
            'comment' => "驗證碼\n若超商取貨為7-ELEVEN超商店到店，則會回傳。",
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('booking_note', 'varchar', [
            'length' => 50,
            'comment' => "托運單號\n物流類型[logstics_type]為『宅配』時有值。",
        ]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
            'on_update' => true,
        ]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->table . '_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\ForeignKey('fk_' . $this->tailTable . '_order', 'order_id', self::$prefixTable . 'order', 'id'));
    }


    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
