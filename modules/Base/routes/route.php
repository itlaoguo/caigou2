<?php

use Illuminate\Support\Facades\Route;
use Modules\Base\Http\Controllers\PlatformController;
use Modules\Base\Http\Controllers\LanguageController;
use Modules\Base\Http\Controllers\CountryController;

Route::prefix('base')->group(function(){

    Route::get('/platform/all', [PlatformController::class, 'all']);


    Route::apiResource('country', CountryController::class);
    Route::apiResource('platform', PlatformController::class);
    Route::apiResource('language', LanguageController::class);

});


