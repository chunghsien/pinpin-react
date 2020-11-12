<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\Documents\TableGateway\SeoTableGateway;
use App\Service\AjaxFormService;

class SeoAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new SeoTableGateway($this->adapter));
        return $response;
    }
    
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new SeoTableGateway($this->adapter);
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
        $tablegateway = new SeoTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
        
    }
}
