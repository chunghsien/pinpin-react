<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\Documents\TableGateway\BannerTableGateway;
use App\Service\AjaxFormService;
use App\Service\ApiQueryService;
use Laminas\Filter\Word\CamelCaseToUnderscore;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Laminas\Db\ResultSet\ResultSet;

class BannerAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }
    
    private function getImage($ids, BannerTableGateway $tableGateway): ResponseInterface
    {
        $select = $tableGateway->getSql()->select();
        
        if($ids) {
            $select->where(['id' => $ids]);
            $select->columns(['id', 'image', 'bg_image']);
            $resultSet = $tableGateway->selectWith($select)->toArray();
        }else {
            $resultSet = new ResultSet();
            $resultSet->initialize([]);
        }
        return new ApiSuccessResponse(0, $resultSet);
    }
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $tableGateway = new BannerTableGateway($this->adapter);
        $query = $request->getQueryParams();
        if(isset($query['method']) && $query['method'] == 'image') {
            $ids = isset($query['ids']) ? $query['ids'] : [];
            return $this->getImage($ids, $tableGateway);
        }
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, $tableGateway);
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/banner.php',
                // 欄位對應的資料表名稱
                [
                    'title' => 'banner',
                    'subtitle' => 'banner',
                    'bg_color' => 'banner',
                    'url' => 'banner',
                    'target' => 'banner',
                    'is_show'=> 'banner',
                    'display_name' => 'language_has_locale',
                ]);
        }
    }
    
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new BannerTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
        
    }

    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $post = $request->getParsedBody();
        if(isset($queryParams['put']) || isset($post['id'])) {
            return $this->put($request);
        }
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new BannerTableGateway($this->adapter);
        return $ajaxFormService->postProcess($request, $tablegateway);
        
    }
}
