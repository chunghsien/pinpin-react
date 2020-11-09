<?php

declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Newsletter\TableGateway\NewsTableGateway;

class NewsAction extends AbstractAction
{

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new NewsTableGateway($this->adapter));
        if($response->getStatusCode() == 200) {
            return $response;
        }else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator(
                $request,
                'modules/App/scripts/db/admin/news.php',
                //欄位對應的資料表名稱
                [
                    'title' => 'news',
                    'content' => 'news',
                    'display_name' => 'language_has_locale',
                    'publish' => 'news',
                    'created_at' => 'news',
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
        return $ajaxFormService->deleteProcess($request, new NewsTableGateway($this->adapter));
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
        $tablegateway = new NewsTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
    
}
