<?php

namespace Chopin\Composer\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Filter\Word\UnderscoreToCamelCase;

class Dev extends Command
{
    protected static $defaultName = 'composer:dev';

    protected function configure()
    {
        $this->addOption('prefix', null, InputOption::VALUE_OPTIONAL, 'ex.chunghsien', 'chunghsien');
        $this->setDescription("建立 composer.json for dev.");
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //global $container;

        $prefix = $input->getOption('prefix');
        $dirIterator = new \DirectoryIterator('src/'.$prefix);
        $dashToCamelCase = new DashToCamelCase();
        $underscoreToCamelCase = new UnderscoreToCamelCase();

        $composerArr = [
            'require' => [

            ],
            'autoload' => [
                "psr-4" => [],
                "files" => [
                    "src/chunghsien/chopin-support/src/helpers.php",
                ],
            ],
        ];


        foreach ($dirIterator as $fileinfo) {
            $name = $fileinfo->getFilename();
            if ($name == '.' || $name == '..') {
                continue;
            }
            if ($fileinfo->isDir()) {
                $tmp = explode('-', $name);
                $prefixClass = ucfirst($underscoreToCamelCase->filter($tmp[0]));
                $namespace = $prefixClass;
                unset($tmp[0]);
                $tailName = implode('-', $tmp);
                $tailName = ucfirst($dashToCamelCase->filter($tailName));

                $namespace = $namespace.'\\'.$tailName.'\\';
                $path = "src/".$prefix.'/'.$name.'/src/';
                $composerArr['autoload']['psr-4'][$namespace] = $path;

                $packageComposer = json_decode(file_get_contents(dirname($path).'/composer.json'), true);
                $composerArr['require'] = array_merge($composerArr['require'], $packageComposer['require']);
            }
        }



        $composerJsonPath = dirname(dirname(__DIR__));
        $composer = json_decode(file_get_contents($composerJsonPath.'/config/chopin-composer.json'), true);

        $composer = array_merge_recursive($composer, $composerArr);
        foreach ($composer['require'] as &$value) {
            if (is_array($value)) {
                $value = $value[0];
            }
        }
        $outputStr = json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $outputStr = str_replace('[]', '{}', $outputStr);
        $outputStr = str_replace('"repositories": {}', '"repositories": []', $outputStr);
        $outputStr = str_replace('"files": {}', '"files": []', $outputStr);
        file_put_contents('composer.json', $outputStr) ;

        $output->writeln("<info>composer.json 置換成功</info>");
        exec("composer update");
    }
}
