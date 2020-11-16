<?php

namespace Chopin\Documents\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\DB\Select;
use Laminas\Db\Sql\Where;

class DocumentsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'documents';

    public function getNavTree($language_id = 0, $locale_id = 0)
    {
        $grandSelect = new Select($this->table);
        $grandWhere = new Where();
        $grandWhere->like('allowed_methods', '%GET%');
        $grandWhere->equalTo('parent_id', 0);
        $grandSelect->columns([
            'id','parent_id','name','route'
        ])
            ->order([
            'sort ASC','id DESC'
        ])
            ->where($grandWhere);
        $grandResult = $grandSelect->get()->toArray();
        foreach ($grandResult as &$gitem) {
            $fartherSelect = new Select($this->table);
            $fartherWhere = new Where();
            $fartherWhere->like('allowed_methods', '%GET%');
            $fartherWhere->equalTo('parent_id', $gitem['id']);
            $fartherSelect->columns([
                'id','parent_id','name','route'
            ])
                ->order([
                'sort ASC','id DESC'
            ])
                ->where($fartherWhere);
            $fatherResult = $fartherSelect->get()->toArray();
            if (count($fatherResult)) {
                foreach ($fatherResult as &$fitem) {
                    $sunSelect = new Select($this->table);
                    $sunWhere = new Where();
                    $sunWhere->like('allowed_methods', '%GET%');
                    $sunWhere->equalTo('parent_id', $fitem['id']);
                    $sunSelect->columns([
                        'id','parent_id','name','route'
                    ])
                        ->order([
                        'sort ASC','id DESC'
                    ])
                        ->where($sunWhere);
                    $sunResult = $fartherSelect->get()->toArray();
                    if (count($sunResult)) {
                        $fitem['child'] = $sunResult;
                    }
                }
                $gitem['child'] = $fatherResult;
            }
        }
        return $grandResult;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\LaminasDb\TableGateway\AbstractTableGateway::insert()
     */
    public function insert($set)
    {
        $keys = array_keys($set);
        if (is_int($keys[0])) {
            foreach ($set as &$value) {
                $value = $this->processValues($value);
            }
        } else {
            $set = $this->processValues($set);
        }
        return parent::insert($set);
    }

    protected function processValues($values)
    {
        if (empty($values['language_id'])) {
            return $values;
        }
        $language_id = $values['language_id'];
        /**
         *
         * @var \Chopin\LanguageHasLocale\TableGateway\LanguageTableGateway $languageTablegateway
         */
        $languageTablegateway = $this->newInstance('language', $this->adapter);
        $row = $languageTablegateway->select([
            'id' => intval($language_id)
        ])->current();
        $code = $languageTablegateway->getSimpleChineseCode($row->code);
        if (isset($values['route'])) {
            if (! preg_match('/^\/' . $code . '/', $values['route'])) {
                $values['route'] = preg_replace('/^\/' . $code . '\//', '', $values['route']);
                $values['route'] = '/' . $code . '/' . $values['route'];
            }
        }
        return $values;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\LaminasDb\TableGateway\AbstractTableGateway::update()
     */
    public function update($set, $where = null, array $joins = null)
    {
        $set = $this->processValues($set);
        return parent::update($set, $where, $joins);
    }
}
