<?php

declare(strict_types=1);

namespace Shart\Observers;

use Illuminate\Database\Eloquent\Model;
use Shart\Authority;
use Shart\BaseModel;

class AuthorObserver
{
    public function creating(Model $model)
    {
        if (null === $model->created_by) {
            $model->created_by = app(Authority::class)->getAuthor();
        }

        $model->created_at = date('Y-m-d H:i:s');
    }

    public function updating(Model $model)
    {
        if ($model instanceof BaseModel) {
            if (true === $model->updateable && 1 == $model->getAttribute('status')) {
                if (null === $model->updated_by) {
                    $model->updated_by = app(Authority::class)->getAuthor();
                }

                $model->updated_at = date('Y-m-d H:i:s');
            }

            if (true === $model->deleteable && 1 != $model->getAttribute('status')) {
                if (null === $model->deleted_by) {
                    $model->deleted_by = app(Authority::class)->getAuthor();
                }

                $model->deleted_at = date('Y-m-d H:i:s');
            }
        }
    }
}
