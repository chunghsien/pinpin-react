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

class AdminNavigationMiddleware implements MiddlewareInterface
{

    use \App\Traits\I18nTranslatorTrait;
    
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
    
    public function __construct(UsersService $usersService, Adapter $adapter) {
        $this->usersService = $usersService;
        $this->adapter = $adapter;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $config = require dirname(dirname(__DIR__)).'/config/admin.navigation.php';
        $container = new Navigation($config);
        /**
         *
         * @var \Mezzio\Session\LazySession $session
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if($session->has('admin')) {
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
                if($nav->uri != '#') {
                    $uriIns[] = $nav->uri;
                }
            }
            
            PermissionTableGateway::$isRemoveRowGatewayFeature = true;
            $permissionTableGateway = new PermissionTableGateway($this->adapter);
            $permissionSelect = $permissionTableGateway->getSql()->select()->columns(['id'/*, 'name'*/, 'uri'/*, 'http_method'*/]);
            $permissionSelect->where(['uri' => $uriIns]);
            $permissionResultSet = $permissionTableGateway->selectWith($permissionSelect);
            $permissions = [];
            foreach($permissionResultSet->toArray() as $p) {
                $key = $p['uri'];
                $permissions[$key] = $p['id'];
            }
            mergePageJsonConfig(['admin_permissions' => $permissions]);
            $tmp = $toArray;
            $output = isset($user['denies']) ? $user['denies'] : [];
            $this->translator = $this->getTranslator($request);
            $admin_permission_status = $tmp;
            if(!$output) {
                $lng = $request->getAttribute('php_lang');
                $domain = 'admin-navigation';
                foreach ($tmp as $index => $level1) {
                    if(empty($output[$index])) {
                        $s = [
                            '_tag' => $level1['tag'],
                        ];
                        if($level1['uri'] == '#') {
                            if($level1['tag'] == 'CSidebarNavTitle') {
                                $_name = $this->translator->translate($level1['name'], $domain, $lng);
                                $s['_children'] = [ $_name ];
                            }else {
                                $s['name'] = $this->translator->translate($level1['name'], $domain, $lng);
                                $s['fontIcon'] = $level1['font-icon'];
                            }
                            
                        }else {
                            $s['to'] = $level1['uri'];
                            $s['fontIcon'] = $level1['font-icon'];
                            $s['name'] = $this->translator->translate($level1['name'], $domain, $lng);
                        }
                        
                        if(
                            empty($s['to']) &&
                            isset($level1['pages']) &&
                            count($level1['pages']) === 0 &&
                            $level1['tag'] === 'CSidebarNavItem'
                            ) {
                                continue;
                            }
                            $output[$index] = $s;
                    }else {
                        $admin_permission_status[$index]['allwo'] = 0;
                    }
                    if(isset($level1['pages']) && $level1['pages'] && count($level1['pages']) > 0) {
                        $i = [];
                        foreach ($level1['pages'] as $l2key => $level2) {
                            $i[] = [
                                'to' => $level2['uri'],
                                '_tag' => $level2['tag'],
                                'name' => $this->translator->translate($level2['name'], $domain, $lng),
                            ];
                        }
                        $output[$index]['_children'] = $i;
                    }
                }
                foreach ($output as $key => $n) {
                    if($n['_tag'] == 'CSidebarNavDropdown' && empty($n['_children'])) {
                        unset($output[$key]);
                    }
                }
                $output = array_values($output);
                $user['denies'] = $denies;
                $session->set('admin', $user);
            }
            mergePageJsonConfig(['admin_permission_status' => $admin_permission_status]);
            mergePageJsonConfig(['admin_navigation' => $output]);
            
            $request_uri = $request->getServerParams()['REQUEST_URI'];
            $request_uri = preg_replace('/\/\w+$/', '', $request_uri);
            $request_uri = preg_replace('/\/\d+$/', '', $request_uri);
            if(!$container->findOneBy('uri', $request_uri)) {
                //缺少瀏覽權限
                if(!preg_match('/^\/admin(\/{0,1})$/', $request_uri) && !preg_match('/^\/admin\/(logout|404|500)$/', $request_uri)) {
                    return new RedirectResponse('/admin/404');
                }
            }
        }
        return $handler->handle($request);
    }
}