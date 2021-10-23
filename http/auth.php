<?php

use Illuminate\Support\Facades\Route;
use Shart\Controllers\AuthController;

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

Route::post('/auth/generate', [
    AuthController::class, 'generate',
]);

Route::get('/auth/profile', [
    AuthController::class, 'profile',
]);

Route::delete('/auth/destroy', [
    AuthController::class, 'destroy',
]);

Route::any('/auth/callback/{provider}', [
    AuthController::class, 'callback',
]);
