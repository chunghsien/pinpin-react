<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use App\Service\ApiQueryService;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;

class ProductsSpecAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $methodOrId = $request->getAttribute('method_or_id', null);
        if ($methodOrId) {
            $PT = AbstractTableGateway::$prefixTable;
            $productsSpecTableGateway = new ProductsSpecTableGateway($this->adapter);
            $productsSpecTableFullName = $productsSpecTableGateway->table;
            $select = $productsSpecTableGateway->getSql()->select();
            $where = $select->where;
            $productsTableFullName = "{$PT}products";
            $select->join(
                $productsTableFullName,
                "{$productsSpecTableFullName}.products_id={$productsTableFullName}.id",
                ["model"]
            );
            $where->isNull("{$productsTableFullName}.deleted_at");
            $productsSpecGroupTableFullName = "{$PT}products_spec_group";
            $productsSpecGroupAttrsTableFullName = "{$PT}products_spec_group_attrs";
            $select->join(
                $productsSpecGroupTableFullName,
                "{$productsSpecTableFullName}.products_spec_group_id={$productsSpecGroupTableFullName}.id",
                []
            );
            $where->isNull("{$productsSpecGroupTableFullName}.deleted_at");
            $select->join(
                $productsSpecGroupAttrsTableFullName,
                "{$productsSpecGroupTableFullName}.products_spec_group_attrs_id={$productsSpecGroupAttrsTableFullName}.id",
                ["group_name" => "name", "group_extra_name" => "extra_name"]
            );
            $where->isNull("{$productsSpecGroupAttrsTableFullName}.deleted_at");
            $productsSpecAttrsTableFullName = "{$PT}products_spec_attrs";
            $select->join(
                $productsSpecAttrsTableFullName,
                "{$productsSpecTableFullName}.products_spec_attrs_id={$productsSpecAttrsTableFullName}.id",
                ["language_id", "locale_id", "name", "extra_name", "triple_name"]
             );
            $where->isNull("{$productsSpecAttrsTableFullName}.deleted_at");
            $where->equalTo("{$productsSpecTableFullName}.id", $methodOrId);
            $select->where($where);
            $dataSource = $productsSpecTableGateway->getSql()->prepareStatementForSqlObject($select)->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($dataSource);
            $data = $resultSet->current();
            //LanguageHasLocaleTableGateway::$isRemoveRowGatewayFeature = true;
            $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
            $languageHasLocaleItem = $languageHasLocaleTableGateway->select([
                "language_id" => $data->language_id,
                "locale_id" => $data->locale_id,
            ])->current();
            $data->language_has_locale = $languageHasLocaleItem->code;
            return new ApiSuccessResponse(0, $data);
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/productsSpec.php', 
                // 欄位對應的資料表名稱
                [
                    'model' => 'products',
                    'name' => 'products_spec_attrs',
                    'group_name' => "products_spec_group_attrs",
                    'stock' => 'products_spec',
                    'stock_status' => 'products_spec',
                    'price' => 'products_spec',
                    'real_price' => 'products_spec',
                    'sort' => 'products_spec',
                    'created_at' => 'products_spec'
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
        return $ajaxFormService->deleteProcess($request, new ProductsSpecTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('method_or_id', null);
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->adapter);
        if ($id) {
            $set = json_decode($request->getBody()->getContents(), true);
            $updatedCount = $productsSpecTableGateway->update($set, [
                "id" => $id
            ]);
            if ($updatedCount == 0) {
                // no data updated
                ApiErrorResponse::$status = 200;
                return new ApiErrorResponse(- 1, [], [
                    'no data updated'
                ]);
            }
            return new ApiSuccessResponse(0, [], [
                'update success'
            ]);
        }else{
            $set = $request->getParsedBody();
            $id = $set["id"];
            unset($set["id"]);
            $where = ["id" => $id];
            $updatedCount = $productsSpecTableGateway->update($set, $where);
            $request = $request->withAttribute("method_or_id", $id);
            $response = $this->get($request);
            $message = ["update success"];
            $responseData = json_decode($response->getBody()->getContents(), true);
            if($updatedCount == 0) {
                ApiErrorResponse::$status = 200;
                return new ApiErrorResponse(1, $responseData['data'], ["no data updated"]);
            }else {
                return new ApiSuccessResponse(0, $responseData['data'], $message);
            }
            
            //return $ajaxFormService->putProcess($request, $productsSpecTableGateway);
        }
        return parent::put($request);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        if(isset($query['put'])) {
            return $this->put($request);
        }
        $tablegateway = new ProductsSpecTableGateway($this->adapter);
        $set = json_decode($request->getBody()->getContents(), true);
        if ($tablegateway->select($set)->count() == 0) {
            $ajaxFormService = new AjaxFormService();
            return $ajaxFormService->postProcess($request, $tablegateway);
        }
        ApiErrorResponse::$status = 200;
        return new ApiErrorResponse(-1, [], ["products-spec-exists"]);
    }
    
    
}
