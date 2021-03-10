<?php

namespace Chopin\LaminasDb\TableGateway;

use Laminas\Db\TableGateway\TableGateway as LaminasTableGateway;
use Laminas\Db\TableGateway\Feature\RowGatewayFeature;
use Chopin\LaminasDb\ColumnCacheBuilder;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\Code\Reflection\FileReflection;
use Chopin\LaminasDb\DB;
use Chopin\LaminasDb\RowGateway\RowGateway;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Where;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Chopin\LaminasDb\ResultSet\ResultSet;
use Chopin\LaminasDb\TableGateway\Feature\CacheTableFeature;

abstract class AbstractTableGateway extends LaminasTableGateway
{

    const UNIQUE = 'UNIQUE';

    const FOREIGN_KEY = 'FOREIGN KEY';

    const PRIMARY_KEY = 'PRIMARY KEY';
    
    /**
     *
     * @var array
     */
    protected $primary = [];

    /**
     *
     * @var \Laminas\Db\Metadata\Object\ColumnObject[]
     */
    protected $columnsObject = [];

    /**
     *
     * @var \Laminas\Db\Metadata\Object\ConstraintObject[]
     */
    protected $constraintsObject = [];

    /**
     * * 判斷該tablegateway是否使用get變數做排序條件
     *
     * @var bool
     */
    public $isRequestOrderByUse = false;

    public static $prefixTable = '';

    public $encryptionColumns = [];

    public function __construct(\Laminas\Db\Adapter\Adapter $adapter)
    {
        $this->table = self::$prefixTable . $this->table;
        parent::__construct($this->table, $adapter, null, new ResultSet());
    }

    /**
     *
     * @param string $classOrTable
     * @param \Laminas\Db\Adapter\Adapter $adapter
     * @return self
     */
    public static function newInstance($classOrTable, $adapter)
    {
        if (is_string($classOrTable) && class_exists($classOrTable)) {
            $reflectionClass = new \ReflectionClass($classOrTable);
            return $reflectionClass->newInstance($adapter);
        } else {
            $filter = new UnderscoreToCamelCase();
            $classname = str_replace(self::$prefixTable, '', $classOrTable);
            $tailClassname = ucfirst($filter->filter($classOrTable)) . 'TableGateway';
            $tailFilename = $tailClassname . '.php';
            $globs = glob('modules/**/**/src/TableGateway/' . $tailFilename);
            if ($globs && count($globs) == 1) {
                $filename = $globs[0];
                $fileGenerator = new FileReflection($filename, true);
                $classname = $fileGenerator->getClass()->name;
                if (class_exists($classname)) {
                    $reflectionClass = new \ReflectionClass($classname);
                    return $reflectionClass->newInstance($adapter);
                }
                // return self::newInstance($classOrTable, $adapter);
            }
            throw new \ErrorException($classname . ': 沒這個東西。');
        }
    }

    protected function setColumns($file)
    {
        $this->columns = [];
        $columnsObjs = unserialize(file_get_contents($file));
        foreach ($columnsObjs as $columnObj) {
            /**
             *
             * @var \Laminas\Db\Metadata\Object\ColumnObject $columnObj
             */
            $name = $columnObj->getName();
            $this->columns[] = $name;
            $this->columnsObject[$name] = $columnObj;
        }
    }

    /**
     *
     * @param array|Predicate $where
     * @return number
     */
    public function softDelete($where)
    {
        if (array_search('deleted_at', $this->columns) !== false) {
            return $this->update([
                'deleted_at' => date("Y-m-d H:i:s")
            ], $where);
        } else {
            return $this->delete($where);
        }
    }

