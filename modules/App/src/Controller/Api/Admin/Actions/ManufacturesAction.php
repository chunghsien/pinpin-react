<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use Chopin\Store\TableGateway\FpClassTableGateway;
use App\Service\AjaxFormService;
use Chopin\Store\TableGateway\ManufacturesTableGateway;
use Laminas\Diactoros\Response\JsonResponse;
use Chopin\HttpMessage\Response\ApiSuccessResponse;

class ManufacturesAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new ManufacturesTableGateway($this->adapter));
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $queryParams = $request->getQueryParams();
            if (isset($queryParams['options'])) {
                $manufacturesTableGateway = new ManufacturesTableGateway($this->adapter);
                $data = $manufacturesTableGateway->getOptions('id', 'name', [], [
                    [
                        'equalTo',
                        'AND',
                        [
                            'language_id',
                            $queryParams['language_id']
                        ]
                    ],
                    [
                        'equalTo',
                        'AND',
                        [
                            'locale_id',
                            $queryParams['locale_id']
                        ]
                    ],
                    [
                        'isNull',
                        'AND',
                        [
                            'deleted_at'
                        ]
                    ]
                ]);
                return new ApiSuccessResponse(0, $data);
            } else {
                $apiQueryService = new ApiQueryService();
                return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/manufactures.php', [
                    'name' => 'manufactures',
                    'sort' => 'manufactures',
                    'display_name' => 'language_has_locale',
                    'created_at' => 'manufactures'
                ]);
            }
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new ManufacturesTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ManufacturesTableGateway($this->adapter);
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
        if(isset($queryParams['put'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ManufacturesTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
    }
    
    
}
