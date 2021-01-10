<?php

declare(strict_types = 1);
namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Helper\UrlHelper;
use App\Middleware\AdminAuthMiddleware;
use Mezzio\Session\SessionMiddleware;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouteResult;

class AdminLogoutController implements RequestHandlerInterface
{
    
    use Traits\AdminTrait;
    
    const ROUTE_NAME = 'admin.logout';
    
    /**
     * 
     * @var UrlHelper
     */
    private $urlHelper;
    
    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        
        /**
         *
         * @var \Mezzio\Session\LazySession $session
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $uri = $this->buildUri($this->urlHelper->getRouteResult(), '/admin-login');
        if($session->has('admin')) {
            $path = '/'.$request->getAttribute('html_lang');
            $session->unset('admin');
            setrawcookie('admin', '', time()-1, $path);
        }
       
        return new RedirectResponse($uri);
    }
}
