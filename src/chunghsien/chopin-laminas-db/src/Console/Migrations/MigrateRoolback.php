<?php

namespace Chopin\LaminasDb\Console\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Chopin\LaminasDb\Services\MigrationService;

//use Laminas\Db\Adapter\Adapter;

class MigrateRoolback extends Command
{
    protected static $defaultName = 'migrate:roolback';

    protected function configure()
    {
        $this->addArgument('step', InputArgument::OPTIONAL, '回退次數', 1);
        $this->setDescription("回退遷移檔案");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $container;
        /**
         *
         * @var MigrationService $service
         */
        $service = $container->get(MigrationService::class);
        $migrationsPath = config('console.migrations');
        $step = intval($input->getArgument('step'));
        $service->migrationRoolBack($output, $migrationsPath, $step);
    }
}
