<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecGroupAttrsTableGateway;

class ProductsSpecGroupAttrsAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new ProductsSpecGroupAttrsTableGateway($this->adapter));
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/productsSpecGroupAttrs.php', [
                'name' => 'products_spec_group_attrs',
                'extra_name' => 'products_spec_group_attrs',
                'display_name' => 'language_has_locale',
                'created_at' => 'products_spec_group_attrs'
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
        return $ajaxFormService->deleteProcess($request, new ProductsSpecGroupAttrsTableGateway($this->adapter));
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecGroupAttrsTableGateway($this->adapter);
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
        if(isset($queryParams['put'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecGroupAttrsTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
}
