<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Message extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'subject',
        'body',
        'sender',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_message';

    public function target()
    {
        return $this->hasOne(MessageTarget::class, 'id_message', 'id');
    }
}
