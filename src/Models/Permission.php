<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Permission extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_parent',
        'identity',
        'label',
        'description',
        'status',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_permission';

    /**
     * @return mixed
     */
    public function abilities()
    {
        return $this->hasMany(self::class, 'id_parent', 'id');
    }
}
