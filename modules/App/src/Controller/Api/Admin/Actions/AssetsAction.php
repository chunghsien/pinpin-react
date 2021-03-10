<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Intervention\Image\ImageManagerStatic;
use Laminas\Db\Sql\Select;

class AssetsAction extends AbstractAction
{

    private function getLists($table, $table_id, AssetsTableGateway $tablegateway) {
        $select = new Select($tablegateway->table);
        $select->order([
            'sort ASC'
        ])->where([
            'table' => $table,
            'table_id' => $table_id,
        ]);
        return $tablegateway->selectWith($select);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $assetsTableGateway = new AssetsTableGateway($this->adapter);
        $queryParams = $request->getQueryParams();
        $table = $queryParams['table'];
        $table_id = $queryParams['table_id'];
        $result = $this->getLists($table, $table_id, $assetsTableGateway);
        return new ApiSuccessResponse(0, $result->toArray());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = intval($request->getAttribute('method_or_id'));
        if($id) {
            
            $tablegateway = new AssetsTableGateway($this->adapter);
            $row = $tablegateway->select(['id' => $id])->current()->toArray();
            
            if($tablegateway->softDelete(['id' => $id])) {
                $result = $this->getLists($row['table'], $row['table_id'], $tablegateway);
                return new ApiSuccessResponse(0, $result->toArray(), ['delete success']);
            }
        }
        return new ApiErrorResponse(1, [], 'oh oh');
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $connection = $this->adapter->getDriver()->getConnection();
        try {
            $connection->beginTransaction();
            $params = $request->getParsedBody();
            $id = intval($params['id']);
            $tablegateway = new AssetsTableGateway($this->adapter);
            $row = $tablegateway->select([
                'id' => $id
            ])->current()->toArray();
            $ajaxFormService = new AjaxFormService();
            $response = $ajaxFormService->putProcess($request, $tablegateway);
            if ($response instanceof ApiSuccessResponse) {
                $result = $this->getLists($row['table'], $row['table_id'], $tablegateway);
                $connection->commit();
                return new ApiSuccessResponse(0, $result->toArray(), [
                    'update success'
                ]);
            } else {
                return $response;
            }
        } catch (\Exception $e) {
            loggerException($e);
            $connection->rollback();
            return new ApiErrorResponse(1, [], [
                $e->getMessage()
            ]);
        }
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
        $tablegateway = new AssetsTableGateway($this->adapter);
        $post = $request->getParsedBody();
        $table = $post['table'];
        $table_id = $post['table_id'];
        $relationTableGateway = AbstractTableGateway::newInstance($post['table'], $this->adapter);
        $relationRow = $relationTableGateway->select()->current();
        $post['language_id'] = $relationRow->language_id;
        $post['locale_id'] = $relationRow->locale_id;
        $request = $request->withParsedBody($post);

        $count = $tablegateway->select([
            'table' => $table,
            'table_id' => $table_id
        ])->count();
        if ($count >= 7) {
            // Too many files, maximum '%max%' are allowed but '%count%' are given
            return new ApiErrorResponse(1, [
                'count' => $count
            ], [
                "Too many files, maximum '%max%' are allowed but '%count%' are given"
            ]);
        }
        // $ajaxFormService->
        $ajaxFormService->processUpload($request, $tablegateway);
        $uploaded = $ajaxFormService->getUploaded();
        $post = $request->getParsedBody();
        $post = array_merge($post, $uploaded);
        $path = $uploaded['path'];
        $image = ImageManagerStatic::make('./public'.$path);
        $post['mime'] = $image->mime();
        $post = $tablegateway->setFilter($post);
        $tablegateway->insert($post);
        $result = $this->getLists($table, $table_id, $tablegateway);
        return new ApiSuccessResponse(0, $result->toArray(), ['add success']);
    }
    
    
}
