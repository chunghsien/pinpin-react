<?php

use Fig\Http\Message\RequestMethodInterface;
use App\Controller\SiteDefaultController;
use App\Controller\AdminDefaultController;
use App\Controller\AdminLoginController;
use App\Middleware\AdminAuthMiddleware;
use App\Controller\AdminLogoutController;
use App\Controller\ReactLocalesController;
use App\Controller\Api\Admin;

return [
    'routes' => [
        'root' => [
            'path' => '/','middleware' => [
                SiteDefaultController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'root'
        ],
        'site.default' => [
            'path' => '/site[/{page}[/{id}[/{lang}]]]',
            'middleware' => [
                SiteDefaultController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'site.default'
        ],
        'admin.root' => [
            'path' => '/admin/',
            'middleware' => [
                AdminDefaultController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'admin.root'
        ],
        'admin.login' => [
            'path' => '/admin/login',
            'middleware' => [
                AdminLoginController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST
            ],
            'name' => AdminAuthMiddleware::LOGIN_ROUTE_NAME,
        ],
        'admin.logout' => [
            'path' => '/admin/logout','middleware' => [
                AdminLogoutController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => AdminLogoutController::ROUTE_NAME,
        ],
        'admin.default' => [
            'path' => '/admin[/{page}[/{method_or_id}]]',
            'middleware' => [
                AdminDefaultController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],
            'name' => 'admin.default'
        ],
        /** define translation **/
        'locales' => [
            'path' => '/locales/resources.json',
            'middleware' => [
                ReactLocalesController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],
            'name' => 'locales'
        ],
        /** 以下定義 API routes **/
        'api.admin' => [
            'path' => '/api/admin/{action}[/{method_or_id}]',
            'middleware' => [
                Admin\ApiController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST,
                RequestMethodInterface::METHOD_PUT,
                RequestMethodInterface::METHOD_DELETE,
            ],
            'name' => 'api.admin'
        ],
    ]
];
