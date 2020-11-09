<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
$PT = AbstractTableGateway::$prefixTable;

$aes_key = config('encryption.aes_key');
$select = new Select();
//"CAST(AES_DECRYPT({$usersProfileTableGateway->table}.`aes_value`, '$aesKey') AS CHAR) LIKE $like"
$select->from($PT.'contact')->columns([
    'id',
    'language_id',
    'locale_id',
    'full_name' => new Expression("CAST(AES_DECRYPT(`{$PT}contact`.`full_name`, '{$aes_key}') AS CHAR)"),
    'email' => new Expression("CAST(AES_DECRYPT(`{$PT}contact`.`email`, '{$aes_key}') AS CHAR)"),
    'subject',
    'is_reply',
    'deleted_at',
    'created_at',
    'updated_at',
]);

$select->join(
    $PT . 'language_has_locale',
    $PT . 'contact.locale_id=' . $PT . 'language_has_locale.locale_id',
    [
        'code',
        'display_name',
        'is_use'
    ]
    );
$select->join(
    ['contact2' => $PT . 'contact'],
    'contact2.locale_id=' . $PT . 'language_has_locale.locale_id',
    []
);

return [
    'pagiantor' => [
        'from' => [
            [$PT.'contact_decrypt' => $select],
        ],
        
        'quantifier' => 'distinct',
        //$select->where->isNull($identifier)
        'where' => [
            ['isNull', 'and', ['deleted_at',]],
            ['equalTo', 'and', ['is_use', 1]]
        ],
    ],
];
