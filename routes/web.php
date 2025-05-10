<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('dashboard');

Route::resource('outlet', OutletController::class);
Route::resource('role', RoleController::class);
Route::resource('employee', EmployeeController::class);
Route::resource('unit', UnitController::class);
Route::resource('department', DepartmentController::class);
Route::resource('category', CategoryController::class);
Route::resource('product', ProductController::class);

