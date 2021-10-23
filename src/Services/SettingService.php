<?php

declare(strict_types=1);

namespace Shart\Services;

use Closure;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Arr;
use Shart\CacheKey;
use Shart\Helpers\SettingCollection;
use Shart\Models\Setting;
use Shart\Models\SettingItem;

/**
 * Config service.
 */
class SettingService
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
    public function all(Closure $handler = null)
    {
        return $this->data;
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

    /**
     * @return mixed
     */
    protected function collect(array $data)
    {
        $collection = new SettingCollection();

        foreach ($data as $key => $value) {
            $collection->put($key, $value instanceof SettingItem ? $value : $this->collect($value));
        }

        return $collection;
    }

    /**
     * @return mixed
     */
    protected function prefill()
    {
        $this->data = $this->cache->rememberForever(CacheKey::SETTING, function () {
            $query = Setting::query();
            $query->with([
                'items' => function ($query) {
                    $query->where('status', 1);
                },
            ]);
            $query->where('status', 1);
            $settings = $query->get();

            $data = [];

            foreach ($settings as $setting) {
                foreach ($setting->items as $item) {
                    Arr::set($data, sprintf('%s.%s', $setting->identity, $item->identity), $item);
                }
            }

            return $this->collect($data);
        });
    }
}
