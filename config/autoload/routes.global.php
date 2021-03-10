<?php

use Fig\Http\Message\RequestMethodInterface;
use App\Controller;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\AdminNavigationMiddleware;
use App\Middleware\ApiAdminAuthMiddleware;
use App\Middleware\SiteNavigatotMiddleware;

return [
    'routes' => [
        'root' => [
            'path' => '/[{lang}[/]]','middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\IndexController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'root'
        ],
        'about' => [
            'path' => '/{lang}/about',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\AboutController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'about'
        ],
        'cart' => [
            'path' => '/{lang}/cart',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\CartController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'cart'
        ],
        'checkout' => [
            'path' => '/{lang}/checkout',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\CheckoutController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'checkout'
        ],
        'compare' => [
            'path' => '/{lang}/compare',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\CompareController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'compare'
        ],
        'contact' => [
            'path' => '/{lang}/contact',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\ContactController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'contact'
        ],
        'faq' => [
            'path' => '/{lang}/faq',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\FaqController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'contact'
        ],
        'login-register' => [
            'path' => '/{lang}/login-registe',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\LoginRegisterController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'login-register'
        ],
        'my-account' => [
            'path' => '/{lang}/my-account',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\MyAccountController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'my-account'
        ],
        'order-tracking' => [
            'path' => '/{lang}/order-tracking',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\OrderTrackingController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'order-tracking'
        ],
        'wishlist' => [
            'path' => '/{lang}/wishlist',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\WishListController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'wishlist'
        ],
        'product' => [
            'path' => '/{lang}/product',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\ProductController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'product'
        ],
        'category' => [
            'path' => '/{lang}/product-category',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\CategoryController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'category'
        ],
        
        'news' => [
            'path' => '/{lang}/news',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\NewsController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'news'
        ],
        'news-category' => [
            'path' => '/{lang}/news-category',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\NewsCategoryController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'news-list'
        ],
        'search' => [
            'path' => '/{lang}/search',
            'middleware' => [
                SiteNavigatotMiddleware::class,
                Controller\Site\SearchController::class
            ],'allowed_methods' => [
                RequestMethodInterface::METHOD_GET
            ],'name' => 'search'
        ],
        
//----------------------------------------------------------------------------------------------------------------------
        
        'admin.default' => [
            'path' => "/{lang}/admin[/{page:.*}[/{method_or_id}]]",
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
        'system_maintain' => [
            'path' => '/system-maintain/maintain',
            'middleware' => [
                Controller\SystemMaintain\MaintainController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST,
            ],
            'name' => 'system_maintain'
        ],
        /** 以下定義 API routes **/
        'api.system_maintain' => [
            'path' => '/{lang}/api/system-maintain/{action}[/{method_or_id}]',
            'middleware' => [
                Controller\Api\SystemMaintain\ApiController::class
            ],
            'allowed_methods' => [
                RequestMethodInterface::METHOD_GET,
                RequestMethodInterface::METHOD_POST,
                RequestMethodInterface::METHOD_PUT,
                RequestMethodInterface::METHOD_DELETE,
            ],
            'name' => 'api.system_maintain'
        ],
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
                SiteNavigatotMiddleware::class,
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
