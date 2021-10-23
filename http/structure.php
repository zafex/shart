<?php

use Shart\Models\Structure;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\StructureController;

/*
|--------------------------------------------------------------------------
| Master Structure Routes
|--------------------------------------------------------------------------
|
| Here is where you can register master routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "master" middleware group. Now create something great!
|
*/

Route::get('/structure', [
    'uses' => sprintf('%s@index', StructureController::class),
    'entity' => Structure::class,
    'permission' => 'master-structure:index'
]);

Route::post('/structure', [
    'uses' => sprintf('%s@create', StructureController::class),
    'entity' => Structure::class,
    'permission' => 'master-structure:create'
]);

Route::get('/structure/{id}', [
    'uses' => sprintf('%s@detail', StructureController::class),
    'entity' => Structure::class,
    'permission' => 'master-structure:detail'
]);

Route::post('/structure/{id}', [
    'uses' => sprintf('%s@update', StructureController::class),
    'entity' => Structure::class,
    'permission' => 'master-structure:update'
]);

Route::delete('/structure/{id}', [
    'uses' => sprintf('%s@delete', StructureController::class),
    'entity' => Structure::class,
    'permission' => 'master-structure:delete'
]);
