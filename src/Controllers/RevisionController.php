<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Shart\BaseController;
use Shart\Helpers\Revision;

class RevisionController extends BaseController
{
    /**
     * @return mixed
     */
    public function create(string $reference, Request $request)
    {
        $this->validate($request, [
            'id_target' => 'required',
            'id_object' => 'required',
        ]);
        $version = new Revision($request->get('id_target'), $reference);
        $version->sync($request->get('id_object'));

        return $version->all();
    }

    public function detail(string $reference, string $id)
    {
        return Revision::make($id, $reference)->all();
    }
}
