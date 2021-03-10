<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Store\TableGateway\NpClassHasProductsTableGateway;
use Chopin\Store\TableGateway\ProductsTableGateway;

class NpClassHasProductsAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $products_id = isset($params['products_id']) ? $params['products_id'] : null;
        if(!$products_id) {
            $products_id = $params['self_id'];
        }
        
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $productsRow = $productsTableGateway->select([
            'id' => $products_id
        ])->current();
        $npClassHasProductsScripts = require 'modules/App/scripts/db/admin/npClassHasProducts.php';
        $options = DB::selectFactory($npClassHasProductsScripts['options'], [
            'language_id' => $productsRow->language_id,
            'locale_id' => $productsRow->locale_id
        ])->toArray();
        
        $values = DB::selectFactory($npClassHasProductsScripts['defaultValue'], [
            'products_id' => $products_id,
        ])->toArray();
        
        return [
            'values' => [
                'np_class' => $values,
            ],
            'options' => [
                'np_class' => $options,
            ]
        ];
    }
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getOptions($request);
        return new ApiSuccessResponse(0, $data);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            $post = $request->getParsedBody();
            $products_id = $post['products_id'];
            $npClassHasProductsTableGateway = new NpClassHasProductsTableGateway($this->adapter);
            if($npClassHasProductsTableGateway->select(['products_id' => $products_id])->count()) {
                $npClassHasProductsTableGateway->delete(['products_id' => $products_id]);
            }
            if(isset($post['np_class_id'])) {
                $np_class_ids = explode(',', $post['np_class_id']);
                foreach ($np_class_ids as $np_class_id) {
                    $set = [
                        'np_class_id' => $np_class_id,
                        'products_id' => $products_id
                    ];
                    $npClassHasProductsTableGateway->insert($set);
                }
            }
            $this->adapter->getDriver()->getConnection()->commit();
            $data = $this->getOptions($request);
            return new ApiSuccessResponse(0, $data, [
                'update success'
            ]);
        } catch (\Exception $e) {
            $this->adapter->getDriver()->getConnection()->rollback();
            loggerException($e);
            return new ApiErrorResponse(417, [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
            
        }
    }
}
