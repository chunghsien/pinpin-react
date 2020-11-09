<?php

namespace Chopin\LaminasDb\Console\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chopin\LaminasDb\Services\MigrationService;

class MigrateStatus extends Command
{
    protected static $defaultName = 'migrate:status';

    protected function configure()
    {
        $this->setDescription("檢視遷移歷程");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $container;

        /**
         *
         * @var MigrationService $service
         */
        $service = $container->get(MigrationService::class);

        $output->write('<comment>'.$service->getStatus().'</comment>');
    }
}
