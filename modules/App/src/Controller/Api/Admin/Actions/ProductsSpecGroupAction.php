<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Laminas\Diactoros\Response;

class ProductsSpecGroupAction extends AbstractAction
{

    private function getOptionsResponse(Response $response, ProductsTableGateway $productsTableGateway)
    {
        $payload = $response->getPayload();
        $data = $payload['data'];

        // $pt = ProductsTableGateway::$prefixTable;
        $id = intval($data['products_id']);

        $options = [];
        $values = [];
        $defaultvalues = [];

        $where = [
            'id' => $id
        ];
        $productsSelected = $productsTableGateway->select($where)->current();
        $values = [
            'value' => $productsSelected->id,
            'label' => $productsSelected->model,
        ];
        $options = [
            $values
        ];
        $options = array_merge($options, $productsTableGateway->getOptions('id', 'model', [], [
            [
                'notEqualTo',
                'AND',
                [
                    'id',
                    $id
                ]
            ],
            [
                'isNull',
                'AND',
                [
                    'deleted_at'
                ],
            ],
        ], 100));
        $defaultvalues = $values;
        $data['options']['products_id'] = $options;
        $data['values']['products_id'] = $values;
        $data['defaultvalues']['products_id'] = $defaultvalues;
        $payload['data'] = $data;
        $response = $response->withPayload($payload);
        return $response;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->adapter);
        if (isset($queryParams['products_id'])) {
            $products_id = intval($queryParams['products_id']);
            $options = $productsSpecGroupTableGateway->getOptions('id', 'name', [], [
                [
                    'equalTo',
                    'AND',
                    [
                        'products_id',
                        $products_id
                    ]
                ],
                [
                    'isNull',
                    'AND',
                    [
                        'deleted_at'
                    ],
                ],
            ]);
            return new ApiSuccessResponse(0, ['options' => $options]);
        }
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, $productsSpecGroupTableGateway);
        if ($response->getStatusCode() == 200) {
            $productsTableGateway = new ProductsTableGateway($this->adapter);
            return $this->getOptionsResponse($response, $productsTableGateway);
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/productsSpecGroup.php', [
                'name' => 'products_spec_group',
                'sort' => 'products_spec_group',
                'model' => 'products',
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
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new ProductsSpecGroupTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecGroupTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $tablegateway);
        return $this->getOptionsResponse($response, new ProductsTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if(isset($queryParams['put'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsSpecGroupTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
    
}
