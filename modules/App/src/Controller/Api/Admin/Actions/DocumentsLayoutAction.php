<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Documents\TableGateway\LayoutZonesTableGateway;
use Chopin\Documents\TableGateway\DocumentsTableGateway;
use Laminas\Db\RowGateway\RowGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\Db\Sql\Where;

class DocumentsLayoutAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        $documentsTableGateway = new DocumentsTableGateway($this->adapter);

        $query = $request->getQueryParams();
        if (isset($query['parent_id']) && isset($query['is_children'])) {
            $parent_id = $query['parent_id'];
            $row = $layoutZonesTableGateway->select([
                'id' => $parent_id
            ])->current();
            $documtnts = $documentsTableGateway->getLayoutUse($row);
            $resultSet = $layoutZonesTableGateway->getChildren($parent_id);
            return new ApiSuccessResponse(0, [
                'children' => $resultSet,
                'documents_link' => $documtnts
            ]);
        }

        $response = $ajaxFormService->getProcess($request, $layoutZonesTableGateway);
        if ($response->getStatusCode() == 200) {
            $contents = json_decode($response->getBody()->getContents(), true);
            $data = $contents['data'];
            $layoutRow = new RowGateway('id', $layoutZonesTableGateway->table, $this->adapter);
            $layoutRow->populate($data);
            $documtnts = $documentsTableGateway->getLayoutUse($layoutRow);
            $data['docuemnts_link'] = $documtnts;
            $parent_id = $request->getAttribute('method_or_id');

            $where = $layoutZonesTableGateway->getSql()->select()->where;
            $where->equalTo('parent_id', $parent_id)->isNull('deleted_at');
            $select = $layoutZonesTableGateway->getSql()->select();
            $select->order([
                'sort asc',
                'id asc'
            ]);
            $select->where($where);
            $data['list'] = $layoutZonesTableGateway->selectWith($select)->toArray();
            return new ApiSuccessResponse(0, $data);
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/documentsLayout.php', 
                // 欄位對應的資料表名稱
                [
                    'name' => $layoutZonesTableGateway->getTailTableName(),
                    'display_name' => 'language_has_locale',
                    'created_at' => $layoutZonesTableGateway->getTailTableName()
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
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);

        $id = $request->getAttribute('method_or_id');
        if ($id) {
            try {
                $row = $layoutZonesTableGateway->select([
                    'id' => $id
                ])->current();
                if ($row) {
                    $row->deleted_at = date("Y-m-d H:i:s");
                    $row->save();
                    $resultSet = $layoutZonesTableGateway->getChildren($row->parent_id);
                    return new ApiSuccessResponse(0, [
                        'list' => $resultSet
                    ], [
                        'delete success'
                    ]);
                    // $layoutZonesTableGateway->softDelete(['id' => $id]);
                }
                return new ApiErrorResponse(1, []);
            } catch (\Exception $e) {
                loggerException($e);
                return new ApiErrorResponse(417, [
                    'trace' => $e->getTrace()
                ], [
                    $e->getMessage()
                ]);
            }
        }
        return $ajaxFormService->deleteProcess($request, $layoutZonesTableGateway);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        //$contents = json_decode($request->getBody()->getContents(), true);
        $ajaxFormService = new AjaxFormService();
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $layoutZonesTableGateway);
        $responseContents = json_decode($response->getBody()->getContents(), true);
        $responseData = $responseContents['data'];
        if (empty($responseContents['data']) || empty($responseData['parent_id'])) {
            return $response;
        } else {
            $responseData = $responseContents['data'];
            $responseMessage = $responseContents['message'];
            $parent_id = $responseData['parent_id'];
            $select = $layoutZonesTableGateway->getSql()->select();
            $where = $select->where;
            $where->isNull('deleted_at');
            $where->equalTo('parent_id', $parent_id);
            $select->order([
                'sort ASC',
                'id ASC'
            ]);
            $select->where($where);
            // $list = $resultSet = $layoutZonesTableGateway->selectWith($select);
            $responseData = [
                'list' => $layoutZonesTableGateway->selectWith($select)
            ];
            return new ApiSuccessResponse(0, $responseData, $responseMessage);
        }
        // $contents = json_decode($response->getBody()->getContents(), true);
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
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        $response = $ajaxFormService->postProcess($request, $layoutZonesTableGateway);
        $contents = json_decode($response->getBody()->getContents(), true);
        $data = $contents['data'];
        $parentRow = $layoutZonesTableGateway->select([
            'id' => $data['parent_id']
        ])->current();

        $listWhere = new Where();
        $listWhere->equalTo('parent_id', $parentRow->id)->isNull('deleted_at');
        $list = $layoutZonesTableGateway->select($listWhere)->toArray();

        $row = $layoutZonesTableGateway->select([
            'id' => $data['id']
        ])->current();
        $row->locale_id = $parentRow->locale_id;
        $row->language_id = $parentRow->language_id;
        $row->save();
        if (empty($queryParams['is_children'])) {
            $newContent = [
                'result' => $row->toArray(),
                'list' => $list
            ];
        } else {
            $documentsTableGateway = new DocumentsTableGateway($this->adapter);
            $documtnts = $documentsTableGateway->getLayoutUse($parentRow, []);
            $newContent = [
                'children' => $list,
                'documents_link' => $documtnts
            ];
        }
        return new ApiSuccessResponse(0, $newContent, [
            'add success'
        ]);
    }
}
