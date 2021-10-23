<?php

declare(strict_types=1);

namespace Shart\Services;

use DateTimeImmutable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\BitbucketProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GitlabProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\LinkedInProvider;
use Shart\Models\User;
use Shart\Models\UserCredential;
use Shart\Models\UserLink;
use Shart\Models\UserSession;

class AuthService
{
    /**
     * @var array
     */
    protected $providers = [
        'github' => GithubProvider::class,
        'facebook' => FacebookProvider::class,
        'google' => GoogleProvider::class,
        'linkedin' => LinkedInProvider::class,
        'bitbucket' => BitbucketProvider::class,
        'gitlab' => GitlabProvider::class,
    ];

    /**
     * @var mixed
     */
    private $container;

    /**
     * @var mixed
     */
    private $hasher;

    public function __construct(Container $container, Hasher $hasher)
    {
        $this->container = $container;
        $this->hasher = $hasher;
    }

    /**
     * @return mixed
     */
    public function login(string $username, string $password, DateTimeImmutable $expired)
    {
        $query = UserCredential::query();
        $query->with('user');
        $query->whereHas('user', function ($query) use ($username) {
            $query->where('status', 1);
            $query->where('username', $username);
        });
        $query->where('status', 1);

        $credentials = $query->get();
        $credential = $credentials->first(function ($credential) use ($password) {
            return $this->hasher->check($password, $credential->password);
        });

        if (null !== $credential) {
            $credential->lastused = date('Y-m-d H:i:s');
            $credential->save();

            return $this->makeSession($credential->user, 'application', $expired);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function makeSession(User $user, string $provider, DateTimeImmutable $expired)
    {
        $session = new UserSession();
        $session->fill([
            'id_user' => $user->getKey(),
            'provider' => $provider,
            'expired_at' => $expired->format('Y-m-d H:i:s'),
        ]);
        $session->save();

        return $session;
    }

    public function withProvider(string $provider, array $config, DateTimeImmutable $expired)
    {
        $target = $this->buildProvider($this->providers[$provider], $config)->stateless();

        if ($data = $target->user()) {
            $attributes = [
                'id_object' => $data->id,
                'username' => $data->nickname ?: $data->email,
                'fullname' => $data->name,
                'avatar' => $data->avatar,
                'email' => $data->email ?: 'fajrulaz@gmail.com',
            ];
            $qlink = UserLink::query();
            $qlink->with('user');
            $qlink->where('provider', $provider);
            $qlink->where('reference', 'profile');
            $qlink->where('id_object', $attributes['id_object']);

            if ($link = $qlink->first()) {
                $user = $link->user;
            } else {
                $user = new User();
                $user->fill(Arr::except($attributes, ['id_object']));
                $user->status = 1;
                $user->created_at = date('Y-m-d H:i:s');
                $user->save();
                $link = new UserLink([
                    'provider' => $provider,
                    'reference' => 'profile',
                    'id_object' => $attributes['id_object'],
                    'id_user' => $user->getKey(),
                ]);
                $link->save();
            }

            if (null !== $user && 1 == $user->status) {
                return $this->makeSession($user, $provider, $expired);
            }
        }

        return null;
    }

    /**
     * @param $provider
     * @param $config
     */
    protected function buildProvider($provider, $config)
    {
        return new $provider(
            $this->container->make('request'),
            $config['client_id'],
            $config['client_secret'],
            $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/') ? $this->container->make('url')->to($redirect) : $redirect;
    }
}
