<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CustomerTypeController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile', [AuthController::class, 'profile']);
});

Route::prefix('admin')->group(function () {
    Route::resource('customer-types', CustomerTypeController::class);
});