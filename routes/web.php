<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralRoomController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseController;
use App\Http\Middleware\AuthLoginMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('auth.login');
});

Route::middleware(AuthLoginMiddleware::class)->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    Route::prefix('/customer')->controller(CustomerController::class)->group(function () {
        Route::get('/', 'index')->name('customer');
        Route::post('/', 'store')->name('customer.store');
    });

    Route::prefix('/vendor')->controller(VendorController::class)->group(function () {
        Route::get('/', 'index')->name('vendor');
        Route::post('/', 'store')->name('vendor.store');
        Route::post('/edit', 'edit')->name('vendor.edit');
        Route::get('/find', 'findJSON')->name('vendor.find');
        Route::get('/delete', 'delete')->name('vendor.delete');
    });

    Route::prefix('/warehouse')->controller(WarehouseController::class)->group(function () {
        Route::get('/', 'index')->name('warehouse');
    });

    Route::prefix('/storage')->controller(StorageController::class)->group(function () {
        Route::get('/', 'index')->name('storage');
        Route::get('/find/area', 'getArea')->name('storage.find.area');
        Route::get('/find/rak', 'getRak')->name('storage.find.rak');
        Route::get('/find/bin', 'getBin')->name('storage.find.bin');

        Route::post('/create', 'store')->name('storage.create');
    });

    Route::prefix('/inbound')->controller(InboundController::class)->group(function () {
        Route::prefix('/purchase-order')->group(function () {
            Route::get('/', 'purchaseOrder')->name('inbound.purchase-order');
            Route::get('/detail', 'purchaseOrderDetail')->name('inbound.purchase-order-detail');
            Route::get('/upload', 'purchaseOrderUpload')->name('inbound.purchase-order-upload');
            Route::post('/upload', 'purchaseOrderUploadProcess')->name('inbound.purchase-order-upload-process');
            Route::post('/update-status', 'changeStatusPurchaseOrder')->name('inbound.changeStatusPurchaseOrder');

            // Upload Serial Number
            Route::get('/upload/serial-number', 'purchaseOrderSerialNumber')->name('inbound.upload.serial-number');
        });

        Route::prefix('/quality-control')->group(function () {
            Route::get('/', 'qualityControl')->name('inbound.quality-control');
            Route::get('/list', 'qualityControlList')->name('inbound.quality-control-list');
            Route::get('/process', 'qualityControlProcess')->name('inbound.quality-control-process');
            Route::post('/process', 'qualityControlStoreProcess')->name('inbound.quality-control-store-process');

            // CCW Process
            Route::get('/process-ccw', 'qualityControlProcessCcw')->name('inbound.quality-control-process-ccw');
            Route::post('/process-ccw', 'qualityControlStoreProcessCcw')->name('inbound.quality-control-process-ccw-store');
            Route::post('/upload-ccw', 'uploadFileCCW')->name('inbound.quality-control.upload.ccw');
        });

        Route::prefix('/put-away')->group(function () {
            Route::get('/', 'putAway')->name('inbound.put-away');
            Route::get('/detail', 'putAwayDetail')->name('inbound.put-away-detail');
            Route::get('/process', 'putAwayProcess')->name('inbound.put-away-process');
            Route::post('/store', 'putAwayStore')->name('inbound.put-away.store');

            // JSON
            Route::get('/find-serial-number', 'findSerialNumber')->name('inbound.put-away.find-serial-number');
            Route::get('/fins-serial-number-inventory', 'findSNInventory')->name('inbound.put-away.find-serial-number-inventory');
        });
    });

    Route::prefix('/inventory')->controller(InventoryController::class)->group(function () {
        Route::get('/', 'index')->name('inventory.index');
        Route::get('/detail', 'detail')->name('inventory.detail');
        Route::get('/cycle-count', 'cycleCount')->name('inventory.cycle-count');

        Route::prefix('/transfer-location')->group(function () {
            Route::get('/', 'transferLocation')->name('inventory.transfer-location');
            Route::get('/create', 'transferLocationCreate')->name('inventory.transfer-location-create');
            Route::post('/store', 'transferLocationStore')->name('inventory.transfer-location-store');

            // JSON
            Route::get('/find-pa-number', 'transferLocationFindNumber')->name('inventory.transfer-location-find-pa-number');
        });
    });

    Route::prefix('/general-room')->controller(GeneralRoomController::class)->group(function () {
        Route::get('/', 'index')->name('general-room.index');
    });

    Route::prefix('/outbound')->controller(OutboundController::class)->group(function () {
        Route::get('/', 'index')->name('outbound.index');
        Route::get('/create', 'create')->name('outbound.create');
        Route::post('/create', 'store')->name('outbound.store');

        // JSON
        Route::get('/sales-doc', 'getItemBySalesDoc')->name('outbound.sales-doc');
        Route::get('/find-inventory-detail', 'getItemByInventoryDetail')->name('outbound.inventory-detail');
        Route::get('/find-product', 'getItemByProduct')->name('outbound.inventory-product');
    });
});

Route::get('/compare-sap-ccw', [InboundController::class, 'compareSapCcw'])->name('compare-sap-ccw');































