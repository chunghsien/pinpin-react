<?php

namespace Chopin\View\Helper;

use Psr\Container\ContainerInterface;

class DojoFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {


        /**
         *
         * @var \LaminasExpressive\Router\FastRouteRouter $router
         */
        $router = $container->get('LaminasExpressive\Router\RouterInterface');

        /**
         *
         * @var \LaminasDiactoros\ServerRequest $request
         */
        $request = $container->get('Psr\Http\Message\ServerRequestInterface')();
        $routeName = explode('.', $router->match($request)->getMatchedRouteName())[0];
        $config = [];
        if ($container->has('config')) {
            $g_config = $container->get('config');
            $config = isset($g_config['dojo']) ? $g_config['dojo'] : [];
            if (isset($g_config[$routeName]) && isset($g_config[$routeName]['dojo'])) {
                $config = array_merge_recursive($config, $g_config[$routeName]['dojo']);
            }
            unset($g_config);
        }
        return new Dojo($config);
    }
}
