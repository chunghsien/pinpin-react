<?php

declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Documents\TableGateway\LayoutZonesTableGateway;

class DocumentsFooterAction extends AbstractAction
{

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        $response = $ajaxFormService->getProcess($request, $layoutZonesTableGateway);
        if($response->getStatusCode() == 200) {
            return $response;
        }else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator(
                $request,
                'modules/App/scripts/db/admin/documentsFooter.php',
                //欄位對應的資料表名稱
                [
                    'name' => $layoutZonesTableGateway->getTailTableName(),
                    'display_name' => 'language_has_locale',
                    'created_at' => $layoutZonesTableGateway->getTailTableName(),
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
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        return $ajaxFormService->deleteProcess($request, $layoutZonesTableGateway);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $layoutZonesTableGateway);
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
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $layoutZonesTableGateway);
    }
    
    
}
