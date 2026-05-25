<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\ProductCategoriesController;
use App\Http\Controllers\Master\ProductBrandController;
use App\Http\Controllers\Master\ProductUnitController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\CatalogController;
Use App\Http\Controllers\Settings\GroupUsersController;

Route::get('/cool', function () {
    return view('dashboard.indexcool');
});

Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/search-categories', [CatalogController::class, 'searchCategories'])->name('catalog.search.categories');
Route::get('/search-brands', [CatalogController::class, 'searchBrands'])->name('catalog.search.brands');

Route::middleware('guest')->controller(AuthController::class)->group(function () {

    Route::get('/login', 'index')
        ->name('login');

    Route::post('/login/authenticate', 'authenticate')
        ->name('login.authenticate');

});

Route::middleware('auth')->group(function(){
    Route::get('/admin', function () {
        return view('dashboard.index');
    })->name('dashboard.index');

    Route::get('/change-password', [AuthController::class, 'changePasswordView'])->name('change_password.index');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('change_password.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('permission:create-user')->group(function(){
        Route::get('master/user/create', [UserController::class, 'create'])->name('master.user.create');
        Route::post('master/user/create', [UserController::class, 'store'])->name('master.user.store');
    });

    Route::middleware('permission:read-user')->group(function(){
        Route::get('master/user', [UserController::class, 'index'])->name('master.user.index');
        Route::get('master/user/show', [UserController::class, 'show'])->name('master.user.show');
        Route::get('master/user/{user:uuid}/edit', [UserController::class, 'edit'])->name('master.user.edit');
    });

    Route::middleware('permission:update-user')->group(function(){
        Route::put('master/user/{user:uuid}/edit', [UserController::class, 'update'])->name('master.user.update');
    });

    Route::middleware('permission:delete-user')->group(function(){
        Route::delete('master/user/{user:uuid}/delete', [UserController::class, 'destroy'])->name('master.user.destroy');
    });


    Route::get('master/categories', [ProductCategoriesController::class, 'index'])->name('master.category.index');
    Route::post('master/categories', [ProductCategoriesController::class, 'store'])->name('master.category.store');
    Route::get('master/categories/show', [ProductCategoriesController::class, 'show'])->name('master.category.show');
    Route::get('master/categories/{category:uuid}/edit', [ProductCategoriesController::class, 'edit'])->name('master.category.edit');
    Route::put('master/categories/{category:uuid}/edit', [ProductCategoriesController::class, 'update'])->name('master.category.update');
    Route::delete('master/categories/{category:uuid}/delete', [ProductCategoriesController::class, 'destroy'])->name('master.category.destroy');

    Route::middleware('permission:read-brand')->group(function(){
        Route::get('master/brands', [ProductBrandController::class, 'index'])->name('master.brand.index');
        Route::get('master/brands/show', [ProductBrandController::class, 'show'])->name('master.brand.show');
        Route::get('master/brands/{brand:uuid}/edit', [ProductBrandController::class, 'edit'])->name('master.brand.edit');
    });

    Route::middleware('permission:create-brand')->group(function(){
        Route::post('master/brands', [ProductBrandController::class, 'store'])->name('master.brand.store');
    });

    Route::middleware('permission:update-brand')->group(function(){
        Route::put('master/brands/{brand:uuid}/edit', [ProductBrandController::class, 'update'])->name('master.brand.update');
        Route::patch('/master/brand/{id}/toggle-status', [ProductBrandController::class, 'toggleStatus'])->name('master.brand.toggleStatus');
    });

    Route::middleware('permission:delete-brand')->group(function(){
        Route::delete('master/brands/{brand:uuid}/delete', [ProductBrandController::class, 'destroy'])->name('master.brand.destroy');
    });


    Route::get('master/units', [ProductUnitController::class, 'index'])->name('master.unit.index');
    Route::post('master/units', [ProductUnitController::class, 'store'])->name('master.unit.store');
    Route::get('master/units/show', [ProductUnitController::class, 'show'])->name('master.unit.show');
    Route::get('master/units/{unit:uuid}/edit', [ProductUnitController::class, 'edit'])->name('master.unit.edit');
    Route::put('master/units/{unit:uuid}/edit', [ProductUnitController::class, 'update'])->name('master.unit.update');
    Route::delete('master/units/{unit:uuid}/delete', [ProductUnitController::class, 'destroy'])->name('master.unit.destroy');

    Route::get('master/products', [ProductController::class, 'index'])->name('master.product.index');
    Route::get('master/products/create', [ProductController::class, 'create'])->name('master.product.create');
    Route::post('master/products/create', [ProductController::class, 'store'])->name('master.product.store');
    Route::get('master/products/show', [ProductController::class, 'show'])->name('master.product.show');
    Route::get('master/products/{product:uuid}/edit', [ProductController::class, 'edit'])->name('master.product.edit');
    Route::put('master/products/{product:uuid}/edit', [ProductController::class, 'update'])->name('master.product.update');
    Route::delete('master/products/{product:uuid}/delete', [ProductController::class, 'forceDestroy'])->name('master.product.destroy');

    Route::middleware('permission:read-role|read-permission')->group(function(){
        Route::get('settings/group-users', [GroupUsersController::class, 'index'])->name('settings.group_user.index');
        Route::get('settings/group-users/show', [GroupUsersController::class, 'show'])->name('settings.group_user.show');

        Route::middleware('permission:read-role')->group(function(){
            Route::get('settings/group-users/{role:id}/edit', [GroupUsersController::class, 'edit'])->name('settings.group_user.edit');
        });
    });

    Route::middleware('permission:create-role')->group(function(){
        Route::get('settings/group-users/create', [GroupUsersController::class, 'create'])->name('settings.group_user.create');
        Route::post('settings/group-users/create', [GroupUsersController::class, 'store'])->name('settings.group_user.store');
    });

    Route::middleware('permission:update-role')->group(function(){
        Route::put('settings/group-users/{role:id}/edit', [GroupUsersController::class, 'update'])->name('settings.group_user.update');
    });

    Route::middleware('permission:delete-role')->group(function(){
        Route::delete('settings/group-users/{role:id}/delete', [GroupUsersController::class, 'destroy'])->name('settings.group_user.destroy');
    });

    Route::middleware('permission:create-permission')->group(function(){
        Route::post('settings/group-users/store-permission', [GroupUsersController::class, 'storePermission'])->name('settings.group_user.store_permission');
    });

    // Route::get('/storage-status', function () {
    //     $path = storage_path('app/public');
    //     $total = disk_total_space($path);
    //     $free  = disk_free_space($path);
    //     $used  = $total - $free;

    //     return view('storage.status', compact('total', 'free', 'used'));
    // });
});
