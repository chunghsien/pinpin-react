<?php

declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\Documents\TableGateway\DocumentsContentTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;

class DocumentsContentAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $tablegateway = new DocumentsContentTableGateway($this->adapter);
        $queryParams = $request->getQueryParams();        
        $where = $tablegateway->getSql()->select()->where;
        $where->equalTo('documents_id', $queryParams['table_id']);
        $resultSet = $tablegateway->select($where);
        $row = [];
        if($resultSet->count() == 1) {
            $row = $resultSet->current()->toArray();
        }
        return new ApiSuccessResponse(0, $row);
    }

    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new DocumentsContentTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
        
    }

    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $post = $request->getParsedBody();
        if(isset($queryParams['put']) || isset($post['id'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new DocumentsContentTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
        
    }
}
