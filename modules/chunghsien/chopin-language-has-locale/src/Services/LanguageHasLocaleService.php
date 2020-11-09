<?php

namespace Chopin\LanguageHasLocale\Services;

use Laminas\Db\Sql\Predicate\Operator;
use Laminas\Db\RowGateway\RowGateway;
use Chopin\LanguageHasLocale\TableGateway\CountriesFlagTableGateway;
use Chopin\LanguageHasLocale\TableGateway\CurrenciesTableGateway;
use Chopin\LanguageHasLocale\TableGateway\I18nTranslationTableGateway;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Chopin\LanguageHasLocale\TableGateway\LanguageTableGateway;
use Chopin\LanguageHasLocale\TableGateway\LocaleTableGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\I18n\Translator\Translator;
use Chopin\LaminasDb\DB;

class LanguageHasLocaleService
{
    /**
     *
     * @var CountriesFlagTableGateway
     */
    protected $countriesFlagTableGateway;

    /**
     *
     * @var CurrenciesTableGateway
     */
    protected $currenciesTableGateway;

    /**
     *
     * @var I18nTranslationTableGateway
     */
    protected $i18nTranslationTableGateway;

    /**
     *
     * @var LanguageHasLocaleTableGateway
     */
    protected $languageHasLocaleTableGateway;

    /**
     *
     * @var LanguageTableGateway
     */
    protected $languageTableGateway;

    /**
     *
     * @var LocaleTableGateway
     */
    protected $localeTableGateway;

    public function __construct(
        CountriesFlagTableGateway $countriesFlagTableGateway,
        CurrenciesTableGateway $currenciesTableGateway,
        I18nTranslationTableGateway $i18nTranslationTableGateway,
        LanguageHasLocaleTableGateway $languageHasLocaleTableGateway,
        LanguageTableGateway $languageTableGateway,
        LocaleTableGateway $localeTableGateway
    ) {
        $this->countriesFlagTableGateway = $countriesFlagTableGateway;
        $this->currenciesTableGateway = $currenciesTableGateway;
        $this->i18nTranslationTableGateway = $i18nTranslationTableGateway;
        $this->languageHasLocaleTableGateway = $languageHasLocaleTableGateway;
        $this->languageTableGateway = $languageTableGateway;
        $this->localeTableGateway = $localeTableGateway;
    }

    /**
     *
     * @param AbstractTableGateway $name
     */
    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     *
     * @param array $columns
     * @param boolean $is_use_check 一般用於middleway的判斷使用，如果要用於後台列表使用可以設為false
     * @return \Laminas\Db\ResultSet\ResultSet
     */
    public function getUseLanguage(
        $columns = ['*'],
        $is_use_check = true
    ) {
        $where = [];
        if ($is_use_check) {
            $where = [
                ['equalTo', 'and', ['is_use', 1]]
            ];
        }
        return DB::selectFactory([
            'from' => $this->languageTableGateway->table,
            'where' => $where,
            'columns' => $columns,
        ]);
    }

    /**
     *
     * @param string $code
     * @param boolean $is_use_check
     * @return RowGateway|null
     */
    public function getCurrentUseLanguage($code, $is_use_check=true)
    {
        $where = [
            ['equalTo', 'and', ['code',  $code] ],
        ];

        if ($is_use_check) {
            $where[] = ['equalTo', 'and', ['is_use', 1]];
        }

        $resultSet = DB::selectFactory([
            'from' => $this->languageTableGateway->table,
            'where' => $where,
            'columns' => ['*'],
        ]);

        if ($resultSet->count() === 0) {
            $where['code'] = Translator::DEFAULT_LANGUAGE;
            $resultSet = DB::selectFactory([
                'from' => $this->languageTableGateway->table,
                'where' => [
                    ['equalTo', 'AND', ['code', 'zh_Hant']],
                ],
                'columns' => [['*']],
            ]);
        }

        $current = $resultSet->current();
        return $current;
    }

    /**
     *
     * @param array $columns
     * @param boolean $is_use_check 一般用於middleway的判斷使用，如果要用於後台列表使用可以設為false
     * @return \Laminas\Db\ResultSet\ResultSet
     */
    public function getUseLocale(
        $columns = ['id', 'code', 'name'],
        $is_use_check = true
    ) {
        $where = [];

        if ($is_use_check) {
            $where = [
                ['equalTo', 'and', 'is_use', 1],
            ];
        }
        return DB::selectFactory([
            'from' => $this->localeTableGateway->table,
            'columns' => $columns,
            'where' => $where,
        ]);
    }

    public function getLanguageHasLocale(
        $columns = [],
        $types = [],
        $is_use_check = true
    ) {
        $join = $this->languageHasLocaleTableGateway->buildInnerJoinScript($columns, $types);
        $where = [];

        if ($is_use_check) {
            $where = [
                ['equalTo', 'and', ['language.is_use', 1]],
                ['equalTo', 'and', ['locale.is_use', 1]],
            ];
        }
        return DB::selectFactory([
            'from' => $this->languageHasLocaleTableGateway->table,
            'join' => $join,
            'where' => $where,
        ]);
    }
}
