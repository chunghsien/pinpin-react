<?php

namespace Chopin\Users\Service;

use Laminas\Db\Adapter\Adapter;
use Chopin\Users\TableGateway\PermissionTableGateway;
use Chopin\Users\TableGateway\RolesTableGateway;
use Chopin\Users\TableGateway\UsersTableGateway;
use Chopin\Users\TableGateway\UsersProfileTableGateway;
use Chopin\Users\TableGateway\UsersHasRolesTableGateway;
use Chopin\LaminasDb\DB;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\DB\Select;
use Chopin\Users\TableGateway\RolesHasPermissionTableGateway;
use Laminas\Db\ResultSet\ResultSetInterface;

class UsersService
{

    /**
     *
     * @var PermissionTableGateway
     */
    private $permissionTableGateway;

    /**
     *
     * @var RolesTableGateway
     */
    private $rolesTableGateway;

    /**
     * 
     * @var RolesHasPermissionTableGateway
     */
    private $rolesHasPermissionTableGateway;
    
    /**
     *
     * @var UsersTableGateway
     */
    private $usersTableGateway;

    /**
     *
     * @var UsersProfileTableGateway
     */
    private $usersProfileTableGateway;

    /**
     *
     * @var UsersHasRolesTableGateway
     */
    private $usersHasRolesTableGateway;

    /**
     * 
     * @var Adapter
     */
    private $adapter;
    
    public function __construct(Adapter $adapter)
    {
        $this->permissionTableGateway = new PermissionTableGateway($adapter);
        $this->rolesHasPermissionTableGateway = new RolesHasPermissionTableGateway($adapter);
        $this->rolesTableGateway = new RolesTableGateway($adapter);
        $this->usersTableGateway = new UsersTableGateway($adapter);
        $this->usersProfileTableGateway = new UsersProfileTableGateway($adapter);
        $this->usersHasRolesTableGateway = new UsersHasRolesTableGateway($adapter);
        $this->adapter = $adapter;
    }
    
    /**
     * 
     * @param int $usersId
     * @param bool $isShowName
     * @return array
     */
    public function getUserAllowedPermission(int $usersId, bool $isShowName=false): array
    {
        $PT = AbstractTableGateway::$prefixTable;
        $usersPermissionResult = DB::selectFactory([
            'from' => $this->usersTableGateway->table,
            'columns' => [[]],
            'join' => [
                [
                    $this->usersHasRolesTableGateway->table,
                    $PT.'users.id='.$this->usersHasRolesTableGateway->table.'.users_id',
                    []
                ],
                [
                    $this->rolesTableGateway->table,
                    $this->usersHasRolesTableGateway->table.'.roles_id='.$this->rolesTableGateway->table.'.id',
                    []
                ],
                [
                    $this->rolesHasPermissionTableGateway->table,
                    $this->rolesHasPermissionTableGateway->table.'.roles_id='.$this->usersHasRolesTableGateway->table.'.roles_id',
                    []
                ],
                [
                    $this->permissionTableGateway->table,
                    $this->rolesHasPermissionTableGateway->table.'.permission_id='.$this->permissionTableGateway->table.'.id',
                    ['uri', 'name']
                ]
            ],
            'where' => [
                ['equalTo', 'and', [$this->usersTableGateway->table.'.id', $usersId]],
            ]
        ]);
        if($isShowName) {
            return $usersPermissionResult->toArray();
        }else {
            $user = [];
            foreach ($usersPermissionResult as $row) {
                $user[] = $row['uri'];
            }
            return $user;
        }
    }
    
    
    /**
     * 
     * @param int $usersId
     * @return array
     */
    public function getDenyPermission(int $usersId): array
    {
        $PT = AbstractTableGateway::$prefixTable;
        $select = new Select();
        $select->where;
        $allPermissionsResultset = DB::selectFactory([
            'from' => $PT.'permission',
            'columns' => [['uri']],
            'where' => [
                ['isNull', 'and', [$PT.'permission.deleted_at']]
            ]
        ]);
        $all = [];
        $user = $this->getUserAllowedPermission($usersId);;
        foreach ($allPermissionsResultset as $row) {
            $all[] = $row['uri'];
        }
        $deny = array_diff($all, $user);
        return $deny;
    }
}