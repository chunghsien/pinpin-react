<?php

namespace Chopin\LaminasDb\Console\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Laminas\ServiceManager\ServiceManager;

class MigrateMake extends Command
{
    protected static $defaultName = 'make:migration';


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
        //InputOption::VALUE_OPTIONAL
        $this->addOption('type', null, InputOption::VALUE_OPTIONAL, '(create|alter|drop)');
        $this->addOption('table', null, InputOption::VALUE_REQUIRED, '資料表名稱');
        $this->addOption('path', 'p', InputOption::VALUE_OPTIONAL, '檔案生成位置(不需要檔名)');
        $this->setDescription("建立遷移檔案");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classname = 'Migrate_';
        $options = $input->getOptions();

        $type = $options['type'];
        $table = $options['table'];

        if ( ! $table) {
            throw new \Exception('請帶入資料表名稱 --table');
        }

        if ($type) {
            switch (strtolower($type)) {
                case 'create':
                    $classname.= 'Create_';
                    break;
                case 'alter':
                    $classname.= 'Alter_';
                    break;
                case 'drop':
                    $classname.= 'Drop_';
                    break;
            }
        } else {
            $classname.= 'Create_';
            $type = 'create';
        }

        $classname.= $table.'_';
        $classname.= date("YmdHis");

        $codeContent = file_get_contents(__DIR__.'/stubs/make');

        $codeContent = str_replace(['{classname}', '{type}', '{table}'], [$classname, $type, $table], $codeContent);

        $path = preg_replace('/^\=/', '', $options['path']);

        if ( ! $path || ! is_dir($path)) {
            $path = 'database/migrations';
            if ( ! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
        $path = preg_replace('/\/$/', '', $path);
        $fullpath = $path.'/'.$classname.'.php';
        $result = file_put_contents($fullpath, $codeContent);


        if ($result) {
            //$output->writeln('');
            $output->writeln('<info>創建遷移('.$classname.')成功</info>');
        } else {
            throw new \Exception('創建遷移('.$classname.')失敗');
        }
    }
}
