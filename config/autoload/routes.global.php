<?php

use Fig\Http\Message\RequestMethodInterface;
use App\Controller;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\AdminNavigationMiddleware;
use App\Middleware\ApiAdminAuthMiddleware;

return [
    'routes' => [
        'root' => [
            'path' => '/[{lang}[/]]','middleware' => [
                Controller\HomeController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'root'
        ],
        'home' => [
            'path' => '/{lang}/index[/]',
            'middleware' => [
                Controller\HomeController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'home'
        ],
        'static' => [
            'path' => '/{lang}/static/{page}',
            'middleware' => [
                Controller\StaticController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'static'
        ],
        'countdown-timers' => [
            'path' => '/{lang}/countdown-timers/{page}',
            'middleware' => [
                Controller\CountdownTimesController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'static'
        ],
        'blog-category' => [
            'path' => '/{lang}/blog-category[/{part1_id}[/{part2_id}[/{part3_id}]]]',
            'middleware' => [
                Controller\BlogCategoryController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'category'
        ],
        
        'category' => [
            'path' => '/{lang}/category[/{part1_id}[/{part2_id}[/{part3_id}]]]',
            'middleware' => [
                Controller\CategoryController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'category'
        ],
        'product' => [
            'path' => '/{lang}/product/{model_or_id}',
            'middleware' => [
                Controller\ProductsController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'product'
        ],
        'cart' => [
            'path' => '/{lang}/cart',
            'middleware' => [
                Controller\CartController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'cart'
        ],
        'wish' => [
            'path' => '/{lang}/wish',
            'middleware' => [
                Controller\WishController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'wish'
        ],
        'testimonials' => [
            'path' => '/{lang}/testimonials',
            'middleware' => [
                Controller\TestimonialsController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'testimonials'
        ],
        'flash-sale' => [
            'path' => '/{lang}/flash-sale',
            'middleware' => [
                Controller\FlashSaleController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'flash-sale'
        ],
        'checkout' => [
            'path' => '/{lang}/checkout',
            'middleware' => [
                Controller\CheckoutController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'checkout'
        ],
        'my-account' => [
            'path' => '/{lang}/my-account',
            'middleware' => [
                Controller\MyAccountController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'my-account'
        ],
        'login-register' => [
            'path' => '/{lang}/login-register',
            'middleware' => [
                Controller\LoginRegisterController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'login-register'
        ],
        'compare' => [
            'path' => '/{lang}/compare',
            'middleware' => [
                Controller\CompareController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'compare'
        ],
        
        'admin.root' => [
            'path' => '/{lang}/admin/',
            'middleware' => [
                AdminAuthMiddleware::class,
                AdminNavigationMiddleware::class,
                Controller\AdminDefaultController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'admin.root'
        ],
        'admin.login' => [
            'path' => '/{lang}/admin-login',
            'middleware' => [
                AdminAuthMiddleware::class,
                AdminNavigationMiddleware::class,
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
                AdminNavigationMiddleware::class,
                Controller\AdminLogoutController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => Controller\AdminLogoutController::ROUTE_NAME,
        ],
        'admin.default' => [
            'path' => '/{lang}/admin[/{page}[/{method_or_id}]]',
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
    ]
];
