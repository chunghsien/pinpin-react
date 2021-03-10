<?php

namespace App\Controller\Api\SystemMaintain;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Mezzio\Csrf\CsrfMiddleware;
use Chopin\HttpMessage\Response\ApiErrorResponse;

class ApiController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = $request->getAttribute('action');
        $query = $request->getQueryParams();
        /**
         *
         * @var \Mezzio\Csrf\SessionCsrfGuard $guard
         */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        //$server = $request->getServerParams();
        if( APP_ENV == 'production'/* || (isset($server['HTTP_ORIGIN']) && preg_match('/localhost:\d+/', $server['HTTP_ORIGIN']))*/) {
            $token = '';
            if(isset($query['__csrf'])) {
                $token = $query['__csrf'];
            }
            if(!$token) {
                $body = $request->getParsedBody();
                if(!$body) {
                    $body = json_decode($request->getBody()->getContents(), true);
                }
                $token = isset($body['__csrf']) ? $body['__csrf'] : '';
            }
            if(!$token && $request->hasHeader('X-CSRF-TOKEN')) {
                $token = $request->getHeaderLine('X-CSRF-TOKEN');
            }
            if($action !== 'csrf') {
                if(!$guard->validateToken($token)) {
                    ApiErrorResponse::$status = 200;
                    return new ApiErrorResponse(1, [], ["驗證失敗"]);
                }
            }
        }
        $dashToCamelCase = new DashToCamelCase();
        $action = $dashToCamelCase->filter($action);
        $underscoreToCamelCase = new UnderscoreToCamelCase();
        $action = $underscoreToCamelCase->filter($action);
        $action = ucfirst($action);
        $classname = __NAMESPACE__.'\\Actions\\'.$action.'Action';
        $reflection = new \ReflectionClass($classname);
        $instance = $reflection->newInstance();
        return $instance->handle($request);
    }
    
}