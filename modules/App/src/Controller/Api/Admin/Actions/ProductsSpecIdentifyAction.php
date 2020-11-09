<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecIdentifyTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;

class ProductsSpecIdentifyAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $productsSpecIdentifyTableGateway = new ProductsSpecIdentifyTableGateway($this->adapter);
        $query = $request->getQueryParams();
        $table_id = $query['table_id'];
        $productsSpecIdentifyResult = $productsSpecIdentifyTableGateway->select(['products_spec_id' => $table_id]);
        $data = [];
        if($productsSpecIdentifyResult->count()) {
            $data = $productsSpecIdentifyResult->current()->toArray();
        }
        return new ApiSuccessResponse(200, $data);
        
    }

    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecIdentifyTableGateway($this->adapter);
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
        $tablegateway = new ProductsSpecIdentifyTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
        
    }
}
