<?php

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Router\RouteResult;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;
use Chopin\Users\Service\UsersService;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\I18n\Translator\Translator;
use App\Controller\Traits\AdminTrait;

class AdminAuthMiddleware implements MiddlewareInterface
{
    
    use \App\Traits\I18nTranslatorTrait;
    use AdminTrait;
    
    const LOGIN_ROUTE_NAME = 'admin.login';
    const ROOT_ROUTE_NAME = 'admin.root';
    
    /**
     * 
     * @var UrlHelper
     */
    private $urlHelper;
    
    /**
     * 
     * @var UsersService
     */
    private $userService;
    
    public function __construct(UrlHelper $urlHelper, UsersService $userService, StorageInterface $cache)
    {
        $this->urlHelper = $urlHelper;
        $this->userService = $userService;
        $this->initTranslator($cache);
    }
    
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         *
         * @var RouteResult $routeResult
         */
        $routeResult = $request->getAttribute(RouteResult::class);
        /**
         *
         * @var \Mezzio\Session\LazySession $session
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $request = $request->withAttribute(Translator::class, $this->translator);
        $_basePath = $request->getAttribute('_base_path', '');
        if($session->has('admin')) {
            
            $useRouteResult = $this->urlHelper->getRouteResult(self::ROOT_ROUTE_NAME);
            setrawcookie('admin', $this->buildUri($useRouteResult, '', $_basePath));
            $user = $session->get('admin');
            $routes = isset($user['routes']) ? $user['routes'] : [];
            $lng = $request->getAttribute('php_lang');
            $domain = 'admin-navigation';
            
            foreach ($routes as &$_r) {
                $_r['name'] = $this->translator->translate($_r['name'], $domain, $lng);
            }
            if(!$routes) {
                $user['routes'] = $routes = $this->userService->getUserAllowedPermission($user['id'], true);
                $user = $session->set('admin', $user);
            }
            $underscoreToCamelCase = new UnderscoreToCamelCase();
            $dashToCamelCase = new DashToCamelCase();
            foreach ($routes as &$route) {
                $matcher = [];
                preg_match('/\/(?P<component>\w+)$/', $route['uri'], $matcher);
                $component = $underscoreToCamelCase->filter($matcher['component']);
                $component = $dashToCamelCase->filter($component);
                $component_path = 'modules/React/views/admin/pages/'.ucfirst($component).'.js';

                
                if(is_file($component_path)) {
                    $route['component'] = preg_replace('/^modules\/React\//', './', $component_path);
                    $route['component'] = preg_replace('/\.js$/', '', $route['component']);
                }else {
                    $component_path = 'modules/React/views/admin/pages/'.ucfirst($component).'.js';
                    //$component_path = 'src/React/views/admin/pages/'.ucfirst($component).'.js';
                    //debug($component_path);
                    if(is_file($component_path)) {
                        $route['component'] = preg_replace('/^modules\/React\//', './', $component_path);
                        $route['component'] = preg_replace('/\.js$/', '', $route['component']);
                    }else {
                        $route['component'] = null;
                    }
                }
                $route['name'] = $this->translator->translate($route['name'], $domain, $lng);
            }
            array_unshift($routes, ['uri' => '/admin/', 'exact' => true, 'name' => $this->translator->translate('Home', $domain, $lng)]);
            mergePageJsonConfig(['routes' => $routes]);
            if($routeResult->getMatchedRouteName() == self::LOGIN_ROUTE_NAME) {
                $uri = $this->buildUri($routeResult, '/', $_basePath);
                return new RedirectResponse($uri);
            }
            $request = $request->withAttribute(Translator::class, $this->translator);
        }else {
            //$path = '/'.$request->getAttribute('html_lang');
            setrawcookie('admin', null, time()-1);
            if($routeResult->getMatchedRouteName() != self::LOGIN_ROUTE_NAME) {
                //$notMatchRouteResult = $this->urlHelper->getRouteResult(self::LOGIN_ROUTE_NAME);
                $uri = $this->buildUri($routeResult, '/admin-login', $_basePath);
                return new RedirectResponse($uri);
            }
           
        }
        if($page = $request->getAttribute('page', null)) {
            if(!$request->getAttribute('method_or_id', null)) {
                if(preg_match('/\//', $page)) {
                    $explode = explode('/', $page);
                    if(count($explode) == 2) {
                        $request = $request->withAttribute('page', $explode[0]);
                        $request = $request->withAttribute('method_or_id', $explode[1]);
                    }
                }
            }
            
        }
        
        return $handler->handle($request);
    }
}