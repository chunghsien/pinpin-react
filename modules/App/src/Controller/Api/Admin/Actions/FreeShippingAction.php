<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\CouponTableGateway;

class FreeShippingAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new CouponTableGateway($this->adapter));
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $query = $request->getQueryParams();
            $query['item_count_per_page'] = 200;
            $request = $request->withQueryParams($query);
            
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/freeshipping.php', 
                // 欄位對應的資料表名稱
                [
                    'name' => 'coupon',
                    'target_value' => 'coupon',
                    'is_use' => 'coupon',
                    'display_name' => 'language_has_locale',
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
        $tablegateway = new CouponTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
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
    }
    
    
}
