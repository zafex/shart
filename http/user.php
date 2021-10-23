<?php

use Shart\Models\User;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\UserController;
use Shart\Controllers\UserCredentialController;

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

Route::get('/user', [
    'uses' => sprintf('%s@index', UserController::class),
    'entity' => User::class,
    'permission' => 'system-user:index',
]);

Route::post('/user', [
    'uses' => sprintf('%s@create', UserController::class),
    'entity' => User::class,
    'permission' => 'system-user:create',
]);

Route::get('/user/{id}', [
    'uses' => sprintf('%s@detail', UserController::class),
    'entity' => User::class,
    'permission' => 'system-user:detail',
]);

Route::post('/user/{id}', [
    'uses' => sprintf('%s@update', UserController::class),
    'entity' => User::class,
    'permission' => 'system-user:update',
]);

Route::delete('/user/{id}', [
    'uses' => sprintf('%s@delete', UserController::class),
    'entity' => User::class,
    'permission' => 'system-user:delete',
]);

Route::post('/user/{id}/credential', [
    'uses' => sprintf('%s@create', UserCredentialController::class),
    'entity' => User::class,
    'permission' => 'system-user:create-credential',
]);

Route::delete('/user/{id}/credential/{password}', [
    'uses' => sprintf('%s@delete', UserCredentialController::class),
    'entity' => User::class,
    'permission' => 'system-user:delete-credential',
]);
