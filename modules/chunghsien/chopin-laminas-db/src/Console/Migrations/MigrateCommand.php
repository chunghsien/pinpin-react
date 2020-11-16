<?php

namespace Chopin\LaminasDb\Console\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chopin\LaminasDb\Services\MigrationService;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this->setDescription("執行檔案遷移");
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
        $service->migrationRun($output, $migrationsPath);
    }
}
