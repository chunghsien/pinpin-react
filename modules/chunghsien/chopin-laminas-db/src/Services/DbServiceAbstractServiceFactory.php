<?php

namespace Chopin\LaminasDb\Services;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Chopin\LaminasServiceManager\AbstractServiceFactory;

class DbServiceAbstractServiceFactory extends AbstractServiceFactory implements AbstractFactoryInterface
{

    /**
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = $this->buildTargetObject($container, $requestedName);
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
            //$reflection = new \ReflectionClass($requestedName);
            if (preg_match('/Service$/', $requestedName)) {
                return true;
            }
        }
        return false;
    }
}
