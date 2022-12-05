<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DeviceController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {


});
Route::post('/register',[LoginController::class,'register']);
Route::post('/loginValidation',[LoginController::class,'loginValidation']);
Route::post('/upload', [InventoryController::class, 'store']); 
Route::post('/addNdUpdateDevice', [DeviceController::class, 'addDevice']);
Route::get('/allDevice', [DeviceController::class, 'allDevice']);
Route::get('/fetchDevice', [DeviceController::class, 'allDevice']);


