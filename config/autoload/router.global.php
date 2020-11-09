<?php
use Mezzio\Router\FastRouteRouter;

return [
    'router' => [
        'fastroute' => [
            FastRouteRouter::CONFIG_CACHE_ENABLED => APP_ENV === 'production',
            FastRouteRouter::CONFIG_CACHE_FILE => 'storage/cache/fastroute.php.cache',
        ],
    ],
];
