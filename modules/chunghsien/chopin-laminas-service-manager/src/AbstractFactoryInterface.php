<?php

namespace Chopin\LaminasServiceManager;

use Interop\Container\ContainerInterface;

interface AbstractFactoryInterface
{
    protected function buildService(ContainerInterface $container, $requestedName, array $options = null);

    //protected function buildCacheKey($requestName);
}