    public function initialize()
    {
        parent::initialize();

        if ($this->table) {
            $baseDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
            $schemaDir = $baseDir . '/storage/database/' . $this->adapter->getCurrentSchema();
            $datDir = $schemaDir . '/' . preg_replace('/^' . self::$prefixTable . '/', '', $this->table);

            if (! is_dir($datDir)) {
                mkdir($datDir, 0755, true);
            }

            if (false === is_file($datDir . '/tablegateway_mapper.dat')) {
                $reflection = new \ReflectionObject($this);
                file_put_contents($datDir . '/tablegateway_mapper.dat', $reflection->name);
            }

            if (is_file($datDir . '/columns.dat')) {
                $columnsObjs = unserialize(file_get_contents($datDir . '/columns.dat'));
                if (is_array($columnsObjs) && $columnsObjs) {
                    $this->setColumns($datDir . '/columns.dat');
                } else {
                    ColumnCacheBuilder::createColumns($this->adapter, $this->table);
                    $this->setColumns($datDir . '/columns.dat');
                }
            } else {
                ColumnCacheBuilder::createColumns($this->adapter, $this->table);
                $this->setColumns($datDir . '/columns.dat');
            }

            if (is_file($datDir . '/constraints.dat')) {
                $constraintsObj = unserialize(file_get_contents($datDir . '/constraints.dat'));
                if (is_array($constraintsObj)) {
                    $this->constraintsObject = $constraintsObj;
                    if (isset($this->constraintsObject[self::PRIMARY_KEY])) {
                        $primaryKeysObj = $this->constraintsObject[self::PRIMARY_KEY];
                        if (! $this->primary) {
                            foreach ($primaryKeysObj as $primary) {
                                $this->primary = array_merge($this->primary, $primary->getColumns());
                            }
                        }
                    }
                }
            } else {
                ColumnCacheBuilder::createColumns($this->adapter, $this->table);
            }

            if (count($this->primary) == 1) {
                $id_index = array_search('id', $this->primary);
                if (($id_index !== false) && static::$isRemoveRowGatewayFeature === false) {
                    $primaryKeyColumn = $this->primary/*[$id_index]*/;
                    $sql = $this->sql;
                    $tableGateClass = get_class($this);
                    $rowGatewayClass = str_replace('Table', "Row", $tableGateClass);
                    
                    if(class_exists($rowGatewayClass)) {
                        $reflection = new \ReflectionClass($rowGatewayClass);
                        $rowGateway = $reflection->newInstance($this->adapter);
                    }else {
                        $rowGateway = new RowGateway($primaryKeyColumn, $this->table, $sql);
                    }
                    $feature = new RowGatewayFeature($rowGateway);
                    $feature->setTableGateway($this);
                    $feature->postInitialize();
                    $this->featureSet->addFeature($feature);
                }
            }
            $tableCacheFeature = new CacheTableFeature();
            $this->featureSet->addFeature($tableCacheFeature);
            if (method_exists($this, 'initCrypt')) {
                $this->initCrypt();
            }
        }
    }

    /**
     *
     * @param string $columnName
     * @return \Laminas\Db\Metadata\Object\ColumnObject
     */
    public function getColumnObj($columnName)
    {
        return $this->columnsObject[$columnName];
    }

    /**
     *
     * @param string $constraintName
     * @return \Laminas\Db\Metadata\Object\ConstraintObject|NULL
     */
    public function getConstraintsObject($constraintName = null)
    {
        if (! $constraintName) {
            return $this->constraintsObject;
        }
        if ($this->constraintsObject && isset($this->constraintsObject[$constraintName])) {
            return $this->constraintsObject[$constraintName];
        }
        return null;
    }

    /**
     * * 從 self::getConstraintsObject 方法中產生關聯表的join script
     *
     * @param array $columns
     * @param array $types
     *            inner, left etc.
     * @return string[]
     */
    public function buildInnerJoinScript($columns = [], $types = [])
    {
        $foreigns = $this->getConstraintsObject('FOREIGN KEY');
        $joins = [];
        if ($foreigns) {
            foreach ($foreigns as $constraintObject) {
                $table = $constraintObject->getTableName();
                $id = $constraintObject->getColumns();
                $id = implode('', $id);
                $refrence_table = $constraintObject->getReferencedTableName();
                $refrence_coulmn = $constraintObject->getReferencedColumns();
                $refrence_coulmn = implode('', $refrence_coulmn);
                $column = [];
                if ($columns && isset($columns[$refrence_table])) {
                    $column = $columns[$refrence_table];
                } else {
                    $schema = $this->adapter->getDriver()
                        ->getConnection()
                        ->getCurrentSchema();
                    $tailTable = str_replace(self::$prefixTable, '', $refrence_table);
                    $filename = 'storage/database/' . $schema . '/' . $tailTable . '/tablegateway_mapper.dat';
                    if (! is_file($filename)) {
                        $this->newInstance($tailTable, $this->adapter);
                    }
                    $fk_table_classname = file_get_contents('storage/database/' . $schema . '/' . $tailTable . '/tablegateway_mapper.dat');
                    $fk_tablegateway = $this->newInstance($fk_table_classname, $this->adapter);
                    $column = $fk_tablegateway->columns;
                }
                $type = 'inner';
                if ($types) {
                    $type = $types[$refrence_table];
                }

                $join = [
                    $refrence_table,
                    $table . '.' . $id . '=' . $refrence_table . '.' . $refrence_coulmn,
                    $column,
                    $type
                ];
                $joins[] = $join;
            }
        }
        return $joins;
    }

    /**
     *
     * @param array $requestData
     * @return array
     */
    public function buildData($requestData)
    {
        $response = [];
        foreach ($this->columns as $column) {
            $match = $this->getTailTableName() . '-' . $column;
            if (isset($requestData[$match]) && $requestData[$match]) {
                $response[$column] = $requestData[$match];
            }
        }

        foreach ($this->columns as $column) {
            // $match = $this->getTailTableName().'-'.$column;
            if (isset($requestData[$column])) {
                $response[$column] = $requestData[$column];
            }
        }

        return $response;
    }

    /**
     * *防止空值或null帶入
     *
     * @param array $set
     * @return array[]
     */
    public function setFilter($set)
    {
        $filtSet = [];
        foreach ($set as $c => $v) {
            if (false !== array_search($c, $this->columns)) {
                if ($v) {
                    $filtSet[$c] = $v;
                }
            }
        }
        return $filtSet;
    }

