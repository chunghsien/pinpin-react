<?php

namespace Chopin\Console\Command\Make;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Laminas\Filter\Word\SeparatorToDash;

//use Symfony\Component\Console\Question\ConfirmationQuestion;


class Module extends Command
{
    protected static $defaultName = 'make:module';

    protected function configure()
    {
        $this->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'prefix namespace');
        //$this->addOption('type', null, InputOption::VALUE_OPTIONAL, 'type: module|library');
        $this->setDescription("創建全新的module");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();

        $prefix = $options['prefix'];
        $prefix = preg_replace('/\\\\$/', '', $prefix);

        if ( ! $prefix) {
            throw new \Exception('請帶入前置 namespace ex.Chopin\\Elfinder');
        }

        $folder = '';
        $separatorToDash = new SeparatorToDash('\\');
        if (preg_match('/^Chopin/', $prefix) && defined('IS_DEVELOPMENT')) {
            $folder = 'modules/chunghsien/';
        } else {
            $folder = 'modules/';
        }

        $tailFolder = strtolower($separatorToDash->filter($prefix));
        $tailFolder = preg_replace('/\-$/', '', $tailFolder);
        $tailFolder.= '/src';
        $folder.=$tailFolder;
        if ( ! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $configProviderContent = file_get_contents(__DIR__.'/stubs/module');

        $configProviderContent = str_replace('{namespace}', $prefix, $configProviderContent);

        if ( ! is_file($folder.'/ConfigProvider.php') && file_put_contents($folder.'/ConfigProvider.php', $configProviderContent)) {
            $output->writeln('<info>Module 創建('.$folder.'/ConfigProvider.php'.')成功</info>');
            return ;
        } else {
            if (is_file($folder.'/ConfigProvider.php')) {
                $output->writeln('<comment>Module ('.$folder.'/ConfigProvider.php'.') 已被建立</comment>');
            } else {
                throw new \Exception('Module 創建('.$folder.'/ConfigProvider.php'.')失敗');
            }
        }
    }
}
