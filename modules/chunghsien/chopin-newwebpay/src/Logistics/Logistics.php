<?php

/**
 * * 超商取貨付款
 */

namespace Chopin\Newwebpay\Logistics;

use Chopin\Store\Logistics\AbstractLogistics;

class Logistics extends AbstractLogistics
{
    public $global_cvs_fee = 60;

    public $global_home_delivert_fee = 60;

    /**
     *
     * @param string $lang
     * @param array $disable_methods
     * @return array
     */
    public function output($lang = 'zh_Hant', $disable_methods = [])
    {
        $output_data = [];
        $resources = $this->getDbLogistics($lang, $disable_methods);
        foreach ($resources as $value => $row) {
            $output_data[] = [
                "id" => $row['id'], "label" => $row['name'], "value" => $value, "price" => $row['price'],
            ];
        }
        return $output_data;
    }
}
