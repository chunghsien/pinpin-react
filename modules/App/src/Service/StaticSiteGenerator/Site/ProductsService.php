<?php
namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Laminas\Diactoros\ServerRequest;

class ProductsService extends AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    protected $data;

    /*
     * @var ProductsTableGateway
     */
    protected $productsTableGateway;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->data = json_decode(file_get_contents(__DIR__ . '/products.json'), true);
        $this->productsTableGateway = new ProductsTableGateway($adapter);
    }

    /**
     *
     * @param ServerRequest $request
     * @return mixed[]|NULL[]|mixed[]
     */
    protected function index($request)
    {
        $theme = config('theme.name');
        switch ($theme) {
            case 'trending':
                return [
                "products" => $this->productsTableGateway->getPopular($request, 10, 'fashion')
                ];
                break;
            case 'smart-design':
                return [
                "products" => $this->productsTableGateway->getPopular($request, 10, 'furniture')
                ];
                break;
            case 'essentials':
                return [
                "popularProducts" => $this->productsTableGateway->getPopular($request, 10, 'fashion'),
                "saleProducts" => $this->productsTableGateway->getSale($request, 8, 'fashion')
                ];
                break;
            case 'collection':
                return [];
                break;
            case 'perfumes':
                return [
                "newProducts" => $this->productsTableGateway->getNew($request, 10, 'perfumes'),
                "popularProducts" => $this->productsTableGateway->getPopular($request, 10, 'perfumes'),
                "saleProducts" => $this->productsTableGateway->getSale($request, 10, 'perfumes')
                ];
                break;
            case 'furniture':
                return [
                "products" => $this->productsTableGateway->getPopular($request, 10, 'furniture')
                ];
                break;
            default:
                return [
                    "newProducts" => $this->productsTableGateway->getNew($request, 9, 'decor'),
                    "popularProducts" => $this->productsTableGateway->getPoupular($request, 9, 'decor'),
                    "saleProducts" => $this->productsTableGateway->getSale($request, 9, 'decor'),
                    "imageCtaData" => [],
                ];
        }
    }
    
    protected function shopCategory(ServerRequestInterface $request)
    {
        return $this->productsTableGateway->buildBaseSelect($request);
    }
    
    public function result(ServerRequestInterface $request, $page = 'index')
    {
        return $this->{$page}($request);
    }
    public function getTopRated($category, $limit = 9)
    {
        $data = [];
        foreach ($this->data as $item) {
            if (false !== array_search($category, $item['category'])) {
                $data[] = $item;
            }
        }
        $saleCount = array_column($data, 'rating');
        $id = array_column($data, 'id');
        array_multisort($saleCount, SORT_DESC, $id, SORT_ASC, $data);
        return array_slice($data, 0, $limit);
    }

}