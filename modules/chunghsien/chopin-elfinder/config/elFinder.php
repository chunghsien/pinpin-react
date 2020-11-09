<?php
if ( ! (PHP_SAPI === 'cli')) {
    return [
        'elFinderConnector' => [
            'roots' => [
                [
                    'driver' => 'LocalFileSystem',
                    'path' => 'public/storage/elfinder/',
                    'URL' => '/storage/elfinder',
                    'tmbURL'        => '/storage/elfinder/.tmb/',
                    'trashHash'     => 't1_Lw',
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
                    'uploadDeny'    => ['all'],                // Recomend the same settings as the original volume that uses the trash
                    'uploadAllow'   => ['image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain'], // Same as above
                    'uploadOrder'   => ['deny', 'allow'],      // Same as above
                    'attributes' => [
                        [
                            'pattern' => '/^\/\./', // dot files are hidden
                            'read'    => false,
                            'write'   => false,
                            'hidden'  => true,
                            //'locked'  => true
                        ],
                    ],
                    // 'session' => LaminasExpressiveSession::class
                ],
                [
                    'id' => '1',
                    'driver'        => 'Trash',
                    'path'          => 'public/storage/elfinder/.trash',
                    'tmbURL'        => '/storage/elfinder/.trash/.tmb/',
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny'    => ['all'],                // Recomend the same settings as the original volume that uses the trash
                    'uploadAllow'   => ['image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain'], // Same as above
                    'uploadOrder'   => ['deny', 'allow'],      // Same as above
                    'attributes' => [
                        [
                            'pattern' => '/^\/\./', // dot files are hidden
                            'read'    => false,
                            'write'   => false,
                            'hidden'  => true,
                            //'locked'  => true
                        ],
                    ],


                ],
            ],
        ],
    ];
} else {
    return [];
}
