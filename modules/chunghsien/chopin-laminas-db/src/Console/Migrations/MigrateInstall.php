<?php

namespace Chopin\LaminasDb\Console\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Ddl;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Metadata\Source;
use Chopin\LaminasDb\ColumnCacheBuilder;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class MigrateInstall extends Command
{
    protected static $defaultName = 'migrate:install';

    /**
     *
     * @var Sql
     */
    protected $sql;

    public function __construct($name = null)
    {
        global $container;
        parent::__construct($name);
        $adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->sql = new Sql($adapter);
    }

    protected function configure()
    {
        $this->setDescription("創建遷移資料表");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // global $container;
        $pt = AbstractTableGateway::$prefixTable;
        $createTable = new Ddl\CreateTable($pt.'migrations');
        $createTable->addColumn(new Ddl\Column\Integer('id', false, null, [
            'auto_increment' => true,
        ]));
        $createTable->addColumn(new Ddl\Column\Varchar('migration', '255'));
        $createTable->addColumn(new Ddl\Column\Integer('batch'));
        $createTable->addColumn(new Ddl\Column\Datetime('created_at'));
        $createTable->addConstraint(new Ddl\Constraint\PrimaryKey('id'));
        $metadata = Source\Factory::createSourceFromAdapter($this->sql->getAdapter());
        $tables = [];
        /**
         *
         * @var \Laminas\Db\Metadata\Object\TableObject[] $tableObjects
         */
        $tableObjects = $metadata->getTables();
        foreach ($tableObjects as $table) {
            $tables[] = $table->getName();
        }
        if (false === array_search('migrations', $tables)) {
            /**
             *
             * @var \Laminas\Db\Adapter\Adapter $adapter
             */
            $adapter = $this->sql->getAdapter();
            $adapter->query($this->sql->buildSqlString($createTable), Adapter::QUERY_MODE_EXECUTE);
            $output->writeln('<info>創建遷移資料表成功</info>');

            ColumnCacheBuilder::createColumns($adapter, 'migrations');
        } else {
            $output->writeln('<info>遷移資料表已建立</info>');
        }
    }
}
