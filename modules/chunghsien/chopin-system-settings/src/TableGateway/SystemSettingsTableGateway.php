<?php

namespace Chopin\SystemSettings\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\DB;
use Chopin\LaminasDb\RowGateway\RowGateway;
use Chopin\LaminasDb\DB\Traits\SecurityTrait;
use Laminas\Db\Sql\Expression;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;

class SystemSettingsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;
    
    protected $regPattern = '/_key|_secret|_token|hash_iv|password$/';
    
    use SecurityTrait;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'system_settings';
    
    private function processCrypt($set, $where=null)
    {
        $key = isset($set['key']) ? $set['key'] : '';
        if(!$key && $where) {
            //debug($where);
            $row = $this->select($where)->current()->toArray();
            $key = $row['key'];
        }
        $encryptionColumns = array_merge(
            $this->defaultEncryptionColumns, 
            ['passwrod', 'from']
        );
        $other_secrut_check = preg_match($this->regPattern, $key);
        if( 
            ( false !== array_search($key, $encryptionColumns)) || 
            $other_secrut_check
        ) {
            $value = isset($set['value']) ? $set['value'] : '';
            if($value) {
                $value = $this->aesCrypter->encrypt($value);
                unset($set['value']);
                $set['value'] = new Expression('null');
                if($value) {
                    $set['aes_value'] = $value;
                }else {
                    $set['aes_value'] = new Expression('null');
                }
            }else {
                $set['value'] = new Expression('null');
                $set['aes_value'] = new Expression('null');
            }
        }
        return $set ;
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Laminas\Db\TableGateway\AbstractTableGateway::insert()
     */
    public function insert($set) {
        $set = $this->processCrypt($set);
        return parent::insert($set);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Laminas\Db\TableGateway\AbstractTableGateway::update()
     */
    public function update($set, $where = null, array $joins = null)
    {
        $set = $this->processCrypt($set, $where);
        return parent::update($set, $where, $joins);
    }
    
    protected function getEnabledLanguagies()
    {
        $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
        return $languageHasLocaleTableGateway->select(['is_use' => 1]);
    }

    /**
     * @desc 驗證多語型態，並新增為增加的選項
     * @return void
     */
    public function verifyLanguageData()
    {
        $language_enabled_resultset = $this->getEnabledLanguagies();

        //表示沒有新增的
        if ($language_enabled_resultset->count() === 0) {
            return ;
        }
        $used_language_in = [];
        foreach ($language_enabled_resultset as $ul) {
            $used_language_in[] = $ul['language_id'];
        }
        $used_language_in = array_unique($used_language_in);
        $used_language_in = array_values($used_language_in);

        $parent_seeds = DB::selectFactory([
            'from' => $this->table,
            'where' => [
                ['equalTo', 'and', ['parent_id', 0]],
                ['isNull', 'and',  ['deleted_at']],
                ['equalTo', 'and', ['language_id', intval($used_language_in[0])]],
            ],
            'columns' => [
                'id', 'parent_id', 'key', 'name',
            ],
        ])->toArray();
        $connection = $this->getAdapter()->driver->getConnection();
        $connection->beginTransaction();

        try {
            foreach ($language_enabled_resultset as $le) {
                if (false === array_search($le->id, $used_language_in)) {
                    foreach ($parent_seeds as $parent_seed) {
                        $seed_parent_id = $parent_seed['id'];
                        unset($parent_seed['id']);
                        $name = $parent_seed['name'];
                        $replace = sprintf('(%s)', $le->display_name);
                        $name = preg_replace('/\([\x{0000}-\x{FFFF}]+\)/u', $replace, $name);
                        $parent_seed['name'] = $name;
                        $parent_seed['language_id'] = $le['language_id'];
                        $parent_seed['locale_id'] = $le['locale_id'];
                        $this->insert($parent_seed);
                        $last_parent_id = $this->lastInsertValue;

                        $resultSet = $this->select(['parent_id' => $seed_parent_id]);

                        foreach ($resultSet as $child) {
                            $name = $child->name;
                            $replace = sprintf('(%s)', $le->display_name);
                            $name = preg_replace('/\([\x{0000}-\x{FFFF}]+\)/u', $replace, $name);

                            $childArr = $child->toArray();
                            $childArr['parent_id'] = $last_parent_id;
                            $childArr['language_id'] = $le['language_id'];
                            $childArr['locale_id'] = $le['locale_id'];
                            $childArr['name'] = $name;
                            unset($childArr['id']);
                            unset($childArr['aes_value']);
                            unset($childArr['created_at']);
                            unset($childArr['deleted_at']);
                            unset($childArr['locale_id']);
                            unset($childArr['value']);
                            unset($childArr['updated_at']);
                            $this->insert($childArr);
                        }
                    }
                }
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            echo $e->getMessage();
            exit();
        }
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function inputTypeToArray($data)
    {
        $this->initCrypt();
        if (isset($data['children'])) {
            $toConfig = [];
            foreach ($data['children'] as &$c) {
                
                $c['input_type'] = json_decode($c['input_type'], true);
                
                if (isset($c['input_type']['0'])) {
                    $params = $c['input_type']['0'];
                    
                    if (strtolower($params['method']) == 'getoptions') {
                        //方法參數有更動，這邊要做一些修正fix舊參數
                        $_params = $params['params'];
                        if (count($_params) == 3) {
                            $b_params = array_slice($_params, 0, 2);
                            $a_params = array_slice($_params, 2, 1);
                            $b_params[] = [];
                            $_params = array_merge($b_params, $a_params);
                            
                            $params['params'] = $_params;
                        }
                       
                    }
                    $dataSourceTablegateway = parent::newInstance($params['class'], $this->adapter);
                    
                    $dataSource = call_user_func_array(
                        [$dataSourceTablegateway, $params['method']],
                        $params['params']
                    );
                    unset($c['input_type'][0]);
                    $c['input_type']['value'] = $dataSource;
                }
                //$c= new SystemSetting($c);
                $config_key = $c['key'];
                if (isset($c['value'])) {
                    $toConfig[$config_key] = $c['value'];
                } else {
                    $c['value'] = '';
                    if($c['aes_value']) {
                        $c['aes_value'] = $c['value'] =  $this->getAesCrypter()->decrypt($c['aes_value']);
                    }
                    $toConfig[$config_key] = $c['aes_value'];
                }
            }
            $data['to_config'] = $toConfig;
        }
        
        
        return $data;
    }

    public function toSerialize($language_id = null, $parent_key = '')
    {
        $result = [];
        $script = [
            'where' => [
                ['equalTo', 'and', ['parent_id', 0]],
                ['isNull', 'and', ['deleted_at']],
            ],
            'order' => [ 'sort ASC', 'id ASC'],
        ];
        if ($parent_key) {
            $script['where'][] = [
                ['equalTo', 'and', ['parent_key', $parent_key]],
            ];
        }

        if ($language_id) {
            $script['where'][] = [
                'and',
                [
                    ['equalTo', 'or', ['language_id', 0]],
                    ['equalTo', 'or', ['language_id', $language_id]],
                ],
            ];
        }
        
        $script['from'] = $this->table;
        $parentResultSet = DB::selectFactory($script);
        $result = [];
        $language_enabled_resultset = $this->getEnabledLanguagies()->toArray();
        foreach ($parentResultSet as $parent) {
            if ($parent instanceof \Chopin\LaminasDb\RowGateway\RowGateway === false) {
                $row = new RowGateway($this->primary[0], $this->table, $this->getSql()->getAdapter());
                $row->populate((array)$parent);
                $parent = $row;
            }
            /**
             * @var \Chopin\LaminasDb\RowGateway\RowGateway $parent
             */
            $key = $parent->key;
            if (empty($result[$key])) {
                $result[$key] = [];
            }
            $childSelect = $this->sql->select();
            $childWhere = $childSelect->where;
            $childWhere->isNull('deleted_at');
            $childWhere->equalTo('parent_id', $parent->id);
            $childSelect->where($childWhere);
            $childResultSet = $this->selectWith($childSelect);
            $parent->with('children', $childResultSet);
            if ($language_id || $parent->language_id == 0) {
                $tmp = $parent->toArray('key');
                $result[$key] = $this->inputTypeToArray($tmp);
            } else {
                $code = '';
                if($language_enabled_resultset) {
                    foreach ($language_enabled_resultset as $le) {
                        if ($le['language_id'] == $parent->language_id) {
                            $code = $le['code'];
                            break;
                        }
                    }
                }
                $tmp = $parent->toArray('key');
                $result[$key][$code] = $this->inputTypeToArray($tmp);
            }
        }
        unset($language_enabled_resultset);
        return $result;
    }
    
    public function deCryptData($data)
    {
        if($data['aes_value']) {
            $data['value'] = $this->aesCrypter->decrypt($data['aes_value']);
            $data['aes_value'] = '';
        }
        return $data;
    }
    
}
