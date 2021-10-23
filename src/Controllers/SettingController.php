<?php

declare(strict_types=1);

namespace Shart\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shart\BaseController;
use Shart\IndexAction;
use Shart\Models\Setting;
use Shart\Models\SettingItem;
use Shart\Services\SettingService;

class SettingController extends BaseController
{
    use IndexAction;

    /**
     * @var string
     */
    protected $indexName = 'setting';

    public function create(string $identity, Request $request, SettingService $settingService)
    {
        $this->validate($request, [
            'identity' => 'required',
            'label' => 'required',
            'value' => 'required',
            'description' => 'required',
            'visibility' => 'required',
        ]);

        $item = app('db')->transaction(function () use ($identity, $request) {
            if ($setting = Setting::where('identity', $identity)->first()) {
                $item = new SettingItem($request->only(['identity', 'label', 'value', 'description', 'status', 'visibility']));
                $item->id_setting = $setting->getKey();
                $item->save();

                return $item;
            }
        });

        if ($item) {
            $settingService->clear();

            return $item;
        }
    }

    public function delete(string $identity, string $id, Request $request, SettingService $settingService)
    {
        $query = SettingItem::query();
        $query->where('id', $id);
        $query->whereHas('setting', function ($query) use ($identity) {
            $query->where('identity', $identity);
        });

        if ($item = $query->first()) {
            $item->status = 0;
            $item->save();
            $settingService->clear();

            return $this->newResponse();
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Setting Not Found');
    }

    /**
     * Description.
     *
     * @return type
     */
    public function detail(string $identity, Request $request)
    {
        if ($setting = Setting::with('items')->where('identity', $identity)->first()) {
            return $setting;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Setting Not Found');
    }

    public function update(string $identity, string $id, Request $request, SettingService $settingService)
    {
        $this->validate($request, [
            'identity' => 'sometimes|required',
            'label' => 'sometimes|required',
            'value' => 'sometimes|required',
            'desciption' => 'sometimes|required',
            'visibility' => 'sometimes|required',
            'status' => 'sometimes|required',
        ]);

        $query = SettingItem::query();
        $query->where('id', $id);
        $query->whereHas('setting', function ($query) use ($identity) {
            $query->where('identity', $identity);
        });

        if ($item = $query->first()) {
            $item->fill($request->only(['identity', 'label', 'status', 'value', 'desciption', 'visibility']));
            $item->save();
            $settingService->clear();

            return $item;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Setting Not Found');
    }
}
