<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\Documents\TableGateway\LayoutZonesTableGateway;
use Chopin\Documents\TableGateway\LayoutZonesHasDocumentsTableGateway;
use Chopin\Documents\TableGateway\DocumentsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\HttpMessage\Response\ApiWarningResponse;

class LayoutZonesHasDocumentsAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $documentsTableGateway = new DocumentsTableGateway($this->adapter);
        $layoutZonesTableGateway = new LayoutZonesTableGateway($this->adapter);
        $layoutZonesHasDocumentsTableGateway = new LayoutZonesHasDocumentsTableGateway($this->adapter);
        // $query = $request->getQueryParams();
        $layout_zones_id = $request->getAttribute('method_or_id');
        $layoutZonesRow = $layoutZonesTableGateway->select([
            'id' => $layout_zones_id
        ])->current();

        $documentsSelect = $documentsTableGateway->getSql()->select();
        $documentsSelect->columns([
            'documents_id' => 'id',
            'name',
            'route'
        ]);
        $documentsSelect->where([
            'language_id' => $layoutZonesRow->language_id,
            'locale_id' => $layoutZonesRow->locale_id,
        ]);
        $dataSource = $documentsTableGateway->getSql()
            ->prepareStatementForSqlObject($documentsSelect)
            ->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        $resultSet = $resultSet->toArray();
        $documents_ids = [];
        foreach ($resultSet as $k => $r) {
            $documents_ids[] = $r['documents_id'];
            $resultSet[$k]['layout_zones_id'] = null;
        }
        $layoutZonesHasDocumentsResultSet = $layoutZonesHasDocumentsTableGateway->select([
            'documents_id' => $documents_ids
        ])->toArray();
        $layoutZonesHasDocumentsColumns = $layoutZonesHasDocumentsTableGateway->getColumns();
        $layoutZonesHasDocumentsEmpty = [];
        foreach ($layoutZonesHasDocumentsColumns as $col) {
            $layoutZonesHasDocumentsEmpty[$col] = null;
        }
        if ($layoutZonesHasDocumentsResultSet) {
            foreach ($layoutZonesHasDocumentsResultSet as $l) {
                //debug($l);
                foreach ($resultSet as $k => $d) {
                    if ($l['documents_id'] == $d['documents_id']) {
                        if ($l['layout_zones_id'] == $layout_zones_id) {
                            $tmp = array_merge($d, $l);
                            $resultSet[$k] = $tmp;
                        }else {
                            unset($resultSet[$k]);
                        }
                    } else {
                        $tmp = array_merge($layoutZonesHasDocumentsEmpty, $resultSet[$k]);
                        $resultSet[$k] = $tmp;
                        // $resultSet[$k]['layout_zones_id'] = $layout_zones_id;
                    }
                }
            }
        } else {
            foreach ($resultSet as $k => $d) {
                $tmp = array_merge($layoutZonesHasDocumentsEmpty, $resultSet[$k]);
                $resultSet[$k] = $tmp;
                // $resultSet[$k]['layout_zones_id'] = $layout_zones_id;
            }
        }
        $resultSet = array_values($resultSet);
        return new ApiSuccessResponse(0, [
            'layout_name' => $layoutZonesRow->name,
            'layouts' => $resultSet
        ]);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $layoutZonesHasDocumentsTableGateway = new LayoutZonesHasDocumentsTableGateway($this->adapter);
        $post = json_decode($request->getBody()->getContents())->params;
        $layout_zones_id = intval($request->getAttribute('method_or_id'));
        $isSaved = 0;
        $documents_id = intval($post->documents_id);
        $where = [
            'documents_id' => $documents_id
        ];
        if(!$post->alias)
        {
            $post->alias = '';
        }
        
        $notify = "";
        if (! $post->layout_zones_id) {
            // delete
            if ($layoutZonesHasDocumentsTableGateway->select($where)->count()) {
                $isSaved = $layoutZonesHasDocumentsTableGateway->delete($where);
                $notify = "delete success";
                // $isSaved = true;
            }
        } else {
            // inser or update
            $where['layout_zones_id'] = $layout_zones_id;
            if ($layoutZonesHasDocumentsTableGateway->select($where)->count() >= 1) {
                // update
                $set = [
                    "alias" => $post->alias,
                    "is_show_childs" => $post->is_show_childs == null ? 0 : intval($post->is_show_childs),
                ];
                $isSaved = $layoutZonesHasDocumentsTableGateway->update($set, $where);
                $notify = "update success";
            } else {
                // insert
                $set = [
                    "layout_zones_id" => $layout_zones_id,
                    "documents_id" => $documents_id,
                ];
                if($post->alias) {
                    $set['alias'] = $post->alias;
                }
                if($post->is_show_childs) {
                    $set['is_show_childs'] = $post->is_show_childs;
                }
                $isSaved = $layoutZonesHasDocumentsTableGateway->insert($set);
                $notify = "add success";
            }
        }
        /**
         *
         * @var \Psr\Http\Message\ResponseInterface $response
         */
        $response = $this->get($request);
        $payload = json_decode($response->getBody()->getContents());
        $data = $payload->data;
        $code = $isSaved > 0 ? 0 : - 1;
        if ($code < 0) {
            $notify = "save faile";
        }
        if($code < 0) {
            return new ApiWarningResponse($code, $data, [$notify]);
        }
        // $notify = $isSaved > 0 ? "update fail" : "";
        return new ApiSuccessResponse($code, $data, [$notify]);
    }
}
