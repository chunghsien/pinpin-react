<?php
declare(strict_types = 1);

namespace App\Controller\Api\SystemMaintain\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Jwt\JwtTools;
use Firebase\JWT\JWT;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;

class SystemSettingsAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $jwt = '';
        if($request->hasHeader('Authorization')) {
            $jwt = implode('', $request->getHeader('Authorization'));
            
        }else {
            $jwt = $request->getQueryParams()['authorization'];
        }
        $jwt = preg_replace('/^bearer /i', '', $jwt);
        
        $key = config('encryption.jwt_key');
        $alg = config('encryption.jwt_alg');
        $payload = JWT::decode($jwt, $key, [$alg]);
        if(JwtTools::verify($payload)) {
            //$where = ['key' => 'system-maintain'];
            $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
            $row = $systemSettingsTableGateway->select(['key' => 'system-maintain'])->current();
            $body = json_decode($request->getBody()->getContents(), true);
            $row->value = $body['system-maintain'] === true ? 1 : 0;
            $row->save();
            if($body['twig_cache'] && is_dir('./storage/cache/twig')) {
                recursiveRemoveFolder('./storage/cache/twig');
                //mkdir('./storage/cache/twig', 0644, true);
            }
            if($body['tables']) {
                $schema = $this->adapter->getCurrentSchema();
                $path = "./storage/database/{$schema}";
                foreach ($body['tables'] as $table)
                {
                    DbCacheMapperTableGateway::refreash($table);
                    if(is_dir("{$path}/{$table}")) {
                        recursiveRemoveFolder("{$path}/{$table}");
                    }
                }
            }
            $metadata = \Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($this->adapter);
            $tableNames = $metadata->getTableNames();
            
            return new ApiSuccessResponse(0, [
                "JWT" => $jwt,
                "status" => $row->value,
                "tables" => $tableNames
            ]);
            
        }else {
            return new ApiErrorResponse(1, [], []);
        }
    }
}
