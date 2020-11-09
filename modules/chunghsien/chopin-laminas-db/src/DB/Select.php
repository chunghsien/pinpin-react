<?php

namespace Chopin\LaminasDb\DB;

use Laminas\Db\Sql\Select as LaminasSelect;
use Chopin\LaminasPaginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\RowGateway\RowGateway;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Predicate\PredicateInterface;
use Laminas\Db\Sql\Sql;

class Select extends LaminasSelect
{
    use Traits\CommonTrait;
    use Traits\SecurityTrait;
    use Traits\CacheTrait;
    use Traits\Profiling;

    public $queryPredicateCombind = 'AND';
    /**
     *
     * @var string
     */
    protected $table;

    public function __construct($table = null)
    {
        $this->initial($table);
        parent::__construct($this->table);
    }

    public function initial($table = null)
    {
        if ($table && $table instanceof AbstractTableGateway) {
            $this->tablegateway = $table;
            $table = $this->tablegateway->table;
            $this->table = $table;
        } else {
            $this->table = $table;
        }

        $this->initCrypt();
        if ($table) {
            $this->buildAESDecryptFrom($this->table);
        }
    }
    /**
     *
     * @param string $sql
     * @param string $mode
     * @param ResultSetInterface $resultPrototype
     * @return ResultSetInterface
     */
    public function query($sql, $mode = Adapter::QUERY_MODE_PREPARE, ResultSetInterface $resultPrototype=null)
    {
        $this->getTableGateway();
        if (preg_match('/^select/i', $sql)) {
            $keys = $this->buildCacheKey($this->tablegateway->getSql(), $sql);
            if ($keys) {
                $cacheResultSet = $this->getCache($keys['key']);
                if ($cacheResultSet instanceof ResultSetInterface) {
                    return $cacheResultSet;
                }
            }
            $dataSource = $this->tablegateway->getSql()->getAdapter()->getDriver()->createStatement($sql)->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($dataSource);

            if ($keys) {
                $this->setCache($keys['key'], $resultSet);
                $this->saveDbCacheMapper($keys['key'], $keys['table']);
            }
            return $resultSet;
        } else {
            return  $this->tablegateway->getSql()->getAdapter()->getDriver()->createStatement($sql)->execute();
        }
    }

    /**
     *
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function get()
    {
        $sql = $this->getTableGateway()->getSql();
        $keys = $this->buildCacheKey($sql, $this);
        if ($keys) {
            $key = $keys['key'];
            if ($resultSet = $this->getCache($key)) {
                if ($resultSet instanceof ResultSet) {
                    return $resultSet;
                }
            }
        }
        if ($this->decryptSubSelectRaw instanceof \Laminas\Db\Sql\Select) {
            $select = $this->decryptSubSelectRaw;
            if ($this->where->count()) {
                $select->where($this->where);
            }
        } else {
            $select = $this;
        }
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        $primary = $this->tablegateway->primary;
        $raw = $this->getRawState();
        if ($primary && count($raw['columns']) == 1 && $raw['columns'][0] == '*' && $raw['joins']->count() == 0) {
            $resultSet->setArrayObjectPrototype(new RowGateway($primary[0], $raw['table']));
        }
        if ($keys) {
            $this->setCache($key, $resultSet);
            $this->saveDbCacheMapper($keys['key'], $keys['table']);
        }
        $this->runDbProfiling();
        return $resultSet;
    }

    /**
     *
     * @param number $countPerPage
     * @return \stdClass
     */
    public function paginate($countPerPage)
    {
        $sql = $this->getTableGateway()->getSql();
        $request = ServerRequestFactory::fromGlobals();
        $queryParams = $request->getQueryParams();
        $keys = $this->buildCacheKey($sql, $this, $queryParams);
        if ($keys) {
            if ($pages = $this->getCache($keys['key'])) {
                if ($pages instanceof \stdClass) {
                    return $pages;
                }
            }
        }

        $pageNumber = 1;
        if (isset($queryParams['page'])) {
            $pageNumber = intval($queryParams['page']);
        }
        if ( ! $paginator = $this->getCache($keys->key)) {
            $paginatorAdapter = new DbSelect($this, $sql);
            $paginator = new Paginator($paginatorAdapter);
            $this->runDbProfiling($sql->buildSqlString($this));
        }
        $paginator->setDefaultItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($pageNumber);
        $items = $paginator->getCurrentItems();
        $pages = $paginator->getPages();
        $pages->items = $items;
        if ($keys) {
            $this->setCache($keys['key'], $pages);
            $this->saveDbCacheMapper($keys['key'], $keys['table']);
        }
        return $pages;
    }

