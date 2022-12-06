<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\OperatingSystemController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {


});
Route::post('/register',[LoginController::class,'register']);
Route::post('/loginValidation',[LoginController::class,'loginValidation']);
Route::post('/upload', [InventoryController::class, 'store']); 
//device
Route::post('/addNdUpdateDevice', [DeviceController::class, 'addDevice']);
Route::get('/allDevice', [DeviceController::class, 'allDevice']);
Route::get('/fetchDevice/{id?}', [DeviceController::class, 'fetchDevice']);
Route::delete('/deleteDevice', [DeviceController::class, 'DeleteDevice']);
//os
Route::post('/addNdUpdateOs', [OperatingSystemController::class, 'addOs']);
Route::get('/allOs', [OperatingSystemController::class, 'allOs']);
