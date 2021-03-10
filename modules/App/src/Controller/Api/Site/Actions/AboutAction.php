<?php
declare(strict_types = 1);

namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use App\Service\Site\BannerService;

class AboutAction extends AbstractAction
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
        $bannerService = new BannerService($this->adapter, $request);
        $lang = $request->getAttribute('lang');
        $route = "/{$lang}/about";
        $banner = $bannerService->getPageCarousel($route)->toArray();
        shuffle($banner);
        $banner = $banner[0];
        $vars = [
            "banner" => $banner,
        ];
        $vars = array_merge(
            $vars,
            $this->getCommonVars($request)
        );
        return $vars;
    }
}
