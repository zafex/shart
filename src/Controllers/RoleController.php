<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Shart\BaseController;
use Shart\Models\Role;
use Shart\Models\RoleComposite;
use Shart\Services\PrivilegeService;

class RoleController extends BaseController
{
    /**
     * @var string
     */
    public function index()
    {
        return Role::all();
    }

    /**
     * @return mixed
     */
    public function create(Request $request, PrivilegeService $privilege)
    {
        $this->validate($request, [
            'label' => 'required|unique:sys_role',
        ]);
        $model = app('db')->transaction(function () use ($request, $privilege) {
            $role = new Role($request->only(['description', 'label']));
            $role->identity = preg_replace('/[^a-z0-9\_]+/', '-', strtolower($request->get('label')));
            $role->save();

            if ($details = $request->get('details')) {
                $role->details()->sync($details);
            }

            if ($permissions = $request->get('permissions')) {
                $role->permissions()->sync($permissions);
            }

            $privilege->clear();

            return $role;
        });

        $model->load(['details', 'permissions']);

        return $this->newResponse($model, Response::HTTP_CREATED);
    }

    public function delete(string $id, Request $request, PrivilegeService $privilege)
    {
        if ($role = Role::where('id', $id)->first()) {
            app('db')->transaction(function () use ($id, $privilege, $role) {
                $role->status = 0;
                $role->save();
                RoleComposite::where('id_role', $id)->orWhere('id_child', $id)->delete();
                $privilege->clear();
            });
        }

        return $this->newResponse();
    }

    public function detail(string $id): ? Role
    {
        if ($role = Role::with(['details', 'permissions'])->where('id', $id)->orWhere('identity', $id)->first()) {
            return $role;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }

    /**
     * @return mixed
     */
    public function update(string $id, Request $request, PrivilegeService $privilege)
    {
        $this->validate($request, [
            'label' => [
                'sometimes',
                'required',
                Rule::unique('sys_role')->ignore($id),
            ],
        ]);

        $model = app('db')->transaction(function () use ($id, $request, $privilege) {
            if ($role = Role::where('id', $id)->first()) {
                $role->fill($request->only(['description', 'label']));
                $role->identity = preg_replace('/[^a-z0-9\_]+/', '-', strtolower($request->get('label')));
                $role->save();

                if ($permissions = $request->get('permissions')) {
                    $role->permissions()->sync($permissions);
                }

                if ($details = $request->get('details')) {
                    $this->throwLoopBack($role->getKey(), $details, RoleComposite::all()->groupBy('id_role'));
                    $role->details()->sync($details);
                }

                $privilege->clear();

                $role->load(['details', 'permissions']);

                return $role;
            }
        });

        return $model;
    }

    /**
     * Description.
     *
     * @param type $id
     * @param type $ids
     * @param type $composites
     *
     * @return type
     */
    private function throwLoopBack($id, $ids, $composites)
    {
        if (\in_array($id, $ids)) {
            throw new \Exception('Error Loop Back Reference');
        }

        foreach ($ids as $parent) {
            if ($composites->has($parent)) {
                $tmps = $composites->get($parent);
                $check = $tmps->first(function ($item) use ($id) {
                    return $item->id_child == $id;
                });

                if ($check) {
                    throw new \Exception('Error Loop Back Reference');
                }

                $this->throwLoopBack($id, $tmps->pluck('id_child'), $composites);
            }
        }
    }
}
