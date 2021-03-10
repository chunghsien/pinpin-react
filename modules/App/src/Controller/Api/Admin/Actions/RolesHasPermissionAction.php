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
        $roles_id = $params['self_id'];
        $rolesHasPermissionScript = require 'modules/App/scripts/db/admin/rolesHasPermission.php';
        $values = DB::selectFactory($rolesHasPermissionScript['defaultValue'], ['roles_id' => $roles_id,])->toArray();
        
        $translator = new Translator();
        $translator->addTranslationFilePattern('phpArray', PROJECT_DIR.'/resources/languages/', '%s/admin-navigation.php');
        $translator->setLocale('zh_TW');
        
        $switchValues = [];
        foreach ($values as $value) {
            $switchValues[] = $value['value'];
        }
        //debug($switchValues);
        return [
            'values' =>$switchValues,
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
            if($post['permission_id']) {
                $permission_ids = explode(',', $post['permission_id']);
                foreach ($permission_ids as $permission_id) {
                    $set = [
                        'permission_id' => $permission_id,
                        'roles_id' => $roles_id
                    ];
                    $rolesHasPermissionTableGateway->insert($set);
                }
            }
            $this->adapter->getDriver()->getConnection()->commit();
            $request = $request->withQueryParams(['self_id' =>$roles_id]);
            $data = $this->getOptions($request);
            return new ApiSuccessResponse(0, $data, [
                'update success'
            ]);
        } catch (\Exception $e) {
            loggerException($e);
            $this->adapter->getDriver()->getConnection()->rollback();
            return new ApiErrorResponse(417, [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
            
        }
    }
}
