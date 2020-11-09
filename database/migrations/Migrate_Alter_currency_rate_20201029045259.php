<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LanguageHasLocale\TableGateway\CurrencyRateTableGateway;
use Chopin\LanguageHasLocale\TableGateway\CurrenciesTableGateway;

class Migrate_Alter_currency_rate_20201029045259 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'alter';
    
    
    /**
     * 
     * @var string
     */
    protected $table = 'currency_rate';
    
    protected $priority = 3;
    
    const TWD_ID = 139;
    
    public function up()
    {
        $tableGateway = new CurrencyRateTableGateway($this->adapter);
        $currenciesTableGateway = new CurrenciesTableGateway($this->adapter);
        $where = $currenciesTableGateway->getSql()->select()->where;
        $where->notEqualTo('id', self::TWD_ID);
        $currenciesResult = $currenciesTableGateway->select($where);
        foreach ($currenciesResult as $row) {
            $set = [
                'main_currencies_id' => self::TWD_ID,
                'rate_currencies_id' => $row->id
            ];
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
