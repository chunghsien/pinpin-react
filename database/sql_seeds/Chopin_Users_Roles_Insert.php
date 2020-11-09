<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Chopin_Users_Roles_Insert extends AbstractSeeds
{
    protected $table = 'roles';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);

        $insert->values([
            'parent_id' => 0,
            'depth' => 0,
            'module_id' => 2,
            'name'=> 'administrator',
        ]);

        $result = $sql->prepareStatementForSqlObject($insert)->execute();
        $administrator_id = $result->getGeneratedValue();

        $insert->values([
            'parent_id' => $administrator_id,
            'depth' => 1,
            'module_id' => 1,
            'name'=> 'member',
        ]);
        $result = $sql->prepareStatementForSqlObject($insert)->execute();
        $last_id = $result->getGeneratedValue();
        $insert->values([
            'parent_id' => $last_id,
            'depth' => 2,
            'module_id' => 1,
            'name'=> 'guest',
        ]);
        $result = $sql->prepareStatementForSqlObject($insert)->execute();

        $insert->values([
            'parent_id' => $administrator_id,
            'depth' => 1,
            'module_id' => 2,
            'name'=> 'manager',
        ]);
        $result = $sql->prepareStatementForSqlObject($insert)->execute();
    }
}
