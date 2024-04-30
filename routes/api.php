<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\favroitController;
use App\Http\Controllers\housesController;
use App\Http\Controllers\SearchHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('auth',[AuthController::class, 'auth']);
// get all houses 

Route::get('houses/{displayType}',[housesController::class, 'show']);
Route::get('houses',[housesController::class, 'showAll']);


Route::middleware('auth:api')->group(function () {

    /// about houses  
    Route::post('postHouse',[housesController::class, 'store']);
    Route::put('updateHouse/{id}',[housesController::class, 'update']);
    Route::delete('delteHouse/{id}', [housesController::class, 'delete']);
    Route::get('house/{id}',[housesController::class, 'index']);    
    // search
    Route::get('houses/search', [SearchHistoryController::class, 'search']);
    Route::get('hisrotey',[SearchHistoryController::class, 'show']);
    Route::delete('deleteHistory/{id}',[SearchHistoryController::class, 'delete']);
    // 
    Route::post('verification',[AuthController::class, 'verfyOtp']);

    Route::post('favroit/{id}',[favroitController::class, 'store']);
    Route::get('getIndexFavorit/{id}',[favroitController::class, 'index']);
    Route::get('getFavorit',[favroitController::class, 'show']);
    Route::get('getFavoritAdmin',[favroitController::class, 'showAll']);

});