    /**
     *
     * @return array|\ArrayObject|NULL
     */
    public function first()
    {
        /**
         *
         * @var \Laminas\Db\ResultSet\ResultSet $resultSet
         */
        $resultSet = $this->get()->current();
        return $resultSet;
    }

    /**
     *
     * @param array $params
     */
    public function predicateFactory($params)
    {
        if (isset($params[0]) && is_string($params[0]) && class_exists($params[0])) {
            $predicateReflection = new \ReflectionClass($params[0]);
            if ($predicateReflection->implementsInterface(PredicateInterface::class)) {
                $predicate = $predicateReflection->newInstanceArgs($params[2]);
                $this->where($predicate, $params[1]);
            }
        } else {
            foreach ($params as $param) {
                if (is_string($param[0]) && class_exists($param[0])) {
                    $predicateReflection = new \ReflectionClass($param[0]);
                    if ($predicateReflection->implementsInterface(PredicateInterface::class)) {
                        $predicate = $predicateReflection->newInstanceArgs($param[2]);
                        $this->where($predicate, $param[1]);
                    }
                } else {
                    //nest
                    if (is_string($param[0]) && ! class_exists($param[0])) {
                        //$bind = $param[0];
                        $predicateSet = new PredicateSet();
                        foreach ($param[1] as $nestParam) {
                            $predicateReflection = new \ReflectionClass($nestParam[0]);
                            if ($predicateReflection->implementsInterface(PredicateInterface::class)) {
                                $predicate = $predicateReflection->newInstanceArgs($nestParam[2]);
                                $predicateSet->addPredicate($predicate, $nestParam[1]);
                            }
                        }
                        $this->where($predicateSet, $param[0]);
                    }
                }
            }
        }
    }

    /**
     *
     * @param array $scripts
     * @param array $bindParams
     * @param boolean $is_paginate
     * @param string $requestQueryPredicateSet
     * @return \stdClass|\Laminas\Db\ResultSet\ResultSet
     */
    public function selectFactory($scripts = [], $bindParams = [], $is_paginate = false, $queryPredicateCombind = 'AND')
    {
        $this->queryPredicateCombind = $queryPredicateCombind;
        $select = new Select();

        $this->processScripts($select, $scripts);
        if ($is_paginate) {
            return $this->processPaginate($select, $scripts, $bindParams);
        } else {
            return $this->processResultset($select, $scripts, $bindParams);
        }
    }

    /**
     *
     * @param array $queryParams
     */
    public function setRequestOrderBy($queryParams)
    {
        if (isset($queryParams['orderby']) && preg_match('/\w+\,ASC|DESC$/i', $queryParams['orderby'])) {
            //處理表格排序
            $sortArr = explode(',', $queryParams['orderby']);
            $sortArr[0] = str_replace('-', '.', $sortArr[0]);
            $this->order(implode(' ', $sortArr));
        }
    }

    /**
     *
     * @param array $queryParams
     * @param string $combined
     */
    public function setRequestQueryPredicatSet($queryParams, $combined = 'AND')
    {
        $selfTablegateway = $this->getTableGateway();
        $columns = $selfTablegateway->getColumns();
        $queryKeys = array_keys($queryParams);
        $verify = array_intersect($columns, $queryKeys);

        //不符合就不用繼續了
        if ( ! $verify) {
            return;
        }

        if ($this instanceof Select == false) {
            throw new \ErrorException('物件不支援此方法');
        }

        $filedKeys = $verify;
        $predicateSet = new PredicateSet([], $combined);

        //先排除一些固定參數(paginate)
        $exclude = ['page_number', 'page_size', 'page_range', 'orderby', 'method', 'id'];

        foreach ($filedKeys as $key) {
            if (false !== array_search($key, $exclude)) {
                continue;
            }
            $field = str_replace('-', '', $key);
            if (false !== array_search($field, $this->columns)) {
                if (array_intersect($this->columns, $this->defaultEncryptionColumns)) {
                    $decryptTable = 'decrypt_'.$this->table;
                    $decryptTable = str_replace(AbstractTableGateway::$prefixTable, '', $decryptTable);
                    $decryptTable = AbstractTableGateway::$prefixTable.$decryptTable;
                    $predicateSet->addPredicate(new Like($decryptTable.'.'.$field, '%'.$queryParams[$field].'%'), Predicate::OP_OR);
                } else {
                    //先用LIKE
                    $predicateSet->addPredicate(new Like($this->table.'.'.$field, '%'.$queryParams[$field].'%'), Predicate::OP_OR);
                }
            } else {
                //fix join 查詢，自行定義方法(繼承類別要實做)，規則如下
                //$value = $queryParams[$field];
                if (is_string($this->table)) {
                    $predicateSet->addPredicate(new Like($this->table.'.'.$field, '%'.$queryParams[$field].'%'), Predicate::OP_OR);
                }
            }
        }
        if ($predicateSet->count()) {
            //$this->requestQueryPredicateSet = $predicateSet;
            $this->where->addPredicate($predicateSet);
        }
    }

