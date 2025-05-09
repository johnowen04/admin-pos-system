<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('dashboard');

Route::resource('outlet', OutletController::class);
Route::resource('role', RoleController::class);
Route::resource('employee', EmployeeController::class);

