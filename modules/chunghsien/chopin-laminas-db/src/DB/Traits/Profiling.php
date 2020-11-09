<?php

namespace Chopin\LaminasDb\DB\Traits;

use Laminas\Db\Adapter\Adapter;

trait Profiling
{
    protected $is_profiling = false;

    protected function runDbProfiling($sqlScript='')
    {
        if ($this->is_profiling === false) {
            $config = config('db.adapters.'.Adapter::class);
            if (isset($config['profiling']) && $config['profiling']) {
                $this->is_profiling = true;
            } else {
                $this->is_profiling = 0;
            }
        }

        if ($this->is_profiling === true) {
            $tablegateway = $this->getTableGateway();
            $adapter = $this->tablegateway->getSql()->getAdapter();
            $profilingResult = $adapter->query('show profiles;', Adapter::QUERY_MODE_EXECUTE);

            if ( ! is_dir('storage/db_profiling')) {
                mkdir('storage/db_profiling', 0755, true);
            }
            if ($sqlScript) {
                $sqlScript = preg_replace('/\r|\n$/', '', $sqlScript);
                $sqlScript = trim($sqlScript);
                $sqlScript = preg_replace('/;$/', '', $sqlScript);
                $sqlScript.= ";\n";
                file_put_contents('storage/db_profiling/'.$tablegateway->table.'_profiled.sql', $sqlScript, FILE_APPEND);
            }

            $filename = $tablegateway->table.'.csv';
            $fp = fopen('storage/db_profiling/'.$filename, 'a+');

            foreach ($profilingResult as $idx => $item) {
                if ($idx === 0) {
                    $keys = array_keys((array)$item);
                    fputcsv($fp, $keys);
                }
                fputcsv($fp, (array)$item);
            }
            fclose($fp);
        }
    }
}
/*
show profile BLOCK IO for query 1;
ALL - displays all information
BLOCK IO - displays counts for block input and output operations
CONTEXT SWITCHES - displays counts for voluntary and involuntary context switches
IPC - displays counts for messages sent and received
MEMORY - is not currently implemented
PAGE FAULTS - displays counts for major and minor page faults
SOURCE - displays the names of functions from the source code, together with the name and line number of the file in which the function occurs
SWAPS - displays swap counts
*/
