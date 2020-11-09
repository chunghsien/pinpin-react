<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Chopin_Member_Roles_Insert extends AbstractSeeds
{
    protected $table = 'member_roles';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);
        $insert->values([
            'language_id' => 119,
            'locale_id' => 229,
            'name'=> '一般會員',
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
}
