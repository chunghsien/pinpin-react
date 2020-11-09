<?php
use Symfony\Component\Console\Output\ConsoleOutput;
use Laminas\Db\TableGateway\TableGateway;
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Chopin_Users_Users_Has_Roles_Insert extends AbstractSeeds
{
    protected $table = 'users_has_roles';

    public function run()
    {
        try {
            $output = new ConsoleOutput();

            $usersTableGateway = new TableGateway(self::$prefixTable . 'users', $this->adapter);
            $usersRow = $usersTableGateway->select([
                'account' => 'admin',
            ])->current();

            $rolesTableGateway = new TableGateway(self::$prefixTable . 'roles', $this->adapter);
            $rolesRows = $rolesTableGateway->select([
                'name' => 'administrator',
            ])->current();
            $usersHasRolesTableGateway = new TableGateway(self::$prefixTable . 'users_has_roles', $this->adapter);
            $usersHasRolesTableGateway->insert([
                'users_id' => $usersRow->id,
                'roles_id' => $rolesRows->id,
            ]);
            // $output->writeln('<info>users</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
