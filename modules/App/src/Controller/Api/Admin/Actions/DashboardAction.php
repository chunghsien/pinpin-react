<?php

declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Middleware\AbstractAction;

class DashboardAction extends AbstractAction
{

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $adapter = \Laminas\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
            /**
             *
             * @var \PDO $resource
             */
            $resource = $adapter->getDriver()->getConnection()->getResource();
            
            $dbScripts = require 'modules/App/scripts/db/admin/dashboard.php';
            $server = $request->getServerParams();
            $data = [
                'today_registed' => intval(DB::selectFactory($dbScripts['today_registed'])->current()['_count']),
                'total_registed' => intval(DB::selectFactory($dbScripts['total_registed'])->current()['_count']),
                'today_ordered' => intval(DB::selectFactory($dbScripts['today_ordered'])->current()['_count']),
                'total_ordered' =>intval(DB::selectFactory($dbScripts['total_ordered'])->current()['_count']),
                'DB_VER' => $resource->getAttribute(\PDO::ATTR_DRIVER_NAME).preg_replace('/\-(.*)$/', '', $resource->getAttribute(\PDO::ATTR_SERVER_VERSION)),
                'PHP_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION ,
                'SERVER_SOFTWARE' => $server['SERVER_SOFTWARE'],
                'PHP_OS' => PHP_OS,
            ];
            
            return new ApiSuccessResponse(0, $data, []);
            
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(417, ['trace' => $e->getTrace()], [$e->getMessage(), ]);
        }
    }
}
