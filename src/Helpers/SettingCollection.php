<?php

declare(strict_types=1);

namespace Shart\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;
use Shart\Models\SettingItem;

class SettingCollection extends Collection
{
    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array_map(function ($value) {
            if ($value instanceof SettingItem) {
                return 0 == $value->visibility ? '******' : $value->value;
            } elseif ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->all());
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->map(function ($value) {
            if ($value instanceof SettingItem) {
                return $value->value;
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        })->all();
    }
}
