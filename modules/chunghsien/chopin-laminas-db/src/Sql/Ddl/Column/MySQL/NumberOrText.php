<?php

namespace Chopin\LaminasDb\Sql\Ddl\Column\MySQL;

use Laminas\Db\Sql\Ddl\Column\AbstractLengthColumn;

/**
 * @description float or int or text or varchar ....
 * @author a_hsi
 *
 */
class NumberOrText extends AbstractLengthColumn
{

    /**
     *
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct($name, $type, array $options = [])
    {
        $this->type = $type;

        $length = null;
        $nullable = false;
        $default = null;

        if (isset($options['length'])) {
            $length = $options['length'];
            unset($options['length']);
        }
        if (isset($options['nullable'])) {
            $nullable = $options['nullable'];
            unset($options['nullable']);
        }

        if (isset($options['default'])) {
            $default = $options['default'];
            unset($options['default']);
        }
        parent::__construct($name, $length, $nullable, $default, $options);
    }
}
