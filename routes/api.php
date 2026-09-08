<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function (){
    Route::prefix('auth')->group(function (){
        Route::controller(\App\Http\Controllers\Auth\AuthController::class)->group(function (){
            Route::post('register', 'register');
            Route::post('login', 'login');
            
            Route::middleware('auth:sanctum')->group(function (){
                Route::post('logout', 'logout');
                Route::get('me', 'me');
            });
        });     
    });

    Route::middleware('auth:sanctum')->group(function (){
        Route::prefix('monitors')->group(function (){
            Route::controller(\App\Http\Controllers\Api\MonitorController::class)->group(function (){
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{monitor}', 'show');
                Route::put('/{monitor}', 'update');
                Route::delete('/{monitor}', 'destroy');
            });
        });
    });
});



