<?php

namespace Chopin\Support;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Math\Rand;
use Laminas\Db\Metadata;

abstract class SecurityVerify
{
    public static function verify(Adapter $adapter)
    {
        $data = require './config/autoload/security.local.php';
        $generate = 0;
        if(isset($data['encryption']['generated'])) {
            $generate = $data['encryption']['generated'];
        }
        $metadata = Metadata\Source\Factory::createSourceFromAdapter($adapter);
        if($metadata->getTableNames())
        {
            $migration = AbstractTableGateway::$prefixTable.'migrations';
            $tableGateway = new TableGateway($migration, $adapter);
            $select = $tableGateway->getSql()->select();
            $select->order('id ASC')->limit(1);
            $current = $tableGateway->selectWith($select)->current();
            $created_at = $current['created_at'];
            return strtotime($created_at) < $generate;
        }
        return true;
    }
    
    public static function generate()
    {
        $chartlist = '`1234567890-=~!@#$%^&*()_+qwertyuiop[]\QWERTYUIOP{}|asdfghjkl;\'ASDFGHJKL:"zxcvbnm,./ZXCVBNM<>?';
        $data = [
            'encryption' => [
                'jwt_key' => Rand::getString(10, $chartlist),
                'jwt_alg' => 'HS256',
                'aes_key' => Rand::getString(8, $chartlist),
                'charlist' => $chartlist,
                'generated' => strtotime("now"),
            ]
        ];
        $code = "<?php\n\n";
        $code .= "return ";
        $tmp = var_export($data, true);
        $tmp = str_replace('array (', '[', $tmp);
        $tmp = str_replace(')', ']', $tmp);
        $tmp.= ";";
        $data = $code.$tmp;
        file_put_contents('./config/autoload/security.local.php', $data);
    }
}