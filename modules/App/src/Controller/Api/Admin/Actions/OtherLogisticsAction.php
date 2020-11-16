<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\AjaxFormService;
use App\Service\ApiQueryService;
use Chopin\Store\TableGateway\LogisticsGlobalTableGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class OtherLogisticsAction extends AbstractAction
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
        $apiQueryService = new ApiQueryService();
        $query = $request->getQueryParams();
        $query['item_count_per_page'] = 100;
        $PT = AbstractTableGateway::$prefixTable;
        $query['extra_where'] = json_encode([
            [
                'equalTo',
                'AND',
                [
                    $PT . 'logistics_global.method',
                    'other'
                ]
            ],
        ]);
        $request = $request->withQueryParams($query);
        return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/logisticsGlobal.php', [
            'manufacturer' => 'logistics_global',
            'name' => 'logistics_global',
            'price' => 'logistics_global',
            'is_use' => 'logistics_global',
            'sort' => 'logistics_global',
            'display_name' => 'language_has_locale',
        ]);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $logisticsGlobalTableGateway = new LogisticsGlobalTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $logisticsGlobalTableGateway);
        
    }

}
