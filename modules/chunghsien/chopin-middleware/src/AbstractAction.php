<?php

namespace Chopin\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Mezzio\Csrf\CsrfMiddleware;
use Chopin\HttpMessage\Response\ApiErrorResponse;

abstract class AbstractAction implements RequestHandlerInterface
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    /**
     *
     * @var AbstractTableGateway[]
     */
    protected $tableGateways;

    public function __construct()
    {
        $this->adapter = GlobalAdapterFeature::getStaticAdapter();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    // abstract public function getStandByVars(ServerRequestInterface $request);
    protected function getCommonVars(ServerRequestInterface $request)
    {
        /**
         *
         * @var \Mezzio\Csrf\SessionCsrfGuard $guard
         */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $csrf = $guard->generateToken();
        if (strtolower($request->getMethod()) != 'get') {
            return [
                '__csrf' => $csrf,
            ];
        }
        $query = $request->getQueryParams();
        if (isset($query['noCommonVars'])) {
            return [];
        }
        $system_settings = $request->getAttribute('system_settings');
        $lang = str_replace('-', '_', $request->getAttribute('lang'));
        $site_info = $system_settings['site_info'][$lang]['to_config'];
        $site_info['operation'] = nl2br($site_info['operation']);
        return [
            '__csrf' => $csrf,
            'site_info' => $site_info,
            'system' => $system_settings['system']['to_config'],
            'site_header' => $request->getAttribute('site_header'),
            'site_footer' => $request->getAttribute('site_footer'),
        ];
    }

    protected function verifyCsrf(ServerRequestInterface $request)
    {
        if (APP_ENV == 'production') {
            $server = $request->getServerParams();
            if (isset($server['HTTP_ORIGIN']) && preg_match('/localhost:\d+/', $server['HTTP_ORIGIN'])) {
                return true;
            }
            $query = $request->getQueryParams();
            $token = '';
            if (isset($query['__csrf'])) {
                $token = $query['__csrf'];
            }
            if (! $token) {
                $body = $request->getParsedBody();
                if (! $body) {
                    $body = json_decode($request->getBody()->getContents(), true);
                }
                $token = isset($body['__csrf']) ? $body['__csrf'] : '';
            }
            /**
             *
             * @var \Mezzio\Csrf\SessionCsrfGuard $guard
             */
            $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
            if (! $guard->validateToken($token)) {
                return new ApiErrorResponse(1, [], [
                    "驗證失敗"
                ]);
            }
        }
        return true;
    }
}