<?php

namespace Chopin\Elfinder\Http\Action;

use Psr\Container\ContainerInterface;

class ElfinderConnectorActionFactory
{
    public function __invoke(ContainerInterface $container): ElfinderConnectorAction
    {
        $config = $container->get('config')['elFinderConnector'];
        return new ElfinderConnectorAction($config);
    }
}
