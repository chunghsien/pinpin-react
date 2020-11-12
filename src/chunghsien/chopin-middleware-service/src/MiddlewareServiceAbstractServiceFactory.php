<?php

namespace Chopin\MiddlewareService;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Chopin\LaminasServiceManager\AbstractServiceFactory;

class MiddlewareServiceAbstractServiceFactory extends AbstractServiceFactory implements AbstractFactoryInterface
{

    /**
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $this->buildTargetObject($container, $requestedName);
        //$service->masterDbConnection = $container->get(MasterAdapterInterface::class)->driver->getConnection();
        //$service->slaveDbConnection = $container->get(SlaveAdapterInterface::class)->driver->getConnection();

        return $service;
    }

    /**
     *
     * @see \Laminas\ServiceManager\Factory\AbstractFactoryInterface::canCreate()
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // $config = $container->get('config');
        if (class_exists($requestedName)) {
            $reflection = new \ReflectionClass($requestedName);
            $parentClass = $reflection->getParentClass();
            if ( ! $parentClass) {
                return false;
            }

            $parantClassname = $parentClass->name;

            if ($grand = $parentClass->getParentClass()) {
                $grandClass = $grand->name;
                if ($grandClass == 'Chopin\MiddlewareService\AbstractMiddlewareService') {
                    return true;
                }
            }

            if ($parantClassname == 'Chopin\MiddlewareService\AbstractMiddlewareService') {
                return true;
            }
        }
        return false;
    }
}
