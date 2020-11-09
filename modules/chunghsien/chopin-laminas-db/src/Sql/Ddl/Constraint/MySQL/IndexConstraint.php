<?php

namespace Chopin\LaminasDb\Sql\Ddl\Constraint\MySQL;

use Laminas\Db\Sql\Ddl\Constraint\AbstractConstraint;

/**
 * @desc 與 Laminas\Db\Sql\Ddl\Index\Index 類別功能重疊，建議改用 Laminas\Db\Sql\Ddl\Index\Index。
 * @deprecated
 * @author hsien
 *
 */
class IndexConstraint extends AbstractConstraint
{
    protected $columnSpecification = ' (%s) ';

    /**
     * @var string
     */
    protected $namedSpecification = 'INDEX %s ';

    /**
     *
     * @param string|array $columns
     * @param string $name
     */
    public function __construct($columns, $name)
    {
        parent::__construct($columns, $name);
    }
}
