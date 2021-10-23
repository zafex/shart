<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Organization extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_parent',
        'name',
        'description',
        'code',
        'level',
    ];

    /**
     * @var string
     */
    protected $table = 'mst_organization';

    /**
     * @return mixed
     */
    public function childs()
    {
        return $this->hasMany(self::class, 'id_parent', 'id')->where('status', 1);
    }

    /**
     * @return mixed
     */
    public function structures()
    {
        return $this->hasMany(Structure::class, 'id_organization', 'id')->with(['employee', 'title'])->where('status', 1)->where(function ($structure) {
            $structure->whereNull('actived_at')->orWhere('actived_at', '<=', date('Y-m-d H:i:s'));
        })->where(function ($structure) {
            $structure->whereNull('expired_at')->orWhere('expired_at', '>=', date('Y-m-d H:i:s'));
        });
    }
}
