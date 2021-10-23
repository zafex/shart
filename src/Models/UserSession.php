<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class UserSession extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_user',
        'status',
        'provider',
        'expired_at',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_user_session';

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
