<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface;

use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

trait GridPutTrait
{

    public function gridPutProcess(ServerRequestInterface $request, AbstractTableGateway $tablegateway)
    {
        $set = json_decode($request->getBody()->getContents(), true);
        try {
            if (isset($set['rowId'])) {
                $field = $set['dataField'];
                $value = $set['newValue'];
                $data = [
                    $field => $value,
                ];
                $id = $set['rowId'];
                
                //注意要依照表的順序建立 ex.1-160 : (table:language_has_locale ,1:language_id, 160:locale_id)
                if(preg_match('/^\d+-\d+$/', $id)) {
                    $primarys = $tablegateway->getConstraintsObject('PRIMARY KEY')[0]->getColumns();
                    $where = [];
                    $ids = explode('-', $id);
                    foreach ($primarys as $k => $c) {
                        $where[$c] = $ids[$k];
                    }
                }else {
                    $where = [
                        'id' => $id
                    ];
                }
                $tablegateway->update($data, $where);
            } else {
                $id = $set['id'];
                if (isset($set['sort']) && $set['sort'] == '') {
                    $set['sort'] = 16777215;
                }
                if (isset($set['language_has_locale'])) {
                    $language_has_locale = json_decode($set['language_has_locale']);
                    unset($set['language_has_locale']);
                    $set['language_id'] = $language_has_locale->language_id;
                    $set['locale_id'] = $language_has_locale->locale_id;
                }
                $tablegateway->update($set, [
                    'id' => $id
                ]);
            }

            return new ApiSuccessResponse(0, $set, [
                'update success'
            ]);
        } catch (\Exception $e) {
            loggerException($e);
            return new ApiErrorResponse(1, isset($set) ? $set : [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
        }
    }

    public function gridPutVerify(ServerRequestInterface $request)
    {
        $set = json_decode($request->getBody()->getContents(), true);
        return isset($set['rowId']) && isset($set['dataField']) && isset($set['newValue']);
    }
}