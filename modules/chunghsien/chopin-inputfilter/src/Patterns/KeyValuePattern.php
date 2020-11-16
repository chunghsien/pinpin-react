<?php

namespace Chopin\Inputfilter\Patterns;

use Laminas\Filter\Word\CamelCaseToUnderscore;

class KeyValuePattern
{
    protected $tables = [
        'users_profile',
        'attributes',
    ];

    public function reBuild($params)
    {
        foreach ($params as $key => $value) {
            if (false === array_search($key, $this->tables)) {
                continue;
            }
            $rebuild_data = [];
            $pattern = '/^(?P<head>_{1})\w+(?P<tail>_{1})$/';

            foreach ($value as $k => $v) {
                $matcher = [];
                if (preg_match($pattern, $k, $matcher)) {
                    $use_key = preg_replace('/^_/', '', $k);
                    $use_key = preg_replace('/_$/', '', $use_key);
                    $filter = new CamelCaseToUnderscore();
                    $use_key = strtolower($filter->filter($use_key));
                    $use_value = $v;
                    if (empty($rebuild_data['key'])) {
                        $rebuild_data['key'] = [];
                    }
                    if (empty($rebuild_data['value'])) {
                        $rebuild_data['value'] = [];
                    }

                    $rebuild_data['key'][] = $use_key;
                    $rebuild_data['value'][] = $use_value;
                }
            }
            if ($rebuild_data) {
                $params[$key]  = $rebuild_data;
            }
        }
        if ($rebuild_data) {
            //$params
            return array_merge($params, $rebuild_data);
        }
        return $params;
    }
}
