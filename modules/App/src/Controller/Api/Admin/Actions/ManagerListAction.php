<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Mezzio\Session\SessionMiddleware;
use Chopin\Users\TableGateway\UsersTableGateway;
use Laminas\Math\Rand;

class ManagerListAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new UsersTableGateway($this->adapter));
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $apiQueryService = new ApiQueryService();
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $admin = $session->get('admin');
            $id = $admin['id'];
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/manager_list.php', [
                'account' => 'users',
                'role_name' => 'roles',
                'created_at' => 'users',
            ]);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new UsersTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new UsersTableGateway($this->adapter);
        $parseBody = $request->getParsedBody();
        if(isset($parseBody['password']) && isset($parseBody['password_confirm'])) {
            if($parseBody['password'] == $parseBody['password_confirm']) {
                unset($parseBody['password_confirm']);
                $salt = Rand::getString(8);
                $parseBody['salt'] = $salt;
                $password = $parseBody['password'].$salt;
                if(floatval(PHP_VERSION) < 7.2) {
                    $algo = PASSWORD_DEFAULT;
                }else {
                    $algo =  PASSWORD_ARGON2I;
                }
                $parseBody['password'] = password_hash($password, $algo);
                
            }
        }
        $request = $request->withParsedBody($parseBody);
        return $ajaxFormService->putProcess($request, $tablegateway);
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
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new UsersTableGateway($this->adapter);
        $parseBody = $request->getParsedBody();
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $admin = $session->get('admin');
        $parseBody['parent_id'] = $admin['id'];
        $parseBody['depth'] = intval($admin['depth'])+1;
        if($parseBody['password'] == $parseBody['password_confirm']) {
            unset($parseBody['password_confirm']);
        }
        $salt = Rand::getString(8);
        $parseBody['salt'] = $salt;
        $password = $parseBody['password'].$salt;
        if(floatval(PHP_VERSION) < 7.2) {
            $algo = PASSWORD_DEFAULT;
        }else {
            $algo =  PASSWORD_ARGON2I;
        }
        $parseBody['password'] = password_hash($password, $algo);
        $request = $request->withParsedBody($parseBody);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
    
}
