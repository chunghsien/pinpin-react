<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Documents\TableGateway\DocumentsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Chopin\Documents\TableGateway\DocumentsContentTableGateway;
use Chopin\Documents\TableGateway\BannerTableGateway;
use Chopin\Documents\TableGateway\SeoTableGateway;
use Chopin\Documents\TableGateway\FacebookTagsTableGateway;

class DocumentsAction extends AbstractAction
{


    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $tableGateway = new DocumentsTableGateway($this->adapter);
        $method_or_id = $request->getAttribute('method_or_id', null);
        
        if($method_or_id && is_int(intval($method_or_id))) {
            $query = $request->getQueryParams();
            if(isset($query['getType'])) {
                $select = $tableGateway->getSql()->select();
                $select->where(['id' => $method_or_id])->columns(['id', 'type']);
                $resultSet = $tableGateway->selectWith($select)->current();
                return new ApiSuccessResponse(0, $resultSet->toArray());
            }
        }
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, $tableGateway);
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/documents.php', 
                // 欄位對應的資料表名稱
                [
                    'name' => 'documents',
                    'routes' => 'documents',
                    'display_name' => 'language_has_locale',
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
        $contents = json_decode($request->getBody()->getContents());
        foreach ($contents as $item) {
            if ($item->type == 1) {
                return new ApiErrorResponse(0, [], [
                    "此項目 ({$item->name})  禁止刪除"
                ]);
            }
        }
        exit();
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new DocumentsTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new DocumentsTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $tablegateway);
        if ($response instanceof ApiSuccessResponse) {
            $payload = json_decode($response->getBody()->getContents(), true);
            $data = $payload['data'];
            $id = $payload['data']['id'];
            $row = $tablegateway->select([
                'id' => $id
            ])->current();
            $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
            $languageHasLocaleItem = $languageHasLocaleTableGateway->select(json_decode($data['language_has_locale'], true))->current();
            $code = str_replace('_', '-', $languageHasLocaleItem->code);
            $type="";
            if($row->type == 2) {
                $type="other";
                $route = "/{$code}/{$type}/".$row->id;
                $route = preg_replace('/\/+/', '/', $route);
                $row->route = $route;
                $row->save();
                $data['route'] = $route;
                $payload['data'] = $data;
                $response = $response->withPayload($payload);
            }
            
        }
        return $response;
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
        $post = $request->getParsedBody();
        if(empty($post['allowed_methods'])){
            $post['allowed_methods'] = json_encode(["GET"]);
        }
        $request = $request->withParsedBody($post);
        
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new DocumentsTableGateway($this->adapter);
        $response = $ajaxFormService->postProcess($request, $tablegateway);
        $id = $tablegateway->getLastInsertValue();
        $row = $tablegateway->select(['id' => $id])->current();
        if($row->type == 2) {
            $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
            $languageHasLocaleRow = $languageHasLocaleTableGateway->select([
                'language_id' => $row->language_id,
                'locale_id' => $row->locale_id,
            ])->current();
            $locale = preg_replace('/_/', '', $languageHasLocaleRow->code);
            $route = "/{$locale}/other/${id}";
            $row->route = $route;
            $row->save();
        }
        return $response;
    }
}
