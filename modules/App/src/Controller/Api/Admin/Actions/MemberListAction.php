<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Users\TableGateway\MemberTableGateway;
use Laminas\Math\Rand;
use Laminas\Diactoros\Response\EmptyResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\HttpMessage\Response\ApiSuccessResponse;

class MemberListAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        ApiSuccessResponse::$is_json_numeric_check = false;
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new MemberTableGateway($this->adapter));
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/member_list.php', [
                'full_name' => 'member_decrypt',
                'cellphone' => 'member_decrypt',
                'email' => 'member_decrypt',
                'address' => 'member_decrypt',
                'created_at' => 'member_decrypt'
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
        return $ajaxFormService->deleteProcess($request, new MemberTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new MemberTableGateway($this->adapter);
        $parseBody = $request->getParsedBody();
        if (isset($parseBody['password']) && $parseBody['password'] == $parseBody['password_confirm']) {
            unset($parseBody['password_confirm']);
        }
        $parseBody = $tablegateway->enCryptData($parseBody);
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
        $tablegateway = new MemberTableGateway($this->adapter);
        $parseBody = $request->getParsedBody();

        // check is exists
        $emailCount = $tablegateway->getEmail($parseBody['email'])->count();
        $cellphoneCount = $tablegateway->getEmail($parseBody['cellphone'])->count();

        if ($emailCount + $cellphoneCount > 0) {
            ApiErrorResponse::$status = 200;
            return new ApiErrorResponse(-1, [], ["email or phone repeat"]);
        }

        if ($parseBody['password'] == $parseBody['password_confirm']) {
            unset($parseBody['password_confirm']);
        }
        $parseBody = $tablegateway->enCryptData($parseBody);
        $request = $request->withParsedBody($parseBody);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
}
