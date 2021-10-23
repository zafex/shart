<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Notification extends BaseModel
{
    public const PREVIEW = 'preview';

    public const VERIFICATION = 'verification';

    /**
     * @var array
     */
    protected $fillable = [
        'id_message',
        'action',
        'status',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_notification';

    /**
     * @return mixed
     */
    public function message()
    {
        return $this->belongsTo(Message::class, 'id_message', 'id');
    }

    /**
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'sys_user_notification', // table between User and Notification
            'id_notification', // key on that table for Notification
            'id_user' // key on that table for User
        );
    }
}
