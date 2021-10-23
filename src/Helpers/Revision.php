<?php

declare(strict_types=1);

namespace Shart\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Ramsey\Uuid\Uuid;

class Revision
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var mixed
     */
    protected $reference;

    /**
     * @var mixed
     */
    protected $revisions;

    /**
     * @param $id
     */
    public function __construct($id, string $reference)
    {
        $this->id = $id;
        $this->reference = trim(str_replace('\\', '.', $reference), '.');
        $query = app('db')->table('sys_revision');
        $query->where('reference', $reference);
        $query->where(function ($query) use ($id) {
            $query->where('id_target', $id)->orWhere('id_object', $id);
        });
        $query->orderBy('number', 'asc');
        $this->revisions = $query->get();
    }

    /**
     * @return mixed
     */
    public function all(): Arrayable
    {
        return $this->revisions;
    }

    /**
     * @return mixed
     */
    public function latest()
    {
        if ($version = $this->revisions->last()) {
            return $version->id_object;
        }

        return $this->id;
    }

    /**
     * @param $id
     */
    public static function make($id, string $reference)
    {
        return new self($id, $reference);
    }

    /**
     * @return mixed
     */
    public function origin()
    {
        if ($version = $this->revisions->first()) {
            return $version->id_target;
        }

        return $this->id;
    }

    public function sync(string $id)
    {
        $data = [
            'id' => Uuid::uuid4()->toString(),
            'id_target' => $this->origin(),
            'id_object' => $id,
            'reference' => $this->reference,
            'number' => \count($this->revisions) + 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        app('db')->table('sys_revision')->insert($data);
        $this->revisions->add($data);
    }
}
