<?php

declare(strict_types = 1);
namespace App\Controller;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\LaminasView\LaminasViewRenderer;
use Mezzio\Router\RouteResult;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Csrf\CsrfMiddleware;
use Chopin\Users\TableGateway\UsersTableGateway;
use Laminas\Diactoros\Response\HtmlResponse;
use App\Middleware\AdminAuthMiddleware;
use Chopin\Jwt\JwtTools;
use Laminas\Diactoros\Response\JsonResponse;
use Firebase\JWT\JWT;
use Chopin\Support\Registry;

class AdminLoginController implements RequestHandlerInterface
{

    /** @var null|LaminasViewRenderer */
    private $template;

    /**
     *
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * 
     * @var UsersTableGateway
     */
    private $usersTableGateway;
    
    public function __construct(
        TemplateRendererInterface $template, 
        UrlHelper $urlHelper,
        UsersTableGateway $usersTableGateway
    )
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->usersTableGateway = $usersTableGateway;
    }
    
    protected function post(ServerRequestInterface $request): ResponseInterface
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
        /**
         *
         * @var \Mezzio\Csrf\SessionCsrfGuard $guard
         */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $body = $request->getParsedBody();
        if(!$body) {
            $_body = json_decode($request->getBody()->getContents(), true);
            if($_body) {
                $body = $_body;
                unset($_body);
            }
        }
        $token = $body['__csrf'];
        $error_uri = $this->urlHelper->generate(AdminAuthMiddleware::LOGIN_ROUTE_NAME);
        if($guard->validateToken($token)) {
            $set = $this->usersTableGateway->buildData($body);
            $resultSet = $this->usersTableGateway->select(['account' => $set['account']]);
            if($resultSet->count()) {
                /**
                 *
                 * @var \Chopin\LaminasDb\RowGateway\RowGateway $row
                 */
                $row = $resultSet->current();
                $hash = $row->password;
                $salt = $row->salt;
                if(password_verify(isset($set['password']) ? trim($set['password']).$salt : ''.$salt, $hash)) {
                    $success_uri = $this->urlHelper->generate(AdminAuthMiddleware::ROOT_ROUTE_NAME);
                    $user_data = $row->toArray();
                    if($request->hasHeader('content-type')) {
                        $content_type = implode('', $request->getHeader('content-type'));
                        if(preg_match('/^application\/json/i', $content_type)) {
                            $payload = JwtTools::buildPayload($user_data);
                            $key = config('encryption.jwt_key');
                            $alg = config('encryption.jwt_alg');
                            return new JsonResponse([
                                'message' => 'success',
                                'code' => 0,
                                'data' => [
                                    'JWT' => JWT::encode($payload, $key, $alg),
                                ],
                            ]);
                        }
                    }
                    $session->set('admin', $user_data);
                    return new RedirectResponse($success_uri, 302, []);
                }else {
                    setrawcookie('error', rawurlencode('login error'));
                    return new RedirectResponse($error_uri, 302, []);
                }
            }else {
                setrawcookie('error', rawurlencode('login error'));
                return new RedirectResponse($error_uri, 302, []);
            }
        }else {
            setrawcookie('error', rawurlencode('login error'));
            return new RedirectResponse($error_uri, 302, []);
        }
        
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if(strtolower($request->getMethod()) == 'post') {
            return $this->post($request);
        }
        /**
         *
         * @var RouteResult $routeResult
         */
        $routeResult = $request->getAttribute(RouteResult::class);
        /**
         * 
         * @var \Mezzio\Csrf\SessionCsrfGuard $csrfGuard
         */
        $csrfGuard = $request->getAttribute('csrf');
        $php_lang = str_replace('-', '_', $request->getAttribute('html_lang'));
        return new HtmlResponse($this->template->render('app::admin-default', [
            'layout' => false,
            '__csrf' => $csrfGuard->generateToken(),
            'html_lang' => $request->getAttribute('html_lang'),
            'site_name' => $request->getAttribute('system_settings')['site_info'][$php_lang]['children']['name']['value'],
            'page_json_config' => Registry::get('page_json_config'),
        ]));
    }
}
