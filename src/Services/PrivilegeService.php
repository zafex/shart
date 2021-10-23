<?php

declare(strict_types=1);

namespace Shart\Services;

use Closure;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Shart\CacheKey;
use Shart\Models\Role;
use Shart\Models\RoleDetail;
use Shart\Models\UserRole;

class PrivilegeService
{
    /**
     * @var mixed
     */
    protected $auth;

    /**
     * @var mixed
     */
    protected $cache;

    /**
     * @var mixed
     */
    protected $data = null;

    public function __construct(Factory $cache)
    {
        $this->cache = $cache->store();
        $this->prefill();
    }

    /**
     * @param Closure $handler
     */
    public function all(? Closure $handler = null): Enumerable
    {
        return null === $handler ? $this->data : $handler($this->data);
    }

    public function clear(): void
    {
        $this->cache->clear();
        $this->prefill();
    }

    public function getDefinedRole(string $identity): Role
    {
        return Role::firstOrCreate(
            [
                'identity' => $identity,
            ],
            [
                'label' => ucfirst($identity),
                'description' => ucfirst($identity),
                'status' => 1,
            ]
        );
    }

    /**
     * @param  $roles
     *
     * @return mixed
     */
    public function getUserPermissions($roles): Enumerable
    {
        $data = $this->data->filter(function ($item) use ($roles) {
            return \in_array($item['identity'], $roles);
        });

        return $data->pluck('permissions')->collapse()->unique();
    }

    /**
     * @param  $username
     *
     * @return mixed
     */
    public function getUserRoles($username): Enumerable
    {
        $usernames = is_scalar($username) ? [$username] : (array) $username;
        $query = UserRole::select('id_role');
        $query->whereHas('user', function ($query) use ($usernames) {
            $query->whereIn('username', $usernames);
            $query->where('status', 1);
        });
        $roleIds = $query->distinct()->get()->pluck('id_role');
        $data = $this->data->filter(function ($item) use ($roleIds) {
            return $roleIds->contains($item['id']);
        });

        return $data->pluck('identity');
    }

    protected function prefill(): void
    {
        $this->data = $this->cache->rememberForever(CacheKey::PRIVILEGE, function () {
            $tmp = new Collection();
            $query = Role::query();
            $query->where('status', 1);
            $query->with([
                'permissions' => function ($query) {
                    $query->where('status', 1);
                },
            ]);
            $roles = $query->get()->keyBy('id');
            $assignments = RoleDetail::all()->groupBy('id_role');

            foreach ($roles as $role) {
                $tmp->add(
                    Collection::wrap([
                        'id' => $role->getKey(),
                        'identity' => $role->identity,
                        'permissions' => $this->recursive($role, $roles, $assignments),
                    ])
                );
            }

            return $tmp;
        });
    }

    protected function recursive(Role $role, Enumerable $roles, Enumerable $assignments): Arrayable
    {
        $permissions = $role->permissions->map(function ($p) {
            return $p->identity;
        });

        if ($assignments->has($role->getKey())) {
            foreach ($assignments->get($role->getKey()) as $rel) {
                if ($roles->has($rel->id_target)) {
                    $child = $roles->get($rel->id_target);
                    $permissions = $permissions->merge($this->recursive($child, $roles, $assignments));
                }
            }
        }

        return $permissions;
    }
}
