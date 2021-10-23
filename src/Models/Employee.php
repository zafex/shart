<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Employee extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_organization',
        'name',
        'email',
        'code',
        'birthday',
    ];

    /**
     * @var string
     */
    protected $table = 'mst_employee';

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
        return $this
            ->hasMany(Structure::class, 'id_employee', 'id')
            ->with(['organization', 'position'])
            ->where('status', 1)
            ->where(function ($structure) {
                $structure->whereNull('actived_at')
                          ->orWhere('actived_at', '<=', date('Y-m-d H:i:s'));
            })
            ->where(function ($structure) {
                $structure->whereNull('expired_at')
                          ->orWhere('expired_at', '>=', date('Y-m-d H:i:s'));
            });
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this
            ->hasOneThrough(User::class, UserLink::class, 'id_object', 'id', 'id', 'id_user')
            ->with('credentials')
            ->where('reference', 'employee')
            ->where('provider', 'application');
    }
}
