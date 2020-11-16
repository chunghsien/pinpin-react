<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\MpClassHasNpClassTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;

class MpClassHasNpClassAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $np_class_id = $params['np_class_id'];
        $npClassTableGateway = new NpClassTableGateway($this->adapter);
        $mpClassRow = $npClassTableGateway->select([
            'id' => $np_class_id
        ])->current();
        $mpClassHasNpClassScripts = require 'src/App/scripts/db/admin/mpClassHasNpClass.php';
        $options = DB::selectFactory($mpClassHasNpClassScripts['options'], [
            'language_id' => $mpClassRow->language_id,
            'locale_id' => $mpClassRow->locale_id
        ])->toArray();
        
        $values = DB::selectFactory($mpClassHasNpClassScripts['defaultValue'], [
            'np_class_id' => $np_class_id,
        ])->toArray();
        
        return [
            'values' => [
                'mp_class' => $values,
            ],
            'options' => [
                'mp_class' => $options,
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
            $np_class_id = $post['np_class_id'];
            $mpClassHasNpClassTableGateway = new MpClassHasNpClassTableGateway($this->adapter);
            if($mpClassHasNpClassTableGateway->select(['np_class_id' => $np_class_id])->count()) {
                $mpClassHasNpClassTableGateway->delete(['np_class_id' => $np_class_id]);
            }
            $mp_class_ids = explode(',', $post['mp_class_id']);
            foreach ($mp_class_ids as $mp_class_id) {
                $set = [
                    'mp_class_id' => $mp_class_id,
                    'np_class_id' => $np_class_id
                ];
                $mpClassHasNpClassTableGateway->insert($set);
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
