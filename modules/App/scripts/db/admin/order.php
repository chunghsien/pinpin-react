<?php
/**
 * @deprecated
 */
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
$PT = AbstractTableGateway::$prefixTable;

$aes_key = config('encryption.aes_key');
$select = new Select();
//"CAST(AES_DECRYPT({$usersProfileTableGateway->table}.`aes_value`, '$aesKey') AS CHAR) LIKE $like"
$select->from($PT.'order')->columns([
    'id',
    'member_id',
    'logistics_global_id',
    'language_id',
    'locale_id',
    'serial',
    'fullname' => new Expression("CAST(AES_DECRYPT(`{$PT}order`.`fullname`, ?) AS CHAR)", [$aes_key]),
    'email' => new Expression("CAST(AES_DECRYPT(`{$PT}order`.`email`, ?) AS CHAR)", [$aes_key]),
    'cellphone' => new Expression("CAST(AES_DECRYPT(`{$PT}order`.`cellphone`, ?) AS CHAR)", [$aes_key]),
    'pay_method',
    'status',
    'deleted_at',
    'created_at',
    'updated_at',
]);

$select->join(
    "{$PT}language_has_locale",
    "{$PT}order.locale_id={$PT}language_has_locale.locale_id",
    ['display_name',]
);
$select->join(
    ['order2' => "{$PT}order"],
    "order2.locale_id={$PT}language_has_locale.locale_id",
    []
);
$memberDecryptSelect = new Select();
$memberDecryptSelect->from("{$PT}member");
$memberDecryptSelect->columns([
    'id',
    'member_full_name' => new Expression("CAST(AES_DECRYPT(`{$PT}member`.`full_name`, ?) AS CHAR)", [$aes_key]),
]);
$memberDecryptWhere = $memberDecryptSelect->where;
$memberDecryptWhere->isNull('deleted_at');
$memberDecryptSelect->where($memberDecryptWhere);
$select->join(
    ["{$PT}member_decrypt" => $memberDecryptSelect],
    "{$PT}order.member_id=member_decrypt.id",
    ["member_full_name"]
);

$select->join(
    "{$PT}logistics_global",
    "{$PT}order.logistics_global_id={$PT}logistics_global.id",
    ["logistics_name" => "name"]
);

$where = $select->where;
$where->isNull("{$PT}logistics_global.deleted_at");
$where->equalTo("{$PT}language_has_locale.is_use", 1);
$select->where($where);

return [
    'pagiantor' => [
        'from' => [
            ['order_decrypt' => $select],
        ],
        'quantifier' => 'distinct',
        'where' => [
            ['isNull', 'AND', ["{$PT}order_decrypt.deleted_at",]],
        ],
    ],
];
