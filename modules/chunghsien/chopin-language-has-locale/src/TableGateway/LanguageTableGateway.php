<?php

namespace Chopin\LanguageHasLocale\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\DB;

class LanguageTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'language';

    public function getSimpleChineseCode($tradCode)
    {
        if (defined('SIMPLE_CHINESE_LANGUAGE_CODE') && SIMPLE_CHINESE_LANGUAGE_CODE == 1) {
            if (strtolower($tradCode) == 'zh_hant') {
                return 'tw';
            }
            if (strtolower($tradCode) == 'zh_hans') {
                return 'cn';
            }
        }
        return $tradCode;
    }

    public function getReleationOptions($table, $valueField = 'id', $lableField = 'name', $dataAttrs = [], $predicateParams = [])
    {
        $tableGateway = $this->newInstance($table, $this->adapter);
        $data = $tableGateway->getOptions($valueField, $lableField, $dataAttrs, $predicateParams);
        foreach ($data as &$item) {
            $label = $item['label'];
            $where = [
                ['equalTo', 'and', [$tableGateway->table.'.'.$valueField, $item['value']]],
                ['equalTo', 'and', [$this->table.'.is_use', 1]],
            ];
            $relationColumns = $tableGateway->getColumns();
            if (false !== array_search('deleted_at', $relationColumns)) {
                $where[] = ['isNull', 'and', [$tableGateway->table.'.deleted_at']];
            }
            $relation_item = DB::selectFactory([
                'form' => $table,
                'join' => [
                    [
                        self::$prefixTable.'language',
                        $tableGateway->table.'.language_id='.$this->table.'.id',
                        ['display_name'],
                    ],
                ],
                'where' => $where,
            ])->current();
            $item['label'] = $relation_item['display_name'].' / '. $label;
        }
        return $data;
    }
}
