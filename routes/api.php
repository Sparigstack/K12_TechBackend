<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PersonalController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth')->group(function () {


});
Route::post('/register',[LoginController::class,'register']);
Route::post('/loginValidation',[LoginController::class,'loginValidation']);
 Route::post('/upload', [PersonalController::class, 'store']); 


