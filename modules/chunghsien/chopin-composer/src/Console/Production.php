<?php

namespace Chopin\Composer\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

//use Laminas\Filter\Word\DashToCamelCase;

class Production extends Command
{
    protected static $defaultName = 'composer:production';

    protected function configure()
    {
        $this->addOption('prefix', null, InputOption::VALUE_OPTIONAL, 'ex.chunghsien', 'chunghsien');
        $this->setDescription("建立 composer.json for production.");
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //global $container;
        $composerArr = [
            "repositories" => [],
            "require" => [],
        ];
        $prefix = $input->getOption('prefix');
        $dirIterator = new \DirectoryIterator('modules/'.$prefix);
        foreach ($dirIterator as $fileinfo) {
            $name = $fileinfo->getFilename();
            if ($name == '.' || $name == '..') {
                continue;
            }
            if ($fileinfo->isDir()) {
                $composerArr["repositories"][] = [
                    "type" => "git",
                    "url" => "https://github.com/chunghsien/".$name,
                ];
                $key = 'chunghsien/'.$name;
                $composerArr['require'][$key] = 'dev-master';
            }
        }

        $composerJsonPath = dirname(dirname(__DIR__));
        $composer = json_decode(file_get_contents($composerJsonPath.'/config/chopin-composer.json'), true);

        $composer['repositories'] = array_merge($composer['repositories'], $composerArr['repositories']);
        $composer['require'] = array_merge($composer['require'], $composerArr['require']);

        $outputStr = json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $outputStr = str_replace('[]', '{}', $outputStr);
        $outputStr = str_replace('"repositories": {}', '"repositories": []', $outputStr);
        $outputStr = str_replace('"files": {}', '"files": []', $outputStr);
        file_put_contents('composer.json', $outputStr);

        $output->writeln("<info>composer.json 置換成功</info>");
        //exec('composer update');
        //exec('rm -rf modules/chunghsien');
    }
}
