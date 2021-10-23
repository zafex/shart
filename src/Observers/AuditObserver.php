<?php

declare(strict_types=1);

namespace Shart\Observers;

use Illuminate\Database\Eloquent\Model;
use Shart\BaseModel;
use Shart\Models\Audit;
use Shart\Services\LogService;

class AuditObserver
{
    public function created(Model $model)
    {
        app(LogService::class)->audit($model->getKey(), $model->getTable(), Audit::CREATE, $model->getAttributes());
    }

    public function deleted(Model $model)
    {
        app(LogService::class)->audit($model->getKey(), $model->getTable(), Audit::DELETE);
    }

    public function updated(Model $model)
    {
        $type = Audit::UPDATE;
        $attrs = $model->getAttributes();
        $origins = $model->getOriginal();

        if ($model instanceof BaseModel) {
            if (\array_key_exists('status', $attrs) && \array_key_exists('status', $origins)) {
                if ($attrs['status'] != $origins['status'] && true === $model->deleteable) {
                    $type = Audit::DELETE;
                }
            }
        }

        if (Audit::DELETE === $type) {
            app(LogService::class)->audit($model->getKey(), $model->getTable(), $type);
        } else {
            $attributes = [];

            foreach ($attrs as $key => $value) {
                if (!\array_key_exists($key, $origins) || $value != $origins[$key]) {
                    $attributes[$key] = $value;
                }
            }

            app(LogService::class)->audit($model->getKey(), $model->getTable(), $type, $attributes);
        }
    }
}
