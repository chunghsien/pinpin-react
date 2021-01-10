<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Predicate\IsNull;
use Laminas\Db\Sql\Expression;

class NpClassTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'np_class';

    public function getLezadaCategory($language_id, $locale_id, $code)
    {
        $select = $this->getSql()->select();
        $select->columns([
            'id',
            'image',
            'name',
            'count' => 'viewed_count',
            "url" => new Expression("CONCAT('/{$code}/shop/left-sidebar')"),
        ]);
        $select->where([
            'language_id' => $language_id,
            'locale_id' => $locale_id,
            new IsNull('deleted_at'),
        ]);
        return $this->selectWith($select)->toArray();
    }
}
