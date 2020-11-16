<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\Store\TableGateway\MpClassTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Store\TableGateway\FpClassHasMpClassTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;

class FpClassHasMpClassAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $mp_class_id = $params['mp_class_id'];
        $mpClassTableGateway = new MpClassTableGateway($this->adapter);
        $mpClassRow = $mpClassTableGateway->select([
            'id' => $mp_class_id
        ])->current();
        $fpClassHasMpClassScripts = require 'modules/App/scripts/db/admin/fpClassHasMpClass.php';
        $options = DB::selectFactory($fpClassHasMpClassScripts['options'], [
            'language_id' => $mpClassRow->language_id,
            'locale_id' => $mpClassRow->locale_id
        ])->toArray();
        
        $values = DB::selectFactory($fpClassHasMpClassScripts['defaultValue'], [
            'mp_class_id' => $mp_class_id,
        ])->toArray();
        
        return [
            'values' => [
                'fp_class' => $values,
            ],
            'options' => [
                'fp_class' => $options,
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
            $mp_class_id = $post['mp_class_id'];
            $fpClassHasMpClassTableGateway = new FpClassHasMpClassTableGateway($this->adapter);
            if($fpClassHasMpClassTableGateway->select(['mp_class_id' => $mp_class_id])->count()) {
                $fpClassHasMpClassTableGateway->delete(['mp_class_id' => $mp_class_id]);
            }
            $fp_class_ids = explode(',', $post['fp_class_id']);
            foreach ($fp_class_ids as $fp_class_id) {
                $set = [
                    'fp_class_id' => $fp_class_id,
                    'mp_class_id' => $mp_class_id
                ];
                $fpClassHasMpClassTableGateway->insert($set);
            }
            $this->adapter->getDriver()->getConnection()->commit();
            $data = $this->getOptions($request);
            return new ApiSuccessResponse(0, $data, [
                'update success'
            ]);
        } catch (\Exception $e) {
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
