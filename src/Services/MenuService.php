<?php

declare(strict_types=1);

namespace Shart\Services;

use Closure;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Arr;
use Shart\CacheKey;
use Shart\Models\Menu;

/**
 * Config service.
 */
class MenuService
{
    /**
     * @var mixed
     */
    protected $cache;

    /**
     * @var array
     */
    protected $data = [];

    public function __construct(Factory $cache)
    {
        $this->cache = $cache->store();
        $this->prefill();
    }

    /**
     * @param Closure $handler
     */
    public function getFilter(Closure $handler = null)
    {
        $results = [];
        foreach ($this->data as $setting => $menus) {
            $tmps = [];
            foreach ($menus as $parent => $items) {
                foreach ($items as $item) {
                    if (\array_key_exists($parent, $tmps)) {
                        $tmps[$parent] = [];
                    }
                    if (true === $handler($item)) {
                        $tmps[$parent][] = array_merge(Arr::only($item->getAttributes(), ['id', 'id_parent', 'label', 'icon', 'description']), [
                            'url' => str_replace('{base}', $item->category->value, $item->url),
                        ]);
                    }
                }
            }
            $results[$setting] = $this->makeRecursive('__parent', $tmps);
        }

        return $results;
    }

    public function clear(): void
    {
        $this->cache->clear();
        $this->prefill();
    }

    public function fetch(string $identity)
    {
        return Arr::get($this->data, $identity);
    }

    public function makeRecursive(string $key, array $menus, ?string $parent = null)
    {
        $results = [];
        if (\array_key_exists($key, $menus)) {
            foreach ($menus[$key] as $index => $menu) {
                $results[] = array_merge($menu, [
                    'childs' => $this->makeRecursive(Arr::get($menu, 'id'), $menus),
                ]);
            }
        }

        return $results;
    }

    public function prepareRecursive(array $menus)
    {
        $tmp = [];
        foreach ($menus as $menu) {
            $key = null === $menu->id_parent ? '__parent' : $menu->id_parent;
            if (!\array_key_exists($key, $tmp)) {
                $tmp[$key] = [];
            }
            $tmp[$key][] = $menu;
        }

        return $tmp;
    }

    /**
     * @return mixed
     */
    protected function prefill()
    {
        $this->data = $this->cache->rememberForever(CacheKey::MENU, function () {
            $query = Menu::query();
            $query->with(['role', 'category']);
            $query->where('status', 1);
            $query->whereHas('category', function ($query) {
                $query->where('status', 1);
            });
            $query->orderBy('order', 'asc');
            $results = $query->get();

            $menus = [];
            $tmps = [];
            foreach ($results as $menu) {
                if (!\array_key_exists($menu->category->identity, $tmps)) {
                    $tmps[$menu->category->identity] = [];
                }
                $tmps[$menu->category->identity][] = $menu;
            }
            foreach ($tmps as $key => $tmp) {
                $menus[$key] = $this->prepareRecursive($tmp);
            }

            return $menus;
        });
    }
}
