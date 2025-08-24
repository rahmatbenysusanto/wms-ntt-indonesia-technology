<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralRoomController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\PmRoomController;
use App\Http\Controllers\SpareRoomController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseController;
use App\Http\Middleware\AuthLoginMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('auth.login');
    Route::post('/mobile/login', 'loginPostMobile')->name('auth.mobile.login');
    Route::get('/logout', 'logout')->name('logout');
});

Route::middleware(AuthLoginMiddleware::class)->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');

        Route::get('/dashboard-po', 'dashboardPo')->name('dashboard.po');
        Route::get('/dashboard-po/detail', 'dashboardDetail')->name('dashboard.po.detail');
        Route::get('/dashboard-so/detail', 'dashboardSoDetail')->name('dashboard.so.detail');
        Route::get('/dashboard-stock-sn', 'dashboardStockSN')->name('dashboard.po.stock.sn');
        Route::get('/dashboard-outbound-sn', 'dashboardOutboundSN')->name('dashboard.po.outbound.sn');

        Route::get('/dashboard-aging', 'dashboardAging')->name('dashboard.aging');
        Route::get('/dashboard-aging-detail', 'dashboardAgingDetail')->name('dashboard.aging.detail');

        Route::get('/dashboard-outbound', 'dashboardOutbound')->name('dashboard.outbound');
        Route::get('/dashboard-outbound-detail', 'dashboardOutboundDetail')->name('dashboard.outbound.detail');
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

        Route::get('/delete', 'delete')->name('storage.delete');
    });

    Route::prefix('/inbound')->controller(InboundController::class)->group(function () {
        Route::prefix('/purchase-order')->group(function () {
            Route::get('/', 'purchaseOrder')->name('inbound.purchase-order');
            Route::get('/detail', 'purchaseOrderDetail')->name('inbound.purchase-order-detail');
            Route::get('/upload', 'purchaseOrderUpload')->name('inbound.purchase-order-upload');
            Route::post('/upload', 'purchaseOrderUploadProcess')->name('inbound.purchase-order-upload-process');
            Route::post('/update-status', 'changeStatusPurchaseOrder')->name('inbound.changeStatusPurchaseOrder');

            Route::get('/download-excel', 'purchaseOrderDownloadExcel')->name('inbound.purchase-order-download-excel');
            Route::get('/download-pdf', 'purchaseOrderDownloadPdf')->name('inbound.purchase-order-download-pdf');

            // Upload Serial Number
            Route::get('/upload/serial-number', 'purchaseOrderSerialNumber')->name('inbound.upload.serial-number');

            Route::prefix('/edit')->group(function () {
                Route::get('/', 'editPurchaseOrder')->name('inbound.edit-purchase-order');
                Route::get('/product', 'editPurchaseOrderProduct')->name('inbound.edit-purchase-order-product');
                Route::post('/request-edit', 'editPurchaseOrderRequestEdit')->name('inbound.edit-purchase-order-request-edit');
                Route::get('/detail', 'editPurchaseOrderDetail')->name('inbound.edit.detail');
                Route::get('/approved', 'editPurchaseOrderApproved')->name('inbound.edit.approved');
                Route::get('/cancel', 'editPurchaseOrderCancel')->name('inbound.edit.cancel');

                // JSON
                Route::get('/list-material', 'listMaterialEditPO')->name('listMaterialEditPO');
            });
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
            Route::get('/detail/open', 'putAwayDetailOpen')->name('inbound.put-away-detail.open');
            Route::get('/process', 'putAwayProcess')->name('inbound.put-away-process');
            Route::post('/store', 'putAwayStore')->name('inbound.put-away.store');

            Route::get('/edit', 'putAwayEdit')->name('inbound.put-away-edit');

            // JSON
            Route::get('/find-serial-number', 'findSerialNumber')->name('inbound.put-away.find-serial-number');
            Route::get('/fins-serial-number-inventory', 'findSNInventory')->name('inbound.put-away.find-serial-number-inventory');
        });
    });

    Route::prefix('/inventory')->controller(InventoryController::class)->group(function () {
        Route::get('/', 'index')->name('inventory.index');
        Route::get('/product-detail', 'indexDetail')->name('inventory.indexDetail');
        Route::get('/aging', 'aging')->name('inventory.aging');
        Route::get('/box', 'box')->name('inventory.box');
        Route::get('/box-detail', 'boxDetail')->name('inventory.box.detail');
        Route::get('/detail', 'detail')->name('inventory.detail');

        Route::get('/cycle-count', 'cycleCount')->name('inventory.cycle-count');
        Route::get('/cycle-count-detail', 'cycleCountDetail')->name('inventory.cycle-count-detail');

        Route::post('/change-type', 'changeTypeProduct')->name('inventory.change.type.product');
        Route::get('/change-box', 'changeBox')->name('inventory.change.box');
        Route::post('/change-box-store', 'changeBoxStore')->name('inventory.change.box.post');

        Route::get('/change-new-box', 'changeNewBox')->name('inventory.change.new-box');
        Route::post('/change-new-box-store', 'changeNewBoxStore')->name('inventory.change.new-box.post');

        Route::prefix('/transfer-location')->group(function () {
            Route::get('/', 'transferLocation')->name('inventory.transfer-location');
            Route::get('/create', 'transferLocationCreate')->name('inventory.transfer-location-create');
            Route::post('/store', 'transferLocationStore')->name('inventory.transfer-location-store');

            // JSON
            Route::get('/find-pa-number', 'transferLocationFindNumber')->name('inventory.transfer-location-find-pa-number');
        });

        // Download Excel
        Route::get('/download-excel', 'downloadExcel')->name('inventory.download-excel');
        Route::get('/download-pdf', 'downloadPdf')->name('inventory.download-pdf');
        Route::get('/aging/download-excel', 'downloadExcelAging')->name('inventory.aging.download-excel');
        Route::get('/aging/download-pdf', 'downloadPdfAging')->name('inventory.aging.download-pdf');
        Route::get('/box/download-excel', 'downloadExcelBox')->name('inventory.box.report-excel');
        Route::get('/box/download-pdf', 'downloadPdfBox')->name('inventory.box.report-pdf');
    });

    Route::prefix('/general-room')->controller(GeneralRoomController::class)->group(function () {
        Route::get('/', 'index')->name('general-room.index');
        Route::get('/detail', 'detail')->name('general-room.detail');
        Route::get('/create-box', 'createBox')->name('general-room.create-box');
        Route::post('/create-box-store', 'createBoxStore')->name('general-room.create-box-store');

        Route::get('/outbound', 'outbound')->name('general-room.outbound');
        Route::get('/create', 'create')->name('general-room.create');
        Route::post('/create-outbound', 'createOutbound')->name('general-room.create.outbound');

        Route::get('/return', 'return')->name('general-room.return');
        Route::post('/return-store', 'returnStore')->name('general-room.return.store');
    });

    Route::prefix('/pm-room')->controller(PMRoomController::class)->group(function () {
        Route::get('/', 'index')->name('pm-room.index');
        Route::get('/detail', 'detail')->name('pm-room.detail');
        Route::get('/create-box', 'createBox')->name('pm-room.create-box');
        Route::post('/create-box-store', 'createBoxStore')->name('pm-room.create-box-store');

        Route::get('/outbound', 'outbound')->name('pm-room.outbound');
        Route::get('/create', 'create')->name('pm-room.create');
        Route::post('/create-outbound', 'createOutbound')->name('pm-room.create.outbound');

        Route::get('/return', 'return')->name('pm-room.return');
        Route::post('/return-store', 'returnStore')->name('pm-room.return.store');
    });

    Route::prefix('/spare-room')->controller(SpareRoomController::class)->group(function () {
        Route::get('/', 'index')->name('spare-room.index');
        Route::get('/detail', 'detail')->name('spare-room.detail');
        Route::get('/create-box', 'createBox')->name('spare-room.create-box');
        Route::post('/create-box-store', 'createBoxStore')->name('spare-room.create-box-store');

        Route::get('/outbound', 'outbound')->name('spare-room.outbound');
        Route::get('/create', 'create')->name('spare-room.create');
        Route::post('/create-outbound', 'createOutbound')->name('spare-room.create.outbound');

        Route::get('/return', 'return')->name('spare-room.return');
        Route::post('/return-store', 'returnStore')->name('spare-room.return.store');
    });

    Route::prefix('/outbound')->controller(OutboundController::class)->group(function () {
        Route::get('/', 'index')->name('outbound.index');
        Route::get('/create', 'create')->name('outbound.create');
        Route::post('/create', 'store')->name('outbound.store');
        Route::get('/detail', 'detail')->name('outbound.detail');

        Route::get('/return', 'return')->name('outbound.return');
        Route::get('/return/get-products', 'returnGetProducts')->name('outbound.return.get-products');
        Route::post('/return-store', 'returnStore')->name('outbound.return.store');

        // JSON
        Route::get('/sales-doc', 'getItemBySalesDoc')->name('outbound.sales-doc');
        Route::get('/find-inventory-detail', 'getItemByInventoryDetail')->name('outbound.inventory-detail');
        Route::get('/find-product', 'getItemByProduct')->name('outbound.inventory-product');

        // Download Report
        Route::get('/download-excel', 'downloadExcel')->name('outbound.download-excel');
    });

    Route::prefix('/user')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('user.index');
        Route::post('/post', 'store')->name('user.store');
        Route::get('/delete', 'delete')->name('user.delete');
        Route::get('/find', 'find')->name('user.find');
        Route::post('/update', 'update')->name('user.update');
    });

    // Mobile APP
    Route::prefix('/mobile')->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'dashboardMobile')->name('dashboardMobile');
        });

        Route::prefix('/inbound')->controller(InboundController::class)->group(function () {
            Route::get('/', 'indexMobile')->name('inbound.index.mobile');
            Route::get('/detail', 'indexDetailMobile')->name('inbound.indexDetail.mobile');
            Route::get('/detail/so', 'indexDetailSoMobile')->name('inbound.indexDetail.so');
            Route::get('/detail/so/sn', 'indexDetailSoSnMobile')->name('inbound.indexDetail.so.sn');
        });

        Route::prefix('/outbound')->controller(OutboundController::class)->group(function () {
            Route::get('/', 'indexMobile')->name('outbound.index.mobile');
            Route::get('/detail', 'indexDetailMobile')->name('outbound.indexDetail.mobile');
            Route::get('/detail/sn', 'indexDetailSnMobile')->name('outbound.indexDetailSN.mobile');
        });

        Route::prefix('/inventory')->controller(InventoryController::class)->group(function () {
            Route::get('/', 'indexMobile')->name('inventory.index.mobile');
            Route::get('/detail', 'indexDetailMobile')->name('inventory.indexDetail.mobile');

            Route::get('/box', 'boxMobile')->name('inventory.box.mobile');
            Route::get('/box-detail', 'boxDetailMobile')->name('inventory.box.detail.mobile');

            Route::get('/aging', 'agingMobile')->name('inventory.aging.mobile');
            Route::get('/aging-detail', 'agingDetailMobile')->name('dashboard.mobile.aging.detail');
        });
    });
});

Route::get('/compare-sap-ccw', [InboundController::class, 'compareSapCcw'])->name('compare-sap-ccw');































