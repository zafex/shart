<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shart\BaseController;
use Shart\IndexAction;
use Shart\Models\Employee;
use Shart\Models\User;
use Shart\Models\UserCredential;
use Shart\Models\UserLink;

class EmployeeController extends BaseController
{
    use IndexAction;

    /**
     * @var string
     */
    protected $indexName = 'employee';

    /**
     * @return mixed
     */
    public function create(Request $request, Hasher $hash)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'code' => 'required|unique:mst_employee,code',
            'id_organization' => 'required|exists:mst_organization,id',
            'username' => 'required|unique:sys_user,username',
            'credentials' => 'array',
            'credentials.*.password' => 'sometimes|required',
            'credentials.*.note' => 'required_with:credentials.*.password',
        ]);

        $employee = app('db')->transaction(function () use ($request, $hash) {
            $employee = new Employee();
            $employee->fill($request->all());
            $employee->status = 1;
            $employee->save();

            $user = new User([
                'username' => $request->get('username'),
                'fullname' => $request->get('name'),
                'email' => $request->get('email'),
            ]);
            $user->save();
            $link = new UserLink([
                'id_user' => $user->getKey(),
                'id_object' => $employee->getKey(),
                'reference' => 'employee',
                'provider' => 'application',
            ]);
            $link->save();

            if ($credentials = $request->get('credentials')) {
                foreach ($credentials as $credential) {
                    $uc = new UserCredential();
                    $uc->id_user = $user->getKey();
                    $uc->password = $hash->make($credential['password']);
                    $uc->note = $credential['note'];
                    $uc->save();
                }
            }

            return $employee;
        });
        $employee->load(['user', 'organization', 'compositions']);

        return $this->newResponse($employee, Response::HTTP_CREATED);
    }

    public function delete(string $id, Request $request)
    {
        if ($employee = Employee::where('id', $id)->first()) {
            $employee->status = 0;
            $employee->deleted_at = date('Y-m-d H:i:s');
            $employee->deleted_by = $request->user()->username;
            $employee->save();
        }

        return $this->newResponse();
    }

    public function detail(string $id)
    {
        return Employee::with(['user', 'organization', 'compositions'])->where('id', $id)->where('status', 1)->first();
    }

    public function update(string $id, Request $request)
    {
        if ($employee = Employee::where('id', $id)->first()) {
            $employee->fill($request->all());
            $employee->status = 1;
            $employee->updated_at = date('Y-m-d H:i:s');
            $employee->updated_by = $request->user()->username;
            $employee->save();

            $employee->load(['user', 'organization', 'compositions']);

            return $employee;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }
}
