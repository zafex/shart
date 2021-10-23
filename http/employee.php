<?php

use Shart\Models\Employee;
use Illuminate\Support\Facades\Route;
use Shart\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Master Employee Routes
|--------------------------------------------------------------------------
|
| Here is where you can register master routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "master" middleware group. Now create something great!
|
*/

Route::get('/employee', [
    'uses' => sprintf('%s@index', EmployeeController::class),
    'entity' => Employee::class,
    'permission' => 'master-employee:@index'
]);

Route::post('/employee', [
    'uses' => sprintf('%s@create', EmployeeController::class),
    'entity' => Employee::class,
    'permission' => 'master-employee:@create'
]);

Route::get('/employee/{id}', [
    'uses' => sprintf('%s@detail', EmployeeController::class),
    'entity' => Employee::class,
    'permission' => 'master-employee:@detail'
]);

Route::post('/employee/{id}', [
    'uses' => sprintf('%s@update', EmployeeController::class),
    'entity' => Employee::class,
    'permission' => 'master-employee:@update'
]);

Route::delete('/employee/{id}', [
    'uses' => sprintf('%s@delete', EmployeeController::class),
    'entity' => Employee::class,
    'permission' => 'master-employee:@delete'
]);
