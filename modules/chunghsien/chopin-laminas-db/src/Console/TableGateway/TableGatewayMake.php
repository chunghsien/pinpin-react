<?php

namespace Chopin\LaminasDb\Console\TableGateway;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Filter\StaticFilter;

class TableGatewayMake extends Command
{
    protected static $defaultName = 'make:tablegateway';

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
        $this->addOption('table', null, InputOption::VALUE_REQUIRED, '資料表名稱');
        $this->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'namespace');
        $this->setDescription("建立 TableGateway");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classname = 'TableGateway';
        $options = $input->getOptions();

        $table = $options['table'];

        if ( ! $table) {
            throw new \Exception('請帶入資料表名稱 --table');
        }

        $namespace = $options['namespace'];
        if ( ! $namespace) {
            throw new \Exception('請帶入 namespace --namespace');
        }

        $dashToCamelCase = new DashToCamelCase();
        $_table = $dashToCamelCase->filter($table);
        $_table = ucfirst($_table);
        $classname = $_table.$classname;

        $codeContent = file_get_contents(__DIR__.'/stubs/make');
        $codeContent = str_replace(['{namespace}', '{class}', '{table}'], [$namespace, $classname, $table], $codeContent);

        $module = str_replace('\\TableGateway', '', $namespace);
        $module = strtolower(StaticFilter::execute($module, CamelCaseToDash::class));
        if ($module == 'chunghsien') {
            throw new \ErrorException('禁止存取Chungshien module.');
        }

        if (0 === strpos($module, 'chopin') && strpos($module, '\\')) {
            $module = str_replace('\\', '-', $module);
        }

        $baseDir = 'modules/'.$module.'/src/TableGateway';
        if ( ! is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        $filePath = $baseDir.'/'.$classname.'.php';
        $result = file_put_contents($filePath, $codeContent);


        if ($result) {
            //$output->writeln('');
            $output->writeln('<info>創建 TableGateway('.$classname.')成功</info>');
        } else {
            throw new \Exception('創建 TableGateway('.$classname.')失敗');
        }
    }
}
