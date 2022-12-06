<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\OperatingSystemController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {


});
Route::post('/register',[LoginController::class,'register']);
Route::post('/loginValidation',[LoginController::class,'loginValidation']);
//inventory
Route::post('/upload', [InventoryController::class, 'uploadInventory']); 
Route::get('/getInventories', [InventoryController::class, 'getInventories']);
//device
Route::post('/addNdUpdateDevice', [DeviceTypeController::class, 'addDevice']);
Route::get('/allDevice', [DeviceTypeController::class, 'allDevice']);
Route::get('/fetchDevice/{id?}', [DeviceTypeController::class, 'fetchDevice']);
Route::delete('/deleteDevice', [DeviceTypeController::class, 'DeleteDevice']);
//os
Route::post('/addNdUpdateOs', [OperatingSystemController::class, 'addOs']);
Route::get('/allOs', [OperatingSystemController::class, 'allOs']);
Route::get('/fetchOs/{id?}', [OperatingSystemController::class, 'fetchOs']);
Route::delete('/deleteOs', [OperatingSystemController::class, 'DeleteOs']);
