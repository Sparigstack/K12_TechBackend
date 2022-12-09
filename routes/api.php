<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\OperatingSystemController;
use Illuminate\Database\Eloquent\Factories\HasFactory;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {


});
Route::post('/register',[LoginController::class,'register']);
Route::post('/loginValidation',[LoginController::class,'loginValidation']);
//inventory
Route::post('/upload', [InventoryController::class, 'uploadInventory']); 
Route::get('/getInventories/{sid?}', [InventoryController::class, 'getInventories']);
Route::get('/getexport', [InventoryController::class, 'getexport']);
Route::post('/addmanualInventoy', [InventoryController::class, 'addmanualInventoy']);
//device
Route::post('/addNdUpdateDevice', [DeviceTypeController::class, 'addDevice']);
Route::get('/allDevice', [DeviceTypeController::class, 'allDevice']);
Route::get('/fetchDevice/{id?}', [DeviceTypeController::class, 'fetchDevice']);
Route::delete('/deleteDevice', [DeviceTypeController::class, 'DeleteDevice']);
Route::get('/fetchDeviceDetails/{id?}', [InventoryController::class, 'fetchDeviceDetail']);


//os
Route::post('/addNdUpdateOs', [OperatingSystemController::class, 'addOs']);
Route::get('/allOs', [OperatingSystemController::class, 'allOs']);
Route::get('/fetchOs/{id?}', [OperatingSystemController::class, 'fetchOs']);
Route::delete('/deleteOs', [OperatingSystemController::class, 'DeleteOs']);
//school
Route::post('/addSchool', [SchoolController::class, 'addSchool']);
