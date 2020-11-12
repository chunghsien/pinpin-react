<?php

/**
 *
 ** 超商取貨付款
 */

namespace Chopin\Store\Logistics;

use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Chopin\Store\TableGateway\LogisticsGlobalTableGateway;
use Laminas\Db\Sql\Where;
use Laminas\Db\ResultSet\ResultSet;

/**
 * @author User
 *
 */
abstract class AbstractLogistics
{
    const HOME_DELIVERY_KEY = 'home_delivery';
    const CVS_PICKUP_PAID_KEY = 'cvs_pickup_paid';
    const CVS_PICKUP_NOT_PAID_KEY = 'cvs_pickup_not_paid';

    protected $method = [
        'home_delivery',
        'cvs_pickup_paid',
        'cvs_pickup_no_paid',
    ];

    /**
     * *預設的超商運費金額(依照服務商公告為主)
     *
     * @var array
     */
    public $global_cvs_fee;

    public $global_home_delivert_fee;

    public static function buildLogisticsOptions()
    {
        $options = [];
        $options[] = [
            'value' => self::HOME_DELIVERY_KEY,
            'label' => translator(self::HOME_DELIVERY_KEY, 'chopin-store'),
        ];
        $options[] = [
            'value' => self::CVS_PICKUP_PAID_KEY,
            'label' => translator(self::CVS_PICKUP_PAID_KEY, 'chopin-store'),
        ];
        $options[] = [
            'value' => self::CVS_PICKUP_NOT_PAID_KEY,
            'label' => translator(self::CVS_PICKUP_NOT_PAID_KEY, 'chopin-store'),
        ];
        return $options ;
    }

    /**
     *
     * @param string $lang
     * @param array $disable_methods
     * @return array
     */
    abstract public function output($lang='zh_Hant', $disable_methods = []);


    /**
     *
     * @return array
     */
    protected function getDbLogistics($lang, $disable_methods = [])
    {
        $output_methods = array_diff($this->method, $disable_methods);
        $adapter = GlobalAdapterFeature::getStaticAdapter();

        $logisticsGlobalTableGateway = new LogisticsGlobalTableGateway($adapter);
        $pt = LogisticsGlobalTableGateway::$prefixTable;
        $logisticsGlobalTableName = $logisticsGlobalTableGateway->table;
        $languageTableName = $pt.'language';

        $where = new Where();
        $where->in($logisticsGlobalTableName.'.type', $output_methods);
        $where->isNull($logisticsGlobalTableName.'.deleted_at');
        $where->equalTo($logisticsGlobalTableName.'.is_use', 1);
        $where->equalTo($languageTableName.'.code', $lang);
        $select = $logisticsGlobalTableGateway->getSql()->select();
        $select->order($logisticsGlobalTableName.'.sort ASC');
        $select->columns(['id', 'type', 'name', 'price']);
        $select->where($where);
        $select->join($languageTableName, $logisticsGlobalTableName.'.language_id='.$languageTableName.'.id', ['code'], 'left');
        $dataSource = $logisticsGlobalTableGateway->getSql()->prepareStatementForSqlObject($select)->execute();
        if ($dataSource->count() == 0) {
            $select->reset('where');
            $where = new Where();
            $where->isNull($logisticsGlobalTableName.'.deleted_at');
            $where->equalTo($logisticsGlobalTableName.'.is_use', 1);
            $where->equalTo('language_id', 0);
            $where->equalTo('locale_id', 0);
            $select->where($where);
            $dataSource = $logisticsGlobalTableGateway->getSql()->prepareStatementForSqlObject($select)->execute();
        }
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        $response = [];
        foreach ($resultSet as $row) {
            $key = $row['type'];
            $response[$key] = $row;
        }
        return $response;
    }
}
