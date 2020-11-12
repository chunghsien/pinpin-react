<?php

namespace Chopin\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//use Symfony\Component\Console\Input\InputOption;
//use Laminas\Filter\Word\SeparatorToDash;

//use Symfony\Component\Console\Question\ConfirmationQuestion;


class SelfUpgradeCommand extends Command
{
    protected static $defaultName = 'selfupgrade';

    protected function configure()
    {
        $this->setDescription("開發階段時從smith.php 更新至 smith");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
    }
}
