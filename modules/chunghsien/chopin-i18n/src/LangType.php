<?php

namespace Chopin\I18n;

use Laminas\Filter\Word\DashToUnderscore;
use Laminas\Filter\Word\UnderscoreToDash;

abstract class LangType
{

    const PHP = 0;
    
    /**
     * ## 完整顯示中文語系及locale ex. zh_Hant_TW
     * @var integer
     */
    const PHP_FULL = 2;
    
    const HTML = 1;

    public static function get($lang, $use = 0)
    {
        if (intval($use) === self::PHP || intval($use) === self::PHP_FULL) {
            $dashToUnderscore = new DashToUnderscore();
            $lang = $dashToUnderscore->filter($lang);
            if(!preg_match('/hant_(hk|tw)$/i', $lang) && preg_match('/_(hk|tw)$/i', $lang)) {
                $lang = 'zh_Hant_TW';
            }
            if(!preg_match('/hans_cn$/i', $lang) && preg_match('/_cn$/i', $lang)) {
                $lang = 'zh_Hans_CN';
            }
            if(intval($use) !== self::PHP_FULL) {
                $lang = preg_replace('/_han(t|s)/i','', $lang);
                $lang = preg_replace('/\s{1}\w{2}$/i','', $lang);
            }
        } else {
            $underscoreToDash = new UnderscoreToDash();
            $lang = $underscoreToDash->filter($lang);
            if(!preg_match('/hant\-(hk|tw)$/i', $lang)) {
                if(!preg_match('/hant\-tw$/i', $lang)) {
                    $lang = 'zh-TW';
                }else {
                    $lang = 'zh-HK';
                }
            }else {
                if(!preg_match('/hans\-cn$/i', $lang)) {
                    $lang = 'zh-CN';
                }
            }
        }
        return $lang;
    }
}
