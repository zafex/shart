<?php

declare(strict_types=1);

namespace Shart;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * @var mixed
     */
    public $deleteable = true;

    /**
     * @var mixed
     */
    public $incrementing = false;

    /**
     * @var mixed
     */
    public $timestamps = false;

    /**
     * @var mixed
     */
    public $updateable = true;

    /**
     * @var array
     */
    protected $hidden = ['password', 'deleted_at', 'deleted_by', 'pivot'];

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return mixed
     */
    public function merge(array $data): array
    {
        $attributes = $this->getOriginal();

        foreach ($attributes as $key => $value) {
            if (!\array_key_exists($key, $data)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
