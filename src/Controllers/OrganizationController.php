<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Shart\BaseController;
use Shart\Models\Organization;

class OrganizationController extends BaseController
{
    /**
     * @var string
     */
    public function index()
    {
        return Organization::all();
    }

    /**
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'id_parent' => 'sometimes|nullable|exists:mst_organization,id',
        ]);
        $organization = new Organization();
        $organization->fill($request->all());

        if (!$request->get('id_parent')) {
            $organization->path = Uuid::uuid4()->toString().':'.$request->get('code');
        } else {
            $organization->path = app('db')->table('mst_organization')->where('id', $request->get('id_parent'))->value('path').'.'.$request->get('code');
        }

        $organization->save();

        return $this->newResponse($organization, Response::HTTP_CREATED);
    }

    public function delete(string $id, Request $request)
    {
        $this->validate($request, [
            'release' => 'required|boolean',
        ]);

        if ($organization = Organization::where('id', $id)->first()) {
            $organization->status = 0;
            $organization->save();

            if ($request->get('release')) {
                Organization::where('id_parent', $id)->update([
                    'id_parent' => null,
                ]);
            } else {
                Organization::where('id_parent', $id)->update([
                    'status' => 0,
                ]);
            }
        }

        return $this->newResponse();
    }

    public function detail(string $id)
    {
        return Organization::with(['compositions', 'childs'])->where('id', $id)->where('status', 1)->first();
    }

    public function update(string $id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'id_parent' => 'sometimes|nullable|exists:mst_organization,id',
        ]);

        if ($organization = Organization::where('id', $id)->first()) {
            $organization->fill($request->only(['id_parent', 'name', 'description', 'level']));
            $organization->status = 1;
            $organization->save();
            $organization->load(['compositions', 'childs']);

            return $organization;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }
}
