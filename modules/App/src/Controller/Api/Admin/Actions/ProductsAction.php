<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ApiQueryService;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use Laminas\Diactoros\Response\EmptyResponse;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\ManufacturesTableGateway;

class ProductsAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }
    
    protected function getManufacturesOptions(ApiSuccessResponse $response)
    {
        $contents = json_decode($response->getBody()->getContents(), true);
        $data = $contents['data'];
        $manufacturesTableGateway = new ManufacturesTableGateway($this->adapter);
        $manufacturesOptions = $manufacturesTableGateway->getOptions();
        $data['manufactures_options'] = $manufacturesOptions;
        return $data;
        //return new ApiSuccessResponse(0, $data);
        
    }
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        if (isset($queryParams['isOptionsRequest'])) {
            if (isset($queryParams['isOptionsRequest'])) {
                if (isset($queryParams['language_id']) && $queryParams['locale_id']/* && $queryParams['word']*/) {
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
                    if ($queryParams['word']) {
                        $where[] = [
                            'like',
                            'AND',
                            [
                                'model',
                                '%' . $queryParams['word'] . '%'
                            ]
                        ];
                    }
                    $options = $productsTableGateway->getOptions('id', 'model', [], $where, 100);
                    return new ApiSuccessResponse(0, [
                        'options' => $options
                    ]);
                } else {
                    return new ApiSuccessResponse(0, [
                        'options' => []
                    ]);
                }
            }
        }

        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, $productsTableGateway);
        if (! ($response instanceof EmptyResponse)) {
            $data = $this->getManufacturesOptions($response);
            return new ApiSuccessResponse(0, $data);
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/products.php', [
                'model' => 'products',
                'display_name' => 'language_has_locale',
                'is_new' => 'products',
                'is_hot' => 'products',
                'is_show' => 'products',
                'viewed_count' => 'products',
                'sort' => 'products',
                'created_at' => 'products',
            ]);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ProductsTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $tablegateway);
        $data = $this->getManufacturesOptions($response);
        return new ApiSuccessResponse(0, $data, ['update success']);
        
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
        $tablegateway = new ProductsTableGateway($this->adapter);
        $response = $ajaxFormService->postProcess($request, $tablegateway);
        $data = $this->getManufacturesOptions($response);
        return new ApiSuccessResponse(0, $data, ['add success']);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new ProductsTableGateway($this->adapter));
    }
}
