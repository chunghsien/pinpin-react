<?php

namespace Chopin\View\Helper;

use Psr\Container\ContainerInterface;

class FootScriptFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = [];
        if ($container->has('config')) {
            $g_config = $container->get('config');
            $config = isset($g_config['dojo']) ? $g_config['dojo'] : [];

            unset($g_config);
        }

        return new FootScript($config);
    }
}
