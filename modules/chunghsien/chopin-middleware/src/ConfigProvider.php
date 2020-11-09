<?php

namespace Chopin\Middleware;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Retrieve Laminas-db default dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'abstract_factories' => [
                MiddlewareAbstractServiceFactory::class,
            ],
        ];
    }
}
