<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class UserLink extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_user',
        'id_object',
        'reference',
        'provider',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_user_link';

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
