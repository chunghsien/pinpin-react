<?php

namespace Chopin\Users\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;
use Laminas\Db\ResultSet\ResultSet;
use NoProtocol\Encryption\MySQL\AES\Crypter;
use Laminas\Math\Rand;
use Chopin\LaminasDb\DB\Traits\SecurityTrait;

class MemberTableGateway extends AbstractTableGateway
{

    use SecurityTrait;
    
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'member';
    
    protected $subSelectColumns = [];
    
    protected $aes_key = '';
    
    public function __construct(\Laminas\Db\Adapter\Adapter $adapter)
    {
        parent::__construct($adapter);
        $this->aes_key = config('encryption.aes_key');
        $this->subSelectColumns = [
            'id',
            'language_id',
            'locale_id',
            'full_anme'=> new Expression("CAST(AES_DECRYPT(`full_name`, '{$this->aes_key}') AS CHAR)"),
            'cellphone'=> new Expression("CAST(AES_DECRYPT(`cellphone`, '{$this->aes_key}') AS CHAR)"),
            'email'=> new Expression("CAST(AES_DECRYPT(`email`, '{$this->aes_key}') AS CHAR)"),
            'country',
            'state',
            'zip',
            'county',
            'district',
            'address'=> new Expression("CAST(AES_DECRYPT(`address`, '{$this->aes_key}') AS CHAR)"),
            'password',
            'salt',
            'is_fb_account',
            'deleted_at',
            'created_at',
            'updated_at'
        ];
    }
    
    public function deCryptData($data)
    {
        $aes_columns = ['email', 'cellphone', 'full_name', 'address'];
        $columns = array_keys($data);
        $crypter = new Crypter($this->aes_key);
        
        foreach ($columns as $column) {
            if( false !== array_search($column, $aes_columns) ) {
                $data[$column] = $crypter->decrypt($data[$column]);
            }
        }
        return $data;
        
    }
    public function enCryptData($data)
    {
       
        $aes_columns = ['email', 'cellphone', 'full_name', 'address'];
        $columns = array_keys($data);
        $crypter = new Crypter($this->aes_key);
        
        foreach ($columns as $column) {
            if( false !== array_search($column, $aes_columns) ) {
                $data[$column] = $crypter->encrypt($data[$column]);
            }
            if($column == 'password') {
                $data['salt'] = Rand::getString(8);
                $password = $data['password'].$data['salt'];
                if(floatval(PHP_VERSION) < 7.2) {
                    $algo = PASSWORD_DEFAULT;
                }else {
                    $algo =  PASSWORD_ARGON2I;
                }
                $password = password_hash($password, $algo);
                $data['password'] = $password;
            }
        }
        return $data;
    }
    
    /**
     * 
     * @param string $email
     * @param bool $idUse
     * @param bool $isAll
     * @return ResultSet
     */
    public function getEmail(string $email, bool $idUse = false, bool $isAll = false):ResultSet {
        if($idUse == true) {
            $subSelect = $this->buildSubSelect(['id', 'email']);
        }else {
            $subSelect = $this->buildSubSelect(['email']);
        }
        
        if($isAll == true) {
            $subSelect = $this->buildSubSelect(['*']);
        }
        
        $select = new Select();
        $pt = self::$prefixTable;
        $select = $select->from([$pt.'member_decrypt' => $subSelect])->where(['email' => $email]);
        $dataSource = $this->sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        return $resultSet;
    }
    
    /**
     * 
     * @param string $cellphone
     * @param bool $idUse
     * @return ResultSet
     */
    public function getCellphone(string $cellphone, bool $idUse = false, bool $isAll = false):ResultSet {
        if($idUse) {
            $subSelect = $this->buildSubSelect(['id', 'cellphone']);
        }else {
            $subSelect = $this->buildSubSelect(['cellphone']);
        }
        if($isAll == true) {
            $subSelect = $this->buildSubSelect(['*']);
        }
        $select = new Select();
        $pt = self::$prefixTable;
        $select = $select->from([$pt.'member_decrypt' => $subSelect])->where(['cellphone' => $cellphone]);
        $dataSource = $this->sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        return $resultSet;
    }
    
    /**
     * 
     * @param int $id
     * @return \Laminas\Db\ResultSet\ResultSet
     */
    public function getMember(int $id) {
        $subSelect = $this->buildSubSelect(['*']);
        $select = new Select();
        $pt = self::$prefixTable;
        $select = $select->from([$pt.'member_decrypt' => $subSelect])->where(['id' => $id]);
        $dataSource = $this->sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        return $resultSet;
    }
    
    /**
     * 
     * @param array $columns
     * @return \Laminas\Db\Sql\Select
     */
    protected function buildSubSelect($columns = ['*'])
    {
        
        $subSelectTable= $this->table;
        $subSelect = new Select();
        $userColumns = [];
        if($columns[0] == '*') {
            $userColumns = $this->subSelectColumns;
        }else {
            foreach ($columns as $column) {
                $userColumns[$column] = $this->subSelectColumns[$column];
            }
        }
        $subSelect->from($subSelectTable)->columns($userColumns);
        return $subSelect;
    }
}
