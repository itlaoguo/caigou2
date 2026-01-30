<?php

use Illuminate\Support\Facades\Route;
use Modules\LinkedMall\Http\Controllers\ShopController;
use Modules\LinkedMall\Http\Controllers\PurchaseOrderController;
use Modules\LinkedMall\Http\Controllers\OrderController;
use Modules\LinkedMall\Http\Controllers\AfterSaleController;

Route::prefix('linkedmall')->group(function(){

    Route::apiResource('shop', ShopController::class);//店铺相关

    //采购单
    Route::get('purchase/parse-excel', [PurchaseOrderController::class,'renderAndSplitPurchaseOrder']);//采购单渲染并拆单
    Route::apiResource('purchase', PurchaseOrderController::class);//采购单

    //订单
    Route::get('orders/{id}', [PurchaseOrderController::class,'lines']);
    Route::apiResource('order', OrderController::class);

    //售后单
    Route::apiResource('refund-order', AfterSaleController::class);

});





