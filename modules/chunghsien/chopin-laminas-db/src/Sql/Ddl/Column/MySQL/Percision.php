<?php

namespace Chopin\LaminasDb\Sql\Ddl\Column\MySQL;

use Laminas\Db\Sql\Ddl\Column\AbstractPrecisionColumn;

/**
 * @description decimal....
 * @author a_hsi
 *
 */
class Percision extends AbstractPrecisionColumn
{
    protected $type = 'DECIMAL';

    /**
     *
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct($name, array $options = [])
    {
        $digits = null;
        $decimal = null;
        $nullable = false;
        $default = null;

        if (isset($options['digits'])) {
            $digits = $options['digits'];
            unset($options['digits']);
        }

        if (isset($options['decimal'])) {
            $digits = $options['decimal'];
            unset($options['decimal']);
        }

        if (isset($options['nullable'])) {
            $digits = $options['nullable'];
            unset($options['nullable']);
        }

        if (isset($options['default'])) {
            $digits = $options['default'];
            unset($options['default']);
        }

        parent::__construct($name, $digits, $decimal, $nullable, $default, $options);
    }
}
