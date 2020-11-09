<?php

namespace Chopin\Elfinder;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Session\SessionPersistenceInterface;
use elFinder;
use elFinderConnector;
use Chopin\Elfinder\Session\ElfinderSession;
use Mezzio\Session\LazySession;

class ElfinderConnectorFactory
{
    public function __invoke(ContainerInterface $container): elFinderConnector
    {
        $request = $container->get(ServerRequestInterface::class);

        $persistence = $container->get(SessionPersistenceInterface::class);
        $lazySession = new LazySession($persistence, $request());

        $config = $container->get('config')['elFinderConnector'];
        $config['roots'][0]['session'] = new ElfinderSession($lazySession);
        $elFinder = new elFinder($config);
        return new elFinderConnector($elFinder);
    }
}
