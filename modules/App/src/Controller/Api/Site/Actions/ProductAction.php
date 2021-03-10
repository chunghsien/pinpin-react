<?php
declare(strict_types = 1);

namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\ProductsTableGateway;

class ProductAction extends AbstractAction
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
        return new ApiSuccessResponse(0, $vars);
    }
    
    public function getStandByVars(ServerRequestInterface $request)
    {
        $vars = [];
        $query = $request->getQueryParams();
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        
        if(isset($query['paths']) && empty($query['slug'])) {
            ApiSuccessResponse::$is_json_numeric_check = false;
            $select = $productsTableGateway->getSql()->select();
            $select->columns(['id']);
            $where = $select->where;
            $where->isNull('deleted_at');
            $where->equalTo('is_show', 1);
            $select->where($where);
            $resultSet = $productsTableGateway->selectWith($select);
            $vars['paths'] = [];
            foreach ($resultSet as $row) {
                $vars['paths'][] = [
                    'params' => ['id' => $row->id]
                ];
            }
        }
        if($id = $request->getAttribute('method_or_id', null)) {
            $where = $productsTableGateway->getSql()->select()->where;
            $where->isNull('deleted_at');
            $where->equalTo('id', intval($id));
            /**
             * @var \Chopin\Store\RowGateway\ProductsRowGateway $row
             */
            $row = $productsTableGateway->select($where)->current();
            $row->withAssets();
            $row->withNpClass(true);
            $row->withSpec();
            $row->withSpecGroup();
            $row->withItemSumStock($id);
            $vars['product'] = $row->toArray();
            $vars = array_merge($vars, $this->getCommonVars($request));
        }
        return $vars;
    }
}
