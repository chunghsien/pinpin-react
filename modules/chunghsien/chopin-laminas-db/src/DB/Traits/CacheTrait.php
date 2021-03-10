<?php

namespace Chopin\LaminasDb\DB\Traits;

use Laminas\Cache\Storage\Adapter\AbstractAdapter;
use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Cache\StorageFactory;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Chopin\LaminasDb\RowGateway\RowGateway;
use Laminas\Diactoros\ServerRequestFactory;
use Chopin\Support\Registry;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Cache\Storage\StorageInterface;

trait CacheTrait
{

    /**
     *
     * @var AbstractAdapter
     */
    protected $cacheAdapter;

    protected $env_cache_use = false;

    protected $env_cache_vars = false;
    
    protected $initAdapter = 0;
    
    protected function initCacheAdapter()
    {
        if($this->initAdapter == 0) {
            if ($this->cacheAdapter instanceof AbstractAdapter == false) {
                $config = config('caches.' . StorageInterface::class);
                if(isset($config['adapter']['options']['cache_dir'])) {
                    $cacheDir = $config['adapter']['options']['cache_dir'];
                    if(!is_dir($cacheDir)) {
                        mkdir($cacheDir, 0755, true);
                    }
                }
                $cacheAdapter = StorageFactory::factory($config);
                
                /**
                 * *先實作filecache就好，後面再來慢慢處理
                 *
                 * @var Filesystem $cacheAdapter
                 */
                $this->cacheAdapter = $cacheAdapter;
            }
            $env_cache_use = config('env_cache');
            $this->env_cache_use = $env_cache_use['db'];
            $this->env_cache_vars = $env_cache_use['vars'];
            
            if ($this->cacheAdapter instanceof AbstractAdapter == false) {
                $this->env_cache_use = false;
                $this->env_cache_vars = false;
            }
        }
        $this->initAdapter++;
    }

    public function getEnvCacheUse()
    {
        $this->initCacheAdapter();
        return $this->env_cache_use;
    }

    public function getEnvCacheVars()
    {
        $this->initCacheAdapter();
        return $this->env_cache_vars;
    }

    /**
     *
     * @param string $serial
     * @param string $table
     * @param string $type
     */
    protected function saveDbCacheMapper($serial, $table, $type = 'env_cache_use')
    {
        $this->initCacheAdapter();
        $verify = $this->{$type};
        if ($verify) {
            $data = [
                'serial' => $serial,'table' => isset($table) && is_string($table) ? $table : '*',
            ];
            $exists = DbCacheMapperTableGateway::select($data)->count();
            if ($exists == 0) {
                DbCacheMapperTableGateway::insert($data);
            }
        }
    }

    protected $bindsuse = [];

    /**
     *
     * @param Sql $sql
     * @param Select $select
     * @param array $queryParams
     *            as 分頁狀況下
     * @param string $type
     * @return \ArrayObject
     */
    protected function buildCacheKey($sql, $select, $queryParams = [], $type = 'env_cache_use')
    {
        $this->initCacheAdapter();
        
        $verify = $this->{$type};
        
        if (! $verify) {
            return [];
        }
        /**
         *
         * @var ServerRequestInterface $serverRequest
         */
        $serverRequest = Registry::get(ServerRequestInterface::class);
        $extra = [];
        if ($serverRequest) {
            $extra = array_merge($extra, $serverRequest->getAttributes());
        } else {
            $serverRequest = ServerRequestFactory::fromGlobals();
        }
        if($serverRequest->getQueryParams()) {
            $extra = array_merge($extra, $serverRequest->getQueryParams());
        }
        
        $queryParams = array_merge($queryParams, $extra);
        $table = '_QUERY_';
        if ($select instanceof Select) {
            $raws = $select->getRawState();
            $table = $raws['table'];

            // 解密fix
            if (is_array($table)) {
                $keys = array_keys($table);
                $table = str_replace('_decrypt', '', $keys[0]);
            }
            if ($table instanceof TableIdentifier) {
                $table = $table->getTable();
            }
            if (isset($raws['joins']) && $raws['joins']->count()) {
                $joins = $raws['joins'];
                $tmp = [
                    $table
                ];
                foreach ($joins as $join) {
                    if(is_array($join['name']))
                    {
                        $joinKeys = array_keys($join['name']);
                        foreach ($joinKeys as $jk)
                        {
                            $tmp[] = $join['name'][$jk];
                        }
                    }else {
                        $tmp[] = $join['name'];
                    }
                    
                }
                $tmp = array_values($tmp);
                $table = implode(',', $tmp);
                $table = preg_replace('/^\,/', '', $table);
            }
        }
        
        if ($this->bindsuse) {
            $key = crc32($sql->buildSqlString($select) . serialize($this->bindsuse));
        } else {
            $key = crc32($sql->buildSqlString($select));
        }
        
        if ($queryParams) {
            $afterKey = serialize($queryParams);
            $afterKey = crc32($afterKey);
            $key += $afterKey;
        }
        
        DbCacheMapperTableGateway::insert([
            'table' => $table,'serial' => $key
        ]);
        return new \ArrayObject([
            'key' => $key,'table' => $table
        ]);
    }

    

    protected function getCache($key)
    {
        $this->initCacheAdapter();
        return $this->cacheAdapter->getItem($key);
    }
    /**
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    protected function setCache($key, $value, $type = 'env_cache_use')
    {
        $this->initCacheAdapter();
        $verify = $this->{$type};
        if ($verify) {
            if ($value instanceof ResultSetInterface) {
                /**
                 * \Laminas\Db\ResultSet\ResultSet $value
                 */
                if ($value->getDataSource() instanceof \Laminas\Db\Adapter\Driver\Pdo\Result) {
                    $result = $value->getDataSource();
                    $dataSource = new \ArrayIterator();
                    foreach ($result as $item) {
                        $dataSource->append($item);
                    }
                    $value->initialize($dataSource);
                }
            }
            return $this->cacheAdapter->setItem($key, $value);
        }
        return false;
    }
}
