<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Menu extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_setting_item',
        'id_parent',
        'id_role',
        'label',
        'url',
        'icon',
        'description',
        'status',
    ];

    /**
     * @var string
     */
    protected $table = 'sys_menu';

    public function category()
    {
        return $this->belongsTo(SettingItem::class, 'id_setting_item', 'id');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'id_role');
    }
}