    public function buildOptionsData($valueField = 'id', $lableField = 'name', $resultSet, $dataAttrs = [])
    {
        $data = [];
        $attrKeys = array_keys($dataAttrs);
        foreach ($resultSet as $row) {
            $value = '';
            if (is_string($valueField)) {
                $value = $row[$valueField];
            } else if (is_array($valueField)) {
                $_t = [];
                foreach ($valueField as $f) {
                    $_t[$f] = $row[$f];
                }
                $value = json_encode($_t, JSON_UNESCAPED_SLASHES);
            }

            if ($lableField) {

                $append = [
                    'value' => $value,
                    'label' => $row[$lableField]
                ];
            } else {
                $append = $value;
            }

            if ($attrKeys) {
                $rowKeys = array_keys((array) $row);
                $dks = array_intersect($attrKeys, $rowKeys);
                foreach ($dks as $k) {
                    if (empty($append['data'])) {
                        $append['data'] = [];
                    }
                    $append['data'][$k] = $row[$k];
                }
            }
            $data[] = $append;
        }
        return $data;
    }

    /**
     *
     * @param array|string $valueField
     * @param string $lableField
     * @param array $dataAttrs
     * @param array $predicateParams
     * @param int $limit
     * @return array
     */
    public function getOptions($valueField = 'id', $lableField = 'name', $dataAttrs = [], $predicateParams = [], $limit = null)
    {
        //

        // $this->sql->select()->where->isNull
        if (is_string($valueField)) {
            $columns = array_merge([
                $valueField,
                $lableField
            ], $dataAttrs);
        } else if (is_array($valueField)) {
            $columns = array_merge($valueField, [
                $lableField
            ], $dataAttrs);
            $columns = array_values($columns);
        } else {
            throw new \ErrorException('$valueField 資料格式錯誤');
        }
        if(false !== array_search('deleted_at', $this->columns)) {
            $predicateParams[] = [
                'isNull',
                'AND',
                ['deleted_at']
            ];
        }
        $scripts = [
            'from' => $this->table,
            'columns' => [
                $columns
            ],
            'where' => $predicateParams
        ];

        if ($limit) {
            $scripts['limit'] = $limit;
        }

        $resultSet = DB::selectFactory($scripts);
        // debug($resultSet->toArray(), ['isContinue' => true, 'output_type' => 'json']);
        return $this->buildOptionsData($valueField, $lableField, $resultSet, $dataAttrs);
    }

    /**
     *
     * @return mixed
     */
    public function getTailTableName()
    {
        return str_replace(self::$prefixTable, '', $this->table);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Laminas\Db\TableGateway\AbstractTableGateway::__get()
     */
    public function __get($property)
    {
        if ($property == 'primary') {
            return $this->primary;
        }
        return parent::__get($property);
    }

    /**
     *
     * @param Where|\Closure|string|array|Predicate\PredicateInterface $where
     * @param array $releation
     * @return RowGatewayInterface|array
     */
    public function getRow($where, $releation = [])
    {
        $select = $this->sql->select();
        $select->where($where);
        $row = $this->selectWith($select)->current();
        if (! $row) {
            if (count($this->primary) == 1) {
                $row = new RowGateway($this->primary[0], $this->table);
                foreach ($this->columns as $col) {
                    $row->{$col} = null;
                }
            } else {
                $row = new \ArrayObject();
                foreach ($this->columns as $col) {
                    $row->{$col} = null;
                }
            }
        }
        if ($releation) {
            if ($row instanceof \Laminas\Db\RowGateway\RowGateway && ! $row->id) {
                $row->populate($releation);
            } else {
                foreach ($releation as $key => $value) {
                    if (! $row->{$key}) {
                        $row->{$key} = $value;
                    }
                }
            }
        }
        $row = $this->injectLanguageHasLocaleJson($row);
        return $row;
    }

    /**
     *
     * @param mixed $child
     * @param int $selfId
     * @return \Laminas\Db\RowGateway\AbstractRowGateway
     */
    public function injectReleationValues($child, $selfId)
    {
        if ($child instanceof RowGatewayInterface) {
            $child = $child->toArray();
        }
        $table = $this->getTailTableName();
        $releation_col = "{$table}_id";
        if (array_key_exists($releation_col, $child)) {
            $child[$releation_col] = $selfId;
        } else {
            $child['table'] = $table;
            $child['table_id'] = $selfId;
        }

        return $child;
    }

    protected function injectLanguageHasLocaleJson(RowGatewayInterface $row, $sequence=['locale_id', 'language_id'])
    {
        if( isset($row->language_id) && isset($row->locale_id) )
        {
            $object = [];
            foreach($sequence as $col) {
                $object[$col] = $row->{$col};
            }
            $row->language_has_locale = json_encode($object, JSON_UNESCAPED_SLASHES);
        }
        return $row;
    }
}
