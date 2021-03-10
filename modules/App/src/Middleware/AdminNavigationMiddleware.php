<?php
namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Navigation\Navigation;
use Mezzio\Session\SessionMiddleware;
use Chopin\Users\Service\UsersService;
use Laminas\Diactoros\Response\RedirectResponse;
use Chopin\Users\TableGateway\PermissionTableGateway;
use Laminas\Db\Adapter\Adapter;
use Mezzio\Router\RouteResult;
use Laminas\I18n\Translator\Translator;

class AdminNavigationMiddleware implements MiddlewareInterface
{

    use \App\Traits\I18nTranslatorTrait;
    use \App\Controller\Traits\AdminTrait;

    /**
     *
     * @var UsersService
     */
    private $usersService;

    /**
     *
     * @var Adapter
     */
    private $adapter;

    public function __construct(UsersService $usersService, Adapter $adapter)
    {
        $this->usersService = $usersService;
        $this->adapter = $adapter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! $request->getAttribute(Translator::class, null)) {
            $this->initTranslator();
        }
        $lang = $request->getAttribute('lang');
        $dir = dirname(dirname(__DIR__));

        $config = require "{$dir}/options/{$lang}_admin_navigation.php";
        $container = new Navigation($config);
        /**
         *
         * @var \Mezzio\Session\LazySession $session
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        /**
         *
         * @var RouteResult $routeResult
         */
        $routeResult = $request->getAttribute(RouteResult::class);
        $_basePath = $request->getAttribute('_base_path', '');
        
        if ($session->has('admin')) {
            $user = $session->get('admin');
            $denies = $this->usersService->getDenyPermission($user['id']);
            foreach ($denies as $deny) {
                $page = $container->findBy('uri', $deny);
                $container->removePage($page, true);
            }
            $toArray = $container->toArray();
            $navRecursive = new \RecursiveIteratorIterator($container);
            $uriIns = [];
            foreach ($navRecursive as $nav) {
                if ($nav->uri != '#') {
                    $uriIns[] = $nav->uri;
                }
            }

            PermissionTableGateway::$isRemoveRowGatewayFeature = true;
            $permissionTableGateway = new PermissionTableGateway($this->adapter);
            $permissionSelect = $permissionTableGateway->getSql()
                ->select()
                ->columns([
                'id'/*, 'name'*/, 'uri' /* , 'http_method' */
            ]);
            $permissionSelect->where([
                'uri' => $uriIns
            ]);
            $permissionResultSet = $permissionTableGateway->selectWith($permissionSelect);
            $permissions = [];
            foreach ($permissionResultSet->toArray() as $p) {
                $key = $_basePath . $p['uri'];
                $key = preg_replace('/^\/{2,}/', '/', $key);
                $permissions[$key] = $p['id'];
            }
            mergePageJsonConfig([
                'admin_permissions' => $permissions
            ]);
            $tmp = $toArray;
            $output = isset($user['denies']) ? $user['denies'] : [];
            $this->translator = $this->getTranslator($request);
            $admin_permission_status = $tmp;
            if (! $output) {
                $lng = $request->getAttribute('php_lang');
                $domain = 'admin-navigation';
                foreach ($tmp as $index => $level1) {
                    if (empty($output[$index])) {
                        $s = [
                            '_tag' => $level1['tag']
                        ];
                        if ($level1['uri'] == '#') {
                            if ($level1['tag'] == 'CSidebarNavTitle') {
                                $_name = $this->translator->translate($level1['name'], $domain, $lng);
                                $s['_children'] = [
                                    $_name
                                ];
                            } else {
                                $s['name'] = $this->translator->translate($level1['name'], $domain, $lng);
                                $s['fontIcon'] = $level1['font-icon'];
                            }
                        } else {
                            $to = $_basePath . $level1['uri'];
                            $to = preg_replace('/^\/{2,}/', '/', $to);
                            $s['to'] = $to;
                            $s['fontIcon'] = $level1['font-icon'];
                            $s['name'] = $this->translator->translate($level1['name'], $domain, $lng);
                        }
                        if (empty($s['to']) && isset($level1['pages']) && count($level1['pages']) === 0 && $level1['tag'] === 'CSidebarNavItem') {
                            continue;
                        }
                        $output[$index] = $s;
                    } else {
                        $admin_permission_status[$index]['allwo'] = 0;
                    }
                    if (isset($level1['pages']) && $level1['pages'] && count($level1['pages']) > 0) {
                        $i = [];

                        foreach ($level1['pages'] as $level2) {
                            $to = $_basePath . $level2['uri'];
                            $to = preg_replace('/^\/{2,}/', '/', $to);
                            $i[] = [
                                'to' => $to,
                                '_tag' => $level2['tag'],
                                'name' => $this->translator->translate($level2['name'], $domain, $lng)
                            ];
                        }
                        $output[$index]['_children'] = $i;
                    }
                }
                foreach ($output as $key => $n) {
                    if ($n['_tag'] == 'CSidebarNavDropdown' && empty($n['_children'])) {
                        unset($output[$key]);
                    }
                }
                $output = array_values($output);
                $user['denies'] = $denies;
                $session->set('admin', $user);
            }
            mergePageJsonConfig([
                'admin_permission_status' => $admin_permission_status
            ]);
            mergePageJsonConfig([
                'admin_navigation' => $output
            ]);

            $tmp = $request->getServerParams()['REQUEST_URI'];
            $tmp = explode('/', $tmp);
            $tmp = array_slice($tmp, 0, 4);
            $request_uri = implode('/', $tmp);
            
            if ($_basePath != '/') {
                $request_uri = str_replace($_basePath, '', $request_uri);
            }
            
            $pageParam = $request->getAttribute("page", null);
            if($routeResult->getMatchedRouteName() == "admin.default" && !$pageParam) {
                $containerPages = $container->getPages();
                foreach ($containerPages as $_page) {
                    if($_page instanceof \Laminas\Navigation\Page\Uri) {
                        $redirectUri = $_basePath.$_page->getUri();
                        $redirectUri = preg_replace('/\/{2,}/', '/', $redirectUri);
                        return new RedirectResponse($redirectUri);
                    }
                }
            }
            if (! $container->findOneBy('uri', $request_uri)) {
                $routePath = $routeResult->getMatchedRoute()->getPath();
                // debug(preg_match('/(admin)(\/{0,1})$/', $routePath));
                if (! preg_match('/admin\-(logout|404|500|login)$/', $routePath) && ! preg_match('/(admin)(\/{0,1})$/', $routePath)) {
                    $lang = $request->getAttribute('lang', 'zh-TW');
                    $uri = $_basePath.'/' . $lang . '/admin-404';
                    $uri = preg_replace('/^\/{2,}/', '/', $uri);
                    return new RedirectResponse($uri);
                }
            }
        }

        return $handler->handle($request);
    }
}