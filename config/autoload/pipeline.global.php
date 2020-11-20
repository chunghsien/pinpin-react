<?php

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\ServerUrlMiddleware;
use Mezzio\Helper\UrlHelperMiddleware;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\ImplicitHeadMiddleware;
use Mezzio\Router\Middleware\ImplicitOptionsMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Session\SessionMiddleware;
use App\Middleware\SystemSettingsMiddleware;
use Mezzio\Csrf\CsrfMiddleware;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\AdminNavigationMiddleware;
use App\Middleware\ApiAdminAuthMiddleware;

return [
    'middleware_pipeline' => [
        [
            'middleware' => [
                ErrorHandler::class,
                ServerUrlMiddleware::class,
                RouteMiddleware::class,
                SessionMiddleware::class,
                CsrfMiddleware::class,
                ImplicitHeadMiddleware::class,
                ImplicitOptionsMiddleware::class,
                MethodNotAllowedMiddleware::class,
                UrlHelperMiddleware::class,
                SystemSettingsMiddleware::class,
            ],
            'priority' => 99,
        ],
        /*
        [
            'path' => '/admin',
            'middleware' => [
                AdminAuthMiddleware::class,
                AdminNavigationMiddleware::class,
            ],
            'priority' => 98,
        ],
        */
        [
            'path' => '/api/admin',
            'middleware' => [
                ApiAdminAuthMiddleware::class,
            ],
            'priority' => 98,
        ],
        
        
        [
            'middleware' => [
                DispatchMiddleware::class,
                NotFoundHandler::class,
            ],
            'priority' => 1,
        ],
        
    ],
];
