<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shart\BaseController;
use Shart\Models\User;

class UserController extends BaseController
{
    /**
     * @var string
     */
    public function index()
    {
        return User::all();
    }

    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:sys_user',
            'fullname' => 'required',
            'email' => 'required',
        ]);

        $model = app('db')->transaction(function () use ($request) {
            $user = new User();
            $user->fill($request->only(['username', 'fullname', 'email']));
            $user->status = 1;
            $user->save();
            if ($roles = $request->get('roles')) {
                $user->roles()->sync($roles);
            }

            return $user;
        });

        return $this->newResponse(
            $this->detail(
                $model->getKey()
            ),
            Response::HTTP_CREATED
        );
    }

    public function delete(string $id, Request $request)
    {
        if ($user = User::where('id', $id)->orWhere('username', $id)->first()) {
            $user->status = 0;
            $user->save();
        }

        return $this->newResponse();
    }

    public function detail(string $id)
    {
        $query = User::query();
        $query->where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('username', $id);
        });

        if ($user = $query->first()) {
            $user->load('roles');

            return $user;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }

    public function update(string $id, Request $request)
    {
        $this->validate($request, [
            'fullname' => 'sometimes|required',
            'email' => 'sometimes|required',
        ]);

        if ($user = User::where('id', $id)->orWhere('username', $id)->first()) {
            app('db')->transaction(function () use ($user, $request) {
                $user->fill($request->only(['fullname', 'email']));
                $user->save();
                if ($request->has('roles')) {
                    $user->roles()->sync($request->get('roles'));
                }
            });
            $user->load(['roles', 'links', 'credentials']);

            return $user;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }
}
