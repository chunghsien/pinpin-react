<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Newsletter\TableGateway\NnClassTableGateway;
use Chopin\Newsletter\TableGateway\MnClassHasNnClassTableGateway;

class MnClassHasNnClassAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $nn_class_id = isset($params['nn_class_id']) ? $params['nn_class_id'] : null;
        if(!$nn_class_id) {
            $nn_class_id = $params['self_id'];
        }
        
        $nnClassTableGateway = new NnClassTableGateway($this->adapter);
        $nnClassRow = $nnClassTableGateway->select([
            'id' => $nn_class_id
        ])->current();
        $mnClassHasNnClassScripts = require 'modules/App/scripts/db/admin/mnClassHasNnClass.php';
        $options = DB::selectFactory($mnClassHasNnClassScripts['options'], [
            'language_id' => $nnClassRow->language_id,
            'locale_id' => $nnClassRow->locale_id
        ])->toArray();
        
        $values = DB::selectFactory($mnClassHasNnClassScripts['defaultValue'], [
            'nn_class_id' => $nn_class_id,
        ])->toArray();
        
        return [
            'values' => [
                'mn_class' => $values,
            ],
            'options' => [
                'mn_class' => $options,
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
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            $post = $request->getParsedBody();
            $nn_class_id = $post['nn_class_id'];
            $mnClassHasNnClassTableGateway = new MnClassHasNnClassTableGateway($this->adapter);
            if($mnClassHasNnClassTableGateway->select(['nn_class_id' => $nn_class_id])->count()) {
                $mnClassHasNnClassTableGateway->delete(['nn_class_id' => $nn_class_id]);
            }
            if(isset($post['mn_class_id'])) {
                $mn_class_ids = explode(',', $post['mn_class_id']);
                foreach ($mn_class_ids as $mn_class_id) {
                    $set = [
                        'mn_class_id' => $mn_class_id,
                        'nn_class_id' => $nn_class_id
                    ];
                    $mnClassHasNnClassTableGateway->insert($set);
                }
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
