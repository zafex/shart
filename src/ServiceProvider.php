<?php

declare(strict_types=1);

namespace Shart;

use Exception;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Events\NullDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Ramsey\Uuid\Uuid;
use Shart\Observers\AuditObserver;
use Shart\Observers\AuthorObserver;
use Shart\Observers\UuidObserver;
use Shart\Services\LogService;
use Shart\Services\MenuService;
use Shart\Services\PrivilegeService;
use Shart\Services\TokenService;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $shart = realpath(__DIR__.'/../config.php');

        $this->publishes([$shart => config_path('shart.php')], 'config');

        $this->mergeConfigFrom($shart, 'shart');

        $permissions = realpath(__DIR__.'/../permissions.php');

        $this->publishes([$permissions => config_path('permissions.php')], 'permissions');

        $this->mergeConfigFrom($permissions, 'permissions');

        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));

        $this->bootObservers();

        $this->bootAuthenticators();

        app('db')->listen(function ($query) {
            app(LogService::class)->query($query->sql, $query->bindings, $query->time);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Request::macro('getRequestIdentity', function () {
            if (!isset($this->requestIdentity)) {
                $this->requestIdentity = Uuid::uuid4()->toString();
            }

            return $this->requestIdentity;
        });

        $this->app->singleton(LogService::class);
    }

    /**
     * @return mixed
     */
    private function bootAuthenticators()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            try {
                $token = null;

                if ($header = $request->header(config(Property::HTTP_HEADER))) {
                    [$name, $tmp] = sscanf($header, '%s %s');

                    if (config(Property::HTTP_BEARER) === $name) {
                        $token = $tmp;
                    }
                }

                if (null === $token) {
                    $token = $request->get(config(Property::HTTP_QTOKEN));
                }

                if (null !== $token && !empty($token) && 2 === substr_count($token, '.')) {
                    return $this->getUserInfo($token);
                }
            } catch (Exception $e) {
                throw $e;
            }

            return null;
        });
    }

    private function bootObservers()
    {
        Models\Employee::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Message::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\MessageTarget::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Notification::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Organization::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Permission::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Position::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Role::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Setting::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\SettingItem::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Structure::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\User::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\UserCredential::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\UserLink::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\UserSession::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
        Models\Menu::observe([AuthorObserver::class, UuidObserver::class, AuditObserver::class]);
    }

    /**
     * @return mixed
     */
    private function getUserInfo(string $token)
    {
        $payload = app(TokenService::class)->extract($token);

        if (!\array_key_exists('session', $payload)) {
            return null;
        }

        if (!\array_key_exists('username', $payload)) {
            return null;
        }

        $ckey = sprintf('%s:%s:%s', CacheKey::TOKEN, $payload['session'], $payload['username']);

        return app(Factory::class)->store()->remember($ckey, 120, function () use ($payload) {
            $result = null;

            $dispatcher = app('db')->getEventDispatcher();

            if ($dispatcher) {
                app('db')->setEventDispatcher(new NullDispatcher($dispatcher));
            }

            try {
                $query = Models\UserSession::with('user.links');
                $query->where('id', $payload['session']);
                $query->whereHas('user', function ($query) use ($payload) {
                    $query->where('status', 1);
                    $query->where('username', $payload['username']);
                });
                $session = $query->first();

                if (!$session) {
                    return null;
                }

                if (1 != $session->status) {
                    return null;
                }

                $user = Arr::except($session->user->getAttributes(), [
                    'updated_at',
                    'updated_by',
                    'deleted_at',
                    'deleted_by',
                ]);
                $links = $session->user->links;
                $roles = app(PrivilegeService::class)->getUserRoles($payload['username'])->toArray();
                $permissions = app(PrivilegeService::class)->getUserPermissions($roles)->toArray();
                $menus = app(MenuService::class)->getFilter(function ($menu) use ($roles) {
                    return null === $menu->role || \in_array($menu->role->identity, $roles);
                });

                $attributes = array_merge($user, compact('session', 'links', 'roles', 'permissions', 'menus'));

                $result = new UserInfo($attributes);
                $result->set('session', $session->getKey());
            } finally {
                if ($dispatcher) {
                    app('db')->setEventDispatcher($dispatcher);
                }
            }

            return $result;
        });
    }
}
