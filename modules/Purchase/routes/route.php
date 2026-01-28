<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseOrderController;
use Modules\Purchase\Http\Controllers\OrderController;
use Modules\Purchase\Http\Controllers\AfterSaleController;

Route::prefix('purchase')->group(function(){

    Route::get('shops', [PurchaseOrderController::class,'shops']);
    Route::get('shop', [PurchaseOrderController::class,'shop']);
    Route::get('prepare', [PurchaseOrderController::class,'renderAndSplitPurchaseOrder']);
    Route::get('orders/{id}', [PurchaseOrderController::class,'lines']);
	Route::apiResource('order', PurchaseOrderController::class);

});


Route::apiResource('order', OrderController::class);
Route::apiResource('afterSale', AfterSaleController::class);


