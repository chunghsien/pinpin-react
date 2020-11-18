<?php

declare(strict_types = 1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Chopin\Support\ModulesLoader;

if ( ! defined('APP_ENV')) {
    if (PHP_SAPI === 'cli') {
        if ( ! defined('APP_ENV')) {
            define('APP_ENV', 'development');
        }
    } else {
        $env = require __DIR__ . '/env.php';
        $serverAddr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1' ;
        if (empty($env[$serverAddr])) {
            if ( ! defined('APP_ENV')) {
                define('APP_ENV', 'production');
                define('ASSETS_LAST_MODI', filemtime(__FILE__));
            }
        } else {
            if ( ! defined('APP_ENV')) {
                define('APP_ENV', $env[$serverAddr]);
                define('ASSETS_LAST_MODI', time());
            }
        }
    }
}

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = null;
if(APP_ENV == 'production') {
    $cacheConfig = [
        ConfigAggregator::ENABLE_CACHE => 1,
        'config_cache_path' => 'storage/cache/config-cache.php', 
        'lifetime' => 86400 * 365,
    ];
}else {
    $cacheConfig = ['debug' => true];
}

$configData =    [
    \Laminas\Serializer\ConfigProvider::class,
    \Laminas\Navigation\ConfigProvider::class,
    \Laminas\Cache\ConfigProvider::class,
    \Laminas\I18n\ConfigProvider::class,
    \Laminas\InputFilter\ConfigProvider::class,
    \Laminas\Filter\ConfigProvider::class,
    \Laminas\Validator\ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    \Mezzio\Helper\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Mezzio\Twig\ConfigProvider::class,
    //\Mezzio\LaminasView\ConfigProvider::class,
    \Mezzio\Session\ConfigProvider::class,
    \Mezzio\Session\Ext\ConfigProvider::class,
    \Mezzio\Flash\ConfigProvider::class,
    \Mezzio\Csrf\ConfigProvider::class,
    \Chopin\LaminasDb\ConfigProvider::class,
    \Chopin\Documents\ConfigProvider::class,
    //\Chopin\Elfinder\ConfigProvider::class,
    \Chopin\LanguageHasLocale\ConfigProvider::class,
    \Chopin\Newsletter\ConfigProvider::class,
    \Chopin\Store\ConfigProvider::class,
    \Chopin\SystemSettings\ConfigProvider::class,
    \Chopin\Users\ConfigProvider::class,
    
    //第三方支付
    \Chopin\Newwebpay\ConfigProvider::class,

    // Swoole config to overwrite some services (if installed)
    //class_exists(\Mezzio\Swoole\ConfigProvider::class) ? \Mezzio\Swoole\ConfigProvider::class : function(){ return[]; },

    // Default App module config
    App\ConfigProvider::class,

    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    //new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),
    new PhpFileProvider(sprintf(realpath(__DIR__) . '/autoload/{,*.}{global,%s,local}.php', preg_replace('/\.debug$/i', '', APP_ENV))),
    // Load development config if it exists
    new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
];
if($cacheConfig) {
    $configData[] = new ArrayProvider($cacheConfig);
}
//$cacheConfig['config_cache_path'];
$configData = array_merge($configData, ModulesLoader::buildConfigProvider());
$config_cache_path = isset($cacheConfig['config_cache_path']) ? $cacheConfig['config_cache_path'] : null;
$aggregator = new ConfigAggregator($configData, $config_cache_path);
return $aggregator->getMergedConfig();
