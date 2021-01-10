<?php
declare(strict_types = 1);

namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use App\Service\StaticSiteGenerator\Site\FooterService;
use App\Service\StaticSiteGenerator\Site\NavigationService;
use App\Service\StaticSiteGenerator\Site\ProductsService;
use App\Service\StaticSiteGenerator\Site\CategoriesService;
use App\Service\StaticSiteGenerator\Site\SlidersService;
use App\Service\StaticSiteGenerator\Site\BlogService;
use App\Service\StaticSiteGenerator\Site\I18nService;
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
        $theme = $request->getAttribute('method_or_id', 'decor');
        $categoriesService = new CategoriesService($this->adapter);
        $productsService = new ProductsService($this->adapter);
        $slidersService = new SlidersService($this->adapter);
        $navigationService = new NavigationService($this->adapter);
        $footerService = new FooterService($this->adapter);
        $blogService = new BlogService($this->adapter);
        $i18nService = new I18nService($this->adapter, ["site-navigation", "site-translation", "site-footer"]);
        $callToActionTableGateway = new CallToActionTableGateway($this->adapter);
        
        $lang = $request->getAttribute('lang');
        $vars = array_merge(
            $categoriesService->result($request),
            $productsService->result($request),
            $footerService->result($request),
            $navigationService->result($request),
            $slidersService->result($request),
            $blogService->result($request),
            $i18nService->result($request),
            [
                "imageCtaData" => $callToActionTableGateway->getFromDocuments("/{$lang}"),
            ]
        );
        $vars['theme'] = $theme;
        return new ApiSuccessResponse(0, $vars);
    }
}
