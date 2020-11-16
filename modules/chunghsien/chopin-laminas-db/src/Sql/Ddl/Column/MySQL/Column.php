<?php

namespace Chopin\LaminasDb\Sql\Ddl\Column\MySQL;

use Laminas\Db\Sql\Ddl\Column\Column as LaminasDblColumn;

/**
 * @description float or int or text or varchar ....
 * @author a_hsi
 *
 */
class Column extends LaminasDblColumn
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
