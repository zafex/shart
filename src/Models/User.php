<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class User extends BaseModel
{
    public const GUEST = 'guest';

    /**
     * @var array
     */
    protected $fillable = [
        'username',
        'fullname',
        'email',
        'type',
        'avatar',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_user';

    /**
     * @return mixed
     */
    public function credentials()
    {
        return $this->hasMany(UserCredential::class, 'id_user', 'id')->where('status', 1);
    }

    /**
     * @return mixed
     */
    public function links()
    {
        return $this->hasMany(UserLink::class, 'id_user', 'id');
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'sys_user_permission', // table between Permission and User
            'id_user', // key on that table for User
            'id_permission' // key on that table for Permission
        );
    }

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'sys_user_role', // table between User and Role
            'id_user', // key on that table for Role
            'id_role' // key on that table for User
        );
    }
}
