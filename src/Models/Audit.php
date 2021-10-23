<?php

declare(strict_types=1);

namespace Shart\Models;

use Shart\BaseModel;

class Audit extends BaseModel
{
    public const CREATE = 'CREATE';

    public const DELETE = 'DELETE';

    public const LOGIN = 'LOGIN';

    public const UPDATE = 'UPDATE';

    /**
     * @var string
     */
    protected $table = 'log_audit';
}
