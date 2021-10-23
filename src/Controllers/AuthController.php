<?php

declare(strict_types=1);

namespace Shart\Controllers;

use DateTimeImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Shart\BaseController;
use Shart\Models\UserSession;
use Shart\Services\AuthService;
use Shart\Services\LogService;
use Shart\Services\PrivilegeService;
use Shart\Services\SettingService;
use Shart\Services\TokenService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends BaseController
{
    /**
     * @return mixed
     */
    public function callback(
        string $provider,
        Request $request,
        AuthService $authService,
        SettingService $settingService,
        TokenService $tokenService
    ) {
        $expired = new DateTimeImmutable('+ 3600 seconds');
        $session = app('db')->transaction(function () use ($provider, $expired, $authService, $settingService) {
            $config = $settingService->fetch(sprintf('oauth.%s', $provider));

            return $authService->withProvider($provider, $config->toArray(), $expired);
        });

        if (null !== $session) {
            return [
                'id' => $request->getRequestIdentity(),
                'token' => $tokenService->generate($expired, [
                    'id' => $session->id_user,
                    'username' => $session->user->username,
                    'fullname' => $session->user->fullname,
                    'email' => $session->user->email,
                    'avatar' => $session->user->avatar,
                    'session' => $session->getKey(),
                ]),
            ];
        }

        throw new HttpException(400, 'User not exists');
    }

    /**
     * @param Authenticatable $auth
     *
     * @return mixed
     */
    public function destroy(? Authenticatable $auth)
    {
        if (null !== $auth) {
            UserSession::where('id', $auth->session)->update([
                'status' => 0,
            ]);
        }

        return $this->newResponse();
    }

    public function generate(
        Request $request,
        AuthService $authService,
        TokenService $tokenService,
        LogService $logService,
        PrivilegeService $privilegeService
    ) {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $expired = new DateTimeImmutable('+ 3600 seconds');
        if ($session = $authService->login($request->get('username'), $request->get('password'), $expired)) {
            return [
                'id' => $request->getRequestIdentity(),
                'token' => $tokenService->generate($expired, [
                    'id' => $session->id_user,
                    'username' => $session->user->username,
                    'fullname' => $session->user->fullname,
                    'email' => $session->user->email,
                    'avatar' => $session->user->avatar,
                    'session' => $session->getKey(),
                ]),
            ];
        }

        throw new HttpException(400, 'Wrong password or username');
    }

    /**
     * @param Authenticatable $auth
     *
     * @return mixed
     */
    public function profile(? Authenticatable $auth)
    {
        if (null !== $auth) {
            return $auth;
        }

        throw new HttpException(401, 'Unauthorized User');
    }
}
