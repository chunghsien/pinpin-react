<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Newsletter\TableGateway\MnClassTableGateway;
use Chopin\Newsletter\TableGateway\FnClassHasMnClassTableGateway;

class FnClassHasMnClassAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $mn_class_id = isset($params['self_id']) ? $params['self_id'] : null;
        if (! $mn_class_id && isset($params['mn_class_id'])) {
            $mn_class_id = $params['mn_class_id'];
        }

        $mnClassTableGateway = new MnClassTableGateway($this->adapter);
        $mnClassRow = $mnClassTableGateway->select([
            'id' => $mn_class_id
        ])->current();
        $fnClassHasMnClassScripts = require 'modules/App/scripts/db/admin/fnClassHasMnClass.php';
        $options = DB::selectFactory($fnClassHasMnClassScripts['options'], [
            'language_id' => $mnClassRow->language_id,
            'locale_id' => $mnClassRow->locale_id
        ])->toArray();

        $values = DB::selectFactory($fnClassHasMnClassScripts['defaultValue'], [
            'mn_class_id' => $mn_class_id
        ])->toArray();

        return [
            'values' => [
                'fn_class' => $values
            ],
            'options' => [
                'fn_class' => $options
            ]
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
            $this->adapter->getDriver()
                ->getConnection()
                ->beginTransaction();
            $post = $request->getParsedBody();
            $mn_class_id = $post['mn_class_id'];
            $fnClassHasMnClassTableGateway = new FnClassHasMnClassTableGateway($this->adapter);
            if ($fnClassHasMnClassTableGateway->select([
                'mn_class_id' => $mn_class_id
            ])->count()) {
                $fnClassHasMnClassTableGateway->delete([
                    'mn_class_id' => $mn_class_id
                ]);
            }
            if (isset($post['fn_class_id'])) {
                $fn_class_ids = explode(',', $post['fn_class_id']);
                foreach ($fn_class_ids as $fn_class_id) {
                    $set = [
                        'fn_class_id' => $fn_class_id,
                        'mn_class_id' => $mn_class_id
                    ];
                    $fnClassHasMnClassTableGateway->insert($set);
                }
            }
            $this->adapter->getDriver()
                ->getConnection()
                ->commit();
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
