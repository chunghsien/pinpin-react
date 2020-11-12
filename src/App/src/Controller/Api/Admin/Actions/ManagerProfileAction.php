<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Mezzio\Session\SessionMiddleware;
use Laminas\Math\Rand;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Users\TableGateway\UsersTableGateway;

class ManagerProfileAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $admin = $session->get('admin');
        return new ApiSuccessResponse(0, ['account' => $admin['account']]);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $set = json_decode($request->getBody()->getContents(), true);
            if($set['password'] === $set['password_confirm']) {
                unset($set['password_confirm']);
                $set['salt'] = Rand::getString(8);
                $password = $set['password'].$set['salt'];
                if(floatval(PHP_VERSION) < 7.2) {
                    $algo = PASSWORD_DEFAULT;
                }else {
                    $algo =  PASSWORD_ARGON2I;
                }
                $password = password_hash($password, $algo);
                $set['password'] = $password;
                UsersTableGateway::$isRemoveRowGatewayFeature = true;
                $tablegateway = new UsersTableGateway($this->adapter);
                $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
                $admin = $session->get('admin');
                
                $tablegateway->update($set, ['id' => $admin['id']]);
            }
            //'add success'
            return new ApiSuccessResponse(1, [], ['update success']);
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(1, [], ['system error']);
        }
        return new ApiErrorResponse(1, [], ['system error']);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['put'])) {
            return $this->put($request);
        }
        return new ApiErrorResponse(1, [], ['system error']);
    }
    
}
