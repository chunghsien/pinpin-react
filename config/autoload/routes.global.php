<?php

use Fig\Http\Message\RequestMethodInterface;
use App\Controller;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\AdminNavigationMiddleware;
use App\Middleware\ApiAdminAuthMiddleware;

return [
    'routes' => [
        'root' => [
            'path' => '/','middleware' => [
                Controller\Site\IndexController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'root'
        ],
        'index' => [
            'path' => '/{lang}[/[index]]',
            'middleware' => [
                Controller\Site\IndexController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'index'
        ],
        'admin.default' => [
            'path' => '/{lang}/admin[/{page:.*}[/{method_or_id}]]',
            'middleware' => [
                AdminAuthMiddleware::class,
                AdminNavigationMiddleware::class,
                Controller\AdminDefaultController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],
            'name' => 'admin.default'
        ],
        'admin.login' => [
            'path' => '/{lang}/admin-login',
            'middleware' => [
                AdminAuthMiddleware::class,
                Controller\AdminLoginController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST
            ],
            'name' => AdminAuthMiddleware::LOGIN_ROUTE_NAME,
        ],
        'admin.logout' => [
            'path' => '/{lang}/admin-logout','middleware' => [
                AdminAuthMiddleware::class,
                Controller\AdminLogoutController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => Controller\AdminLogoutController::ROUTE_NAME,
        ],
        
        /** define translation **/
        'locales' => [
            'path' => '/locales/resources.json',
            'middleware' => [
                Controller\ReactLocalesController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],
            'name' => 'locales'
        ],
        /** 以下定義 API routes **/
        'api.admin' => [
            'path' => '/{lang}/api/admin/{action}[/{method_or_id}]',
            'middleware' => [
                ApiAdminAuthMiddleware::class,
                Controller\Api\Admin\ApiController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST,
                RequestMethodInterface::METHOD_PUT,
                RequestMethodInterface::METHOD_DELETE,
            ],
            'name' => 'api.admin'
        ],
        'api.site' => [
            'path' => '/{lang}/api/site/{action}[/{method_or_id}]',
            'middleware' => [
                
                Controller\Api\Site\ApiController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST,
                RequestMethodInterface::METHOD_PUT,
                RequestMethodInterface::METHOD_DELETE,
            ],
            'name' => 'api.site'
        ],
        
    ]
];
