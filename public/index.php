<?php

declare(strict_types = 1);



use Mezzio\Container\ApplicationConfigInjectionDelegator;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\ServiceManager\ServiceManager;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

if(!is_dir(__DIR__.DIRECTORY_SEPARATOR.'public')) {
    define('PROJECT_DIR', dirname(__DIR__));
}else {
    define('PROJECT_DIR', __DIR__);
}


// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

date_default_timezone_set("Asia/Taipei");

chdir(PROJECT_DIR);
require 'vendor/autoload.php';


if(isset($_SERVER['HTTP_ORIGIN']) && preg_match('/^http\:\/\/localhost\:\d+/', $_SERVER['HTTP_ORIGIN'])) {
     header('Access-Control-Allow-Origin: *');
     header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
     header('Access-Control-Allow-Credentials: true');
     header('Access-Control-Allow-Headers: content-type');
}

$request_uri = preg_replace('/^\//', '', $_SERVER['REQUEST_URI']);
$locale = explode('/', $request_uri)[0];
if($locale && $locale != 'locales') {
    define('FRONTEND_LOCALE', $locale);
    //BACKEND指的是PHP不是後台。
    define('BACKEND_LOCALE', str_replace('-', '_', FRONTEND_LOCALE));
}
unset($request_uri, $locale);

/**
 * Self-called anonymous function that creates its own scope and keeps the global namespace clean.
 */
(function () {
    /** @var \Psr\Container\ContainerInterface $container */
    $container = require 'config/container.php';

    /**
     *
     * @var Adapter $adapter
     */
    $adapter = $container->get(Adapter::class);
    GlobalAdapterFeature::setStaticAdapter($adapter);
    \Chopin\Support\Registry::set(ServiceManager::class, $container);
    $dbAdaptersOptions = config('db.adapters');
    if( isset($dbAdaptersOptions[Adapter::class]) ) {
        AbstractTableGateway::$prefixTable = $dbAdaptersOptions[Adapter::class]['prefix'];
    }
    $app = (new ApplicationConfigInjectionDelegator())($container, \Mezzio\Application::class, function () use ($container) {
        return $container->get(\Mezzio\Application::class);
    });
    $app->run();
})();
