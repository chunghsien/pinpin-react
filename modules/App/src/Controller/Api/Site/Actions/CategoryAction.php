<?php
declare(strict_types = 1);

namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecAttrsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupAttrsTableGateway;

class CategoryAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $vars = $this->getStandByVars($request);
        $query = $request->getQueryParams();
        if (isset($query['paths']) && empty($query['slug'])) {
            ApiSuccessResponse::$is_json_numeric_check = false;
        }
        return new ApiSuccessResponse(0, $vars);
    }

    public function getStandByVars(ServerRequestInterface $request)
    {
        $query = $request->getQueryParams();
        $npClassTableGateway = new NpClassTableGateway($this->adapter);
        $vars = [];
        if (isset($query['paths']) && empty($query['slug'])) {
            $vars["paths"] = array_merge($npClassTableGateway->getType(), $npClassTableGateway->getIds($request));
            return $vars;
        }
        if (isset($query['paths']) && isset($query['slug'])) {
            $method_or_id = $request->getAttribute('method_or_id');
            if ($method_or_id == 'item_detail') {} else {
                $vars['np_class'] = $npClassTableGateway->getCategory($request);
                $productsTableGateway = new ProductsTableGateway($this->adapter);
                $vars = array_merge($vars, $productsTableGateway->getPaginator($request));

                // ex.color
                $productsSpecAttrsTableGateway = new ProductsSpecAttrsTableGateway($this->adapter);
                $vars['product_spec'] = $productsSpecAttrsTableGateway->getAll($request);
                //ex. size
                $productsSpecGroupAttrsTableGateway = new ProductsSpecGroupAttrsTableGateway($this->adapter);
                $vars['product_spec_group'] = $productsSpecGroupAttrsTableGateway->getAll($request);
            }
        }
        $vars = array_merge(
            $vars,
            $this->getCommonVars($request)
        );
        return $vars;
    }
}
