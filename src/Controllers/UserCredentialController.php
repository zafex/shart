<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shart\BaseController;
use Shart\Models\User;
use Shart\Models\UserCredential;

class UserCredentialController extends BaseController
{
    /**
     * @return mixed
     */
    public function create(string $id, Request $request, Hasher $hash)
    {
        $request->validate([
            'password' => 'required|confirmed',
            'note' => 'required',
        ]);

        if ($user = User::where('id', $id)->first()) {
            $credential = new UserCredential();
            $credential->password = $hash->make($request->get('password'));
            $credential->note = $request->get('note');
            $credential->id_user = $user->getKey();
            $credential->status = 1;
            $credential->expired_at = $request->get('expired_at');
            $credential->save();

            return $this->newResponse($credential, Response::HTTP_CREATED);
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }

    public function delete(string $id, string $password, Request $request)
    {
        if ($credential = UserCredential::where('id', $password)->where('id_user', $id)->first()) {
            $credential->status = 0;
            $credential->save();
        }

        return $this->newResponse();
    }
}
