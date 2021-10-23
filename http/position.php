<?php

use Shart\Models\Position;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\PositionController;

/*
|--------------------------------------------------------------------------
| Master Position Routes
|--------------------------------------------------------------------------
|
| Here is where you can register master routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "master" middleware group. Now create something great!
|
*/

Route::get('/position', [
    'uses' => sprintf('%s@index', PositionController::class),
    'entity' => Position::class,
    'permission' => 'master-position:index'
]);

Route::post('/position', [
    'uses' => sprintf('%s@create', PositionController::class),
    'entity' => Position::class,
    'permission' => 'master-position:create'
]);

Route::get('/position/{id}', [
    'uses' => sprintf('%s@detail', PositionController::class),
    'entity' => Position::class,
    'permission' => 'master-position:detail'
]);

Route::post('/position/{id}', [
    'uses' => sprintf('%s@update', PositionController::class),
    'entity' => Position::class,
    'permission' => 'master-position:update'
]);

Route::delete('/position/{id}', [
    'uses' => sprintf('%s@delete', PositionController::class),
    'entity' => Position::class,
    'permission' => 'master-position:delete'
]);
