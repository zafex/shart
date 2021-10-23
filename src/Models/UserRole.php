<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class UserRole extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'sys_user_role';

    /**
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id')->where('status', 1);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id')->where('status', 1);
    }
}
