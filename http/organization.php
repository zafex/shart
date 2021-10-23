<?php

use Shart\Models\Organization;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\OrganizationController;

/*
|--------------------------------------------------------------------------
| Master Organization Routes
|--------------------------------------------------------------------------
|
| Here is where you can register master routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "master" middleware group. Now create something great!
|
*/

Route::get('/organization', [
    'uses' => sprintf('%s@index', OrganizationController::class),
    'entity' => Organization::class,
    'permission' => 'master-organization:index'
]);

Route::post('/organization', [
    'uses' => sprintf('%s@create', OrganizationController::class),
    'entity' => Organization::class,
    'permission' => 'master-organization:create'
]);

Route::get('/organization/{id}', [
    'uses' => sprintf('%s@detail', OrganizationController::class),
    'entity' => Organization::class,
    'permission' => 'master-organization:detail'
]);

Route::post('/organization/{id}', [
    'uses' => sprintf('%s@update', OrganizationController::class),
    'entity' => Organization::class,
    'permission' => 'master-organization:update'
]);

Route::delete('/organization/{id}', [
    'uses' => sprintf('%s@delete', OrganizationController::class),
    'entity' => Organization::class,
    'permission' => 'master-organization:delete'
]);

Route::post('/organization/{id}/sign', [
    'uses' => sprintf('%s@sign', OrganizationController::class),
    'entity' => Organization::class,
    'permission' => 'master-organization:sign'
]);
