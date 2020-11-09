<?php

namespace Chopin\SystemSettings\Console;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Sql;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

trait SystemSettingsTrait
{

    /**
     *
     * @param Adapter $adapter
     * @param integer $language_id
     * @param integer $locale_id
     * @return array
     */
    private function getLanguages($adapter, $language_id, $locale_id)
    {
        $sql = new Sql($adapter);
        if ( ! $locale_id) {
            $select = $sql->select(AbstractTableGateway::$prefixTable.'language')->where([
                'id' => $language_id,
            ]);
        } else {
            $select = $sql->select(AbstractTableGateway::$prefixTable.'language_has_locale')->where([
                'locale_id' => $locale_id,
                'language_id' => $language_id,
            ]);
        }

        $resultSet = $sql->prepareStatementForSqlObject($select)->execute()->current();
        return $resultSet;
    }

    /**
     *
     * @param Adapter $adapter
     * @param int $language_id
     * @param int $locale_id
     * @return mixed
     */
    protected function addSiteInfo($adapter, $language_id, $locale_id)
    {
        try {
            $systemSettingsTableGateway = new TableGateway(AbstractTableGateway::$prefixTable.'system_settings', $adapter);
            $resultSet = $this->getLanguages($adapter, $language_id, $locale_id);

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => 0,
                'key' => 'site_info',
                'name' => sprintf('網站資料 [ %s ]', $resultSet['display_name']),
            ]);

            $lastID = $systemSettingsTableGateway->lastInsertValue;

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'name',
                'name' => sprintf('網站名稱 [ %s ]', $resultSet['display_name']),
            ]);
            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'owner',
                'name' => sprintf('擁有者 [ %s ]', $resultSet['display_name']),
            ]);
            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'email',
                'name' => sprintf('網站信箱 [ %s ]', $resultSet['display_name']),
            ]);

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'address',
                'name' => sprintf('住址 [ %s ]', $resultSet['display_name']),
            ]);

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'telphone',
                'name' => sprintf('聯絡電話 [ %s ]', $resultSet['display_name']),
            ]);
            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'fax',
                'name' => sprintf('傳真 [ %s ]', $resultSet['display_name']),
            ]);

            return $resultSet['display_name'].': 新增網站資料功能完成。';
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     *
     * @param Adapter $adapter
     * @param int $language_id
     * @param int $locale_id
     * @return mixed
     */
    protected function addGeneralSeo($adapter, $language_id, $locale_id)
    {
        try {
            $systemSettingsTableGateway = new TableGateway(AbstractTableGateway::$prefixTable.'system_settings', $adapter);
            $resultSet = $this->getLanguages($adapter, $language_id, $locale_id);

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => 0,
                'key' => 'general_seo',
                'name' => sprintf('基礎SEO [ %s ]', $resultSet['display_name']),
            ]);
            $lastID = $systemSettingsTableGateway->lastInsertValue;

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'title',
                'name' => sprintf('網站標題 [ %s ]', $resultSet['display_name']),
            ]);
            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'text', 'required' => false]),
                'key' => 'meta_keyword',
                'name' => sprintf('網站關鍵字 [ %s ]', $resultSet['display_name']),
            ]);

            $systemSettingsTableGateway->insert([
                'language_id' => $language_id,
                'locale_id' => $locale_id,
                'parent_id' => $lastID,
                'input_type' => json_encode(['type' => 'textarea', 'required' => false]),
                'key' => 'meta_description',
                'name' => sprintf('網站描述 [ %s ]', $resultSet['display_name']),
            ]);

            return $resultSet['display_name'].': 新增基礎 SEO 功能完成。';
        } catch (\Exception $e) {
            return $e;
        }
    }
}
