<?php

namespace Chopin\LaminasDb\DB\Traits;

use Laminas\Db\Sql\Predicate\PredicateInterface;
use Laminas\Db\Sql\Predicate\PredicateSet;

trait PredicateTrait
{
    public function predicateFactory($params)
    {
        if (is_string($params[0]) && class_exists($params[0])) {
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
}
