<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    Route::post("/users", [UserController::class, "register"]);
    Route::post("/users/login", [UserController::class, "login"]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/current', [\App\Http\Controllers\UserController::class, 'get']);
    Route::patch('/users/current', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/logout', [\App\Http\Controllers\UserController::class, 'logout']);
    Route::patch('/users/change-password', [\App\Http\Controllers\UserController::class, 'changePassword']);
});

Route::post('/forgot-password', [UserController::class, 'sendResetLink']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);


