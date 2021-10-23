<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Setting extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'identity',
        'label',
        'description',
        'status',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_setting';

    public function items()
    {
        return $this->hasMany(SettingItem::class, 'id_setting', 'id');
    }
}
