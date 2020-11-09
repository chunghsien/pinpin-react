<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\LaminasDb\DB;
use App\Constants;

class ApiQueryService {

    /**
     * 
     * @var \Laminas\Db\TableGateway\AbstractTableGateway
     */
    protected $tablegateway = null;
    
    public function __construct(\Laminas\Db\TableGateway\AbstractTableGateway $tablegateway = null)
    {
        if($tablegateway instanceof \Laminas\Db\TableGateway\AbstractTableGateway) {
            $this->tablegateway = $tablegateway;
            DB::setStaticTablegateway($tablegateway);
        }
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param string|array $scriptPath
     * @param array $columnMapper , 傳送過來的欄位與資料表的對應。
     * @param array $bindParams
     * @return ResponseInterface
     */
    public function processPaginator(
        ServerRequestInterface $request, 
        $scriptPath, array $columnMapper, 
        array $bindParams = []
        ): ResponseInterface {
        try {
            if(is_file($scriptPath)) {
                $dbScripts = require $scriptPath;
                $queryParams = $request->getQueryParams();
            }else {
                $queryParams = $scriptPath;
            }
            $currentPageNumber = isset($queryParams['page']) ? intval($queryParams['page']) : 1;
            $paginatorScript = $dbScripts['pagiantor'];
            if(isset($queryParams['sort'])) {
                $sort = $queryParams['sort'];
                $paginatorScript['order'] = [implode(' ', $sort)];
            }
            if(isset($queryParams['filters'])) {
                $PT = AbstractTableGateway::$prefixTable;
                $filters = json_decode($queryParams['filters'], true);
                if($filters) {
                    $orWhere = [];
                    $columns = array_keys($filters);
                    foreach ($columns as $idx => $column) {
                        $logi = 'OR';
                        if($idx === 0) {
                            $logi = 'AND';
                        }
                        $table = str_replace($PT, '', $columnMapper[$column]);
                        $field = $PT.$table.'.'.$column;
                        $value = $filters[$column]['filterVal'];
                        if(is_numeric($value)) {
                            $orWhere[] = ['equalTo', $logi, [$field, $value]];
                        }else {
                            if($value) {
                                if(is_array($value)) {
                                    $comparator = $value['comparator'];
                                    $val = isset($value['number']) ? $value['number'] : date("Y-m-d H:i:s", strtotime($value['date']));
                                    $mapper = [
                                        '<' => 'lessThan',
                                        '<=' => 'lessThanOrEqualTo',
                                        '>' => 'greaterThan',
                                        '>=' => 'greaterThanOrEqualTo',
                                        '=' => 'equalTo',
                                        '!=' => 'notEqualTo',
                                    ];
                                    $method = $mapper[$comparator];
                                    $orWhere[] = [$method, $logi, [$field, $val]];
                                }else {
                                    $orWhere[] = ['like', $logi, [$field, '%'.$value.'%']];
                                }
                                
                            }
                        }
                    }
                    if( $orWhere ) {
                        $where = isset($paginatorScript['where']) ? $paginatorScript['where'] : [];
                        $where[] = ['AND', $orWhere];
                        $paginatorScript['where'] = $where;
                    }
                }
            }
            if(isset($queryParams['extra_where'])) {
                if(empty($paginatorScript['where'])) {
                    $paginatorScript['where'] = [];
                }
                $tmp = json_decode($queryParams['extra_where'], true);
                $paginatorScript['where'] = array_merge( $paginatorScript['where'], $tmp);
            }
            $paginatorConfig = [
                'item_count_per_page' => isset($queryParams['item_count_per_page']) ? $queryParams['item_count_per_page'] : Constants::DEFAULT_ITEM_COUNT_PER_PAGE,
            ];
            $data = DB::paginatorFactory($paginatorScript, $bindParams, $paginatorConfig, $currentPageNumber);
            return new ApiSuccessResponse(0, $data, []);
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(417, ['tract' => $e->getTrace()], [$e->getMessage(), ]);
        }
    }
}