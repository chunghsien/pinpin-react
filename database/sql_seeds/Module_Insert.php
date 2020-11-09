<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Module_Insert extends AbstractSeeds
{
    protected $table = 'module';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);

        $insert->values([
            'name'=> 'frontend',
        ]);

        $sql->prepareStatementForSqlObject($insert)->execute();

        $insert = $sql->insert($this->table);

        $insert->values([
            'name'=> 'admin',
        ]);

        $sql->prepareStatementForSqlObject($insert)->execute();
    }
}
