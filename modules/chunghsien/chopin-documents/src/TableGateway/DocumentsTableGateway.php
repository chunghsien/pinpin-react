<?php

namespace Chopin\Documents\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\RowGateway\RowGatewayInterface;

class DocumentsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'documents';

    public function getLayoutUse( RowGatewayInterface $layoutRow, $categoryNotIn = [], $normalNotIn = [])
    {
        $categorySelect = $this->sql->select();
        $categoryWhere = $categorySelect->where;
        $categoryWhere->like('route', '%category');
        $categoryWhere->equalTo('language_id', $layoutRow->language_id);
        $categoryWhere->equalTo('locale_id', $layoutRow->locale_id);
        if($categoryNotIn) {
            $categoryWhere->notIn('route', $categoryNotIn);
        }
        $categorySelect->where($categoryWhere);
        $categoryResult = $this->selectWith($categorySelect)->toArray();

        $select = $this->sql->select();
        $where = $select->where;
        $where->isNull('deleted_at');
        $where->equalTo('language_id', $layoutRow->language_id);
        $where->equalTo('locale_id', $layoutRow->locale_id);
        $where->notLike('route', '%category');
        $where->notLike('route', '%/product');
        $where->notLike('route', '%/news');
        if($normalNotIn) {
            $where->notIn('route', $normalNotIn);
        }
        $select->where($where);
        $normalResult = $this->selectWith($select)->toArray();
        return [
            'normal' => $normalResult,
            'category' => $categoryResult
        ];
    }
}
