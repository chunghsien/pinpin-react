<?php

namespace Chopin\Support;

use Laminas\Log\Logger;

abstract class Log
{

    /**
     *
     * @var Logger
     */
    private static $logger;

    const STREAM_POS = 'storage/log/php_syslog_%s.log';

    protected static function init()
    {
        if ( ! self::$logger instanceof Logger) {
            $pos = sprintf(self::STREAM_POS, date("Ymd"));
            self::$logger = new Logger([
                'writers' =>[
                    [
                        'name' => \Laminas\Log\Writer\Stream::class,
                        'priority' => 1,
                        'options' => [
                            'mod' => 'a+',
                            'stream' => $pos,
                        ],
                    ],
                ],
            ]);
        }
    }

    public static function log()
    {
        self::init();
        return self::$logger;
    }

    public static function __callstatic($name, $args)
    {
        self::init();
        $logger = self::$logger;
        call_user_func_array([$logger, $name], $args);
    }
}
