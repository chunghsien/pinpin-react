<?php
declare(strict_types = 1);

namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use App\Service\Site\ProductsService;
use App\Service\Site\BannerService;
use Chopin\Documents\TableGateway\CallToActionTableGateway;

class IndexAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $vars = $this->getStandByVars($request);
        return new ApiSuccessResponse(0, $vars);
    }
    
    public function getStandByVars(ServerRequestInterface $request)
    {
        $productsService = new ProductsService($this->adapter, $request);
        $bannerService = new BannerService($this->adapter, $request);
        $lang = $request->getAttribute('lang');
        $route = "/{$lang}";
        $callToActionTableGateway = new CallToActionTableGateway($this->adapter);
        $call_to_action = $callToActionTableGateway->getFromDocuments($route);
        $category = null;
        $queryParams = $request->getQueryParams();
        $vars = [
            "newProducts" => $productsService->getNewProducts($category),
            "popularProducts" => $productsService->getPopularProducts($category),
            "saleProducts" => $productsService->getSaleProducts($category),
            "carousel" => $bannerService->getPageCarousel($route),
            "s_carousel" => $bannerService->getPageCarousel($route, "s_carousel"),
            "call_to_action" => $call_to_action,
            'np_class' => $productsService->getNearCategories(true),
        ];
        if(isset($queryParams['category']))
        {
            $category = $queryParams['category'];
            foreach($category as $key => $cat)
            {
                $index = "part".($key+1)."Products";
                $vars[$index] = $productsService->getPopularProducts($cat);
            }
        }
        $vars = array_merge(
            $vars,
            $this->getCommonVars($request)
        );
        return $vars;
    }
}
