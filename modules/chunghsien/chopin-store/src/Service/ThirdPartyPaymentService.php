<?php

namespace Chopin\Store\Service;

use Chopin\I18n\LangType;
use Chopin\Store\Logistics\AbstractLogistics;
use Chopin\Store\Logistics\AbstractPayMethods;
use Chopin\Store\TableGateway\LogisticsGlobalTableGateway;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\Sql\Where;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\Store\TableGateway\CartTableGateway;
use Chopin\Store\TableGateway\CouponTableGateway;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use Chopin\Store\TableGateway\OrderDetailTableGateway;
use Laminas\Diactoros\ServerRequest;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Chopin\Store\TableGateway\OrderTableGateway;
use Chopin\Store\TableGateway\ThirdPartyPayResponseTableGateway;
use Laminas\Db\Sql\Expression;

abstract class ThirdPartyPaymentService
{

    /**
     *
     * @var AbstractLogistics
     */
    protected $logistics;

    /**
     *
     * @var LogisticsGlobalTableGateway
     */
    protected $logisticsTableGateway;

    /**
     *
     * @var CartTableGateway
     */
    protected $cartTableGateway;

    /**
     *
     * @var CouponTableGateway
     */
    protected $couponTableGateway;

    /**
     *
     * @var SystemSettingsTableGateway
     */
    protected $systemSettingTableGateway;

    /**
     *
     * @var OrderTableGateway
     */
    protected $orderTableGateway;

    /**
     *
     * @var OrderDetailTableGateway
     */
    protected $orderDetailTableGateway;

    /**
     *
     * @var ProductsTableGateway
     */
    protected $productsTableGateway;

    /**
     *
     * @var ProductsSpecTableGateway
     */
    protected $productsSpecTableGateway;

    /**
     *
     * @var ThirdPartyPayResponseTableGateway
     */
    protected $thirdPartyPayResponseTableGateway;

    /**
     *
     * @var AbstractPayMethods
     */
    protected $payMethods;

    protected $env = 'test';

    protected $lang_code;

    protected $simple_lang_code;

    const TEST = 'test';

    const PRODUCTION = 'production';

    public function __construct($lang_code, $simple_lang_code)
    {
        $this->lang_code = $lang_code;
        $this->simple_lang_code = $simple_lang_code;
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $this->logisticsTableGateway = new LogisticsGlobalTableGateway($adapter);
        $this->cartTableGateway = new CartTableGateway($adapter);
        $this->couponTableGateway = new CouponTableGateway($adapter);
        $this->systemSettingTableGateway = new SystemSettingsTableGateway($adapter);
        $this->orderDetailTableGateway = new OrderDetailTableGateway($adapter);
        $this->productTableGateway = new ProductsTableGateway($adapter);
        $this->productsSpecTableGateway = new ProductsSpecTableGateway($adapter);
        $this->orderTableGateway = new OrderTableGateway($adapter);
        $this->thirdPartyPayResponseTableGateway = new ThirdPartyPayResponseTableGateway($adapter);
        $this->setPayMetohds();
        $this->setLogistics();
        $this->setConfigKeys();
    }

    /**
     *
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function cleanCart()
    {
        $guest_serial_data = $this->cartTableGateway->getGuestSerial();
        $where = new Where();
        $where->equalTo('guest_serial', $guest_serial_data['serial']);
        return $this->cartTableGateway->delete($where);
    }

    abstract protected function setPayMetohds();

    abstract protected function setLogistics();

    abstract public function buildMpgForm(ServerRequest $request, $data);

    protected $configKeys = [];

    abstract protected function setConfigKeys();

    /**
     *
     * @param string $key
     * @return array
     */
    protected function getConfig($key)
    {
        // $key = 'newwebpay';
        $systemSettings = $this->systemSettingTableGateway->toSerialize();
        $options = $systemSettings[$key]['children'];
        $response = [];
        foreach ($this->configKeys as $key) {
            $response[$key] = $options[$key]['value'];
        }
        return $response;
    }

