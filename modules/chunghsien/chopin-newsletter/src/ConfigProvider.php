<?php

namespace Chopin\Newsletter;

use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

//use Laminas\Debug\Debug;

class ConfigProvider
{

    /**
     * Returns the configuration array
     *
     * @return array
     */
    public function __invoke()
    {
        $configAffregator = new ConfigAggregator([
            new PhpFileProvider(dirname(__DIR__) . '/config/*.php'),
            new PhpFileProvider(dirname(__DIR__) . '/config/**/*.php'),
        ]);
        return $configAffregator->getMergedConfig();
    }
}
