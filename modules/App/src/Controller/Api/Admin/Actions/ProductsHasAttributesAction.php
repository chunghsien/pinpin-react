<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\Store\TableGateway\AttributesTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\ProductsHasAttributesTableGateway;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Insert;

class ProductsHasAttributesAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $products_id = intval($queryParams['products_id']);
        $items = $this->getItems($products_id);
        return new ApiSuccessResponse(0, $items);
    }

    private function getItems($products_id)
    {
        $table = 'products';
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $productsRow = $productsTableGateway->select([
            'id' => $products_id
        ])->current();
        $attributesTableGateway = new AttributesTableGateway($this->adapter);
        $attributesSlect = new Select($attributesTableGateway->table);
        $attributesSlect->order([
            'sort ASC',
            'id DESC'
        ]);
        $attributesPredicate = new Predicate();
        $attributesPredicate->equalTo('table', $table)
            ->equalTo('table_id', 0)
            ->equalTo('parent_id', 0)
            ->equalTo('language_id', $productsRow->language_id)
            ->equalTo('locale_id', $productsRow->locale_id)
            ->isNull('deleted_at');

        $attributesSlect->where($attributesPredicate);
        $items = $attributesTableGateway->selectWith($attributesSlect)->toArray();

        if ($items) {
            $productsHasAttributesTableGateway = new ProductsHasAttributesTableGateway($this->adapter);
            foreach ($items as &$item) {
                $select = new Select($attributesTableGateway->table);
                $select->order([
                    'sort ASC',
                    'id DESC'
                ]);
                $predicate = new Predicate();
                $predicate->equalTo('parent_id', $item['id'])->isNull('deleted_at');
                $select->where($predicate);
                $childItems = $attributesTableGateway->selectWith($select)->toArray();
                if ($childItems) {
                    foreach ($childItems as &$citem) {
                        $attributes_id = intval($citem['id']);
                        $test = $productsHasAttributesTableGateway->select([
                            'products_id' => $products_id,
                            'attributes_id' => $attributes_id
                        ]);
                        if ($test->count() == 1) {
                            $citem['checked'] = 1;
                        } else {
                            $citem['checked'] = 0;
                        }
                    }
                }
                $item['child'] = $childItems;
            }
        }
        return $items;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $productsHasAttributesTableGateway = new ProductsHasAttributesTableGateway($this->adapter);
        $post = $request->getParsedBody();
        $products_id = $post['products_id'];
        if ($productsHasAttributesTableGateway->select([
            'products_id' => $products_id
        ])->count()) {
            $productsHasAttributesTableGateway->delete([
                'products_id'
            ]);
        }
        if (isset($post['attributes_id'])) {
            $post['attributes_id'] = explode(',', $post['attributes_id']);
        }

        foreach ($post['attributes_id'] as $attributes_id) {
            $attributes_id = intval($attributes_id);
            if($attributes_id) {
                $productsHasAttributesTableGateway->insert([
                    'products_id' => $products_id,
                    'attributes_id' => $attributes_id
                ]);
            }
        }
        $items = $this->getItems($products_id);
        return new ApiSuccessResponse(0, $items, ['update success']);
        
    }
}
