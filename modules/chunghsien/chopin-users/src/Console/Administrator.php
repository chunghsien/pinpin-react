<?php

namespace Chopin\Users\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Laminas\Db\Sql\Sql;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class Administrator extends Command
{
    protected static $defaultName = 'users:administrator';


    /**
     *
     * @var ServiceManager
     */
    private $container;

    /**
     *
     * @var Sql
     */
    protected $sql;


    public function __construct(ServiceManager $container=null)
    {
        parent::__construct();

        if ( ! $container) {
            global $container;
        }

        $this->container = $container;
        $adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->sql = new Sql($adapter);
    }

    protected function configure()
    {
        //InputOption::VALUE_OPTIONAL
        $this->addOption('module', null, InputOption::VALUE_OPTIONAL, '(app|backend|all)');
        $this->setDescription("套用或刷新administrator的權限");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$classname = 'Migrate_';
        $options = $input->getOptions();

        $module = strtolower($options['module']);

        $permissionSelect = $this->sql->select(AbstractTableGateway::$prefixTable.'permission')->columns(['id']);

        if ($module != 'all') {
            $permissionSelect->where(new Expression("name like ?", $module.'%'));
        }
        $permissionsResultSet = new ResultSet();
        $permissionsResultSet->initialize($this->sql->prepareStatementForSqlObject($permissionSelect)->execute());

        $adminSelect = $this->sql->select(AbstractTableGateway::$prefixTable.'roles')->columns(['id'])->where(['name' => 'administrator']);
        $administrator = $this->sql->prepareStatementForSqlObject($adminSelect)->execute()->current();

        try {
            foreach ($permissionsResultSet as $permission) {
                $data = [
                    'roles_id' => $administrator['id'],
                    'permission_id' => $permission['id'],
                ];

                $existSelect = $this->sql->select(AbstractTableGateway::$prefixTable.'roles_has_permission')->columns(['roles_id'])->where($data);
                if ($this->sql->prepareStatementForSqlObject($existSelect)->execute()->count()) {
                    $output->writeln('<comment>該權限關聯( roles.'.$administrator['id'].' , permission.'.$permission['id'].')已建立</comment>');
                } else {
                    $insert = $this->sql->insert(AbstractTableGateway::$prefixTable.'roles_has_permission')->values($data);
                    $this->sql->prepareStatementForSqlObject($insert)->execute();
                    $output->writeln('<info>該權限關聯( roles.'.$administrator['id'].' , permission.'.$permission['id'].')建立成功</info>');
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
