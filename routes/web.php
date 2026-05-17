<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\ProductCategoriesController;
use App\Http\Controllers\Master\ProductBrandController;
use App\Http\Controllers\Master\ProductUnitController;

Route::get('/cool', function () {
    return view('dashboard.indexcool');
});

Route::get('/catalog', function(){
    return view('catalog.index');
});

Route::middleware('guest')->controller(AuthController::class)->group(function () {

    Route::get('/login', 'index')
        ->name('login');

    Route::post('/login/authenticate', 'authenticate')
        ->name('login.authenticate');

});

Route::middleware('auth')->group(function(){
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::get('/change-password', [AuthController::class, 'changePasswordView'])->name('change_password.index');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('change_password.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('master/user', [UserController::class, 'index'])->name('master.user.index');
    Route::get('master/user/create', [UserController::class, 'create'])->name('master.user.create');
    Route::post('master/user/create', [UserController::class, 'store'])->name('master.user.store');
    Route::get('master/user/show', [UserController::class, 'show'])->name('master.user.show');
    Route::get('master/user/{user:uuid}/edit', [UserController::class, 'edit'])->name('master.user.edit');
    Route::put('master/user/{user:uuid}/edit', [UserController::class, 'update'])->name('master.user.update');
    Route::delete('master/user/{user:uuid}/delete', [UserController::class, 'destroy'])->name('master.user.destroy');

    Route::get('master/categories', [ProductCategoriesController::class, 'index'])->name('master.category.index');
    Route::post('master/categories', [ProductCategoriesController::class, 'store'])->name('master.category.store');
    Route::get('master/categories/show', [ProductCategoriesController::class, 'show'])->name('master.category.show');
    Route::get('master/categories/{category:uuid}/edit', [ProductCategoriesController::class, 'edit'])->name('master.category.edit');
    Route::put('master/categories/{category:uuid}/edit', [ProductCategoriesController::class, 'update'])->name('master.category.update');
    Route::delete('master/categories/{category:uuid}/delete', [ProductCategoriesController::class, 'destroy'])->name('master.category.destroy');

    Route::get('master/brands', [ProductBrandController::class, 'index'])->name('master.brand.index');
    Route::post('master/brands', [ProductBrandController::class, 'store'])->name('master.brand.store');
    Route::get('master/brands/show', [ProductBrandController::class, 'show'])->name('master.brand.show');
    Route::get('master/brands/{brand:uuid}/edit', [ProductBrandController::class, 'edit'])->name('master.brand.edit');
    Route::put('master/brands/{brand:uuid}/edit', [ProductBrandController::class, 'update'])->name('master.brand.update');
    Route::delete('master/brands/{brand:uuid}/delete', [ProductBrandController::class, 'destroy'])->name('master.brand.destroy');

    Route::get('master/units', [ProductUnitController::class, 'index'])->name('master.unit.index');
    Route::post('master/units', [ProductUnitController::class, 'store'])->name('master.unit.store');
    Route::get('master/units/show', [ProductUnitController::class, 'show'])->name('master.unit.show');
    Route::get('master/units/{unit:uuid}/edit', [ProductUnitController::class, 'edit'])->name('master.unit.edit');
    Route::put('master/units/{unit:uuid}/edit', [ProductUnitController::class, 'update'])->name('master.unit.update');
    Route::delete('master/units/{unit:uuid}/delete', [ProductUnitController::class, 'destroy'])->name('master.unit.destroy');
});
