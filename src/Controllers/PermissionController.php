<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Shart\BaseController;
use Shart\Models\Permission;

class PermissionController extends BaseController
{
    /**
     * @return mixed
     */
    public function index()
    {
        $query = Permission::query();
        $query->with([
            'abilities' => function ($query) {
                $query->where('status', 1);
            },
        ]);
        $query->where('status', 1);
        $query->whereNull('id_parent');

        return [
            'total' => $query->count(),
            'items' => $query->get(),
        ];
    }
}
