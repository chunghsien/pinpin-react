<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\I18n\Translator\Translator;
use Chopin\Users\TableGateway\UsersHasRolesTableGateway;

class UsersHasRolesAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $users_id = $params['self_id'];
        $usersHasRolesScript = require 'modules/App/scripts/db/admin/usersHasRoles.php';
        $options = DB::selectFactory($usersHasRolesScript['options'])->toArray();
        $values = DB::selectFactory($usersHasRolesScript['defaultValue'], [
            'users_id' => $users_id
        ])->toArray();
        $translator = new Translator();
        $translator->addTranslationFilePattern('phpArray', PROJECT_DIR . '/resources/languages/', '%s/admin-navigation.php');
        $translator->setLocale('zh_TW');
        foreach ($options as &$option) {
            $option['label'] = $translator->translate($option['label'], 'default');
        }
        foreach ($values as &$value) {
            $value['label'] = $translator->translate($value['label']);
        }

        return [
            'values' => [
                'roles' => $values
            ],
            'options' => [
                'roles' => $options
            ]
            // 'translateUse' => 1
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
            $users_id = $post['users_id'];
            $usersHasRolesTableGateway = new UsersHasRolesTableGateway($this->adapter);
            if ($usersHasRolesTableGateway->select([
                'users_id' => $users_id
            ])->count()) {
                $usersHasRolesTableGateway->delete([
                    'users_id' => $users_id
                ]);
            }

            $roles_id = $post['roles_id'];
            $set = [
                'roles_id' => $roles_id,
                'users_id' => $users_id
            ];
            $usersHasRolesTableGateway->insert($set);

            $this->adapter->getDriver()->getConnection()->commit();
            $request = $request->withQueryParams(['self_id' => $users_id]);
            $data = $this->getOptions($request);
            return new ApiSuccessResponse(0, $data, [
                'update success'
            ]);
        } catch (\Exception $e) {
            loggerException($e);
            $this->adapter->getDriver()
                ->getConnection()
                ->rollback();
            return new ApiErrorResponse(417, [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
        }
    }
}
