<?php

namespace Chopin\LaminasDb\Sql\Ddl\Column\MySQL;

use Laminas\Db\Sql\Ddl\Column\AbstractTimestampColumn;

/**
 * @description  ....
 * @author a_hsi
 *
 */
class Timestamp extends AbstractTimestampColumn
{
    protected $type = 'TIMESTAMP';

    /**
     *
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct($name, array $options = [])
    {
        $nullable = false;
        $default = null;

        if (isset($options['nullable'])) {
            $nullable = $options['nullable'];
            unset($options['nullable']);
        }
        if (isset($options['default'])) {
            $default = $options['default'];
            unset($options['default']);
        }

        parent::__construct($name, $nullable, $default, $options);
    }
}
