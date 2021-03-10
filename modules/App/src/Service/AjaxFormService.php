<?php
namespace App\Service;

use Psr\Http\Message\ServerRequestInterface;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\Db\Sql\Predicate\IsNull;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Sql;
use Laminas\Db\ResultSet\ResultSetInterface;
use Intervention\Image\ImageManagerStatic;
use Laminas\Db\Sql\Expression;

class AjaxFormService
{

    use UploadTrait;
    use GridPutTrait;

    /**
     *
     * @param ServerRequestInterface $request
     * @param AbstractTableGateway $tablegateway
     * @return \Chopin\HttpMessage\Response\ApiSuccessResponse|\Chopin\HttpMessage\Response\ApiErrorResponse|EmptyResponse
     */
    public function getProcess(ServerRequestInterface $request, AbstractTableGateway $tablegateway)
    {
        try {
            $queryParams = $request->getQueryParams();
            //id重複指向應以method_or_id為主
            if($request->getAttribute('method_or_id', null)) {
                if(isset($queryParams['table_id'])) {
                    unset($queryParams['table_id']);
                }
            }
            $colums = $tablegateway->getColumns();
            $releation_id = '';
            if (isset($queryParams['table']) && empty($queryParams['table_id'])) {
                $releation_id = $queryParams['table'] . '_id';
            }

            $resultSet = null;
            if (isset($queryParams['table']) && isset($queryParams['table_id']) && false !== array_search('table', $colums) && false !== array_search('table_id', $colums)) {
                $where = [
                    new Like('table', '%' . $queryParams['table']),
                    'table_id' => $queryParams['table_id']
                ];
                $resultSet = $tablegateway->select($where);
            } elseif (isset($colums[$releation_id])) {
                $resultSet = $tablegateway->select([
                    $releation_id => $queryParams['table_id']
                ]);
            }
            if (isset($queryParams['table']) && $queryParams['table'] == 'fp_class') {
                //debug($request->getRequestTarget());
                //debug($request->getAttribute('method_or_id', ''));
            }
            
            $id = $request->getAttribute('method_or_id', '');
            if ($id && empty($queryParams['table_id'])) {
                $resultSet = $tablegateway->select([
                    'id' => $id
                ])/*->current()*/;
            }
            if ($resultSet && $resultSet->count() > 1) {
                $data = $resultSet->toArray();
            }
            
            if ($resultSet && $resultSet->count() == 1) {
                $row = $resultSet->current();
                $data = $row instanceof RowGateway ? $row->toArray() : $row;
                if (isset($data['language_id']) && isset($data['locale_id'])) {
                    $data['language_has_locale'] = json_encode([
                        'locale_id' => $data['locale_id'],
                        'language_id' => $data['language_id']
                    ]);
                }
            }
            if ($resultSet && $resultSet->count() == 0) {
                $PT = AbstractTableGateway::$prefixTable;
                $data = [
                    'table' => $queryParams['table'],
                    'table_id' => $queryParams['table_id']
                ];
                if (false !== array_search('table', $colums) && false !== array_search('table_id', $colums)) {
                    $adapter = $tablegateway->adapter;
                    $sql = new Sql($adapter);
                    $select = $sql->select($PT . $queryParams['table'])->where([
                        'id' => $queryParams['table_id']
                    ]);
                    $result = $sql->prepareStatementForSqlObject($select)
                        ->execute()
                        ->current();
                    if (isset($result['language_id']) && isset($result['locale_id'])) {
                        $data['language_has_locale'] = json_encode([
                            'locale_id' => $result['locale_id'],
                            'language_id' => $result['language_id']
                        ]);
                    }
                }
            }
            
            if (isset($data)) {
                if (isset($tablegateway->defaultEncryptionColumns)) {
                    $data = $tablegateway->deCryptData($data);
                }
                return new ApiSuccessResponse(0, $data, []);
            }
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(417, [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
        }
        return new EmptyResponse();
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param AbstractTableGateway $tablegateway
     * @return \Chopin\HttpMessage\Response\ApiSuccessResponse|\Chopin\HttpMessage\Response\ApiErrorResponse
     */
    public function deleteProcess(ServerRequestInterface $request, AbstractTableGateway $tablegateway)
    {
        try {
            $data = json_decode($request->getBody()->getContents());
            $primarys = $tablegateway->getConstraintsObject('PRIMARY KEY')[0]->getColumns();
            $id = $request->getAttribute('method_or_id', null);
            $message = [];
            if($id && preg_match('/^\d+$/', $id) && count($primarys) == 1) {
                $column = $primarys[0];
                $tablegateway->delete([$column => $id]);
                $message[] = 'delete success';
            }else {
                foreach ($data as $row) {
                    $id = isset($row->id) ? $row->id : null;
                    $where = [];
                    if ($id) {
                        if (preg_match('/^\d+\-\d+$/', $id)) {
                            $ids = explode('-', $id);
                            foreach ($primarys as $key => $col) {
                                $where[$col] = $ids[$key];
                            }
                        } else {
                            $where['id'] = $id;
                        }
                        $tablegateway->softDelete($where);
                    } else {
                        foreach ($primarys as $key => $col) {
                            $where[$col] = $data->{$col};
                        }
                    }
                }
            }
            return new ApiSuccessResponse(0, [], $message);
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(417, [
                'trace' => $e->getTrace()
            ], [
                $e->getMessage()
            ]);
        }
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param AbstractTableGateway $tablegateway
     * @param string $result
     * @return \Chopin\HttpMessage\Response\ApiSuccessResponse|\Chopin\HttpMessage\Response\ApiErrorResponse
     */
    public function postProcess(ServerRequestInterface $request, AbstractTableGateway $tablegateway)
    {
        $post = $request->getParsedBody();
        if (! $post) {
            $post = json_decode($request->getBody()->getContents(), true);
        }
        try {
            // $connection->beginTransaction();
            if (isset($post['language_has_locale'])) {
                $language_has_locale = json_decode($post['language_has_locale']);
                unset($post['language_has_locale']);
                $post['language_id'] = $language_has_locale->language_id;
                $post['locale_id'] = $language_has_locale->locale_id;
            }
            $where = $post;
            if (isset($where['sort'])) {
                unset($where['sort']);
            }
            if (isset($post['sort']) && $post['sort'] = '' || ! is_int($post)) {
                unset($post['sort']);
            }
            $columns = $tablegateway->getColumns();
            if (array_search('deleted_at', $columns)) {
                $where[] = new IsNull('deleted_at');
            }

            if (false === array_search('table', $columns) && false === array_search('table_id', $columns)) {
                if (isset($post['table']) && isset($post['table_id'])) {
                    $releationCol = $post['table'] . '_id';
                    if (false !== array_search($releationCol, $columns)) {
                        $releation_id = $post['table_id'];
                        unset($post['table_id']);
                        unset($post['table']);
                        $post[$releationCol] = $releation_id;
                    }
                }
            }
            // if ($tablegateway->select($where)->count() === 0) {
            if ($this->verifyUpload($request)) {
                // 檔案上傳 begin
                $this->processUpload($request, $tablegateway);
                $errorResponse = $this->getUploadResponse();
                if ($errorResponse && $errorResponse instanceof ApiErrorResponse) {
                    return $errorResponse;
                }
                $uploaded = $this->getUploaded();
                if ($uploaded) {
                    $post = array_merge($post, $uploaded);
                }
                if (false !== array_search('mime', $columns)) {
                    $path = './public' . implode('', $uploaded);
                    $image = ImageManagerStatic::make($path);
                    $post['mime'] = $image->mime;
                    $image->destroy();
                }
                // 檔案上傳 end
            }

            // checkbox fixed
            foreach ($columns as $column) {
                if (preg_match('/^is_/', $column) && empty($post[$column])) {
                    $post[$column] = 0;
                }
            }
            $tablegateway->insert($post);
            $lastid = $tablegateway->getLastInsertValue();
            if ($lastid) {
                $row = $tablegateway->select([
                    'id' => $lastid
                ])->current();
            } else {
                $primary = $tablegateway->getConstraintsObject('PRIMARY KEY')[0]->getColumns();
                $where = [];
                foreach ($primary as $column) {
                    if (isset($post[$column])) {
                        $where[$column] = $post[$column];
                    }
                }
                $row = $tablegateway->select($where)->current();
            }
            if ($row instanceof RowGateway) {
                $row = $row->toArray();
            } else {
                if ($row instanceof ResultSetInterface) {
                    $row = $row->toArray();
                } else {
                    $row = (array) $row;
                }
            }
            if (isset($tablegateway->defaultEncryptionColumns)) {
                $row = $tablegateway->deCryptData($row);
            }
            if (isset($row['language_id']) && isset($row['locale_id'])) {
                $row['language_has_locale'] = [
                    'locale_id' => $row['locale_id'],
                    'language_id' => $row['language_id']
                ];
                ApiSuccessResponse::$is_json_numeric_check = false;
            }
            return new ApiSuccessResponse(0, $row, ['add success']);
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(1, isset($post) ? $post : [], [
                'message' => $e->getMessage()
            ], [
                $e->getMessage()
            ]);
        }
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param AbstractTableGateway $tablegateway
     * @return \Chopin\HttpMessage\Response\ApiSuccessResponse|\Chopin\HttpMessage\Response\ApiErrorResponse|\Chopin\HttpMessage\Response\ApiErrorResponse|\Laminas\Diactoros\Response\JsonResponse|\Chopin\HttpMessage\Response\ApiSuccessResponse|\Chopin\HttpMessage\Response\ApiErrorResponse
     */
    public function putProcess(ServerRequestInterface $request, AbstractTableGateway $tablegateway)
    {
        $set = $request->getParsedBody();
        if (! $set) {
            $tmp = $request->getBody()->getContents();
            $set = json_decode($tmp, true);
        }
        try {
            if ($this->gridPutVerify($request)) {
                return $this->gridPutProcess($request, $tablegateway);
            } else {
                $where = [];
                if (isset($set['id'])) {
                    $id = $set['id'];
                    unset($set['id']);
                    $where['id'] = $id;
                } else {
                    $primary = $tablegateway->getConstraintsObject('PRIMARY KEY')[0]->getColumns();
                    foreach ($primary as $column) {
                        if (isset($set[$column])) {
                            $where[$column] = $set[$column];
                            unset($set[$column]);
                        }
                    }
                }

                if (isset($set['sort']) && $set['sort'] == '') {
                    $set['sort'] = 16777215;
                }

                if (isset($set['language_has_locale'])) {
                    $language_has_locale = json_decode($set['language_has_locale']);
                    unset($set['language_has_locale']);
                    $set['language_id'] = $language_has_locale->language_id;
                    $set['locale_id'] = $language_has_locale->locale_id;
                }

                if ($this->verifyUpload($request)) {
                    $this->processUpload($request, $tablegateway);
                    $errorResponse = $this->getUploadResponse();
                    if ($errorResponse && $errorResponse instanceof ApiErrorResponse) {
                        return $errorResponse;
                    }
                    $uploaded = $this->getUploaded();
                    if ($uploaded) {
                        $set = array_merge($set, $uploaded);
                    }
                }
                // checkbox fixed
                $columns = $tablegateway->getColumns();
                /*
                if (false !== array_search('sort', $columns) && empty($set['sort'])) {
                    $set['sort'] = 16777215;
                }
                */
                foreach ($columns as $column) {
                    if (preg_match('/^is_/', $column) && empty($set[$column])) {
                        $set[$column] = 0;
                    }
                    // bool to int
                    if (preg_match('/^is_/', $column) && ! empty($set[$column])) {
                        $set[$column] = intval($set[$column]);
                    }
                }
                $isRemoveImageFields = [];
                $setKeys = array_keys($set);
                foreach ($setKeys as $col) {
                    if (preg_match('/^is_remove/', $col)) {
                        $matchCol = preg_replace('/^is_remove_/', '', $col);
                        $isRemoveImageFields[] = $matchCol;
                        unset($set[$col]);
                        $set[$matchCol] = new Expression('null');
                    }
                }
                if ($isRemoveImageFields) {
                    $row = $tablegateway->select($where)->current();
                    foreach ($isRemoveImageFields as $field) {
                        $path = PROJECT_DIR . $row->{$field};
                        if (is_file($path)) {
                            unlink($path);
                        }
                    }
                }
                //debug([$set, $where]);
                $updateCount = $tablegateway->update($set, $where);
                $row = $tablegateway->select($where)->current();
                if ($row instanceof RowGateway) {
                    $row = $row->toArray();
                } else {
                    $row = (array) $row;
                }
                if (isset($row['language_id']) && isset($row['locale_id'])) {
                    $row['language_has_locale'] = json_encode([
                        'locale_id' => $row['locale_id'],
                        'language_id' => $row['language_id']
                    ]);
                    unset($row['language_id']);
                    unset($row['locale_id']);
                }
                if($tablegateway->encryptionColumns) {
                    $row = $tablegateway->deCryptData($row);
                }
                if($tablegateway->getTailTableName() == 'system_settings') {
                    if(isset($row['aes_value'])) {
                        $row = $tablegateway->deCryptData($row);
                    }
                }
                if($updateCount == 0) {
                    ApiErrorResponse::$status = 200;
                    return new ApiErrorResponse(1, [], ['no data updated']);
                }
                return new ApiSuccessResponse(0, $row, ['update success']);
            }
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(1, isset($set) ? $set : [], [
                'message' => $e->getMessage()
            ], [
                $e->getMessage()
            ]);
        }
    }
}