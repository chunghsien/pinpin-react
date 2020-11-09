<?php

namespace Chopin\LaminasServiceManager;

use Interop\Container\ContainerInterface;

abstract class AbstractServiceFactory
{
    protected function buildTargetObject(ContainerInterface $container, $requestedName)
    {
        $reflection = new \ReflectionClass($requestedName);
        $constructor = $reflection->getConstructor();
        $args = [];

        if ($reflection->hasMethod('__Construct')) {
            $construct = $reflection->getMethod('__Construct');
            $params = $construct->getParameters();
            foreach ($params as $param) {
                /**
                 *
                 * @var \ReflectionParameter $param
                 */
                if ( ! $param->getClass()) {
                    if ($param->name == 'config' && is_string($param->getDefaultValue())) {
                        $key = $param->getDefaultValue();
                        $args[] = json_encode($container->get('config')[$key]);
                        continue;
                    } else {
                        return false;
                    }
                } else {
                    $id = $param->getClass()->name;
                    $args[] = $container->get($id);
                }
            }
        }
        return $reflection->newInstanceArgs($args);
    }
}
