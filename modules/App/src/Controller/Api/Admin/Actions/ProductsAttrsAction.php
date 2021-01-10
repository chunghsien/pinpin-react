<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ApiQueryService;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\AttributesTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Laminas\Db\Sql\Select;

class ProductsAttrsAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    private function getLists($parent_id, AttributesTableGateway $tablegateway) {
        $parentRow = $tablegateway->select(['id' => $parent_id])->current();
        $select = new Select($tablegateway->table);
        $select->order(['sort ASC', 'id DESC'])->where(['parent_id' => $parent_id]);
        $data = $tablegateway->selectWith($select)->toArray();
        $form = [
            'table' => $parentRow->tablename,
            'parent_id' => $parentRow->id,
            'language_id' => $parentRow->language_id,
            'locale_id' => $parentRow->locale_id,
        ];
        return [
            'data' => $data,
            'form' => $form,
            'table' => $tablegateway->getTailTableName(),
        ];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $attribute = $request->getAttribute('method_or_id', '');
        $tablegateway = new AttributesTableGateway($this->adapter);
        if (preg_match('/\d+/', $attribute)) {
            $id = intval($attribute);
            $data = $tablegateway->select([
                'id' => $id
            ])->current()->toArray();
           
            if (isset($data['language_id']) && isset($data['locale_id'])) {
                $data['language_has_locale'] = json_encode([
                    'language_id' => $data['language_id'],
                    'locale_id' => $data['locale_id'],
                ]);
            }
            return new ApiSuccessResponse(0, $data);
        }
        
        $queryParams = $request->getQueryParams();
        if(isset($queryParams['parent_id'])) {
            $parent_id = intval($queryParams['parent_id']);
            $vars = $this->getLists($parent_id, $tablegateway);
            return new ApiSuccessResponse(0, $vars);
        }else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/productsAttrs.php', [
                'name' => 'attributes',
                'value' => 'attributes',
                'sort' => 'attributes',
                'display_name' => 'language_has_locale',
                'created_at' => 'attributes',
            ]);
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $postObj = json_decode($request->getBody()->getContents());
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new AttributesTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $tablegateway);
        if($postObj) {
            return $response;
        }
        $payload = $response->getPayload();
        $parent_id = intval($payload['data']['parent_id']);
        if($parent_id == 0) {
            return $response;
        }
        $vars = $this->getLists($parent_id, $tablegateway);
        return new ApiSuccessResponse(0, $vars, ['update success']);
    }


    /**
     *
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        //$queryParams = $request->getQueryParams();
        $post = $request->getParsedBody();
        if(isset($post['id'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new AttributesTableGateway($this->adapter);
        $response = $ajaxFormService->postProcess($request, $tablegateway);
        $payload = $response->getPayload();
        $parent_id = intval($payload['data']['parent_id']);
        if($parent_id == 0) {
            return $response;
        }
        $vars = $this->getLists($parent_id, $tablegateway);
        return new ApiSuccessResponse(0, $vars, ['add success']);
        
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        //預設給form
        $id = intval($request->getAttribute('method_or_id', 0));
        if(!$id) {
            //列表使用
            $post = json_decode($request->getBody()->getContents())[0];
            $id = intval($post->id);
        }
        $tablegateway =  new AttributesTableGateway($this->adapter);
        $row = $tablegateway->select(['id' => $id])->current()->toArray();
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->deleteProcess($request, new $tablegateway($this->adapter));
        if($row['parent_id'] == 0) {
            return $response;
        }
        $parent_id = $row['parent_id'];
        $tablegateway->softDelete(['id' => $id]);
        $vars = $this->getLists($parent_id, $tablegateway);
        return new ApiSuccessResponse(0, $vars, ['delete success']);
        
    }
}
