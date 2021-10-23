<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Structure extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_organization',
        'id_employee',
        'id_position',
        'actived_at',
        'expired_at',
    ];

    /**
     * @var string
     */
    protected $table = 'mst_structure';

    /**
     * @return mixed
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_employee', 'id');
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
    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position', 'id');
    }
}