    /**
     *
     * @param LaminasSelect $select
     * @param array $scripts
     * @param array $bindParams
     * @return \Laminas\Db\ResultSet\ResultSet
     */
    protected function processResultset(Select $select, $scripts, $bindParams)
    {
        /**
         *
         * @var Sql $sql
         */
        $sql = $this->getTableGateway()->getSql();
        $this->bindsuse = $bindParams;
        $keys = $this->buildCacheKey($sql, $select);
        if ($keys) {
            $key = $keys['key'];
            if ($cacheResult = $this->getCache($key)) {
                return $cacheResult;
            }
        }
        $results = $sql->prepareStatementForSqlObject($select)->execute($bindParams);
        $this->runDbProfiling($sql->buildSqlString($select));
        $resultSet = new ResultSet();
        $dataSource = new \ArrayIterator();
        foreach ($results as $item) {
            $dataSource->append($item);
        }
        if (
            empty($scripts['columns']) &&
            empty($scripts['join']) &&
            empty($scripts['group']) &&
            empty($scripts['union'])
            ) {
            if ($select->joins->count() == 0 && implode('', $select->getRawState('columns')) == '*') {
                $rowGateway = new RowGateway($this->tablegateway->primary[0], $this->table/*, $adapter*/);
                $resultSet->setArrayObjectPrototype($rowGateway);
            } else {
                $resultSet->setArrayObjectPrototype(new \ArrayObject());
            }
        }
        $resultSet->initialize($dataSource);

        if ($keys) {
            $this->setCache($key, $resultSet);
            $this->saveDbCacheMapper($keys['key'], $keys['table']);
        }
        if ($resultSet instanceof ResultSetInterface) {
            $resultSet->rewind();
        }
        return $resultSet;
    }

    public function queryFactoryScriptToSqlString($scripts)
    {
        $this->getTableGateway();
        $select = $this->processScripts(new Select(), $scripts);
        return $this->tablegateway->getSql()->buildSqlString($select);
    }
    /**
     *
     * @param Select $select
     * @param array $scripts
     * @return mixed
     */
    protected function processScripts($select, $scripts)
    {
        $this->getTableGateway();

        if ($this->decryptSubSelectRaw) {
            $decryptTable = 'decrypt_' . $this->table;
            $decryptTable = str_replace(AbstractTableGateway::$prefixTable, '', $decryptTable);
            $decryptTable = AbstractTableGateway::$prefixTable.$decryptTable;
            $select->from([$decryptTable => $this->decryptSubSelectRaw]);
        } else {
            if (isset($scripts['from'])) {
                $table = str_replace(AbstractTableGateway::$prefixTable, '', $scripts['from']);
                $table = AbstractTableGateway::$prefixTable.$table;
                unset($scripts['from']);
            } else {
                $table = $this->table;
            }
            $select->from($table);
        }



        if ($scripts) {
            foreach ($scripts as $method => $params) {
                switch ($method) {
                    case 'quantifier':
                        $select->quantifier($params);
                        break;
                    case 'from':
                        //棄用，會在self::selectFactory方法中處理掉
                        //$select->reset('table');
                        //$params = preg_replace('/^'.AbstractTableGateway::$prefixTable.'/', '', $params);
                        //$select->from(AbstractTableGateway::$prefixTable.$params);
                        break;
                    case 'columns':
                    case 'group':
                    case 'order':
                    case 'limit':
                    case 'offset':
                        $select->{$method}($params);
                        break;
                    case 'where':
                    case 'having':
                        $select->predicateFactory($params);
                        break;
                    case 'join':
                        if (is_array($params[0])) {
                            foreach ($params as $param) {
                                if (count($param) >= 3 && count($param) <= 4) {
                                    if ( ! preg_match('/^'.AbstractTableGateway::$prefixTable.'/', $param[0])) {
                                        $param[0] = AbstractTableGateway::$prefixTable.$param[0];
                                        $matcher = [];
                                        preg_match_all('/\w+\./', $param[1], $matcher);
                                        if ($matcher) {
                                            $matcher = $matcher[0];
                                            foreach ($matcher as $column) {
                                                $column = preg_replace('/\.$/', '', $column);
                                                if ( ! preg_match('/^'.AbstractTableGateway::$prefixTable.'/', $column)) {
                                                    $param[1] = str_replace($column.'.', AbstractTableGateway::$prefixTable.$column.'.', $param[1]);
                                                }
                                            }
                                        }
                                    }
                                    $select = call_user_func_array([$select, 'join'], $param);
                                }
                            }
                        } else {
                            if (count($params) >= 3 && count($params) <= 4) {
                                if ( ! preg_match('/^'.AbstractTableGateway::$prefixTable.'/', $params[0])) {
                                    $params[0] = AbstractTableGateway::$prefixTable.$params[0];
                                    $matcher = [];
                                    preg_match_all('/\w+\./', $params[1], $matcher);
                                    if ($matcher) {
                                        $matcher = $matcher[0];
                                        foreach ($matcher as $column) {
                                            $column = preg_replace('/\.$/', '', $column);
                                            if ( ! preg_match('/^'.AbstractTableGateway::$prefixTable.'/', $column)) {
                                                $params[1] = str_replace($column.'.', AbstractTableGateway::$prefixTable.$column.'.', $param[1]);
                                            }
                                        }
                                    }
                                }

                                $select = call_user_func_array([$select, 'join'], $params);
                            }
                        }
                        break;
                }
            }
        }

        $request = ServerRequestFactory::fromGlobals();
        $queryParams = $request->getQueryParams();
        if ($queryParams) {
            if ($this->tablegateway->isRequestQueryPredicatSetUse) {
                /**
                 * @var \Chopin\LaminasDb\DB\Select $select
                 */
                $select->setRequestQueryPredicatSet($queryParams);
            }
            if ($this->tablegateway->isRequestOrderByUse) {
                $select->setRequestOrderBy($queryParams);
            }
        }
        if (preg_match('/\.debug/', APP_ENV)) {
            logger()->debug($this->tablegateway->getSql()->buildSqlString($select));
        }
        return $select;
    }

