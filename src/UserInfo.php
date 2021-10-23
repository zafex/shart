<?php

declare(strict_types=1);

namespace Shart;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Shart\Models\Role;
use Shart\Type;

class UserInfo extends GenericUser implements AuthorizableContract, Arrayable
{
    use Authenticatable;
    use Authorizable;

    /**
     * @param $name
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $link
     */
    public function addLink($link): void
    {
        $this->setLinks([$link]);
    }

    /**
     * @param $permission
     */
    public function addPermission($permission): void
    {
        $this->setPermissions([$permission]);
    }

    /**
     * @param $role
     */
    public function addRole($role): void
    {
        $this->setRoles([$role]);
    }

    /**
     * @param $name
     * @param $def
     */
    public function get($name, $def = null)
    {
        return \array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $def;
    }

    /**
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->get($this->getAuthIdentifierName());
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthPassword()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getPermissions(): array
    {
        return $this->get('permissions', []);
    }

    public function getRememberToken()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getRoles(): array
    {
        return $this->get('roles', []);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->get('type') === Type::SUPER) {
            return true;
        }
        return \in_array($permission, $this->get('permissions'));
    }

    public function hasRole(string $role): bool
    {
        if ($this->get('type') === Type::SUPER) {
            return true;
        }
        return \in_array($role, $this->get('roles'));
    }

    /**
     * @param $name
     * @param $val
     */
    public function set($name, $val = null)
    {
        $this->attributes[$name] = $val;
    }

    public function setLinks(array $links): void
    {
        if (!\array_key_exists('links', $this->attributes)) {
            $this->attributes['links'] = [];
        }

        foreach ($links as $link) {
            $this->attributes['links'][] = $link;
        }
    }

    public function setPermissions(array $permissions): void
    {
        if (!\array_key_exists('permissions', $this->attributes)) {
            $this->attributes['permissions'] = [];
        }

        foreach ($permissions as $permission) {
            if (!\in_array($permission, $this->attributes['permissions'])) {
                $this->attributes['permissions'][] = $permission;
            }
        }
    }

    /**
     * @param $value
     */
    public function setRememberToken($value)
    {
        // do nothing
    }

    public function setRoles(array $roles): void
    {
        if (!\array_key_exists('roles', $this->attributes)) {
            $this->attributes['roles'] = [];
        }

        foreach ($roles as $role) {
            if (!\in_array($role, $this->attributes['roles'])) {
                $this->attributes['roles'][] = $role;
            }
        }
    }

    /**
     * @return mixed
     */
    public function toArray(): array
    {
        return null === $this->attributes ? [] : $this->attributes;
    }
}
