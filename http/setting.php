<?php

use Shart\Models\Setting;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/setting', [
    'uses' => sprintf('%s@index', SettingController::class),
    'entity' => Setting::class,
    'permission' => 'system-setting:index'
]);

Route::get('/setting/{identity}', [
    'uses' => sprintf('%s@detail', SettingController::class),
    'entity' => Setting::class,
    'permission' => 'system-setting:detail'
]);

Route::post('/setting/{identity}/item', [
    'uses' => sprintf('%s@create', SettingController::class),
    'entity' => Setting::class,
    'permission' => 'system-setting:create'
]);

Route::post('/setting/{identity}/item/{id}', [
    'uses' => sprintf('%s@update', SettingController::class),
    'entity' => Setting::class,
    'permission' => 'system-setting:update'
]);

Route::delete('/setting/{identity}/item/{id}', [
    'uses' => sprintf('%s@delete', SettingController::class),
    'entity' => Setting::class,
    'permission' => 'system-setting:delete'
]);
