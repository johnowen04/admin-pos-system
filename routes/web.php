<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseUnitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/logout', function () {
    return redirect()->route('login');
});

Route::get('/pos', [POSController::class, 'index'])->name('pos.index')->middleware('auth');
Route::post('/pos/payment', [POSController::class, 'payment'])->name('pos.payment')->middleware('auth');
Route::post('/pos/receipt', [POSController::class, 'receipt'])->name('pos.receipt')->middleware('auth');

Route::get('/dashboard', function () {
    return view('index');
})->name('dashboard')->middleware('auth');


Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('outlet', OutletController::class);
    Route::resource('role', RoleController::class);
    Route::resource('employee', EmployeeController::class);
    Route::resource('baseunit', BaseUnitController::class);
    Route::resource('unit', UnitController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('product', ProductController::class);
    Route::resource('purchase-invoice', PurchaseInvoiceController::class);
    Route::resource('sales-invoice', SalesInvoiceController::class);
});
