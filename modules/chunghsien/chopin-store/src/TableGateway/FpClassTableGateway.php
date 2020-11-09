<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Where;
use Chopin\LaminasDb\DB\Select;

class FpClassTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'fp_class';

    public function getProdsClassTree($language_id=0, $locale_id=0)
    {
        $fpcWhere = new Where();
        $fpcWhere->equalTo('language_id', $language_id);
        $fpcWhere->equalTo('locale_id', $locale_id);
        $fpcWhere->isNull('deleted_at');
        $fpcSelect = new Select($this->table);
        $fpcSelect->columns(['id', 'name'])->where($fpcWhere)->order(['sort ASC', 'id ASC']);
        $fpcResultSet = $fpcSelect->get();
        //$classTree = [];
        $pt = self::$prefixTable;
        if ($fpcResultSet->count()) {
            $fpcArr = $fpcResultSet->toArray();
            foreach ($fpcArr as &$item) {
                $select = new Select($pt.'fp_class_has_mp_class');
                $select->columns([])
                ->join($pt.'mp_class', $pt.'fp_class_has_mp_class.mp_class_id='.$pt.'mp_class.id', ['id', 'name'])
                ->where(['fp_class_id' => $item['id']])->order([$pt.'mp_class.sort ASC', $pt.'mp_class.id ASC']);
                $resultSet = $select->get();
                if ($resultSet->count()) {
                    $item['child'] = [];
                } else {
                    $mpResult = $resultSet->toArray();
                    foreach ($mpResult as &$mpItem) {
                        $select = new Select($pt.'mp_class_has_np_class');
                        $select->columns([])
                        ->join($pt.'np_class', $pt.'mp_class_has_np_class.np_class_id='.$pt.'np_class.id', ['id', 'name'])
                        ->where(['mp_class_id' => $mpItem['id']])->order([$pt.'np_class.sort ASC', $pt.'np_class.id ASC']);
                        ;
                        $resultSet = $select->get();
                        if ($resultSet->count()) {
                            $mpItem['child'] = $resultSet->toArray();
                        } else {
                            $mpItem['child'] = [];
                        }
                    }
                    $item['child'] = $mpResult;
                }
            }
            return $fpcArr;
        }
        $mpcWhere = new Where();
        $mpcWhere->equalTo('language_id', $language_id);
        $mpcWhere->equalTo('locale_id', $locale_id);
        $mpcWhere->isNull('deleted_at');
        $mpcSelect = new Select($pt.'mp_class');
        $mpcSelect->columns(['id', 'name'])->where($fpcWhere)->order(['sort ASC', 'id ASC']);
        $mpcResultSet = $fpcSelect->get();
        if ($mpcResultSet->count()) {
            $mpResult = $mpcResultSet->toArray();
            foreach ($mpResult as &$mpItem) {
                $select = new Select('mp_class_has_np_class');
                $select->columns([])
                ->join($pt.'np_class', $pt.'mp_class_has_np_class.np_class_id='.$pt.'np_class.id', ['id', 'name'])
                ->where(['mp_class_id' => $mpItem['id']])->order([$pt.'np_class.sort ASC', $pt.'np_class.id ASC']);
                ;
                $resultSet = $select->get();
                if ($resultSet->count()) {
                    $mpItem['child'] = $resultSet->toArray();
                } else {
                    $mpItem['child'] = [];
                }
            }
            return $mpResult;
        }
        $npcWhere = new Where();
        $npcWhere->equalTo('language_id', $language_id);
        $npcWhere->equalTo('locale_id', $locale_id);
        $npcWhere->isNull('deleted_at');
        $npcSelect = new Select($pt.'np_class');
        $npcSelect->columns(['id', 'name'])->where($fpcWhere)->order(['sort ASC', 'id ASC']);
        $npcResultSet = $npcSelect->get();
        return $npcResultSet->toArray();
    }
}
