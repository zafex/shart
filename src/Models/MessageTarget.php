<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class MessageTarget extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_message',
        'id_object',
        'reference',
        'status',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_message_target';
}
