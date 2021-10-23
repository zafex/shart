<?php

declare(strict_types=1);

namespace Shart\Observers;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class UuidObserver
{
    public function saving(Model $model)
    {
        if (null === $model->getKey()) {
            if (false == $model->getIncrementing() && 'string' == $model->getKeyType()) {
                $model->setAttribute($model->getKeyName(), Uuid::uuid4()->toString());
            }
        }
    }
}
