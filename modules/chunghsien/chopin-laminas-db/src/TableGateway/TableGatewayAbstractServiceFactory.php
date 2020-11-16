<?php

namespace Chopin\LaminasDb\TableGateway;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Interop\Container\ContainerInterface;
use Chopin\LaminasServiceManager\AbstractServiceFactory;

class TableGatewayAbstractServiceFactory extends AbstractServiceFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tablegateway = $this->buildTargetObject($container, $requestedName);
        return $tablegateway;
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (class_exists($requestedName)) {
            $reflection = new \ReflectionClass($requestedName);
            if ($reflection->isSubclassOf(AbstractTableGateway::class)) {
                return true;
            }
        }
        return false;
    }
}
