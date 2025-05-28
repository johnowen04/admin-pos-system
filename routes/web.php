<?php

use App\Http\Controllers\ACLController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseUnitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\SelectOutletController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');

    Route::post('/select-outlet', [SelectOutletController::class, 'select'])->name('select-outlet.select');
    Route::post('/import-products', [ProductController::class, 'importProducts'])->name('import.products');

    Route::prefix('acl')->group(function () {
        Route::get('/', [ACLController::class, 'index'])->name('acl.index');
        Route::match(['put', 'patch'], '{acl}', [ACLController::class, 'update'])->name('acl.update');
        Route::fallback(function () {
            abort(404);
        });
    });

    Route::post('/permission/toggle-superuser', [PermissionController::class, 'toggleSuperUserOnly'])->name('permission.toggle-superuser');
    Route::post('/permission/batch', [PermissionController::class, 'batch'])->name('permission.batch');
    Route::resource('permission', PermissionController::class);

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('payment', [POSController::class, 'payment'])->name('payment');
        Route::post('process-payment', [POSController::class, 'processPayment'])->name('processPayment');
        Route::get('receipt/{id?}', [POSController::class, 'receipt'])->name('receipt');

        Route::prefix('cart')->group(function () {
            Route::get('/', [POSController::class, 'getCart'])->name('getCart');
            Route::post('add', [POSController::class, 'addToCart'])->name('addToCart');
            Route::post('remove', [POSController::class, 'removeFromCart'])->name('removeFromCart');
            Route::post('clear', [POSController::class, 'clearCart'])->name('clearCart');
        });
    });

    Route::put('/purchase/{purchase}/void', [PurchaseController::class, 'void'])->name('purchase.void');
    Route::resource('purchase', PurchaseController::class);

    Route::put('/sales/{sale}/void', [SalesController::class, 'void'])->name('sales.void');
    Route::resource('sales', SalesController::class);

    Route::prefix('reports/sales')->name('reports.sales.')->group(function () {
        Route::get('/products', [SalesReportController::class, 'products'])->name('products');
        Route::get('/products/export', [SalesReportController::class, 'productsExport'])->name('products.export');

        Route::get('/categories', [SalesReportController::class, 'categories'])->name('categories');
        Route::get('/categories/export', [SalesReportController::class, 'categoriesExport'])->name('categories.export');

        Route::get('/departments', [SalesReportController::class, 'departments'])->name('departments');
        Route::get('/departments/export', [SalesReportController::class, 'departmentsExport'])->name('departments.export');
    });

    Route::resource('bu', BaseUnitController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('outlet', OutletController::class);
    Route::resource('employee', EmployeeController::class);
    Route::resource('feature', FeatureController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('operation', OperationController::class);
    Route::resource('product', ProductController::class);
    Route::resource('position', PositionController::class);
    Route::resource('unit', UnitController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
