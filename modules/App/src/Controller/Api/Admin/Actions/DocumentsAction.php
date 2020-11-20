<?php

declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Newsletter\TableGateway\NewsTableGateway;
use Chopin\Documents\TableGateway\DocumentsTableGateway;

class DocumentsAction extends AbstractAction
{

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $query = $request->getQueryParams();
        if(isset($query['table']) && isset($query['table_id'])) {
            if($query['table'] == 'undefined') {
                $id = intval($query['table_id']);
                $query['method_or_id'] = $id;
                unset($query['table']);
                unset($query['table_id']);
                $request = $request->withAttribute('method_or_id', $id);
                //$response = $ajaxFormService->getProcess($request, new DocumentsTableGateway($this->adapter));
            }
        }
        $response = $ajaxFormService->getProcess($request, new DocumentsTableGateway($this->adapter));
        if($response->getStatusCode() == 200) {
            return $response;
        }else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator(
                $request,
                'modules/App/scripts/db/admin/documents.php',
                //欄位對應的資料表名稱
                [
                    'name' => 'documents',
                    'routes' => 'documents',
                    'display_name' => 'language_has_locale',
                ]
            );
        }
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new DocumentsTableGateway($this->adapter));
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new NewsTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if(isset($queryParams['put'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new DocumentsTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
    
}
