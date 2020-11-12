<?php

namespace Chopin\LaminasDb\Sql\Ddl\Column\MySQL;

abstract class MySQLColumnFactory
{
    private static $NumberTextType = [
        'tinyint',
        'smallint',
        'mediumint',
        'int',
        'bigint',
        'float',
        'dobule',
        'varchar',
        'varbinary',
        'tinytext',
        'text',
        'mediumtext',
        'longtext',
        'blob',
        'longblob',
    ];

    private static $TimestampType = [
        'timestamp',
    ];

    private static $DecimalType = [
        'decimal',
    ];

    /**
     *
     * @param string $name
     * @param string $type
     * @param array $options
     * @return \Laminas\Db\Sql\Ddl\Column\Column
     */
    public static function buildColumn($name, $type, $options=[])
    {
        //$column = null;
        if (false !== array_search($type, static::$NumberTextType)) {
            return new NumberOrText($name, $type, $options);
        }

        if (false !== array_search($type, static::$TimestampType)) {
            return new Timestamp($name, $options);
        }

        if (false !== array_search($type, static::$DecimalType)) {
            return new Percision($name, $options);
        }

        return new Column($name, $type, $options);
    }
}
