<?php

namespace Chopin\Users\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Db\Sql\Sql;
use Symfony\Component\Console\Input\InputOption;
use Laminas\Navigation\Navigation;
use Chopin\Users\TableGateway\PermissionTableGateway;

class Permission extends Command
{

    protected static $defaultName = 'users:permission';

    /**
     *
     * @var Sql
     */
    protected $sql;

    /**
     *
     * @var PermissionTableGateway
     */
    private $tablegateway;

    // protected $table = 'permission';
    public function __construct($name = null)
    {
        global $container;
        parent::__construct($name);
        $adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->tablegateway = new PermissionTableGateway($adapter);
    }

    protected function configure()
    {
        $this->setDescription("權限表刷新")->addOption('source', null, InputOption::VALUE_REQUIRED, '設定檔位置');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getOption('source');
        if (is_file($source)) {
            $configs = require $source;
            
            $navigation = new Navigation($configs);
            $recursiveIteratorIterator = new \RecursiveIteratorIterator($navigation, \RecursiveIteratorIterator::SELF_FIRST);
            try {
                foreach ($recursiveIteratorIterator as $page) {
                    if ($page->uri != '#') {
                        if ($this->tablegateway->select([
                            'uri' => $page->uri
                        ])->count() == 0) {
                            $method = isset($page->http_method) ? $page->http_method : [
                                'GET'
                            ];
                            $this->tablegateway->insert([
                                'name' => $page->name ? $page->name : $page->uri,
                                'uri' => $page->uri,
                                'http_method' => json_encode($method),
                            ]);
                            $output->writeln('<info>權限: ' . $page->name . ' 建立成功</info>');
                        } else {
                            $output->writeln('<comment>權限: ' . $page->name . ' 已建立</comment>');
                        }
                    }
                }
            } catch (\Exception $e) {
                loggerException($e);
                throw $e;
            }
        }
    }
}
