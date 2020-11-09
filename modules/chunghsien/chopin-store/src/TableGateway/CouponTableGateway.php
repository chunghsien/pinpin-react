<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Math\Rand;
use Chopin\Store\CouponRule\FreeShippingCouponRule;
use Laminas\Db\Sql\Where;
use Chopin\Support\Registry;
use Laminas\ServiceManager\ServiceManager;
use function class_exists;
use Mezzio\Session\SessionPersistenceInterface;

class CouponTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'coupon';

    protected $use_type = [
        'deduct_amount',
        'percent_off_tw',
        //'target_amount',
        // 'auto_trigger',
        'rule_object',
    ];

    protected $limit_type = [
        'all_member',
        //'assign_member',
        //'member_quota'
    ];

    public $userSessionKey = 'member';

    /**
     *
     * @param array $coupon
     * @param number $subtotal
     * @return boolean
     */
    public function verifyCoupon($coupon, $subtotal)
    {
        if ($coupon['use_type'] == 'deduct_amount' && floatval($subtotal) > floatval($coupon['target_value'])) {
            return true;
        }
        if ($coupon['use_type'] == 'percent_off_tw' && floatval($subtotal) > floatval($coupon['target_value'])) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param array $coupons
     * @param number $subtotal
     * @return number
     */
    public function calCoupon($coupons, $subtotal)
    {
        $amount = 0;
        foreach ($coupons as $index => $co) {
            //只先實做所有會員
            if ($co['use_type'] == 'deduct_amount' && floatval($subtotal) > floatval($co['target_value'])) {
                $amount += floatval($co['use_value']);
                continue;
            }
            if ($co['use_type'] == 'deduct_amount' && floatval($subtotal) < floatval($co['target_value'])) {
                unset($coupons[$index]);
                $this->updateCouponsToSession($coupons);
            }

            if ($co['use_type'] == 'percent_off_tw' && floatval($subtotal) > floatval($co['target_value'])) {
                $off_value = (100 - intval($co['use_value'])) / 100;
                $amount += $subtotal * $off_value;
                continue;
            }
            if ($co['use_type'] == 'percent_off_tw' && floatval($subtotal) < floatval($co['target_value'])) {
                unset($coupons[$index]);
                $this->updateCouponsToSession($coupons);
            }
        }
        return $amount;
    }

    public function updateCouponsToSession($coupons)
    {
        /**
         *
         * @var ServiceManager $serviceManager
         */
        $serviceManager = Registry::get(ServiceManager::class);
        $verify = $serviceManager->has(SessionPersistenceInterface::class);
        if ($verify) {
            $session = $serviceManager->get(SessionPersistenceInterface::class);
            $session->set('coupon', $coupons);
        } else {
            $_SESSION['coupon'] = $coupons;
        }
    }
    /**
     *
     * @param array $values
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function insert($values)
    {
        $keys = array_keys($values);
        if (is_int($keys[0])) {
            foreach ($values as &$value) {
                $value['code'] = $this->generalCode();
                $start = trim($value['start']);
                if (strlen($start) == 10) {
                    $value['start'] = $start . ' 00:00:00';
                }
                $expiration = trim($value['expiration']);
                if (strlen($expiration) == 10) {
                    $value['expiration'] = $expiration . ' 23:59:59';
                }
            }
        } else {
            $values['code'] = $this->generalCode();
            $start = trim($values['start']);
            if (strlen($start) == 10) {
                $values['start'] = $start . ' 00:00:00';
            }

            $expiration = trim($values['expiration']);
            if (strlen($expiration) == 10) {
                $values['expiration'] = $expiration . ' 23:59:59';
            }
        }
        return parent::insert($values);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\LaminasDb\TableGateway\AbstractTableGateway::update()
     */
    public function update($set, $predicate = null, array $join = null)
    {
        if(isset($set['start'])) {
            $start = trim($set['start']);
            if (strlen($start) == 10) {
                $set['start'] = $start . ' 00:00:00';
            }
        }
        if(isset($set['expiration'])) {
            $expiration = trim($set['expiration']);
            if (strlen($expiration) == 10) {
                $set['expiration'] = $expiration . ' 23:59:59';
            }
        }

        return parent::update($set, $predicate, $join);
    }

    /**
     **系統產生折扣碼
     *
     * @return string
     */
    protected function generalCode()
    {
        $code = Rand::getString(10, 'abcdefghijklmnopqrstuvwxyz1234567890');
        if ($this->select([
            'code' => $code,
        ])->count()) {
            return $this->generalCode();
        }
        return $code;
    }

    /**
     *
     * @param boolean $include_rule_object
     * @return array
     */
    public function getUseTypeOption($include_rule_object = false)
    {
        $options = [];
        foreach ($this->use_type as $ut) {
            $label = translator($ut, 'chopin-store');
            if ($ut == 'rule_object') {
                if ($include_rule_object == false) {
                    continue;
                }
                $options[] = [
                    'value' => $ut,
                    'label' => $label,
                ];
            } else {
                $options[] = [
                    'value' => $ut,
                    'label' => $label,
                ];
            }
        }
        return $options;
    }

    /**
     *
     * @param string $locale
     * @return array
     */
    public function getLimitTypeOption()
    {
        $options = [];
        foreach ($this->limit_type as $ut) {
            $label = translator($ut, 'chopin-store');
            $options[] = [
                'value' => $ut,
                'label' => $label,
            ];
        }
        return $options;
    }

    /**
     *
     * @param number $subtotal
     * @return number
     */
    public function getGlobalFreeShippingFee($subtotal)
    {
        $where = new Where();
        $where->equalTo('rule_object', FreeShippingCouponRule::class);
        $where->greaterThanOrEqualTo('expiration', date("Y-m-d H:i:s"));
        $resultSet = $this->select($where);
        if ($resultSet->count()) {
            $row = $resultSet->current();
            $target_value = $row->target_value;
            $freeShippingCouponRule = new FreeShippingCouponRule();
            return $freeShippingCouponRule->getValue($subtotal, $target_value);
        }
        return PHP_INT_MAX;
    }

    /**
     *
     * @param array $couponItem
     * @return array|boolean
     */
    public function storeCouponRepository($couponItem = [])
    {
        $userSessionKey = $this->userSessionKey;
        /**
         *
         * @var ServiceManager $serviceManager
         */
        $serviceManager = Registry::get(ServiceManager::class);
        $verify = $serviceManager->has(SessionPersistenceInterface::class);
        if ($verify) {
            /**
             *
             * @var SessionPersistenceInterface $session
             */
            $session = $serviceManager->get(SessionPersistenceInterface::class);
            if ( ! $session->has('coupon')) {
                $session->set('coupon', []);
            }
            $couponRepository = $session->get('coupon');
            if ($session->has($userSessionKey)) {
                $memberRepository = $session->get($userSessionKey);
            }
        } else {
            if (empty($_SESSION['coupon'])) {
                $_SESSION['coupon'] = [];
            }
            $couponRepository = $_SESSION['coupon'];
            if (isset($_SESSION[$userSessionKey])) {
                $memberRepository = $_SESSION[$userSessionKey];
            }
        }
        $code = $couponItem['code'];
        if (empty($couponRepository[$code])) {
            $couponRepository[$code] = $couponItem;
        }

        $userVerify = false;
        if ($verify) {
            $session->set('coupon', $couponRepository);
            $userVerify = $session->has('coupon');
        } else {
            $_SESSION['coupon'] = $couponRepository;
            $userVerify = isset($_SESSION[$userSessionKey]);
        }
        if ($userVerify && isset($memberRepository)) {
            //驗證是否使用過coupon券
            $usersHasCouponTableGateway = new UsersHasCouponTableGateway($this->adapter);
            $where = new Where();
            $where->equalTo('users_id', $memberRepository['id']);
            $minValue = $couponItem['start'];
            $maxValue = $couponItem['expiration'];
            $where->between('created_at', $minValue, $maxValue);
            if ($usersHasCouponTableGateway->select($where)->count()) {
                unset($couponRepository[$code]);
                if ($serviceManager->has(SessionPersistenceInterface::class)) {
                    $session->set('coupon', $couponRepository);
                } else {
                    $_SESSION['coupon'] = $couponRepository;
                }
                return -1;
            } else {
                return $couponRepository;
            }
        }
        return false;
    }
}
