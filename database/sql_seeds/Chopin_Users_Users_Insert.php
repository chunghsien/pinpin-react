<?php
use Symfony\Component\Console\Output\ConsoleOutput;
use Laminas\Db\Sql\Sql;
use Laminas\Math\Rand;
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\TableGateway\TableGateway;

class Chopin_Users_Users_Insert extends AbstractSeeds
{
    protected $table = 'users';

    public function run()
    {
        try {
            $sql = new Sql($this->adapter);

            $output = new ConsoleOutput();

            $insert = $sql->insert($this->table);

            $charlist = "`1234567890-=~!@#$%^&*()_+qwertyuiop[]QWERTYUIOP{}|asdfghjkl;ASDFGHJKL:zxcvbnm,.ZXCVBNM<>?";
            $salt = Rand::getString(8, $charlist);
            $password = Rand::getString(16, $charlist);
            $insert->values([
                'account' => 'admin',
                'depth' => 0,
                'salt' => $salt,
                'password' => password_hash($password . $salt, PASSWORD_DEFAULT),
            ]);

            $sql->prepareStatementForSqlObject($insert)->execute();

            file_put_contents('storage/admin_init_'.date("Ymd").'.json', json_encode(['account' => 'admin', 'password' => $password]));
            $output->writeln('<info>最高使用者 administrator   建立成功 ，密碼:' . $password . ' </info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
