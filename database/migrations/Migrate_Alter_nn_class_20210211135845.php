<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Chopin\Newsletter\TableGateway\NnClassTableGateway;

class Migrate_Alter_nn_class_20210211135845 extends AbstractMigration
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
    protected $table = 'nn_class';

    protected $priority = 2;

    public function up()
    {
        $data = [
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "娛樂"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "國際"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "政治"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "社會地方"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "財經"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "運動"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "生活"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "科技"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "健康"
            ],
            [
                "language_id" => 119,
                "locale_id" => 229,
                "name" => "天氣"
            ],
        ];
        $tablegateway = new NnClassTableGateway($this->adapter);
        foreach ($data as $set) {
            $tablegateway->insert($set);
        }
    }

    public function down()
    {
        //
    }
}
