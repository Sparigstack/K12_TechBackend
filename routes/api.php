<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GoogleAuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test',[RegisteredUserController::class,'getData']);
Route::post('/addUser',[GoogleAuthController::class,'addUser']);
//Route::get('auth/google',[GoogleAuthController::class, 'redirect'])->name('google-auth');
//Route::get('/call',[GoogleAuthController::class, 'callbackGoogle']);

