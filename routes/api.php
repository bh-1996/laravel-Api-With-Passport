<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register'])->name('register.user');

Route::post('/login', [AuthController::class, 'login'])->name('login');



Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('showProfile');
    Route::put('/profile-update', [AuthController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/profile-delete', [AuthController::class, 'deleteAccount'])->name('deleteAccount');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('posts', PostController::class);
});
