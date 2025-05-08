<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\OtpVerificationsController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StaffPermissionsController;
use App\Http\Controllers\Api\ExternalServiceLogsController;

/*
|--------------------------------------------------------------------------
| Public Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function () {
    Route::controller(OtpVerificationsController::class)->group(function () {
        Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
        Route::post('resend-otp', 'resendOtp')->name('resend-otp');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('admin/login', 'superAdminLogin')->name('admin.login');
        Route::post('store_owner/login', 'storeOwnerLogin')->name('store-owner.login');
        Route::post('staff/login', 'staffLogin')->name('staff.login');
    });
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('user.type:super_admin')->group(function () {
        Route::get('store', [StoreController::class, 'index'])->name('store.index');
        Route::post('store', [StoreController::class, 'store'])->name('store.store');
        Route::put('store/{store}', [StoreController::class, 'update'])->name('store.update');
        Route::delete('store/{store}', [StoreController::class, 'destroy'])->name('store.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Store Routes (All Roles)
    |--------------------------------------------------------------------------
    */
    Route::get('store/{store}', [StoreController::class, 'show'])->name('store.show');

    Route::prefix('store/{store}')->name('store.')->group(function () {

        // Products - Only authorized users with "manageProducts" policy
        Route::middleware('can:manageProducts,store')->apiResource('products', ProductController::class);

        Route::middleware('can:manageOrders,store')->apiResource('orders', OrderController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Settings - Only store owners or authorized roles
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:manageSettings,store')
        ->put('settings', [StoreController::class, 'updateSettings'])
        ->name('settings.update');

    /*
    |--------------------------------------------------------------------------
    | Staff Management - Only for users with manageStaff permission
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:manageStaff')->apiResource('store.staff', StaffController::class)
        ->only(['index', 'store', 'update', 'destroy', 'show'])
        ->parameters(['staff' => 'user'])
        ->names([
            'index' => 'store.staff.index',
            'store' => 'store.staff.store',
            'update' => 'store.staff.update',
            'destroy' => 'store.staff.destroy',
            'show' => 'store.staff.show',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Staff Permissions - Only super_admin or store_owner
    |--------------------------------------------------------------------------
    */
    Route::prefix('store/{store}/staff/{user}')->name('staff.')->group(function () {
        Route::middleware('user.type:store_owner,super_admin')
            ->apiResource('permissions', StaffPermissionsController::class)
            ->only(['index', 'update', 'destroy'])
            ->names([
                'index' => 'permissions.index',
                'update' => 'permissions.update',
                'destroy' => 'permissions.destroy',
            ]);
    });

    /*
    |--------------------------------------------------------------------------
    | External Service Logs
    |--------------------------------------------------------------------------
    */
    Route::controller(ExternalServiceLogsController::class)->group(function () {
        Route::apiResource('external-logs', ExternalServiceLogsController::class)
            ->except(['store', 'update'])
            ->names([
                'index' => 'external-logs.index',
                'show' => 'external-logs.show',
                'destroy' => 'external-logs.destroy',
            ]);

        Route::post('external-logs/{log}/retry', 'retry')->name('external-logs.retry');
    });

});
