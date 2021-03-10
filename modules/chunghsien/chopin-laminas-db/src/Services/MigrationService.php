<?php

namespace Chopin\LaminasDb\Services;

use Laminas\Db\Adapter\AdapterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\TableGateway\MigrationsTableGateway;
use Chopin\LaminasDb\ColumnCacheBuilder;
use Laminas\Filter\StaticFilter;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\Filter\Word\DashToCamelCase;
use Chopin\LaminasDb\DB;

class MigrationService
{

    /*
     * @var MigrationsTableGateway $migrationTableGateway
     */
    protected $migrationTableGateway;

    public function __construct(\Laminas\Db\Adapter\Adapter $adapter)
    {
        $this->migrationTableGateway =  new MigrationsTableGateway($adapter);
    }

    /**
     *
     * @return number
     */
    public function getLastBatch()
    {
        $resultSet = DB::selectFactory([
            'from' => $this->migrationTableGateway->getTable(),
            'order' => 'batch desc',
            'limit' => 1,
        ]);

        $current = $resultSet->current();
        $batch = 1;
        if ($current) {
            $batch = intval($current['batch']) + 1;
        }

        return $batch;
    }

    /**
     *
     * @param string $migration
     * @return number
     */
    public function isUse($migration)
    {
        $resultSet = DB::selectFactory([
            'from' => $this->migrationTableGateway->getTable(),
            'where' => [
                ['equalTo', 'and', ['migration', $migration]],
            ],
        ]);
        return $resultSet->count();
    }

    /**
     *
     * @param \SplFileInfo $fileinfo
     * @param AdapterInterface $adapter
     * @return AbstractMigration
     */
    protected function lodMigrationClass(\SplFileInfo $fileinfo)
    {
        require_once $fileinfo->getPathname();
    }

    /**
     *
     * @param string $migrationsPath
     * @param AdapterInterface $adapter
     * @return AbstractMigration[]
     */
    protected function loadMigrationsForRun($migrationsPath, AdapterInterface $adapter)
    {
        // \Laminas\Db\Adapter\AdapterInterface
        $migrations = [];
        foreach ($migrationsPath as $migrationPath) {
            if ( ! is_dir($migrationPath)) {
                continue;
            }
            $recursiveDirectoryIterator = new \RecursiveDirectoryIterator($migrationPath);
            $iterator = new \RecursiveIteratorIterator($recursiveDirectoryIterator);
            foreach ($iterator as $fileinfo) {
                /**
                 *
                 * @var \SplFileInfo $fileinfo
                 */
                if ($fileinfo->isFile() && false !== strpos($fileinfo->getFilename(), '.php')) {
                    $class = str_replace('.php', '', $fileinfo->getFilename());
                    if ( ! class_exists($class)) {
                        if ( ! $this->isUse($class)) {
                            $this->lodMigrationClass($fileinfo, $adapter);
                            $migration = (new \ReflectionClass($class))->newInstance($adapter);
                            $priority = intval($migration->priority);
                            if (empty($migrations[$priority])) {
                                $migrations[$priority] = [];
                            }
                            $migrations[$priority][] = $migration;
                        }
                    }
                }
            }
        }
        return $migrations;
    }

    /**
     *
     * @param string $migrationsPath
     * @param AdapterInterface $adapter
     */
    protected function loadMigrationsForRoolback($migrationsPath, AdapterInterface $adapter)
    {
        foreach ($migrationsPath as $migrationPath) {
            $recursiveDirectoryIterator = new \RecursiveDirectoryIterator($migrationPath);
            $iterator = new \RecursiveIteratorIterator($recursiveDirectoryIterator);
            foreach ($iterator as $fileinfo) {
                /**
                 *
                 * @var \SplFileInfo $fileinfo
                 */
                if ($fileinfo->isFile()) {
                    $class = str_replace('.php', '', $fileinfo->getFilename());
                    if ( ! class_exists($class)) {
                        $this->lodMigrationClass($fileinfo);
                    }
                }
            }
        }
    }

