<?php

use App\Http\Controllers\OutletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('dashboard');

Route::resource('outlet', OutletController::class);

