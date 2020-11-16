<?php

namespace Chopin\IlluminateDatabase\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{

    /**
     *
     * @var Model
     */
    protected $model;

    /**
     *
     * @var string
     */
    protected $table;

    /**
     *
     * @var array
     */
    protected $queryScripts = [];

    /**
     *
     * @param array $attributes
     * @param array $options
     * @return boolean
     */
    public function save(array $attributes, $options = [])
    {
        $model = $this->model->newInstance($attributes);
        return $model->save($options);
    }

    public function delete($where)
    {
        $queryBuilder = DB::table($this->table);
        call_user_func_array([$queryBuilder, 'where'], $where);
        $attributes = $queryBuilder->first()->toArray();
        $model = $this->model->newInstance($attributes);
        return $model->delete();
    }

    /**
     *
     * @param array $options
     * @param array $return
     * @return \Illuminate\Support\Collection|mixed
     */
    public function queryFetchAll($options = [], $return = [])
    {
        $queryBuilder = DB::table($this->table);
        foreach ($options as $method => $args) {
            $isArgsIterator = true;
            foreach ($args as $arg) {
                if ( ! is_array($arg)) {
                    $isArgsIterator = false;
                    break;
                }
            }
            if ($isArgsIterator) {
                foreach ($args as $arg) {
                    call_user_func_array([
                        $queryBuilder,
                        $method,
                    ], $arg);
                }
            } else {
                call_user_func_array([
                    $queryBuilder,
                    $method,
                ], $args);
            }
        }

        if ( ! $return) {
            return $queryBuilder->get();
        }

        $type = $return['type'];
        $typeParams = $return['params'];
        switch ($type) {
            case 'get':
                return call_user_func_array([
                    $queryBuilder,
                    'get',
                ], $typeParams);
                break;
            case 'first':
                return call_user_func_array([
                    $queryBuilder,
                    'first',
                ], $typeParams);
                break;
            case 'paginate':
                return call_user_func_array([
                    $queryBuilder,
                    'paginate',
                ], $typeParams);
                break;
        }
    }
}
