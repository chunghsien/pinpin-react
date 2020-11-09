<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class TwBankListInsert extends AbstractSeeds
{
    protected $table = 'tw_bank_list';
    
    public function run()
    {
        $sql = new Sql($this->adapter);
        
        if (($handle = fopen(__DIR__."/Comm1_MEMBER.csv", "r")) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                if($row > 0) {
                    $insert = $sql->insert($this->table);
                    $insert->values([
                        "service"=> $data[0],
                        "bic"=> $data[1],
                        "name"=> $data[2],
                    ]);
                    $sql->prepareStatementForSqlObject($insert)->execute();
                }
                $row++;
                
            }
        }
    }
}