<?php

use Shart\Models\Role;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| Role Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/role', [
	'uses' => sprintf('%s@index', RoleController::class),
	'entity' => Role::class,
	'permission' => 'system-role:index'
]);

Route::post('/role', [
	'uses' => sprintf('%s@create', RoleController::class),
	'entity' => Role::class,
	'permission' => 'system-role:create'
]);

Route::get('/role/{id}', [
	'uses' => sprintf('%s@detail', RoleController::class),
	'entity' => Role::class,
	'permission' => 'system-role:detail'
]);

Route::post('/role/{id}', [
	'uses' => sprintf('%s@update', RoleController::class),
	'entity' => Role::class,
	'permission' => 'system-role:update'
]);

Route::delete('/role/{id}', [
	'uses' => sprintf('%s@delete', RoleController::class),
	'entity' => Role::class,
	'permission' => 'system-role:delete'
]);
