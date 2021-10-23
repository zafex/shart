<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Role extends BaseModel
{

    /**
     * @var array
     */
    protected $fillable = [
        'identity',
        'label',
        'description',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_role';

    /**
     * @return mixed
     */
    public function details()
    {
        return $this->belongsToMany(
            self::class,
            'sys_role_detail', // table between Parent and Child
            'id_role', // key on that table for Parent
            'id_detail' // key on that table for Child
        );
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'sys_role_permission', // table between Permission and Role
            'id_role', // key on that table for Role
            'id_permission' // key on that table for Permission
        );
    }

    /**
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'sys_user_role', // table between User and Role
            'id_role', // key on that table for Role
            'id_user' // key on that table for User
        );
    }
}