    /**
     *
     * @param OutputInterface $output
     * @param array $migrationsPath
     */
    public function migrationRun(OutputInterface $output, $migrationsPath)
    {
        $sql = $this->migrationTableGateway->getSql();
        $adapter = $sql->getAdapter();
        $migrations = $this->loadMigrationsForRun($migrationsPath, $adapter);
        
        try {
            ksort($migrations);
            if ( ! $migrations) {
                $output->writeln('<info>目前沒有可執行的migrate</info>');
            }

            foreach ($migrations as $batch => $migrationPrioritys) {
                if(is_null($migrationPrioritys)) {
                    continue;
                }
                foreach ($migrationPrioritys as $migration) {
                    $migrationClass = get_class($migration);

                    $migrationClassReflection = new \ReflectionClass($migrationClass);
                    $migrationClassPath = $migrationClassReflection->getFileName();
                    $search = str_replace('/', DIRECTORY_SEPARATOR, 'database/migrations').DIRECTORY_SEPARATOR.$migrationClass.'.php';
                    $packageFolder = str_replace($search, '', $migrationClassPath);
                    //

                    if (false !== strpos($packageFolder, 'chunghsien')) {
                        //$packageFolder.='/Table';
                        $namespaceTmp = explode('chunghsien', $packageFolder);
                        $tablegatewayBaseNamespace = end($namespaceTmp);
                        $tablegatewayBaseNamespace = str_replace(DIRECTORY_SEPARATOR, '', $tablegatewayBaseNamespace);

                        //chopin-language-has-locale
                        $namespaceTmp = preg_replace('/^chopin\-/', '', $tablegatewayBaseNamespace);
                        $namespaceTmp = StaticFilter::execute($namespaceTmp, DashToCamelCase::class);
                        $namespaceTmp = ucfirst($namespaceTmp);
                        $tablegatewayBaseNamespace = 'Chopin\\'.$namespaceTmp;
                        $tablegatewayBaseNamespace.= '\\TableGateway';
                        $tablegatewayClass = ucfirst(StaticFilter::execute($migration->tailTable, UnderscoreToCamelCase::class));
                        $tablegatewayClass.= 'TableGateway';
                        $tablegatewayTable = $migration->tailTable;
                        $code = file_get_contents('modules/chunghsien/chopin-Laminas-db/src/Console/TableGateway/stubs/make');
                        $code = str_replace(
                            ['{namespace}', '{class}', '{table}'],
                            [$tablegatewayBaseNamespace, $tablegatewayClass, $tablegatewayTable],
                            $code
                        );
                        $savePath = $packageFolder.'src/TableGateway/'.$tablegatewayClass.'.php';
                        $savePath = str_replace('/', DIRECTORY_SEPARATOR, $savePath);
                        if ( ! is_dir(dirname($savePath))) {
                            mkdir(dirname($savePath), 0755, true);
                        }
                        if ( ! is_file($savePath)) {
                            file_put_contents($savePath, $code);
                        }
                    }
                    $migration->up();
                    $ddl = $migration->ddl;
                    $sqlString = $sql->buildSqlString($ddl);
                    $adapter->query($sqlString, $adapter::QUERY_MODE_EXECUTE);
                    $seed = $migration->seed;
                    if (isset($seed) && $seed instanceof \Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds) {
                        $seed->run();
                    }
                    $data = [
                        'migration' => $migrationClass,
                        'batch' => $batch,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    //$this->migrationRepository->save($data);
                    $this->migrationTableGateway->insert($data);
                    $output->writeln('<info>' . $migrationClass . ' 執行成功。</info>');

                    if ($migration->table) {
                        ColumnCacheBuilder::createColumns($adapter, $migration->table);
                    }
                }
            }
        } catch (\Exception $e) {
            loggerException($e);
            echo $e->getFile().PHP_EOL;
            echo $e->getMessage().PHP_EOL;
            echo $e->getTraceAsString();
            exit();
            //throw $e;
        }
    }

    /**
     *
     * @return \Laminas\Db\ResultSet\ResultSet|array
     */
    public function getStatus()
    {
        $resultSet = $this->migrationTableGateway->select();
        $table = new \Laminas\Text\Table\Table([
            'columnWidths' => [
                10,
                80,
                10,
            ],
        ]);
        $table->appendRow([
            'id',
            'migration',
            'batch',
        ]);
        foreach ($resultSet as $row) {
            $table->appendRow([
                $row['id'],
                $row['migration'],
                $row['batch'],
            ]);
        }
        return $table->__toString();
    }

    /**
     *
     * @param OutputInterface $output
     * @param string $migrationsPath
     */
    public function migrationRoolBack(OutputInterface $output, $migrationsPath, $step)
    {
        $sql = $this->migrationTableGateway->getSql();
        $adapter = $sql->getAdapter();
        $this->loadMigrationsForRoolback($migrationsPath, $adapter);
        $select = $sql->select();
        $select->order('id desc')->limit($step);
        $resultSet = $this->migrationTableGateway->selectWith($select);
        foreach ($resultSet as $row) {
            $migrationClass = $row['migration'];
            if (class_exists($migrationClass)) {
                $migrationObj  = new $migrationClass($adapter);
                $migrationObj->down();
                $ddl = $migrationObj->roolbackDdl;
                if ( !$ddl) {
                    $output->writeln( "<comment>$migrationClass::down() 未設定 roolbackDdl.</comment>");
                    //throw new \ErrorException($migrationClass . "::down() 未設定 roolbackDdl.");
                }else {
                    $sqlString = $sql->buildSqlString($ddl);
                    $adapter->query($sqlString, $adapter::QUERY_MODE_EXECUTE);
                }
                $this->migrationTableGateway->delete(['id' => $row['id']]);
                $output->writeln('<info>' . $migrationClass . ' 回退成功。</info>');
            } else {
                throw new \ErrorException($migrationClass . "不存在");
            }
        }
    }
}
