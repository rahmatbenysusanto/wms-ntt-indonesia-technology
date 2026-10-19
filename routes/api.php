<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WarehouseApiController;
use App\Http\Middleware\ApiKeyMiddleware;

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
});

Route::prefix('v1')->middleware(ApiKeyMiddleware::class)->group(function () {
    Route::get('/inbound', [WarehouseApiController::class, 'inbound']);
    Route::get('/inbound/{id}', [WarehouseApiController::class, 'inboundDetail'])->whereNumber('id');

    Route::get('/outbound', [WarehouseApiController::class, 'outbound']);
    Route::get('/outbound/{id}', [WarehouseApiController::class, 'outboundDetail'])->whereNumber('id');

    Route::get('/inventory', [WarehouseApiController::class, 'inventory']);
    Route::get('/inventory/{purchaseOrderDetailId}', [WarehouseApiController::class, 'inventoryDetail'])->whereNumber('purchaseOrderDetailId');
});
