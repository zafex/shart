<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shart\BaseController;
use Shart\IndexAction;
use Shart\Models\Structure;

class StructureController extends BaseController
{
    use IndexAction;

    /**
     * @var string
     */
    protected $indexName = 'composition';

    /**
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'id_organization' => 'required|exists:mst_organization,id',
            'id_employee' => 'required|exists:mst_employee,id',
            'id_position' => 'required|exists:mst_position,id',
        ]);
        $composition = new Structure();
        $composition->fill($request->all());
        $composition->status = 1;
        $composition->save();

        $composition->load(['employee', 'organization', 'position']);

        return $this->newResponse($composition, Response::HTTP_CREATED);
    }

    public function delete(string $id, Request $request)
    {
        if ($composition = Structure::where('id', $id)->first()) {
            $composition->status = 0;
            $composition->save();
        }

        return $this->newResponse();
    }

    public function detail(string $id)
    {
        return Structure::with(['organization', 'employee', 'position'])->where('id', $id)->where('status', 1)->first();
    }

    public function update(string $id, Request $request)
    {
        if ($composition = Structure::where('id', $id)->first()) {
            $composition->fill($request->all());
            $composition->status = 1;
            $composition->save();
        }
    }
}
