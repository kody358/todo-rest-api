<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

// 認証不要のルート
Route::post('/login', [AuthController::class, 'login']);

// 認証が必要なルート
Route::middleware('auth:sanctum')->group(function () {
    // 認証関連
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Todo関連
    Route::apiResource('todos', TodoController::class);
    Route::patch('todos/{id}/restore', [TodoController::class, 'restore'])->name('todos.restore');
});
