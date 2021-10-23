<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class UserCredential extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_user',
        'password',
        'note',
        'status',
        'expired_at',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_user_credential';

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
