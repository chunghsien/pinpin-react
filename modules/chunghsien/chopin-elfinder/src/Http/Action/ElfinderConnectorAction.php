<?php

namespace Chopin\Elfinder\Http\Action;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
//use elFinderConnector;
//use Laminas\Diactoros\Response;
use Chopin\Elfinder\Session\ElfinderSession;
use Mezzio\Session\LazySession;

class ElfinderConnectorAction implements MiddlewareInterface
{
    private $opts;

    public function __construct(array $opts)
    {
        $this->opts = $opts;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        
        $opts = $this->opts;
        /**
         *
         * @var LazySession $lazySession
         */
        $lazySession = $request->getAttribute('session');
        $session = new ElfinderSession($lazySession);
        $opts['session'] = $session;

        foreach ($opts['roots'] as $opt) {
            if ( ! is_dir($opt['path'])) {
                mkdir($opt['path'], 0777, true);
            }
        }
        $elFinder = new \elFinder($opts);

        (new \elFinderConnector($elFinder))->run();
        //return new Response\JsonResponse([]);
    }
}
