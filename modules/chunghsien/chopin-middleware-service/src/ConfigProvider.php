<?php

namespace Chopin\MiddlewareService;

use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

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
