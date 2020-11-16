<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LanguageHasLocale\TableGateway\CurrencyRateTableGateway;
use App\Service\AjaxFormService;
use App\Service\ApiQueryService;

class CurrenciesAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $query['item_count_per_page'] = 200;
        $request = $request->withQueryParams($query);
        $apiQueryService = new ApiQueryService();
        return $apiQueryService->processPaginator(
            $request,
            'modules/App/scripts/db/admin/currencies.php',
            [
                'code' => 'currencies',
                'name' => 'currencies',
                'rate' => 'currency_rate',
            ]
        );
    }

    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new CurrencyRateTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
        
    }

}
