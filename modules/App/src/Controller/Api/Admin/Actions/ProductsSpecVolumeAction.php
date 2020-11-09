<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecVolumeTableGateway;
use Chopin\I18n\Units\Length;
use Chopin\I18n\Units\Weight;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\I18n\Units\Volume;

class ProductsSpecVolumeAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }
    
    private function getUnit()
    {
        $length = new Length();
        $weight = new Weight();
        $volume = new Volume();
        $vars = [
            'dimensions_unit' => $length->getShortNames(),
            'weight_unit' => $weight->getShortNames(),
            'volume_unit' => $volume->getShortNames(),
        ];
        return $vars;
    }
    
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        //$table = AbstractTableGateway::$prefixTable.'products_spec';
        $attribute = $request->getAttribute('method_or_id', null);
        if($attribute && strtolower($attribute) == 'getunit') {
            $vars = $this->getUnit();
            return new ApiSuccessResponse(200, $vars);
        }
        $query = $request->getQueryParams();
        $table_id = $query['table_id'];
        $productsSpecVolumeTableGateway = new ProductsSpecVolumeTableGateway($this->adapter);
        $productsSpecVolumeResult = $productsSpecVolumeTableGateway->select(['products_spec_id' => $table_id]);
        $data = [];
        if($productsSpecVolumeResult->count()) {
            $data = $productsSpecVolumeResult->current()->toArray();
        }
        return new ApiSuccessResponse(200, $data);
    }

    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecVolumeTableGateway($this->adapter);
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
        $tablegateway = new ProductsSpecVolumeTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
        
    }
}
