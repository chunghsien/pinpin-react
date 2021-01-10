<?php

use Laminas\I18n\Translator\Resources;

return [
    'translator' => [
        'translation_file_patterns' => [
            [
                //ReactJs default
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/translation.php',
                'text_domain' => 'translation',
            ],
            [
                //ReactJs default
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/currencies.php',
                'text_domain' => 'currencies',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/google.php',
                'text_domain' => 'google',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/admin-login.php',
                'text_domain' => 'admin-login',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/admin-404.php',
                'text_domain' => 'admin-404',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/admin-navigation.php',
                'text_domain' => 'admin-navigation',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/admin-dashboard.php',
                'text_domain' => 'admin-dashboard',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/admin-language.php',
                'text_domain' => 'admin-language',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/admin-fpClass.php',
                'text_domain' => 'admin-fpClass',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/facebook.php',
                'text_domain' => 'facebook',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/site-footer.php',
                'text_domain' => 'site-footer',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/site-navigation.php',
                'text_domain' => 'site-navigation',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/site-product-grid.php',
                'text_domain' => 'site-product-grid',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => dirname(dirname(__DIR__)).'/resources/languages',
                'pattern' => '%s/site-product-tab.php',
                'text_domain' => 'site-product-tab',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => Resources::getBasePath(),
                'pattern' => Resources::getPatternForCaptcha(),
                'text_domain' => 'laminas-captcha',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => Resources::getBasePath(),
                'pattern' => Resources::getPatternForValidator(),
                'text_domain' => 'laminas-validator',
            ],
        ],
    ],
];
