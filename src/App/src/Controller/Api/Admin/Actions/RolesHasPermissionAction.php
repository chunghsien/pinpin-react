<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Users\TableGateway\RolesHasPermissionTableGateway;
use Laminas\I18n\Translator\Translator;

class RolesHasPermissionAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $roles_id = $params['roles_id'];
        //$rolesTableGateway = new RolesTableGateway($this->adapter);
        //$rolesRow = $rolesTableGateway->select(['id' => $roles_id])->current();
        $rolesHasPermissionScript = require 'src/App/scripts/db/admin/rolesHasPermission.php';
        $options = DB::selectFactory($rolesHasPermissionScript['options'])->toArray();
        $values = DB::selectFactory($rolesHasPermissionScript['defaultValue'], ['roles_id' => $roles_id,])->toArray();
        
        $translator = new Translator();
        $translator->addTranslationFilePattern('phpArray', PROJECT_DIR.'/resources/languages/', '%s/admin-navigation.php');
        $translator->setLocale('zh_TW');
        
        $switchValues = [];
        foreach ($options as &$option) {
            $option['label'] = $translator->translate($option['label'], 'default');
            $id = $option['value'];
            $switchValues[$id] = 0;
        }
        $switchValues = [];
        foreach ($values as &$value) {
            $value['label'] = $translator->translate($value['label']);
            $id = $value['value'];
            $switchValues[$id] = 1;
        }
        //debug($switchValues);
        return [
            'values' => [
                'permission' => $values,
            ],
            'options' => [
                'permission' => $options,
            ],
            'switch_values' =>[
                'permission' => $switchValues,
            ],
            'switch_default_values' =>[
                'permission' => $switchValues,
            ]
            
            //'translateUse' => 1
        ];
    }
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getOptions($request);
        return new ApiSuccessResponse(0, $data);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            $post = $request->getParsedBody();
            $roles_id = $post['roles_id'];
            $rolesHasPermissionTableGateway = new RolesHasPermissionTableGateway($this->adapter);
            if($rolesHasPermissionTableGateway->select(['roles_id' => $roles_id])->count()) {
                $rolesHasPermissionTableGateway->delete(['roles_id' => $roles_id]);
            }
            $permission_ids = explode(',', $post['permission_id']);
            foreach ($permission_ids as $permission_id) {
                $set = [
                    'permission_id' => $permission_id,
                    'roles_id' => $roles_id
                ];
                $rolesHasPermissionTableGateway->insert($set);
            }
            $this->adapter->getDriver()->getConnection()->commit();
            $data = $this->getOptions($request);
            return new ApiSuccessResponse(0, $data, [
                'update success'
            ]);
        } catch (\Exception $e) {
            $this->adapter->getDriver()->getConnection()->rollback();
            loggerException($e);
            return new ApiErrorResponse(417, [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
            
        }
    }
}
