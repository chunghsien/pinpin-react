<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;


class ProductsSpecAction extends AbstractAction
{

    private function getRelationData($products_id, $products_spec_group_id) {
        
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $productsDefaultOption = $productsTableGateway->getOptions('id', 'model', [], [
            [
                'equalTo',
                'AND',
                [
                    'id',
                    $products_id
                ],
            ],
        ]);
        //(new Select())->where->notEqualTo($left, $right);
        $productsAppendOptions = $productsTableGateway->getOptions('id', 'model', [], [
            [
                'notEqualTo',
                'AND',
                [
                    'id',
                    $products_id
                ],
            ],
        ], 100);
        $productsOptions = array_merge($productsDefaultOption, $productsAppendOptions);

        $productsSpecGroupDefaultOption = [];
        $productsSpecGroupOptions = [];
        if($products_spec_group_id > 0) {
            $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->adapter);
            $productsSpecGroupDefaultOption = $productsSpecGroupTableGateway->getOptions('id', 'name', [], [
                [
                    'equalTo',
                    'AND',
                    [
                        'id',
                        $products_spec_group_id
                    ],
                ],
            ]);
            $productsSpecGroupAppendOptions = $productsSpecGroupTableGateway->getOptions('id', 'name', [], [
                [
                    'notEqualTo',
                    'AND',
                    [
                        'id',
                        $products_spec_group_id
                    ],
                ],
            ], 100);
            $productsSpecGroupOptions = array_merge($productsSpecGroupDefaultOption, $productsSpecGroupAppendOptions);
        }
        return [
            'options' => [
                'products_id' => $productsOptions,
                'products_spec_group_id' => $productsSpecGroupOptions,
            ],
            'values' => [
                'products_id' => $productsDefaultOption,
                'products_spec_group_id' => $productsSpecGroupDefaultOption,
            ],
        ];
    }
    
    private function getStockStatus(ServerRequestInterface $request) {
        //將庫存狀態加入(前端select option資料使用)
        $options = require 'src/App/config/store.php';
        return new ApiSuccessResponse(0, ['options' => $options]);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $attribute = $request->getAttribute('method_or_id', null);
        
        if($attribute && strtolower($attribute) == 'getrelationdata') {
            return $this->getRelationData($request);
        }
        if($attribute && strtolower($attribute) == 'getstockstatus') {
            return $this->getStockStatus($request);
        }
        
        
        if($attribute && strtolower($attribute) == 'getoption') {
            if(isset($queryParams['language_id']) && isset($queryParams['locale_id'])) {
                //對應產品
                $productsTableGateway = new ProductsTableGateway($this->adapter);
                $where = [
                    [
                        'equalTo',
                        'AND',
                        [
                            'language_id',
                            intval($queryParams['language_id'])
                        ],
                    ],
                    [
                        'equalTo',
                        'AND',
                        [
                            'locale_id',
                            intval($queryParams['locale_id'])
                        ],
                    ],
                    [
                        'isNull',
                        'AND',
                        [
                            'deleted_at'
                        ],
                    ],
                ];
                if(isset($queryParams['model'])) {
                    $where[] = [
                        'like',
                        'AND',
                        ['model', '%'.$queryParams['model'].'%']
                    ];
                }
                $options = $productsTableGateway->getOptions(
                    'id', 
                    'model', 
                    [],
                    $where,
                    50
                );
                return new ApiSuccessResponse(0, ['options' => $options]);
            }
            
            if(isset($queryParams['products_id'])) {
                //規格群組
                $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->adapter);
                $options = $productsSpecGroupTableGateway->getOptions(
                    'id', 
                    'name', 
                    [],
                    [
                        [
                            'equalTo',
                            'AND',
                            [
                                'products_id',
                                intval($queryParams['products_id'])
                            ],
                        ],
                        [
                            'isNull',
                            'AND',
                            [
                                'deleted_at'
                            ],
                        ],
                ],100);
                return new ApiSuccessResponse(0, ['options' => $options]);
            }
            return new ApiSuccessResponse(0, ['options' => []]);
        }
        
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new ProductsSpecTableGateway($this->adapter));
        if ($response->getStatusCode() == 200) {
            $data = $response->getPayload()['data'];
            $products_id = intval($data['products_id']);
            $products_spec_group_id = intval($data['products_spec_group_id']);
            $options = $this->getRelationData($products_id, $products_spec_group_id);
            $responseData = array_merge($data, $options);
            $response = $response->withPayload(['data' => $responseData]);
            return $response;
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'src/App/scripts/db/admin/productsSpec.php', 
                // 欄位對應的資料表名稱
                [
                    'name' => 'products_spec',
                    'stock' => 'products_spec',
                    'price' => 'products_spec',
                    'real_price' => 'products_spec',
                    'stock_status' => 'products_spec',
                    'sort' => 'products_spec',
                    'created_at' => 'products_spec',
                    'model' => 'products',
                    'group_name' => 'products_spec_group'
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
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
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
        $tablegateway = new ProductsSpecTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
    
}
