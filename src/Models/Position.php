<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Position extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_organization',
        'id_type',
        'code',
        'name',
        'description',
        'level',
    ];

    /**
     * @var string
     */
    protected $table = 'mst_position';

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
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'id_organization', 'id');
    }

    /**
     * @return mixed
     */
    public function structures()
    {
        return $this->hasMany(Structure::class, 'id_position', 'id')->with(['organization', 'employee'])->where('status', 1)->where(function ($structure) {
            $structure->whereNull('actived_at')->orWhere('actived_at', '<=', date('Y-m-d H:i:s'));
        })->where(function ($structure) {
            $structure->whereNull('expired_at')->orWhere('expired_at', '>=', date('Y-m-d H:i:s'));
        });
    }
}
