<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    Route::post("/users", [UserController::class, "register"]);
    Route::post("/users/login", [UserController::class, "login"]);
});

Route::middleware('auth:sanctum')->group(function () {
    // ENDPOINT UNTUK SEMUA USER (PUBLIC)
    Route::get('/users/current', [\App\Http\Controllers\UserController::class, 'get']);
    Route::patch('/users/current', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/logout', [\App\Http\Controllers\UserController::class, 'logout']);
    Route::patch('/users/change-password', [\App\Http\Controllers\UserController::class, 'changePassword']);


    // ENDPOINT UNTUK ADMIN
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {

        // USERS MANAGEMENT
        Route::post("/admin/users", [\App\Http\Controllers\AdminController::class, "register"]);
        Route::get("/admin/users/{id}", [\App\Http\Controllers\AdminController::class, "getUserById"]);
        Route::patch("/admin/users/{id}", [\App\Http\Controllers\AdminController::class, "updateUserById"]);
        Route::delete("/admin/users/{id}", [\App\Http\Controllers\AdminController::class, "deleteUserById"]);
        Route::patch("/admin/users/{id}/restore", [\App\Http\Controllers\AdminController::class, "restoreUserById"]);
        Route::delete("/admin/users/{id}/force", [\App\Http\Controllers\AdminController::class, "forceDeleteUserById"]);
        Route::get("/admin/users", [\App\Http\Controllers\AdminController::class, "getUsers"]);
        Route::patch("/admin/users/{id}/status", [\App\Http\Controllers\AdminController::class, "updateStatus"]);

        // ROLES MANAGEMENT
        Route::post("/admin/roles", [\App\Http\Controllers\AdminController::class, "addRole"]);
        Route::get("/admin/roles/{id}", [\App\Http\Controllers\AdminController::class, "getRoleById"]);
        Route::patch("/admin/roles/{id}", [\App\Http\Controllers\AdminController::class, "updateRoleById"]);
        Route::delete('/admin/roles/{id}', [\App\Http\Controllers\AdminController::class, "deleteRoleById"]);
        Route::patch("/admin/roles/{id}/restore", [\App\Http\Controllers\AdminController::class, "restoreRoleById"]);
        Route::delete("/admin/roles/{id}/force", [\App\Http\Controllers\AdminController::class, "forceDeleteRoleById"]);
        Route::get("/admin/roles", [\App\Http\Controllers\AdminController::class, "getRoles"]);
        Route::get("/admin/roles/{id}/users", [\App\Http\Controllers\AdminController::class, "getUsersByRoleId"]);
        Route::patch("/admin/users/{id}/roles", [\App\Http\Controllers\AdminController::class, "updateUserRole"]);

    });
});

Route::post('/forgot-password', [UserController::class, 'sendResetLink']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);




