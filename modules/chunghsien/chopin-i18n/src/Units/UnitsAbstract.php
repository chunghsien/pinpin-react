<?php

namespace Chopin\I18n\Units;

use Laminas\Filter\Word\CamelCaseToUnderscore;

abstract class UnitsAbstract
{
    protected $shortNames;
    protected $longNames;
    protected $defult;

    /**
     *
     * @var int 四捨五入
     */
    const ROUND_UP = 1;
    /**
     *
     * @var int 無條件捨去
     */
    const ROUND_DOWN = 2;

    protected function getIndex(string $unit): int
    {
        $index = array_search($unit, $this->shortNames);

        if (false === $index) {
            $index = array_search($unit, $this->longNames);
        }
        return $index;
    }

    public function __construct(string $defaultUnit = '')
    {
        if ($defaultUnit) {
            $this->setDefault($defaultUnit);
        }
    }
    
    public function getShortNames()
    {
        return $this->shortNames;
    }
    
    public function getLongNames()
    {
        return $this->longNames;
    }
    
    public function setDefault(string $unitName)
    {
        $index = $this->getIndex($unitName);
        if (false !== $index) {
            $this->default = $this->longNames[$index];
        }
    }

    public function shortToLong(string $shortName): string
    {
        $shortName = strtolower($shortName);
        $index = array_search($shortName, $this->shortNames);
        if (false === $index) {
            return '';
        }
        return $this->longNames[$index];
    }

    public function longToShort(string $longName): string
    {
        $longName = strtolower($longName);
        $index = array_search($longName, $this->longNames);
        if (false === $index) {
            return '';
        }
        return $this->shortNames[$index];
    }

    /**
     *
     * @param string $ConvertUnit 單位可寫長名稱或短名稱(ex.mm or millimeter)
     * @param int $value
     * @param string $type 是否要顯示單位名稱(short or long or '')
     * @param int $decimals 顯示幾位數的小數點
     */
    public function defautlConvertToAny(string $ConvertUnit, int $value, string $type = '', int $decimals = 0, int $round = 0)
    {
        $index = array_search($ConvertUnit, $this->longNames);
        if (false === $index) {
            $index = array_search($ConvertUnit, $this->shortNames);
        }

        if (false === $index) {
            //找不到要轉換的單位名稱
            return '';
        }

        $ConvertUnit = $this->longNames[$index];
        $methodName = $this->default.'To'.$ConvertUnit;
        $this->{$methodName}($value, $type, $decimals, $round);
    }

    public function __call($name, $argments)
    {
        if (count($argments) >= 1 && count($argments) <= 4) {
            $name = str_replace('To', ',', $name);
            $nameArr = explode(',', $name);

            $use = $nameArr[0];
            $use = strtolower($use);
            $convertToName = (new CamelCaseToUnderscore())->filter($nameArr[1]);
            $convertToName = strtolower($convertToName);


            if (isset($this->{$use})) {
                //echo $convertToName.PHP_EOL;
                $values = $this->{$use};
                //$convertToName = str_replace('_', ' ', $convertToName);
                //var_export([$convertToName, $values]);
                if (isset($values[$convertToName])) {
                    if (extension_loaded('bcmath')) {
                        $_decimal = isset($argments[2]) ? $argments[2] : 0;
                        foreach ($this->{$use} as $value) {
                            $_d = 1 / $value;
                            $arr = explode('.', $_d);
                            if (count($arr) == 2) {
                                if (strlen($arr[1]) > $_decimal) {
                                    $_decimal = strlen($arr[1]);
                                }
                            }
                        }
                        $value = bcdiv($argments[0], $values[$convertToName], $_decimal);
                    //$value = floatval($value);
                    } else {
                        $value =  $argments[0] / $values[$convertToName];
                    }

                    $unit = '';

                    if (count($argments) >= 2 && (strtolower($argments[1]) == 'short' || strtolower($argments[1]) == 'long')) {
                        $type = $argments[1];
                        $var = $type."Names";
                        $showUnits = isset($this->{$var}) ? $this->{$var} : [];


                        if ($showUnits) {
                            $_convertToName = str_replace('_', ' ', $convertToName);
                            $index = array_search($_convertToName, $this->longNames);
                            if (false !== $index) {
                                $unit = $showUnits[$index];
                            }
                        }
                    }
                    if (count($argments) >= 3) {
                        if (count($argments) == 4 && intval($argments[3]) > 0) {
                            $value = round($value, intval($argments[2]), intval($argments[3]));
                        } else {
                            if (isset($argments[2]) && intval($argments[2])) {
                                $value = number_format($value, intval($argments[2]));
                            }
                        }
                    }


                    //$value = preg_replace('/0*$/', '', $value);
                    //$value = preg_replace('/\.$/', '', $value);
                    return ($value.$unit);
                }
            }
        }
        return false;
    }
}
