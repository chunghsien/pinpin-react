<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupAttrsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Chopin\Store\TableGateway\ProductsSpecAttrsTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;

class ProductsSpecGroupAction extends AbstractAction
{

    protected function getOptions(ServerRequestInterface $request, ProductsSpecGroupTableGateway $productsSpecGroupTableGateway)
    {
        $query = $request->getQueryParams();
        $products_id = intval($query['products_id']);
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $productsSpecGroupAttrsTableGateway = new ProductsSpecGroupAttrsTableGateway($this->adapter);
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->adapter);
        $productsSpecAttrsTableGateway = new ProductsSpecAttrsTableGateway($this->adapter);

        $specAttrsSelect = $productsSpecAttrsTableGateway->getSql()->select();
        $specAttrsWhere = $productsSpecAttrsTableGateway->getSql()->select()->where;
        $specAttrsWhere->isNull("deleted_at");
        $specAttrsSelect->where($specAttrsWhere);
        $specAttrsSelect->order("id ASC");
        $specAttrsDataSource = $productsSpecAttrsTableGateway->getSql()
            ->prepareStatementForSqlObject($specAttrsSelect)
            ->execute();
        $specAttrsResultSet = new ResultSet();
        $specAttrsResultSet->initialize($specAttrsDataSource);
        $specAttrsOptions = [];
        foreach ($specAttrsResultSet as $item) {
            $specAttrsOptions[] = [
                "value" => $item["id"],
                "label" => $item["name"],
                "extra_name" => $item["extra_name"],
                "triple_name" => $item["triple_name"],
            ];
        }
        
        
        $productWhere = $productsTableGateway->getSql()->select()->where;
        $productWhere->isNull('deleted_at');
        $productWhere->equalTo('id', $products_id);
        $productResultSet = $productsTableGateway->select($productWhere);
        if ($productResultSet->count()) {

            $productRow = $productResultSet->current();
            $options = [];
            $lists = [];

            $mainSelect = $productsSpecGroupTableGateway->getSql()->select();
            $where = $mainSelect->where;
            $where->equalTo("{$productsSpecGroupTableGateway->table}.products_id", $products_id);
            $where->isNull($productsTableGateway->table . ".deleted_at");
            $where->isNull($productsSpecGroupAttrsTableGateway->table . ".deleted_at");
            $where->isNull($productsSpecGroupTableGateway->table . ".deleted_at");
            $mainSelect->order([
                $productsSpecGroupTableGateway->table . ".sort ASC",
                $productsSpecGroupAttrsTableGateway->table . ".id ASC"
            ]);
            $mainSelect->join($productsTableGateway->table, "{$productsTableGateway->table}.id={$productsSpecGroupTableGateway->table}.products_id", []);
            $mainSelect->join($productsSpecGroupAttrsTableGateway->table, "{$productsSpecGroupTableGateway->table}.products_spec_group_attrs_id={$productsSpecGroupAttrsTableGateway->table}.id", [
                "name",
                "extra_name",
                "image"
            ]);
            $mainSelect->where($where);
            $dataSource = $productsSpecGroupTableGateway->getSql()
                ->prepareStatementForSqlObject($mainSelect)
                ->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($dataSource);
            $lists = $resultSet->toArray();

            foreach ($lists as &$list) {
                $specsSlect = $productsSpecTableGateway->getSql()->select();
                $specsSlect->join($productsSpecAttrsTableGateway->table, "{$productsSpecTableGateway->table}.products_spec_attrs_id={$productsSpecAttrsTableGateway->table}.id", [
                    "name",
                    "extra_name",
                    "triple_name"
                ]);
                $specsWhere = $specsSlect->where;
                $specsWhere->equalTo("{$productsSpecTableGateway->table}.products_spec_group_id", $list['id']);
                $specsWhere->isNull("{$productsSpecTableGateway->table}.deleted_at");
                $specsWhere->isNull("{$productsSpecAttrsTableGateway->table}.deleted_at");
                $specsSlect->where($specsWhere);
                $specsSlect->order("{$productsSpecAttrsTableGateway->table}.id ASC");
                $dataSource = $productsSpecTableGateway->getSql()
                    ->prepareStatementForSqlObject($specsSlect)
                    ->execute();
                $resultSet = new ResultSet();
                $resultSet->initialize($dataSource);

                $list['specs'] = $resultSet->toArray();
            }
            $productsSpecGroupAttrsWhere = $productsSpecGroupAttrsTableGateway->getSql()->select()->where;
            $productsSpecGroupAttrsWhere->isNull('deleted_at');
            $productsSpecGroupAttrsResult = $productsSpecGroupAttrsTableGateway->select($productsSpecGroupAttrsWhere);
            foreach ($productsSpecGroupAttrsResult as $row) {
                $options[] = [
                    "value" => $row->id,
                    "label" => $row->name,
                    "extra_name" => $row->extra_name
                ];
            }
            return [
                "language_id" => $productRow->language_id,
                "locale_id" => $productRow->locale_id,
                "options" => [
                    "products_spec_group" => $options,
                    "products_spec" => $specAttrsOptions,
                ],
                "lists" => $lists,
            ];
        }
        return [
            "options" => [],
            "lists" => [],
            "language_id" => "",
            "locale_id" => "",
        ];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->adapter);
        $query = $request->getQueryParams();
        if (isset($query['products_id'])) {
            $data = $this->getOptions($request, $productsSpecGroupTableGateway);
            return new ApiSuccessResponse(0, $data);
        }
        parent::get($request);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new ProductsSpecGroupTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecGroupTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $tablegateway);
        return $this->getOptionsResponse($response, new ProductsTableGateway($this->adapter));
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
        $tablegateway = new ProductsSpecGroupTableGateway($this->adapter);
        $where = json_decode($request->getBody()->getContents(), true);
        if($tablegateway->select($where)->count() > 0) {
            ApiErrorResponse::$status = 200;
            return new ApiErrorResponse(-1, [], ["products-spec-group-exists"]);
        }
        
        //$tablegateway->select([]);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
}
