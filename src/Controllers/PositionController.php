<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shart\BaseController;
use Shart\Models\Position;

class PositionController extends BaseController
{
    /**
     * @var string
     */
    public function index()
    {
        return Position::all();
    }

    /**
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'id_organization' => 'required|exists:mst_organization,id',
            'id_type' => 'required|exists:sys_setting_detail,id',
        ]);
        $position = new Position();
        $position->fill($request->all());
        $position->save();

        return $this->newResponse($position, Response::HTTP_CREATED);
    }

    public function delete(string $id, Request $request)
    {
        if ($position = Position::where('id', $id)->first()) {
            $position->status = 0;
            $position->save();
        }

        return $this->newResponse();
    }

    public function detail(string $id)
    {
        return Position::with(['organization', 'compositions'])->where('id', $id)->where('status', 1)->first();
    }

    public function update(string $id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'id_organization' => 'required|exists:mst_organization,id',
            'id_type' => 'required|exists:sys_setting_detail,id',
        ]);

        if ($position = Position::where('id', $id)->first()) {
            $position->fill($request->all());
            $position->status = 1;
            $position->save();

            return $this->newResponse($position);
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }
}
