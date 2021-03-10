<?php

declare(strict_types = 1);
namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Session\SessionMiddleware;
use Laminas\Diactoros\Response\JsonResponse;
use Firebase\JWT\JWT;

class ApiAdminAuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         *
         * @var \Mezzio\Session\LazySession $session
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $admin = null;
        try {
            if(!$session->has('admin')) {
                if($request->hasHeader('Authorization')) {
                    
                    $jwt = implode('', $request->getHeader('Authorization'));
                    $jwt = preg_replace('/^bearer /i', '', $jwt);
                    $key = config('encryption.jwt_key');
                    $alg = config('encryption.jwt_alg');
                    $admin = JWT::decode($jwt, $key, [$alg]);
                }
                
            }else {
                $admin = $session->get('admin');
            }
            if(!$admin) {
                //-2定義成沒有權限，或sesssion失效
                return new JsonResponse([
                    'message' => '403 forbidden',
                    'code' => -2,
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'code' => -1,
                'data' => [],
            ], 500);
            
        }
        return $handler->handle($request);
    }
}