    /**
     *
     * @param array $scripts
     * @param array $bindParams
     * @return \stdClass
     */
    protected function processPaginate(Select $select, $scripts, $bindParams)
    {
        $sql = $this->getTableGateway()->getSql();
        $request = ServerRequestFactory::fromGlobals();
        $queryParams = $request->getQueryParams();
        $this->bindsuse = $bindParams;
        $keys = $this->buildCacheKey($sql, $select, $queryParams);
        if ($keys) {
            if ($pages = $this->getCache($keys['key'])) {
                if ($pages instanceof \stdClass) {
                    return $pages;
                }
            }
        }
        $request = ServerRequestFactory::fromGlobals();
        $query = $request->getQueryParams();

        $paginatorAttrs = [
            'page_number', 'page_range', 'page_size',
        ];

        if (
            empty($scripts['columns']) &&
            empty($scripts['join']) &&
            empty($scripts['group']) &&
            empty($scripts['union'])
            ) {
            $adapter = $this->getTablegateway()->getSql()->getAdapter();
            $table = $select->getRawState(Select::TABLE);
            if (is_array($table)) {
                $keys = array_keys($table);
                $table = end($keys);
            }
            $metadata = \Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter);
            $constraints = $metadata->getConstraints($table);
            $primary = null;
            foreach ($constraints as $constraint) {
                /**
                 * @var \Laminas\Db\Metadata\Object\ConstraintObject $constraint
                 */
                if (strtolower($constraint->getType()) == 'primary key') {
                    $primary = $constraint->getColumns();
                    break;
                }
            }
            $resultSet = new ResultSet();
            try {
                $rowGateway = new RowGateway($primary, $table, $adapter);
                $resultSet->setArrayObjectPrototype($rowGateway);
            } catch (\Exception $e) {
                //Nothing
            }
            $dbSelect = new DbSelect($select, $adapter, $resultSet);
        } else {
            $sql = $select->getTableGateway()->getSql();
            $dbSelect = new DbSelect($select, $sql);
        }
        $dbSelect->setBindParams($bindParams);
        $paginator = new Paginator($dbSelect);
        $paginate = [];
        foreach ($query as $key => $value) {
            if (array_search($key, $paginatorAttrs) !== false) {
                $paginate[$key] = $value;
            }
        }
        foreach ($paginate as $method => $value) {
            switch ($method) {
                    case 'page_number':
                        $paginator->setCurrentPageNumber($value);
                        break;
                    case 'page_size':
                        $paginator->setItemCountPerPage($value);
                        break;
                    case 'page_range':
                        $paginator->setPageRange($value);
                        break;

                }
        }
        $items = $paginator->getCurrentItems();
        $pages = $paginator->getPages();
        $pages->items = $items;
        $this->runDbProfiling($sql->buildSqlString($select));
        if ($keys) {
            $this->setCache($keys['key'], $pages);
            $this->saveDbCacheMapper($keys['key'], $keys['table']);
        }
        return $pages;
    }
}