    protected function saveNotifyLog($data, $post)
    {
        $notifyResult = $data['Result'];
        $orderRow = $this->orderTableGateway->select(['serial' => $notifyResult['MerchantOrderNo'] ])->current();
        $to = json_encode($data, JSON_UNESCAPED_UNICODE);
        $count = $this->thirdPartyPayResponseTableGateway->select(['order_id' => $orderRow->id])->count();
        if ($count == 0) {
            $this->thirdPartyPayResponseTableGateway->insert([
                'order_id' => $orderRow->id,
                'response' => $to,
                'status' => $post['Status'],
                'message' => isset($data['Message']) ? $data['Message'] : new Expression('NULL'),
            ]);
            $pay_log_foder = 'storage/log/pay';
            if ( ! is_dir($pay_log_foder)) {
                mkdir($pay_log_foder, 0755, true);
            }
            $path = $pay_log_foder . '/' . date("Ymd") . '.json';
            if ( ! is_file($path)) {
                file_put_contents($path, '[' . PHP_EOL . ']');
            }
            $content = file_get_contents($path);
            $content = preg_replace('/\]$/', "    " . $to . ',' . PHP_EOL . ']', $content);
            return file_put_contents($path, $content);
        }
    }

    abstract public function notify(ServerRequest $request, $product_table='products_spec');

    public function getCvsMethods()
    {
        return $this->payMethods->getCvsMethods();
    }

    public function getMethod($index)
    {
        return $this->payMethods[$index];
    }

    public function getLangType()
    {
        return LangType::get($this->lang_code);
    }

    /**
     * *取得運費
     *
     * @param int $logistics_id logistics表的id
     * @param int $good_id 商品另行設定的id
     * @return number
     */
    public function getLogisticsFee($logistics_id, $good_id = 0)
    {
        $where = new Where();
        $where->equalTo('id', $logistics_id);
        $where->equalTo('is_use', 1);
        $row = $this->logisticsTableGateway->select($where)->current();
        return floatval($row->price);
    }

    /**
     *
     * @return array
     */
    public function getPayMethodOptions($language_id=0, $locale_id = 0)
    {
        $payMethods = $this->payMethods->getPayMethodOptions($language_id, $locale_id);
        /*
        $subtotalArr = $this->cartTableGateway->subTotal();
        $free_shipping_fee = $this->couponTableGateway->getGlobalFreeShippingFee($subtotalArr['data']['subtotal']);
        $logistics = $this->buildLogistictOptions($free_shipping_fee === 0, $language_id, $locale_id);
        foreach ($payMethods as &$pay) {
            $mapperClone = json_decode($pay['mapper'], true);
            $logisticsItems = [];
            foreach ($mapperClone as $mapper) {
                foreach ($logistics as $logistic) {
                    if ($logistic['type'] == $mapper) {
                        $logisticsItems[] = $logistic;
                    }
                }
            }
            $pay['mapper'] = json_encode($logisticsItems);
        }
        */
        return $payMethods;
    }

    /**
     * * 運費的options資料來源
     *
     * @param bool $isFreeShipping
     * @param int $language_id
     * @param int $locale_id
     * @return array
     */
    public function buildLogistictOptions($isFreeShipping = false, $language_id, $locale_id = 0)
    {
        // $mappers = $this->payMethods->payMethodMapperLogistics;
        $where = new Where();
        if ($language_id > 0) {
            $where->equalTo('language_id', $language_id);
        }
        $where->equalTo('locale_id', $locale_id);
        $select = $this->logisticsTableGateway->getSql()
            ->select()
            ->order('sort ASC')
            ->where($where);
        $dataSource = $this->logisticsTableGateway->getSql()
            ->prepareStatementForSqlObject($select)
            ->execute();
        if ($dataSource->count() == 0) {
            $where = new Where();
            $where->equalTo('language_id', 0);
            $where->equalTo('locale_id', 0);
            $select = $this->logisticsTableGateway->getSql()
                ->select()
                ->order('sort ASC')
                ->where($where);
            $dataSource = $this->logisticsTableGateway->getSql()
                ->prepareStatementForSqlObject($select)
                ->execute();
        }
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        $logistics = [];
        foreach ($resultSet as $row) {
            $price = floatval($row->price);
            if ($isFreeShipping) {
                $price = 0;
            }
            $logistics[] = [
                "value" => $row->id,
                "type" => $row->type,
                "label" => $row->name,
                "price" => $price,
            ];
        }
        return $logistics;
    }

    public function setEnv($env)
    {
        switch (strtolower($env)) {
            case self::PRODUCTION:
                $this->env = $env;
                break;
            case self::TEST:
                $this->env = $env;
                break;
            default:
                $this->env = self::TEST;
        }
    }
}
