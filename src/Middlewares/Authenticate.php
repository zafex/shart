<?php

declare(strict_types=1);

namespace Shart\Middlewares;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Routing\Router;
use Shart\Authority;
use Shart\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Authenticate extends IlluminateAuthenticate
{
    /**
     * @var mixed
     */
    protected $author;

    /**
     * @var mixed
     */
    protected $route;

    /**
     * @var mixed
     */
    protected $user;

    public function __construct(Auth $auth, Router $router, Authority $author)
    {
        parent::__construct($auth);
        $this->user = $auth->user();
        $this->route = $router->current();
        $this->author = $author;
    }

    /**
     * @param  $request
     * @param  $guards
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $res = true;
        $entity = $this->route->getAction('entity');
        $permission = $this->route->getAction('permission');

        if (null !== $permission) {
            $res = null !== $this->user && $this->user->hasPermission($permission);

            if (false === $res && null !== $entity && $this->route->hasParameter('id')) {
                $query = $entity::query();
                $query->where('id', $this->route->parameter('id'));
                $query->where(function ($query) use ($entity) {
                    $query->where('created_by', $this->author->getAuthor());

                    if (User::class === $entity && null !== $this->user && $this->user->getAuthIdentifier() !== User::GUEST) {
                        $query->orWhere(function ($query) {
                            $query->where('username', $this->user->getAuthIdentifier());
                            $query->where('status', 1);
                        });
                    }
                });
                $res = $query->exists();
            }
        }

        if (false === $res) {
            $this->unauthenticated($request, []);
        }

        return $next($request);
    }

    /**
     * @param $request
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new HttpException(403, 'Not Permitted');
    }
}
