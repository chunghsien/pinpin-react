<?php

namespace Chopin\LaminasDb\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Db\Adapter\Adapter;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class TablePrefix extends Command
{
    protected static $defaultName = 'prefix:table';


    /**
     *
     * @var ServiceManager
     */
    private $container;


    public function __construct(ServiceManager $container=null)
    {
        parent::__construct();

        if ( ! $container) {
            global $container;
        }

        $this->container = $container;
    }

    protected function configure()
    {
        $this->addArgument('rm', InputArgument::OPTIONAL, '移除之前的前置詞');
        $this->setDescription("替所有資料表加入前置詞(prefix)");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         *
         * @var Adapter $adapter
         */
        $adapter = $this->container->get(Adapter::class);
        $metadata = \Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter);
        $tableNames = $metadata->getTableNames();
        AbstractTableGateway::$prefixTable = config('db.adapters.'.Adapter::class)['prefix'];
        $argment = $input->getArgument('rm');
        foreach ($tableNames as $table) {
            $prefix = AbstractTableGateway::$prefixTable;
            if ($argment) {
                if (preg_match('/^'.$prefix.'/', $table)) {
                    $newName = preg_replace('/^'.$prefix.'/', '', $table);
                    $alter = sprintf('ALTER TABLE `%s` RENAME TO `%s`;', $table, $newName);
                    $output->writeln('<info>'.$alter.'</info>');
                } else {
                    continue;
                }
            } else {
                if ($prefix && strpos($table, $prefix) === false) {
                    $newName = $prefix.$table;
                    $alter = sprintf('ALTER TABLE `%s` RENAME TO `%s`;', $table, $newName);
                    $output->writeln('<info>'.$alter.'</info>');
                } else {
                    continue;
                }
            }
            $adapter->query($alter, Adapter::QUERY_MODE_EXECUTE);
        }
        if ($input->getArgument('rm')) {
            $output->writeln('<info>資料表前綴移除成功，請記得移除組態檔的前綴</info>');
        } else {
            $output->writeln('<info>資料表前綴更新成功</info>');
        }
    }
}
