<?php

use Shart\Models\Notification;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Master Notification Routes
|--------------------------------------------------------------------------
|
| Here is where you can register master routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "master" middleware group. Now create something great!
|
*/

Route::get('/notification', [
    'uses' => sprintf('%s@index', NotificationController::class),
]);

Route::get('/notification/{id}', [
    'uses' => sprintf('%s@detail', NotificationController::class),
]);