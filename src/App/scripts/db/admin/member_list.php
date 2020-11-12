<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

$PT = AbstractTableGateway::$prefixTable;
$aes_key = config('encryption.aes_key');
$select = new Select();
//"CAST(AES_DECRYPT({$usersProfileTableGateway->table}.`aes_value`, '$aesKey') AS CHAR) LIKE $like"
$select->from($PT.'member')->columns([
    'id',
    'language_id',
    'locale_id',
    'password',
    'temporay_password',
    'salt',
    'is_fb_account',
    'full_name' => new Expression("CAST(AES_DECRYPT($PT`member`.`full_name`, '{$aes_key}') AS CHAR)"),
    'cellphone' => new Expression("CAST(AES_DECRYPT($PT`member`.`cellphone`, '{$aes_key}') AS CHAR)"),
    'email' => new Expression("CAST(AES_DECRYPT($PT`member`.`email`, '{$aes_key}') AS CHAR)"),
    'zip', 'county', 'district',
    'address' => new Expression("CAST(AES_DECRYPT($PT`member`.`address`, '{$aes_key}') AS CHAR)"),
    'deleted_at',
    'created_at',
    'updated_at',
]);
$select->join(
    $PT . 'language_has_locale', 
    $PT . 'member.locale_id=' . $PT . 'language_has_locale.locale_id',
    [
        'code',
        'display_name',
        'is_use'
    ]
);
$select->join(
    ['member2' => $PT . 'member'], 
    'member2.locale_id=' . $PT . 'language_has_locale.locale_id',
    []
);
return [
    'pagiantor' => [
        'from' => [
            [$PT.'member_decrypt' => $select],
        ],
        
        'quantifier' => 'distinct',
        //$select->where->isNull($identifier)
        'where' => [
            ['isNull', 'and', ['deleted_at',]],
            ['equalTo', 'and', ['is_use', 1]]
        ],
    ],
];
//(new Select())->where->equalTo($left, $right)
