<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [UserController::class, 'show']);
    Route::get('/balance', [UserController::class, 'balance']);
    Route::get('/transactions', [UserController::class, 'transactions']);

    Route::post('/transfer', [TransferController::class, 'store']);
    
});
