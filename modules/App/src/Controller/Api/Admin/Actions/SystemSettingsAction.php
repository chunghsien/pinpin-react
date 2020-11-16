<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use App\Service\AjaxFormService;
use Laminas\Diactoros\Response\EmptyResponse;
use App\Service\ApiQueryService;
use Laminas\Db\Sql\Expression;

class SystemSettingsAction extends AbstractAction
{

    //protected $use;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        //$this->use = config('logistics');
        return $this->{$method}($request);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $attribute = $request->getAttribute('method_or_id', null);
        if($attribute && $attribute == 'logistics') {
            $filter = new UnderscoreToCamelCase();
            $classname = ucfirst($filter->filter(config('logistics')));
            $system_setting_key = strtolower($classname);
            $systemSettingTablegateway = new SystemSettingsTableGateway($this->adapter);
            $systemSettingRow = $systemSettingTablegateway->select([
                'key' => $system_setting_key
            ])->current();
            $query = $request->getQueryParams();
            $query['extra_where'] = json_encode([
                [
                    'equalTo', 'AND', [$systemSettingTablegateway->table.'.parent_id', $systemSettingRow->id]
                ],
            ]);
            $request = $request->withQueryParams($query);
        }

        $apiQueryService = new ApiQueryService();
        $query = $request->getQueryParams();
        $query['item_count_per_page'] = 100;
        $request = $request->withQueryParams($query);
        return $apiQueryService->processPaginator($request, 'modules/App/scripts/db/admin/systemSettings.php', [
            'name' => 'system_settings',
            'key' => 'system_settings',
            'value' => 'system_settings',
            'display_name' => 'language_has_locale',
        ]);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new SystemSettingsTableGateway($this->adapter);
        return $ajaxFormService->putProcess($request, $tablegateway);
        
    }

}
