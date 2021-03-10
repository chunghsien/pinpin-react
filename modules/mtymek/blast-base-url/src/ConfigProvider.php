<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    BasePathHelper::class => InvokableFactory::class,
                    BaseUrlMiddleware::class => BaseUrlMiddlewareFactory::class,
                ],
            ],
            'view_helpers' => [
                'aliases' => [
                    'basePath' => BasePathHelper::class,
                ],
                'factories' => [
                    BasePathHelper::class => BasePathViewHelperFactory::class,
                ],
            ],
        ];
    }
}
