<?php
declare(strict_types = 1);

namespace App\Controller\Api\SystemMaintain\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Users\TableGateway\UsersTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Jwt\JwtTools;
use Firebase\JWT\JWT;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use Mezzio\Csrf\CsrfMiddleware;

class LoginAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $usersTableGateway = new UsersTableGateway($this->adapter);
        $data = json_decode($request->getBody()->getContents(), true);
        
        $csrfVerify = $this->verifyCsrf($request);
        if($csrfVerify instanceof ApiErrorResponse)
        {
            return $csrfVerify;
        }
        if($data['account'] != 'admin') {
            return new ApiErrorResponse(1, [], []);
        }
        $row = $usersTableGateway->select(['account' => $data['account']])->current();
        if(!$row) {
            return new ApiErrorResponse(1, [], []);
        }
        $salt = $row->salt;
        $password = $data['password'];
        $verify = password_verify($password.$salt, $row->password);
        if($verify) {
            $payload = JwtTools::buildPayload($row->toArray());
            $key = config('encryption.jwt_key');
            $alg = config('encryption.jwt_alg');
            $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
            $systemSettingsRow = $systemSettingsTableGateway->select(['key' => 'system-maintain'])->current();
            $metadata = \Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($this->adapter);
            $tableNames = $metadata->getTableNames();
            
            return new ApiSuccessResponse(0, [
                "JWT" => JWT::encode($payload, $key, $alg),
                "status" => $systemSettingsRow->value,
                "tables" => $tableNames,
            ]);
        }else {
            return new ApiErrorResponse(1, [], []);
        }
    }
}
