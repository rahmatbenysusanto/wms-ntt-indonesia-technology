<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'login')->name('login');
});

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
    Route::get('/find/level', 'getLevel')->name('storage.find.level');
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
    });

    Route::prefix('/put-away')->group(function () {
        Route::get('/', 'putAway')->name('inbound.put-away');
        Route::get('/detail', 'putAwayDetail')->name('inbound.put-away-detail');
        Route::get('/process', 'putAwayProcess')->name('inbound.put-away-process');
    });
});
