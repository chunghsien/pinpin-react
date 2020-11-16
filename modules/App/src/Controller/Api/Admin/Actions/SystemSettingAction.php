<?php

declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Middleware\AbstractAction;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use App\Service\AjaxFormService;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;
use App\Service\ApiQueryService;

class SystemSettingAction extends AbstractAction
{

    protected $gridPatternUse = [
        'site_info',
        'general_seo'
    ];

    protected $notUse = [
        'ecpay',
        'newwebpay'
    ];
    
    private function other(ServerRequestInterface $request): ResponseInterface
    {
        $tableGateway = new SystemSettingsTableGateway($this->adapter);
        $query = $request->getQueryParams();
        $pk = $query['pk'];
        $select = $tableGateway->getSql()->select();
        $select->columns(['id']);
        $select->where(['parent_id' =>0, 'key' => $pk]);
        $parents = $tableGateway->selectWith($select);
        $parents_id = [];
        foreach ($parents as $p) {
            $parents_id[] = $p['id'];
        }
        $query['item_count_per_page'] = 200;
        $request = $request->withQueryParams($query);
        $apiQueryService = new ApiQueryService();
        return $apiQueryService->processPaginator(
            $request,
            'src/App/scripts/db/admin/systemSettingsOther1.php',
            [
                'name' => 'system_settings',
                'value' => 'system_settings',
                'display_name' => 'language_has_locale',
            ],
            [
                implode(',', $parents_id)
            ]
         );
    }
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        if(isset($query['pk'])) {
            //$method = $query['pk'];
            return $this->other($request);
        }
        try {
            $tableGateway = new SystemSettingsTableGateway($this->adapter);
            $parentSelect = $tableGateway->getSql()->select();
            $parentWhere = $parentSelect->where;
            $parentWhere->equalTo('parent_id', 0);
            $parentWhere->isNull('deleted_at');
            $parentWhere->notIn('key', array_merge($this->gridPatternUse, $this->notUse));
            $parentSelect->where($parentWhere);
            $parentResultSet = $tableGateway->selectWith($parentSelect);
            $data = [];
            $crypt = $tableGateway->getCrypter();
            foreach ($parentResultSet as $parentRow) {
                $key = $parentRow->key;
                $name = $parentRow->name;
                $childSelect = $tableGateway->getSql()->select();
                $childSelect->order('sort ASC');
                $childWhere = $childSelect->where;
                $childWhere->equalTo('parent_id', $parentRow->id);
                $childWhere->isNull('deleted_at');
                $childSelect->where($childWhere);
                $child = $tableGateway->selectWith($childSelect)->toArray();
                if($child) {
                    foreach ($child as &$c) {
                        $input_type = $c['input_type'];
                        if(is_string($input_type) && $input_type) {
                            $c['input_type'] = json_decode($input_type, true);
                        }
                        if($c['aes_value']) {
                            $c['value'] = $crypt->decrypt($c['aes_value']);
                            $c['aes_value'] = '';
                        }
                    }
                }
                $data[$key] = [
                    'name' => $name,
                    'child' => $child,
                ];
            }
            return new ApiSuccessResponse(0, $data);
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(417, ['trace' => $e->getTrace()], [$e->getMessage(), ]);
        }
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $tableGateway = new SystemSettingsTableGateway($this->adapter);
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->putProcess($request, $tableGateway);
        if ($response instanceof ApiSuccessResponse) {
            return $this->get($request);
        } else {
            return $response;
        }
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        return $this->put($request);
    }
}
