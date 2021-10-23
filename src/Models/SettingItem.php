<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class SettingItem extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'identity',
        'value',
        'label',
        'visibility',
        'status',
        'id_setting',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_setting_item';

    public function setting()
    {
        return $this->belongsTo(Setting::class, 'id_setting', 'id');
    }
}
