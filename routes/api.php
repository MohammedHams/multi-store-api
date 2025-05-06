<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\OtpVerificationsController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StaffPermissionsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ExternalServiceLogsController;
use App\Http\Controllers\Api\StaffController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/verify-otp', [OtpVerificationsController::class, 'verifyOtp']);
    Route::post('/resend-otp', [OtpVerificationsController::class, 'resendOtp']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/admin/login', [AuthController::class, 'superAdminLogin']);
    Route::post('/store_owner/login', [AuthController::class, 'storeOwnerLogin']);
    Route::post('/staff/login', [AuthController::class, 'staffLogin']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('stores', StoreController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('external-logs', ExternalServiceLogsController::class)->except(['store', 'update']);
    Route::post('/orders/{order}/send-whatsapp', [OrderController::class, 'sendToWhatsApp']);
    Route::post('/external-logs/{log}/retry', [ExternalServiceLogsController::class, 'retry']);

    Route::prefix('stores/{store}/staff/{user}')->group(function () {
        Route::apiResource('permissions', StaffPermissionsController::class)->only([
            'index', 'update', 'destroy'
        ]);
    });
});
Route::middleware(['auth:sanctum', 'user.type:store_owner,staff'])->group(function () {
    Route::prefix('store/{store}')->group(function () {
        // إدارة الطلبات
        Route::get('/orders', [OrderController::class, 'index'])
            ->middleware('can:manageOrders,App\Models\Store');

        Route::apiResource('/products', ProductController::class)
            ->middleware('can:manageProducts,App\Models\Store');

        Route::put('/settings', [StoreController::class, 'updateSettings'])
            ->middleware('can:manageSettings,App\Models\Store');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::prefix('stores/{store}/staff/{user}')->group(function () {
                Route::apiResource('permissions', StaffPermissionsController::class)
                    ->only(['index', 'update', 'destroy']);
            });
        });
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('stores.staff', StaffController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['staff' => 'user']);
});
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
})->name('verification.verify');
